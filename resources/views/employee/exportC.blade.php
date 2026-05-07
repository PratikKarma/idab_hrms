<table>
    <tr>
        <th>
            <h1><b>FORM C</b></h1>
        </th>
    </tr>
    <tr>
        <th>
            <h1>REGISTER OF LOAN/RECOVERIES</h1>
        </th>
    </tr>
    <tr>
        <th colspan="13">
            <h1><b>Month</b></h1><h3>{{$month}}</h3>
        </th>
    </tr>
    <tr>
        <th colspan="7">
            <h1><b>Name of Establishment:</b></h1>
            <h6>{{\Auth::user()->name}}</h6>
            @if(!empty($company_address))
              <h3>{{$company_address->value}}</h3>
            @endif
        </th>
        <th colspan="6">
            <h1><b>Name of the Principal Employer:</b></h1>
            <h6>{{\Auth::user()->name}}</h6>
            @if(!empty($company_address))
              <h3>{{$company_address->value}}</h3>
            @endif
        </th>
    </tr>
    <tr>
        <th colspan="7">
            <h1><b>Site Locaton:</b></h1><h3>{{\Auth::user()->name}}</h3>
        </th>
        <th colspan="6">
            <h1><b>LIN:</b>1234567890</h1>
        </th>
    </tr>
</table>
<table>
<thead>
    <tr>
        <th>S. No. in Employee Register</th>
        <th>Name</th>
        <th>Recovery Type (Damage/Loss/Fine/Advance/Loans)</th>
        <th>Particulars</th>
        <th>Date of Damage/ loss</th>
        <th>Amount</th>
        <th>Whether show cause issued</th>
        <th>Explaination heard in presence of</th>
        <th>Number of Installment</th>
        <th>First Month/ Year </th>
        <th>Last Month/ Year </th>
        <th>Date of Complete Recovery </th>
        <th>Remarks</th>
    </tr>
</thead>
<tbody>
    @foreach ($data as $key => $loan)
        <tr>
            <td>{{$key+1}}</td>
            <td>{{$loan->name}}</td>
            <td>{{$loan->type}}</td>
            <td></td>
            <td>-</td>
            <td>{{$loan->amount}}</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>{{$loan->start_date}}</td>
            <td>{{$loan->end_date}}</td>
            <td>{{$loan->end_date}}</td>
            <td></td>
        </tr>
    @endforeach
</tbody>
</table>