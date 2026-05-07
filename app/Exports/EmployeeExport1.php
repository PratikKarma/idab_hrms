<?php

namespace App\Exports;

use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmployeeExport1 implements FromView, WithStyles
{
    use Exportable;

    public function view(): View
    {
        $data = Employee::where('created_by', Auth::user()->creatorId())->get();
        $company_address = DB::table('settings')->where(['name' => 'company_address', 'created_by' => Auth::user()->creatorId()])->first();

        foreach ($data as $k => $employees) {
            unset($employees->id, $employees->user_id, $employees->subdepartment_id, $employees->shift_id, $employees->shift_date, $employees->documents, $employees->tax_payer_id, $employees->pf_id, $employees->is_active, $employees->created_at, $employees->updated_at);

            $data[$k]["branch_id"] = !empty($employees->branch) ? $employees->branch->name : '-';
            $data[$k]["department_id"] = !empty($employees->department) ? $employees->department->name : '-';
            $data[$k]["designation_id"] = !empty($employees->designation) ? $employees->designation->name : '-';
            $data[$k]["salary_type"] = !empty($employees->salary_type) ? $employees->salaryType->name : '-';
            $data[$k]["salary"] = Employee::employee_salary($employees->salary);
            $data[$k]["created_by"] = Employee::login_user($employees->created_by);

        }

        $month = date('M-Y');
        return view('employee.exportA', compact('data', 'company_address', 'month'));
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:AC1');
        $sheet->mergeCells('A2:AC2');
        $sheet->mergeCells('A3:D3');
        $sheet->mergeCells('E3:Q3');
        $sheet->mergeCells('R3:U3');
        $sheet->mergeCells('V3:AC3');
        $sheet->mergeCells('A4:D4');
        $sheet->mergeCells('E4:J4');
        // Apply styles to cells or columns
        $sheet->getStyle('A1:AC1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        $sheet->getStyle('A2:AC2')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);
        $sheet->getStyle('A3:D3')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
        ]);
        $sheet->getStyle('E3:Q3')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);
        $sheet->getStyle('R3:U3')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);
        $sheet->getStyle('V3:AC3')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);
        $sheet->getStyle('A4:D4')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
        ]);
        $sheet->getStyle('E4:J4')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getRowDimension(2)->setRowHeight(60);
        $sheet->getRowDimension(3)->setRowHeight(50);
        $sheet->getRowDimension(4)->setRowHeight(60);
    }
}
