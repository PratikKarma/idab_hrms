<style>
    .addBtn {
        float: right;
    }
</style>
@extends('layouts.admin')

@php
    $company_settings = \App\Models\Utility::settings();
@endphp
@section('page-title')
{{ __('Edit Employee') }}
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
<li class="breadcrumb-item"><a href="{{ url('employee') }}">{{ __('Employee') }}</a></li>
<li class="breadcrumb-item">{{ __('Edit Employee') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="">
        <div class="">

            {{ Form::model($employee, ['route' => ['employee.update', $employee->id], 'method' => 'PUT', 'enctype' => 'multipart/form-data', 'class' => 'employee-edit-form']) }}
            <div class="row">
                <div class="col-md-6 ">
                    <div class="card em-card">
                        <div class="card-header">
                            <h5>{{ __('Personal Detail') }}</h5>
                        </div>
                        <div class="card-body">

                            <div class="row">
                                <div class="form-group col-md-6">
                                    {!! Form::label('name', __('First Name'), ['class' => 'form-label']) !!}<span class="text-danger pl-1">*</span>
                                    {!! Form::text('name', null, ['class' => 'form-control', 'required' => 'required']) !!}
                                </div>
                                <div class="form-group col-md-6">
                                    {!! Form::label('last_name', __('Last Name'), ['class' => 'form-label']) !!}<span class="text-danger pl-1">*</span>
                                    {!! Form::text('last_name', null, ['class' => 'form-control', 'required' => 'required']) !!}
                                </div>
                                <x-mobile divClass="col-md-6" name="phone" label="{{ __('Phone') }}"
                                    placeholder="{{ __('Enter employee phone') }}" id="phone" required="true">
                                </x-mobile>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::date('dob',
                                        !empty($employee->dob)
                                        ? \Carbon\Carbon::parse($employee->dob)->format($company_settings['site_date_format'])
                                        : null,
                                        [
                                        'class' => 'form-control w-100',
                                        'required',
                                        'autocomplete' => 'off',
                                        'placeholder' => 'Select Date of Birth'
                                        
                                        ])
                                        !!}
                                    </div>
                                </div>
                                <div class="col-md-6 ">
                                    <div class="form-group ">
                                        {!! Form::label('gender', __('Gender'), ['class' => 'form-label']) !!}<span class="text-danger pl-1">*</span>
                                        <div class="d-flex radio-check">
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" id="g_male" value="Male" name="gender"
                                                    class="form-check-input" required
                                                    {{ $employee->gender == 'Male' ? 'checked' : '' }}>
                                                <label class="form-check-label"
                                                    for="g_male">{{ __('Male') }}</label>
                                            </div>
                                            <div class="custom-control custom-radio ms-1 custom-control-inline">
                                                <input type="radio" id="g_female" value="Female" name="gender"
                                                    class="form-check-input"
                                                    {{ $employee->gender == 'Female' ? 'checked' : '' }}>
                                                <label class="form-check-label"
                                                    for="g_female">{{ __('Female') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    {!! Form::label('email', __('Email'), ['class' => 'form-label']) !!}<span class="text-danger pl-1">*</span>
                                    {!! Form::email('email', old('email'), [
                                    'class' => 'form-control',
                                    'required' => 'required',
                                    'placeholder' => 'Enter employee email',
                                    ]) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('address', __('Address'), ['class' => 'form-label']) !!}<span class="text-danger pl-1">*</span>
                                {!! Form::textarea('address', null, ['class' => 'form-control', 'required' => 'required', 'rows' => 2]) !!}
                            </div>
                            @if (\Auth::user()->type == 'employee')
                            {!! Form::submit('Update', ['class' => 'btn-create btn-xs badge-blue radius-10px float-right']) !!}
                            @endif
                        </div>
                    </div>
                </div>
                @if (\Auth::user()->type != 'employee')
                <div class="col-md-6 ">
                    <div class="card em-card">
                        <div class="card-header">
                            <h5>{{ __('Company Detail') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @csrf
                                <div class="form-group ">
                                    {!! Form::label('employee_id', __('Employee ID'), ['class' => 'form-label']) !!}
                                    {!! Form::text('employee_id', $employeesId, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('branch_id', __('Select Branch'), ['class' => 'form-label']) }}<span
                                        class="text-danger pl-1">*</span>
                                    <a href="javascript:void(0)" data-title="{{ __('Create New Branch') }}"
                                        onclick="modalShow([{'name' : ''}], 'create-branch', 'Create Branch','branch')"
                                        data-bs-toggle="tooltip" title="{{ __('Create New Branch') }}"
                                        class="btn btn-sm btn-primary addBtn"
                                        data-bs-original-title="{{ __('Create') }}">
                                        <i class="ti ti-plus"></i>
                                    </a>
                                    {{ Form::select('branch_id', $branches, null, ['class' => 'form-control branch_id', 'required' => 'required', 'placeholder' => 'Select Branch']) }}
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('department_id', __('Select Department'), ['class' => 'form-label']) }}<span
                                        class="text-danger pl-1">*</span>
                                    <a href="javascript:void(0)" data-title="{{ __('Create New Department') }}"
                                        onclick="modalShow([{'branch': '{{ $branches }}'}, {'name' : ''}, {'slug' : ''}], 'create-department','Create Department','department')"
                                        data-bs-toggle="tooltip" title="{{ __('Create New Department') }}"
                                        class="btn btn-sm btn-primary addBtn "
                                        data-bs-original-title="{{ __('Create') }}">
                                        <i class="ti ti-plus"></i>
                                    </a>
                                    {{ Form::select('department_id', $departments, null, ['class' => 'form-control department_id', 'id' => 'department_id', 'required' => 'required']) }}
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('subdepartment_id', __('Select Sub Department'), ['class' => 'form-label']) }}<span
                                        class="text-danger pl-1">*</span>
                                    <a href="javascript:void(0)" data-title="{{ __('Create New Sub Department') }}"
                                        onclick="modalShow([{'department': '{{ $departments }}'}, {'name' : ''}], 'create-subDepartment','Create Sub Department','subdepartment')"
                                        data-bs-toggle="tooltip" title="{{ __('Create New Sub Department') }}"
                                        class="btn btn-sm btn-primary addBtn "
                                        data-bs-original-title="{{ __('Create') }}">
                                        <i class="ti ti-plus"></i>
                                    </a>
                                    {{ Form::select('subdepartment_id', $subdepartments, $employee->subdepartment, ['class' => 'form-control subdepartment_id', 'id' => 'subdepartment_id', 'required' => 'required']) }}
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('designation_id', __('Select Designation'), ['class' => 'form-label']) }}
                                    <a href="javascript:void(0)" data-title="{{ __('Create New Designation') }}"
                                        onclick="modalShow([{'department': '{{ $departments }}'}, {'name' : ''}], 'create-designation','Create Designation','designation')"
                                        data-bs-toggle="tooltip" title="{{ __('Create New Designation') }}"
                                        class="btn btn-sm btn-primary addBtn"
                                        data-bs-original-title="{{ __('Create') }}">
                                        <i class="ti ti-plus"></i>
                                    </a>


                                    <div class="form-icon-user">
                                        <div class="designation_div">
                                            <select class="form-control designation_id" name="designation_id"
                                                id="choices-multiple" placeholder="Select Designation">
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    {{ Form::label('shift_id', __('Select Shift'), ['class' => 'form-label']) }}<span
                                        class="text-danger pl-1">*</span>
                                    <a href="javascript:void(0)" data-title="{{ __('Create New  Shift') }}"
                                        onclick="modalShow([{'name' : ''}], 'create-shift', 'Create Shift','shift')"
                                        data-bs-toggle="tooltip" title="{{ __('Create New  Shift') }}"
                                        class="btn btn-sm btn-primary addBtn"
                                        data-bs-original-title="{{ __('Create') }}">
                                        <i class="ti ti-plus"></i>
                                    </a>

                                    <div class="form-icon-user">
                                        <div class="shift_div">
                                            {{ Form::select('shift_id', $shift, $employee->shift_id, ['class' => 'form-control shift_id', 'required' => 'required', 'placeholder' => 'Select Shift']) }}
                                        </div>
                                    </div>

                                </div>

                                <div class="form-group col-md-6">
                                    {{ Form::label('company_shift_time', __('Select Employee Zone Time'), ['class' => 'form-label']) }}
                                    <span class="text-danger pl-1">*</span>

                                    <div class="form-icon-user">
                                        @php
                                        $shiftOptions = [];
                                        for ($i = 0; $i <= 3; $i++) {
                                            $startKey=$i===0 ? 'company_start_time' : 'company_start_time' .$i;
                                            $endKey=$i===0 ? 'company_end_time' : 'company_end_time' .$i;

                                            if (!empty($company_settings[$startKey]) && !empty($company_settings[$endKey])) {
                                            $label=$company_settings[$startKey].' - '.$company_settings[$endKey];
                                                            $shiftOptions[$label] = $label;
                                                        }
                                                    }

                                                    $selectedShiftTime = null;
                                                    if (!empty($employee->company_start_time) && !empty($employee->company_end_time)) {
                                                        $selectedShiftTime = $employee->company_start_time.' - '.$employee->company_end_time;
                                                    }
                                                @endphp

                                                @if($shiftOptions)
                                                    {{ Form::select('company_shift_time', $shiftOptions, $selectedShiftTime, [
                                                        'class' => 'form-control',
                                                        'id' => 'company_shift_time',
                                                        'placeholder' => __('Select Zone Time')
                                                    ]) }}
                                                @else
                                                    <div class="alert alert-info mt-2">
                                                        {{ __('Please set company start and end times in Company Settings first.') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                         <div class="form-group col-md-12">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        {!! Form::label('company_doj', __('Company Date Of Joining'), ['class'=> 'form-label']) !!}<span class="text-danger pl-1">*</span>
                                                        {{ Form::date('company_doj', null, [
                                                            'class' => 'form-control w-100',
                                                            'required',
                                                            'autocomplete' => 'off',
                                                            'placeholder' => 'Select company date of joining',
                                                        ]) }}
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        {{ Form::label('lunch_hours', __('Hours'), ['class' => 'form-label']) }}
                                                        {{ Form::number('lunch_hours', 0, ['class' => 'form-control', 'min' => 0]) }}
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        {{ Form::label('lunch_minutes', 'Minutes') }}
                                                        {{ Form::number('lunch_minutes', 0, ['class' => 'form-control', 'min' => 0, 'max' => 59]) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="col-md-6 ">
                        <div class="employee-detail-wrap ">
                            <div class="card em-card">
                                <div class="card-header">
                                    <h5>{{ __('Company Detail') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info">
                                                <strong>{{ __('Branch') }}</strong>
                                                <span>{{ !empty($employee->branch) ? $employee->branch->name : '' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info font-style">
                                                <strong>{{ __('Department') }}</strong>
                                                <span>{{ !empty($employee->department) ? $employee->department->name : '' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info font-style">
                                                <strong>{{ __('Designation') }}</strong>
                                                <span>{{ !empty($employee->designation) ? $employee->designation->name : '' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info font-style">
                                                <strong>{{ __('shift') }}</strong>
                                                <span>{{ !empty($employee->shift) ? $employee->shift->name : '' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info">
                                                <strong>{{ __('Date Of Joining') }}</strong>
                                                <span>{{ \Auth::user()->dateFormat($employee->company_doj) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                @if (\Auth::user()->type != 'employee')
                <div class="row">
                    <div class="col-md-6 ">
                        <div class="card em-card">
                            <div class="card-header">
                                <h5>{{ __('Document') }}</h5>
                            </div>
                            <div class="card-body">
                                @php
                                $employeedoc = $employee
                                ->documents()
                                ->pluck('document_value', __('document_id'));
                                @endphp

                                @foreach ($documents as $key => $document)
                                <div class="row">
                                    <div class="form-group col-12 d-flex">
                                        <div class="float-left col-4">
                                            <label for="document" class=" form-label">{{ $document->name }}
                                                @if ($document->is_required == 1)
                                                <span class="text-danger">*</span>
                                                @endif
                                            </label>
                                        </div>
                                        <div class="float-right col-8">
                                            <input type="hidden" name="emp_doc_id[{{ $document->id }}]"
                                                id="" value="{{ $document->id }}">

                                            @php
                                            $employeedoc = !empty($employee->documents)
                                            ? $employee
                                            ->documents()
                                            ->pluck('document_value', __('document_id'))
                                            : [];
                                            @endphp
                                            <div class="choose-files ">
                                                <label for="document[{{ $document->id }}]">
                                                    <div class=" bg-primary document "> <i
                                                            class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                    </div>
                                                    <input type="file"
                                                        class="form-control file  d-none @error('document') is-invalid @enderror"
                                                        @if ($document->is_required == 1) @endif
                                                    name="document[{{ $document->id }}]"
                                                    id="document[{{ $document->id }}]"
                                                    data-filename="{{ $document->id . '_filename' }}"
                                                    onchange="document.getElementById('{{ 'blah' . $key }}').src = window.URL.createObjectURL(this.files[0])">
                                                </label>
                                                @php
                                                $logo = \App\Models\Utility::get_file('uploads/document/');

                                                @endphp
                                                {{-- <a href="javascript:void(0)"><p class="{{ $document->id . '_filename' }} "></p></a> --}}
                                                <img id="{{ 'blah' . $key }}"
                                                    src="{{ isset($employeedoc[$document->id]) && !empty($employeedoc[$document->id]) ? $logo . '/' . $employeedoc[$document->id] : '' }}"
                                                    width="50%" />

                                            </div>

                                            @if (!empty($employeedoc[$document->id]))
                                            <span class="text-xs-1"><a
                                                    href="{{ !empty($employeedoc[$document->id]) ? asset(Storage::url('uploads/document')) . '/' . $employeedoc[$document->id] : '' }}"
                                                    target="_blank"></a>
                                            </span>
                                            @endif
                                        </div>

                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card em-card">
                            <div class="card-header">
                                <h5>{{ __('Bank Account Detail') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        {!! Form::label('account_holder_name', __('Account Holder Name'), ['class' => 'form-label']) !!}
                                        {!! Form::text('account_holder_name', null, ['class' => 'form-control']) !!}

                                    </div>
                                    <div class="form-group col-md-6">
                                        {!! Form::label('account_number', __('Account Number'), ['class' => 'form-label']) !!}
                                        {!! Form::number('account_number', null, ['class' => 'form-control']) !!}

                                    </div>
                                    <div class="form-group col-md-6">
                                        {!! Form::label('bank_name', __('Bank Name'), ['class' => 'form-label']) !!}
                                        {!! Form::text('bank_name', null, ['class' => 'form-control']) !!}

                                    </div>
                                    <div class="form-group col-md-6">
                                        {!! Form::label('bank_identifier_code', __('Bank Identifier Code'), ['class' => 'form-label']) !!}
                                        {!! Form::text('bank_identifier_code', null, ['class' => 'form-control']) !!}
                                    </div>
                                    <div class="form-group col-md-6">
                                        {!! Form::label('branch_location', __('Branch Location'), ['class' => 'form-label']) !!}
                                        {!! Form::text('branch_location', null, ['class' => 'form-control']) !!}
                                    </div>
                                    <div class="form-group col-md-6">
                                        {!! Form::label('tax_payer_id', __('Tax Payer Id'), ['class' => 'form-label']) !!}
                                        {!! Form::text('tax_payer_id', null, ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <div class="row">
                    <div class="col-md-6 ">
                        <div class="employee-detail-wrap">
                            <div class="card em-card">
                                <div class="card-header">
                                    <h5>{{ __('Document Detail') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @php
                                        $employeedoc = $employee
                                        ->documents()
                                        ->pluck('document_value', __('document_id'));
                                        $logo = \App\Models\Utility::get_file('uploads/document/');

                                        @endphp
                                        @foreach ($documents as $key => $document)
                                        <div class="col-md-12">
                                            <div class="info">

                                                <strong>{{ $document->name }}</strong>
                                                <span><a href="{{ !empty($employeedoc[$document->id]) ? $logo . '/' . $employeedoc[$document->id] : '' }}"
                                                        target="_blank">{{ !empty($employeedoc[$document->id]) ? $employeedoc[$document->id] : '' }}</a></span>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 ">
                        <div class="employee-detail-wrap">
                            <div class="card em-card">
                                <div class="card-header">
                                    <h5>{{ __('Bank Account Detail') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info">
                                                <strong>{{ __('Account Holder Name') }}</strong>
                                                <span>{{ $employee->account_holder_name }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info font-style">
                                                <strong>{{ __('Account Number') }}</strong>
                                                <span>{{ $employee->account_number }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info font-style">
                                                <strong>{{ __('Bank Name') }}</strong>
                                                <span>{{ $employee->bank_name }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info">
                                                <strong>{{ __('Bank Identifier Code') }}</strong>
                                                <span>{{ $employee->bank_identifier_code }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info">
                                                <strong>{{ __('Branch Location') }}</strong>
                                                <span>{{ $employee->branch_location }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info">
                                                <strong>{{ __('Tax Payer Id') }}</strong>
                                                <span>{{ $employee->tax_payer_id }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info">
                                                <strong>{{ __('PF/UAN Number') }}</strong>
                                                <span>{{ $employee->pf_id }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info">
                                                <strong>{{ __('ESIC') }}</strong>
                                                <span>{{ $employee->esic_id }}</span>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if (\Auth::user()->type != 'employee')
                <div class="float-end">
                    <button type="submit" class="btn  btn-primary">{{ 'Update' }}</button>
                </div>
                @endif
                <div class="col-12">
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
    @endsection

    <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel"></h5>
                    {{-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button> --}}
                </div>
                <form id="myForm">
                    <div class="modal-body" id="newModalBody">

                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Close</button>
                    <button type="button" class="btn btn-primary" onclick="submitForm()">Submit</button>
                </div>
            </div>
        </div>
    </div>
    @push('script-page')
    <script type="text/javascript">
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                const modal = document.querySelector('.modal.show');

                if (modal) {
                    submitForm();
                } else {
                    const mainForm = document.querySelector('.employee-edit-form');
                    if (mainForm) {
                        mainForm.querySelector('button[type="submit"]')?.click();
                    }
                }
            }
        });

        function getDesignation(did) {

            $.ajax({
                url: '{{ route('employee.json') }}',
                type: 'POST',
                data: {
                    "department_id": did,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    $('.designation_id').empty();
                    var emp_selct = ` <select class="form-control  designation_id select2" name="designation_id" id="choices-multiple"
                                            placeholder="Select Designation" >
                                            </select>`;
                    $('.designation_div').html(emp_selct);
                    $('.designation_id').append('<option value="">Select any Designation</option>');
                    $.each(data, function(key, value) {
                        var select = '';
                        if (key == '{{ $employee->designation_id }}') {
                            select = 'selected';
                        }

                        $('.designation_id').append('<option value="' + key + '"  ' + select + '>' +
                            value + '</option>');
                    });
                    new Choices('#choices-multiple', {
                        removeItemButton: true,
                    });
                }
            });
        }

        $(document).ready(function() {
            var d_id = $('#department_id').val();
            var designation_id = '{{ $employee->designation_id }}';

            console.log(d_id);
            getDesignation(d_id);

            var s_id = $('#shift_id').val();
            var shift_id = '{{ $employee->shift_id }}';
        });

        $(document).on('change', 'select[name=department_id]', function() {
            var department_id = $(this).val();
            getDesignation(department_id);
        });
        $(document).on('change', 'select[name=shift_id]', function() {
            var shift_id = $(this).val();
            getDesignation(shift_id);
        });
    </script>
    <script>
        // $(document).ready(function()){

        $('#department_id').change(function() {
            var department_id = $(this).val();
            $.ajax({
                url: '{{ route('employee.sub') }}',
                type: "post",
                data: {
                    '_token': '{{ csrf_token() }}',
                    'department_id': department_id
                },
                success: function(response) {
                    console.log(response);
                    $('#subdepartment_id').html(response)
                }
            })
        })
        // }
    </script>

    <script>
        var globalRoute = '';
        var html = "";
        var globalBranch = '';
        var globalDepartment = '';
        var globalSubDepartment = '';
        var globalDesignation = '';
        var globalShift = '';
        var newData = "";

        function modalShow(data, route, title, section) {
            html = "";
            globalRoute = route;
            newData = "";
            switch (section) {
                case 'department':
                    getData('get-branch');
                    break;
                case 'subdepartment':
                    getData('get-department');
                    break;
                case 'designation':
                    getData('get-department');
                    break;
                default:
                    break;
            }
            $('#staticBackdropLabel').text(title);

            html += '<div class="row">';
            $.each(data, function(key, value) {
                $.each(value, function(key1, value1) {
                    html += '<div class="col-lg-12 col-md-12 col-sm-12">\
                                                                            <div class="form-group">\
                                                                                <label class="form-label">' + key1
                        .toUpperCase() +
                        '</label>';
                    // console.log(value1);

                    if (value1 != '') {
                        html += '<div class="form-icon-user">\
                                                                                <select class="form-control" name="' +
                            key1 +
                            '">';
                        value1 = JSON.parse(value1);
                        if (newData != '') {
                            value1 = newData;
                        }
                        $.each(value1, function(key2, value2) {
                            html += '<option value="' + key2 + '">' + value2 + '</option>';
                        })
                        html += '</select>\
                                                                            </div>';
                    } else {
                        html += '<div class="form-icon-user">\
                                                                                    <input name="' + key1 + '" id="' +
                            key1 +
                            '" placeholder="' +
                            key1
                            .toUpperCase() + '" class="form-control">\
                                                                                </div>';
                    }

                    html += '   </div>\
                                                                            </div>';
                })
            });
            html += '</div>';
            $('#newModalBody').html(html);
            $('#staticBackdrop').modal('show');
        }

        function submitForm() {
            var input = [];
            $("#myForm :input").each(function() {
                var value = $(this).val();
                var name = $(this).attr('name');
                input.push({
                    'name': name,
                    'value': value
                });
            });
            var url = "<?= url('') ?>/" + globalRoute;

            $.ajax({
                type: "post",
                url: url,
                data: input,
                success: function(response) {
                    closeModal();
                    response = JSON.parse(response);
                    switch (response.section) {
                        case 'branch':
                            $('.branch_id').html(response.output);
                            break;
                        case 'department':
                            $('.department_id').html(response.output);
                            break;
                        case 'subdepartment':
                            $('.subdepartment_id').html(response.output);
                            break;
                        case 'designation':
                            $('.designation_id').html(response.output);
                            break;
                        case 'shift':
                            $('.shift_id').html(response.output);
                            break;
                        default:
                            break;
                    }
                }
            });
        }

        function closeModal() {
            $('#staticBackdrop').modal('hide');
            html = "";
        }

        function getData(url) {
            var url = "<?= url('') ?>/" + url;
            $.ajax({
                type: "get",
                url: url,
                async: false,
                success: function(response) {
                    newData = JSON.parse(response);
                }
            });
        }
    </script>
    @endpush