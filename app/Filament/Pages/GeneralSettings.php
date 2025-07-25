<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Pages\Page;
use Filament\Forms\Components\{
    TextInput,
    ColorPicker,
    Toggle,
    Textarea,
    Section,
    Select,
    FileUpload
};
use Filament\Notifications\Notification;
use Filament\Forms\Form;

class GeneralSettings extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'General';
    protected static string $view = 'filament.pages.general-settings';
    protected static ?int $navigationSort = 4;

    public $settings = [];
    public $main_logo = '';
    public $dark_logo = '';
    public $favicon = '';
    public $data = [];

    public function mount(): void
    {
        $this->settings = Setting::getByType('general');
        $this->loadFormData();
    }

    protected function loadFormData(): void
    {
        $this->form->fill([
            'settings' => $this->settings,
            'main_logo' => $this->settings['main_logo'] ?? '',
            'dark_logo' => $this->settings['dark_logo'] ?? '',
            'favicon' => $this->settings['favicon'] ?? '',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('General Settings')->schema([
                    TextInput::make('settings.company_name')
                        ->label('Title')
                        ->required(),

                    TextInput::make('settings.currency_code')
                        ->label('Currency Code')
                        ->required(),

                    TextInput::make('settings.currency_symbol')
                        ->label('Currency Symbol')
                        ->required(),

                    Textarea::make('settings.google_analytics')
                        ->label('Google Analytics Code')
                        ->rows(1),

                    Select::make('settings.timezone')
                        ->label('Timezone')
                        ->options($this->getTimezones())
                        ->default('UTC')
                        ->searchable()
                        ->required(),

                    Select::make('settings.date_format')
                        ->label('Date Format')
                        ->options($this->getDateFormats())
                        ->required(),

                    Select::make('settings.time_format')
                        ->label('Time Format')
                        ->options($this->getTimeFormats())
                        ->required(),

                    Textarea::make('settings.footer_text')
                        ->label('Footer Text')
                        ->rows(1)
                        ->helperText('Text after: © 20XX '),

                    ColorPicker::make('settings.primary_color')
                        ->label('Primary Color')
                        ->default('#193a66'),

                    ColorPicker::make('settings.secondary_color')
                        ->label('Secondary Color')
                        ->default('#f8c102'),

                    FileUpload::make('main_logo')
                        ->label('Main Logo')
                        ->directory('uploads/logos')
                        ->image()
                        ->maxFiles(1)
                        ->preserveFilenames(),

                    FileUpload::make('dark_logo')
                        ->label('Dark Mode Logo')
                        ->directory('uploads/logos')
                        ->image()
                        ->maxFiles(1)
                        ->preserveFilenames(),

                    FileUpload::make('favicon')
                        ->label('Favicon')
                        ->directory('uploads/logos')
                        ->image()
                        ->maxFiles(1)
                        ->preserveFilenames()
                        ->columnSpan(2),

                    \Filament\Forms\Components\Grid::make(2)
                        ->schema([
                            Toggle::make('settings.email_activation')
                                ->label('Email Activation')
                                ->default(false),
                            Toggle::make('settings.turn_off_new_user_registration')
                                ->label('Disable New User Registration')
                                ->default(false),
                        ])
                        ->columnSpan(2),
                ])->columns(2),
            ])
            ->statePath('data');
    }

    protected function getTimezones(): array
    {
        return array_combine(
            \DateTimeZone::listIdentifiers(),
            \DateTimeZone::listIdentifiers()
        );
    }

    protected function getDateFormats(): array
    {
        return [
            'Y-m-d'  => now()->format('Y-m-d')  . ' (Y-m-d)',
            'd-m-Y'  => now()->format('d-m-Y')  . ' (d-m-Y)',
            'm/d/Y'  => now()->format('m/d/Y')  . ' (m/d/Y)',
            'd M Y'  => now()->format('d M Y')  . ' (d M Y)',
            'M d, Y' => now()->format('M d, Y') . ' (M d, Y)',
        ];
    }

    protected function getTimeFormats(): array
    {
        return [
            'H:i'   => '24-hour (e.g., ' . now()->format('H:i') . ')',
            'h:i A' => '12-hour (e.g., ' . now()->format('h:i A') . ')',
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $nestedSettings = $data['settings'] ?? [];
        $this->settings = array_merge($this->settings, $nestedSettings);

        // Always update these fields, even if the value is null (i.e., image deleted)
        $this->settings['main_logo'] = $data['main_logo'] ?? null;
        $this->settings['dark_logo'] = $data['dark_logo'] ?? null;
        $this->settings['favicon'] = $data['favicon'] ?? null;

        Setting::setByType('general', $this->settings);

        // Refresh the form data after saving
        $this->settings = Setting::getByType('general');
        $this->loadFormData();

        Notification::make()
            ->title('Settings updated successfully!')
            ->success()
            ->send();
    }
}
