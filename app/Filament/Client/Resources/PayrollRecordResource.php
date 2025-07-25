<?php

namespace App\Filament\Client\Resources;

use App\Filament\Client\Resources\PayrollRecordResource\Pages;
use App\Filament\Client\Resources\PayrollRecordResource\RelationManagers;
use App\Models\Department;
use App\Models\Payroll;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Filament\Tables\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Notifications\Notification;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Collection;
use ZipArchive;
use Illuminate\Support\Str;

class PayrollRecordResource extends Resource
{
    protected static ?string $model = Payroll::class;

    protected static ?string $navigationLabel = 'Payslips';
    protected static ?string $navigationGroup = 'Payroll Management';
    protected static ?string $modelLabel = 'Payslips';
    protected static ?int $navigationSort = 2;
    protected static ?string $tenantOwnershipRelationshipName = 'team';

    public static function getEloquentQuery(): Builder
    {
        return Filament::getTenant()
            ->payrolls()
            ->getQuery()
            ->with(['user.assignedDepartment.department', 'user.assignedShift.shift'])
            ->orderByDesc('date_range_start')
            ->visibleToCurrentUser();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Employee'),

                TextColumn::make('user.assignedDepartment.department.name')
                    ->label('Department'),

                TextColumn::make('user.assignedShift.shift.name')
                    ->label('Shift'),

                TextColumn::make('tax_data.monthly_tax_calculated')
                    ->label('Tax')
                    ->formatStateUsing(fn($state) => self::formatCurrency($state)),

                TextColumn::make('net_payable_salary')
                    ->label('Net Pay')
                    ->formatStateUsing(fn($state) => self::formatCurrency($state)),
            ])
            ->groups([
                Group::make('date_range_start')
                    ->label('')
                    ->collapsible()
                    ->getTitleFromRecordUsing(function ($record) {
                        return $record->date_range_start->format('F Y');
                    })
                    ->orderQueryUsing(fn($query, string $direction) => $query->orderBy('date_range_start', $direction)),
            ])
            ->defaultGroup('date_range_start')
            ->filters([
                ...(Auth::user()->hasRole('Admin') ||
                    Auth::user()->can('payroll.manageRecords') ||
                    Auth::user()->can('payroll.approve')
                    ? [
                        SelectFilter::make('payroll_period')
                            ->label('Period')
                            ->searchable()
                            ->placeholder('Select Month')
                            ->options(function () {
                                return Payroll::query()
                                    ->selectRaw('DISTINCT DATE_FORMAT(date_range_start, "%Y-%m-01") as start')
                                    ->orderByDesc('start')
                                    ->pluck('start', 'start')
                                    ->mapWithKeys(fn($date) => [
                                        $date => \Carbon\Carbon::parse($date)->format('F Y'),
                                    ]);
                            })
                            ->default(function () {
                                // Get the latest payroll period
                                return Payroll::query()
                                    ->selectRaw('DATE_FORMAT(MAX(date_range_start), "%Y-%m-01") as latest')
                                    ->value('latest');
                            })
                            ->query(function (Builder $query, array $data) {
                                if (!empty($data['value'])) {
                                    $query->whereDate('date_range_start', $data['value']);
                                }
                            }),

                        SelectFilter::make('user_id')
                            ->label('Employee')
                            ->searchable()
                            ->placeholder('Search Employee')
                            ->options(
                                fn() =>
                                Filament::getTenant()
                                    ->users()
                                    ->whereHas('bankDetails', function ($q) {
                                        $q->where('team_id', Filament::getTenant()->id)
                                            ->where('active', true)
                                            ->where('base_salary', '>', 0);
                                    })
                                    ->pluck('name', 'id')
                                    ->toArray()
                            )
                            ->query(
                                fn(Builder $query, array $data) =>
                                $query->when(
                                    $data['value'],
                                    fn($q) => $q->where('user_id', $data['value'])
                                )
                            ),

                        SelectFilter::make('department_id')
                            ->label('Department')
                            ->searchable()
                            ->placeholder('Select Department')
                            ->options(function () {
                                $user = Auth::user();
                                return Filament::getTenant()->departments()
                                    ->pluck('name', 'id')
                                    ->toArray();
                            })
                            ->query(function (Builder $query, array $data): Builder {
                                if (!isset($data['value']) || $data['value'] === '') {
                                    return $query;
                                }

                                return $query->whereHas('user.assignedDepartment', function ($q) use ($data) {
                                    $q->where('department_id', $data['value']);
                                });
                            }),

                        SelectFilter::make('shift_id')
                            ->label('Shift')
                            ->searchable()
                            ->placeholder('Select Shift')
                            ->options(function () {
                                return Filament::getTenant()->shifts()
                                    ->pluck('name', 'id')
                                    ->toArray();
                            })
                            ->query(function (Builder $query, array $data): Builder {
                                if (!isset($data['value']) || $data['value'] === '') {
                                    return $query;
                                }

                                return $query->whereHas('user.assignedShift', function ($q) use ($data) {
                                    $q->where('shift_id', $data['value']);
                                });
                            }),
                    ] : [])
            ], layout: FiltersLayout::AboveContent)

            ->actions([
                ViewAction::make('viewPayroll')
                    ->label('View')
                    ->slideOver()
                    ->modalHeading('Pay Details')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->modalContent(
                        fn($record) =>
                        view('livewire.view-payslip', [
                            'payroll' => $record,
                        ])
                    )
                    ->visible(
                        fn($record) =>
                        $record->status == 1 && (
                            Auth::user()->hasRole('Admin') ||
                            Auth::user()->can('payroll.manageRecords') ||
                            Auth::user()->can('payroll.viewRecords') ||
                            Auth::user()->can('payroll.approve')
                        )
                    ),

                TableAction::make('downloadPdf')
                    ->label('Download')
                    ->icon('heroicon-o-document-arrow-down')
                    ->url(fn($record) => route('payroll.download', $record))
                    ->visible(
                        fn($record) =>
                        $record->status == 1 && (
                            Auth::user()->hasRole('Admin') ||
                            Auth::user()->can('payroll.manageRecords') ||
                            Auth::user()->can('payroll.approve')
                        )
                    ),
            ]);
    }

    protected static function getCurrencySymbol(): string
    {
        $country = Filament::getTenant()->country_id;

        return DB::table('tax_slabs')
            ->where('country_id', $country)
            ->value('salary_currency') ?? '';
    }

    protected static function formatCurrency(float|int|null $amount): string
    {
        $symbol = self::getCurrencySymbol();
        return $symbol . ' ' . number_format(round($amount ?? 0));
    }

    public static function getGeneratePayrollHeaderAction(): Action
    {
        return Action::make('generatePayroll')
            ->label('Export Payroll')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('success')
            ->tooltip('Export as Excel File')
            ->modalHeading('Export Payroll')
            ->modalSubmitActionLabel('Download')
            ->form(fn() => [
                Select::make('selectedMonth')
                    ->placeholder('Select Month')
                    ->options(self::getAvailableMonths())
                    ->required()
                    ->preload(),
            ])
            ->action(fn(array $data) => self::generatePayrollExport($data['selectedMonth']))
            ->visible(fn() => self::canExportPayroll());
    }

    private static function getAvailableMonths(): array
    {
        return Filament::getTenant()->payrolls()
            ->select('date_range_start')
            ->where('status', 1)
            ->orderByDesc('date_range_start')
            ->distinct()
            ->pluck('date_range_start')
            ->map(fn($date) => Carbon::parse($date)->format('F Y'))
            ->unique()
            ->mapWithKeys(fn($month) => [$month => $month])
            ->toArray();
    }

    private static function generatePayrollExport(string $selectedMonth): StreamedResponse
    {
        $monthStartDate = Carbon::parse("1 {$selectedMonth}");

        $payrolls = Filament::getTenant()->payrolls()
            ->select(['id', 'user_id', 'net_payable_salary'])
            ->with(['user:id,name'])
            ->where('status', 1)
            ->whereDate('date_range_start', $monthStartDate)
            ->orderBy('user_id')
            ->get();

        if ($payrolls->isEmpty()) {
            throw new \Exception('No payroll data found for the selected month.');
        }

        return self::createExcelResponse($payrolls, $selectedMonth);
    }

    private static function createExcelResponse(Collection $payrolls, string $selectedMonth): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers with styling
        $headers = ['Employee Name', 'Net Salary'];
        $sheet->fromArray($headers, null, 'A1');

        // Style header row
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2E8F0']
            ]
        ];
        $sheet->getStyle('A1:B1')->applyFromArray($headerStyle);

        // Add data
        $data = $payrolls->map(fn($payroll) => [
            $payroll->user->name ?? 'N/A',
            $payroll->net_payable_salary
        ])->toArray();

        $sheet->fromArray($data, null, 'A2');

        // Auto-size columns
        foreach (range('A', 'B') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Format salary column as currency
        $lastRow = count($data) + 1;
        $sheet->getStyle("B2:B{$lastRow}")
            ->getNumberFormat()
            ->setFormatCode('#,##0.00');

        $fileName = "Payroll_" . str_replace(' ', '_', $selectedMonth) . ".xlsx";

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    private static function canExportPayroll(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();

        // Check permissions first (cheaper query)
        if (!($user->hasRole('Admin') || $user->can('payroll.approve'))) {
            return false;
        }

        return Filament::getTenant()->payrolls()
            ->where('status', 1)
            ->exists();
    }


    public static function getDownloadAllPdfsForPeriodHeaderAction(): Action
    {
        return Action::make('downloadAllPdfsForPeriod')
            ->label('Download Payslips')
            ->icon('heroicon-m-document-arrow-down')
            ->color('primary')
            ->tooltip('Bulk download payslips')
            ->modalHeading('Download Payslips')
            ->modalSubmitActionLabel('Download')
            ->form(fn() => [
                Select::make('selectedMonth')
                    ->placeholder('Select Month')
                    ->options(self::getAvailableMonths())
                    ->required()
                    ->preload(),
            ])
            ->action(fn(array $data) => self::generatePayslipsZip($data['selectedMonth']))
            ->visible(fn() => self::canDownloadPayroll());
    }

    private static function generatePayslipsZip(string $selectedMonth): StreamedResponse
    {
        $monthStartDate = Carbon::parse("1 {$selectedMonth}");

        $payrolls = Filament::getTenant()->payrolls()
            ->with(['user:id,name,designation,email,payment_method,account_number'])
            ->where('status', 1)
            ->whereDate('date_range_start', $monthStartDate)
            ->orderBy('user_id')
            ->get();

        if ($payrolls->isEmpty()) {
            Notification::make()
                ->title('No Records Found')
                ->body('No payroll records found for the selected month.')
                ->warning()
                ->send();
            throw new \Exception('No payroll data found for the selected month.');
        }

        return self::createZipResponse($payrolls, $selectedMonth);
    }

    private static function createZipResponse(Collection $payrolls, string $selectedMonth): StreamedResponse
    {
        $zipFileName = 'Payslips_' . str_replace(' ', '_', $selectedMonth) . '.zip';

        return response()->streamDownload(function () use ($payrolls) {
            $zip = new ZipArchive();
            $tempZipFile = tempnam(sys_get_temp_dir(), 'payslips_zip_');

            if (!$zip->open($tempZipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
                echo "Failed to create zip file.";
                return;
            }

            foreach ($payrolls as $payroll) {
                $employeeName = $payroll->user
                    ? Str::slug($payroll->user->name)
                    : 'employee_' . $payroll->user_id;
                $periodName = $payroll->date_range_start->format('M_Y');
                $fileName = "Payslip_{$employeeName}_{$periodName}.pdf";

                try {
                    $pdf = Pdf::loadView('pdfs.payroll', compact('payroll'));
                    $pdf->setOptions([
                        'isHtml5ParserEnabled' => true,
                        'isRemoteEnabled' => true,
                    ]);
                    $pdfContent = $pdf->output();
                    $zip->addFromString($fileName, $pdfContent);
                } catch (\Exception $e) {
                    // Add error file to zip for debugging
                    $zip->addFromString("ERROR_{$fileName}.txt", $e->getMessage());
                }
            }

            $zip->close();
            readfile($tempZipFile);
            unlink($tempZipFile);
        }, $zipFileName, [
            'Content-Type' => 'application/zip',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    private static function canDownloadPayroll(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();

        // Check permissions first (cheaper query)
        if (!($user->hasRole('Admin') ||
            $user->can('payroll.manageRecords') ||
            $user->can('payroll.approve'))) {
            return false;
        }

        return Filament::getTenant()->payrolls()
            ->where('status', 1)
            ->exists();
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayrollRecords::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return Auth::check() && (
            Auth::user()->hasRole('Admin') ||
            Auth::user()->can('payroll.viewRecords') ||
            Auth::user()->can('payroll.manageRecords') ||
            Auth::user()->can('payroll.approve')
        );
    }
}
