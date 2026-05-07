<table>
        <tr>
            <th>
                <div class="heading">
                    <h1>{{\Auth::user()->name}}</h1>
                    @if(!empty($company_address))
                    <p>{{$company_address->value}}</p>
                  @endif
                </div>
            </th>
        </tr>
        <tr>
            <th>
                <h1><b>FORM NO. 15</b></h1><h6>(Prescribed under rule 88)</h6><h3><b>Register of adult workers</b></h3>
            </th>
        </tr>
</table>
<table>
    <thead>
        <tr>
            <th rowspan="2">Sr no.</th>
            <th rowspan="2">Employee Name</th>
            <th rowspan="2">Date of Birth</th>
            <th rowspan="2">Gender</th>
            <th rowspan="2">Residental Address</th>
            <th rowspan="2">Father/Husband Name</th>
            <th rowspan="2">Date of Appointment</th>
            <th colspan="2">Group to which Worker belongs</th>
            <th rowspan="2">Number of relay if working in Shifts</th>
            <th colspan="2">Adolescent if certified as adults</th>
            <th rowspan="2">Remark</th>
        </tr>
        <tr>
            <th>Alphabet Assigned</th>
            <th>Nature of Work</th>
            <th>Number and Date of Certificate of Fitness</th>
            <th>Number under section 68</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $key => $emp)
            <tr>
                <td rowspan="2">{{$key+1}}</td>
                <td rowspan="2">{{$emp->name}}</td>
                <td rowspan="2">{{$emp->dob}}</td>
                <td rowspan="2">{{$emp->gender}}</td>
                <td rowspan="2">{{$emp->address}}</td>
                <td rowspan="2">-</td>
                <td rowspan="2">-</td>
                <td colspan="2"></td>
                <td rowspan="2">-</td>
                <td colspan="2"></td>
                <td rowspan="2">-</td>
            </tr>
            <tr>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
            </tr>
        @endforeach
    </tbody>
</table>