<?php

namespace App\Http\Controllers;

use App\Models\OffCyclePayroll;
use App\Models\Payroll;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class PayrollDownloadController extends Controller
{
    public function download(Payroll $payroll)
    {
        $pdf = Pdf::loadView('pdfs.payroll', compact('payroll'));
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);
        return $pdf->download(
            Str::slug($payroll->user->name) . ' ' .
                $payroll->date_range_start->format('M Y') .
                '.pdf'
        );
    }
}
