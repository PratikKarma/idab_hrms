<?php

namespace App\Exports;

use App\Models\Loan;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmployeeExport3 implements FromView, WithStyles
{
    use Exportable;

    public function view(): View
    {
        $data = Loan::leftJoin('employees', 'employees.id', '=', 'loans.employee_id')->where('loans.created_by', \Auth::user()->id)->get();

        $company_address = DB::table('settings')->where(['name' => 'company_address', 'created_by' => \Auth::user()->creatorId()])->first();

        $month = date('M-Y');

        return view('employee.exportC', compact('data', 'company_address', 'month'));
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:M1');
        $sheet->mergeCells('A2:M2');
        $sheet->mergeCells('A3:M3');
        $sheet->mergeCells('A4:D4');
        $sheet->mergeCells('H4:M4');
        $sheet->mergeCells('A5:D5');
        $sheet->mergeCells('H5:M5');
        $sheet->mergeCells('A15:M15');
        $sheet->mergeCells('A16:M16');
        $sheet->mergeCells('A17:M17');
        // Apply styles to cells or columns
        $sheet->getStyle('A1:M1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        $sheet->getStyle('A2:M2')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);
        $sheet->getStyle('A3:M3')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
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
        $sheet->getStyle('H4:M4')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
        ]);
        $sheet->getStyle('A5:D5')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
        ]);
        $sheet->getStyle('H5:M5')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            ],
        ]);
        $sheet->getStyle('A15:M15')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);
        $sheet->getStyle('A16:M16')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);
        $sheet->getStyle('A17:M17')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(20);
        $sheet->getRowDimension(2)->setRowHeight(20);
        $sheet->getRowDimension(3)->setRowHeight(50);
        $sheet->getRowDimension(4)->setRowHeight(50);
        $sheet->getRowDimension(5)->setRowHeight(50);
        $sheet->getRowDimension(15)->setRowHeight(20);
        $sheet->getRowDimension(16)->setRowHeight(20);
        $sheet->getRowDimension(17)->setRowHeight(20);
    }
}
