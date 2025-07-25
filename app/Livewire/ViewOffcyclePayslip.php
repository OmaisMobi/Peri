<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\OffCyclePayroll;

class ViewPayslip extends Component
{
    public OffCyclePayroll $payroll;

    public function mount(OffCyclePayroll $payroll)
    {
        $this->payroll = $payroll;
    }

    public function render()
    {
        return view('livewire.view-offcycle-payslip');
    }
}
