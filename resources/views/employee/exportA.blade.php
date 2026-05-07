<table>
    <tr>
        <th>
            <div class="heading">
                <h1>SCHEDULE</h1>
                <h2>[See Rule  2(1)]</h2>
            </div>
        </th>
    </tr>
    <tr>
        <th>
            <h1><b>FORM A</b></h1>
            <h1>FORMAT OF EMPLOYEE REGISTER</h1>
            <h2>[Part-A: For All Establishment]</h2>
        </th>
    </tr>
    <tr>
        <th colspan="4">
            <h1><b>Name of Establishment:</b></h1>
            <h6>{{\Auth::user()->name}}</h6>
            @if(!empty($company_address))
              <h3>{{$company_address->value}}</h3>
            @endif
        </th>
        <th colspan="13">
            <h1><b>Name of the Principal Employer:</b></h1>
            <h6>{{\Auth::user()->name}}</h6>
            @if(!empty($company_address))
             <h3>{{$company_address->value}}</h3>
            @endif
        </th>
        <th colspan="4">
            <h1><b>LIN:</b>1234567890</h1>
        </th>
        <th colspan="8">
            <h1><b>Site Locaton:</b></h1><h3>{{\Auth::user()->name}}</h3>
        </th>
    </tr>
    <tr>
        <th colspan="4">
            <h1><b>Name Of the Owner:</b></h1><h3>XYZ</h3>
        </th>
        <th colspan="6">
            <h1><b>Month</b></h1><h3>{{$month}}</h3>
        </th>
    </tr>
</table>
<table>
<thead>
    <tr>
        <th>Sr no.</th>
        <th>Employee Code</th>
        <th>Name</th>
        <th>Surname</th>
        <th>Gender</th>
        <th>Father's Spouse  Name </th>
        <th>Date of Birth </th>
        <th>Nationality</th>
        <th>Education level</th>
        <th>Date of joining </th>
        <th>Designation </th>
        <th>Category Address (MS/S/SS/US)</th>
        <th>Type of Employment</th>
        <th>Mobile</th>
        <th>UAN</th>
        <th>PAN</th>
        <th>ESIC IP</th>
        <th>LWF</th>
        <th>Aadhar</th>
        <th>Bank A/c No.</th>
        <th>Bank</th>
        <th>Branch (ifsc)</th>
        <th>Permanent Address</th>
        <th>Date of Exit</th>
        <th>Reason of Exit</th>
        <th>Mark of identification </th>
        <th>Photo</th>
        <th>Speciment Signature/ Thumb Impresson </th>
        <th>Remarks</th>
    </tr>
</thead>
<tbody>
    @foreach ($data as $key => $emp)
        <tr>
            <td>{{$key+1}}</td>
            <td>#EMPR000{{$emp->employee_id}}</td>
            <td>{{$emp->name}}</td>
            <td>{{$emp->last_name}}</td>
            <td>{{$emp->gender}}</td>
            <td>-</td>
            <td>{{$emp->dob}}</td>
            <td>-</td>
            <td>-</td>
            <td>{{$emp->company_doj}}</td>
            <td>{{!empty($emp->designation) ? $emp->designation->name : '-'}}</td>
            <td>-</td>
            <td>{{!empty($emp->department) ? $emp->department->name : '-'}}</td>
            <td>{{$emp->phone}}</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>{{$emp->account_number}}</td>
            <td>{{$emp->bank_name}}</td>
            <td>{{$emp->bank_identifier_code}}</td>
            <td>{{$emp->address}}</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
        </tr>
    @endforeach
</tbody>
</table>