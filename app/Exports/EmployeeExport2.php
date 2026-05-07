<?php

namespace App\Exports;

use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use App\Models\Wage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmployeeExport2 implements FromView, WithStyles
{
    use Exportable;

    public function view(): View
    {
        $data = Employee::select('employees.employee_id','employees.name','employees.salary','employees.account_number','overtimes.hours as overtime_hours','saturation_deductions.title as deduction_title','saturation_deductions.amount as deduction_amount','allowances.title as allowance_title','allowances.amount as allowance_amount','deduction_options.name as deduction_option')
        ->where('employees.created_by',Auth::user()->creatorId())
        ->leftJoin('overtimes','overtimes.employee_id',  '=', 'employees.id')
        ->leftJoin('saturation_deductions','saturation_deductions.employee_id','=','employees.id')
        ->leftJoin('allowances','allowances.employee_id','=','employees.id')
        ->leftJoin('deduction_options','deduction_options.id','=','saturation_deductions.deduction_option')->get();

        $company_address = DB::table('settings')->where(['name' => 'company_address','created_by' => Auth::user()->creatorId()])->first();

        $wages = Wage::where('created_by', Auth::user()->creatorId())->get();
        $wagesArray = array();
        foreach($wages as $key => $item){
            $wagesArray[$item->wages_type]['minimum'] = $item->minimum;
            $wagesArray[$item->wages_type]['da'] = $item->da;
        }
        if(!array_key_exists('highly_skilled',$wagesArray)){
            $wagesArray['highly_skilled']['minimum'] = 0;
            $wagesArray['highly_skilled']['da'] = 0;
        }
        if(!array_key_exists('skilled',$wagesArray)){
            $wagesArray['skilled']['minimum'] = 0;
            $wagesArray['skilled']['da'] = 0;
        }
        if(!array_key_exists('semi_skilled',$wagesArray)){
            $wagesArray['semi_skilled']['minimum'] = 0;
            $wagesArray['semi_skilled']['da'] = 0;
        }
        if(!array_key_exists('un_skilled',$wagesArray)){
            $wagesArray['un_skilled']['minimum'] = 0;
            $wagesArray['un_skilled']['da'] = 0;
        }
        $keys = array('highly_skilled','skilled','semi_skilled','un_skilled');
        $newArray = array();
        foreach ($keys as $key) {
            $newArray[$key] = $wagesArray[$key];
        }
        $month = date('M-Y');

        return view('employee.exportB', compact('data','company_address','month','newArray'));
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:AC1');
        $sheet->mergeCells('A2:AC2');
        $sheet->mergeCells('A3:AC3');
        $sheet->mergeCells('A4:AC4');

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
        $sheet->getStyle('A3:AC3')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);
        $sheet->getStyle('A4:AC4')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
            ],
        ]);


        $sheet->getRowDimension(1)->setRowHeight(20);
        $sheet->getRowDimension(2)->setRowHeight(20);
        $sheet->getRowDimension(3)->setRowHeight(20);
        $sheet->getRowDimension(4)->setRowHeight(30);
        $sheet->getRowDimension(11)->setRowHeight(60);
        $sheet->getRowDimension(12)->setRowHeight(20);
    }
}
