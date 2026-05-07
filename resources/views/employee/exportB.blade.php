<table>
    <tr>
        <th>
            <h1><b>FORM B</b></h1>
        </th>
    </tr>
    <tr>
        <th>
            <h1>FORMAT OF WAGE REGISTER</h1>
        </th>
    </tr>
    <tr>
        <th>Rate of Minimum Wages and since the Date: 01/04/2023</th>
    </tr>
    <tr>
        <th>
            <h1><b>Month</b></h1><h3>{{$month}}</h3>
        </th>
    </tr>
</table>


<table>
    <thead>
        <tr>
            <th></th>
            <th>Highly Skilled</th> 
            <th>Skilled</th>
            <th>Semi Skilled</th>
            <th>Un-Skilled</th>
         </tr> 
     </thead>
     <tbody>
         <tr>
            <td>Minimum Basic</td>
            @foreach($newArray as $key => $value)
                @if ($key == 'highly_skilled')
                 <td>{{(isset($value['minimum'])) ? $value['minimum'] : '-'}}</td>    
                @endif
                @if ($key == 'skilled')
                 <td>{{(isset($value['minimum'])) ? $value['minimum'] : '-'}}</td>
                @endif
                @if ($key == 'semi_skilled')
                 <td>{{(isset($value['minimum'])) ? $value['minimum'] : '-'}}</td>
                @endif
                @if ($key == 'un_skilled')
                 <td>{{(isset($value['minimum'])) ? $value['minimum'] : '-'}}</td>
                @endif
            @endforeach
         </tr>
         <tr>
            <td>DA</td>
            @foreach($newArray as $key => $value)
                @if ($key == 'highly_skilled')
                 <td>{{(isset($value['da'])) ? $value['da'] : '-'}}</td>    
                @endif
                @if ($key == 'skilled')
                 <td>{{(isset($value['da'])) ? $value['da'] : '-'}}</td>
                @endif
                @if ($key == 'semi_skilled')
                 <td>{{(isset($value['da'])) ? $value['da'] : '-'}}</td>
                @endif
                @if ($key == 'un_skilled')
                 <td>{{(isset($value['da'])) ? $value['da'] : '-'}}</td>
                @endif
            @endforeach
         </tr>
         <tr>
            <td>Total</td>
            @foreach($newArray as $key => $value)
                @if ($key == 'highly_skilled')
                 <td>{{$value['minimum'] + $value['da']}}</td>    
                @endif
                @if ($key == 'skilled')
                 <td>{{$value['minimum'] + $value['da']}}</td>
                @endif
                @if ($key == 'semi_skilled')
                 <td>{{$value['minimum'] + $value['da']}}</td>
                @endif
                @if ($key == 'un_skilled')
                 <td>{{$value['minimum'] + $value['da']}}</td>
                @endif
            @endforeach
         </tr>
     </tbody>
</table>
  
<table>
    <tr>
        <th colspan="8">
            <h1><b>Name of Establishment:</b></h1>
            <h6>{{\Auth::user()->name}}</h6>
            @if(!empty($company_address))
              <h3>{{$company_address->value}}</h3>
            @endif
        </th>
        <th colspan="9">
            <h1><b>Name of the Principal Employer:</b></h1>
            <h6>{{\Auth::user()->name}}</h6>
            @if(!empty($company_address))
              <h3>{{$company_address->value}}</h3>
            @endif
        </th>
        <th colspan="14">
            <h1><b>Site Locaton:</b></h1><h3>{{\Auth::user()->name}}</h3>
        </th>
    </tr>
    <tr>
        <th colspan="8">For the Period From : 01/05/2023 to 30/05/2023 (Monthly)</th>
        <th colspan="23">
            <h1><b>LIN:</b>1234567890</h1>
        </th>
    </tr>
</table>


<table>
    <thead>
        <tr>
            <th>sl. no.</th>
            <th>S. No. in Employee Register </th>
            <th>Name</th>
            <th>Rate of  Wages</th>
            <th>No of Days Worked</th>
            <th>Overtime hours worked</th>
            <th>Basic</th>
            <th>Special Basic</th>
            <th>DA</th>
            <th>Payments Overtime</th>
            <th>HRA</th>
            <th>Food Allowance</th>
            <th>Conveyance Allowance</th>
            <th>Work All.</th>
            <th>Bonus</th>
            <th>Leave with Wage </th>
            <th>Total Earning </th>
            <th>PF</th>
            <th>ESIC</th>
            <th>Society</th>
            <th>P.T</th>
            <th>Insurance</th>
            <th>Others</th>
            <th>Recoveries (Advance)</th>
            <th>Total Deduction</th>
            <th>Net Payments (12_20) </th>
            <th>Employer Share PF welfare Fund</th>
            <th>Reciept by Employee/Bank Transaction on Id</th>
            <th>Reciept by Employee/Bank Transaction on Id</th>
            <th>Date of Payment</th>
            <th>Remarks</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $key => $emp)
            <tr>
                <td>{{$key+1}}</td>
                <td>00{{$emp->employee_id}}</td>
                <td>{{$emp->name}}</td>
                <td></td>
                <td></td>
                <td>{{$emp->overtime_hours}}</td>
                <td>{{$emp->salary}}</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                @if($emp->allowance_title == 'Food')
                  <td>{{$emp->allowance_amount}}</td>
                @else
                   <td>-</td>
                @endif

                @if($emp->allowance_title == 'conveyance')
                   <td>{{$emp->allowance_amount}}</td>
                @else
                  <td>-</td>
                @endif
                <td></td>
                <td></td>
                <td></td>
                <td>{{$emp->overtime_hours + $emp->salary + $emp->allowance_amount}}</td>

                @if($emp->deduction_option == 'PF')
                  <td>{{$emp->deduction_amount}}</td>
                @else
                  <td>-</td>
                @endif

                <td>-<td>

                <td></td>
                @if($emp->deduction_option == 'PT')
                  <td>{{$emp->deduction_amount}}</td>
                @else
                  <td>-</td>
                @endif
                <td></td>
                <td></td>
                {{-- <td></td> --}}
                <td>{{$emp->deduction_amount + $emp->pt}}</td>
                <td></td>
                <td>{{$emp->account_number}}</td>
                <td></td>
                <td></td>
            </tr>
        @endforeach
    </tbody>
</table>