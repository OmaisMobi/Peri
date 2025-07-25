<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Payroll;

class ViewPayslip extends Component
{
    public Payroll $payroll;

    public function mount(Payroll $payroll)
    {
        $this->payroll = $payroll;
    }

    public function render()
    {
        return view('livewire.view-payslip');
    }
}
