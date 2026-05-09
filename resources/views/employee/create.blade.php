<style>
    .addBtn {
        float: right;
    }
</style>
@extends('layouts.admin')
@section('page-title')
{{ __('Create Employee') }}
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
<li class="breadcrumb-item"><a href="{{ url('employee') }}">{{ __('Employee') }}</a></li>
<li class="breadcrumb-item">{{ __('Create Employee') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="">
        <div class="">
            <div class="row">

            </div>
            {{ Form::open(['route' => ['employee.store'], 'method' => 'post', 'enctype' => 'multipart/form-data', 'class' => 'employee-create-form']) }}
            <div class="row">
                <div class="col-md-6">
                    <div class="card em-card">
                        <div class="card-header">
                            <h5>{{ __('Personal Detail') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    {!! Form::label('name', __('First Name'), ['class' => 'form-label']) !!}<span class="text-danger pl-1">*</span>
                                    {!! Form::text('fname', old('name'), [
                                    'class' => 'form-control',
                                    'required' => 'required',
                                    'placeholder' => 'Enter Employee First Name',
                                    ]) !!}
                                </div>
                                <div class="form-group col-md-6">
                                    {!! Form::label('name', __('Last Name'), ['class' => 'form-label']) !!}<span class="text-danger pl-1">*</span>
                                    {!! Form::text('lname', old('name'), [
                                    'class' => 'form-control',
                                    'required' => 'required',
                                    'placeholder' => 'Enter Employee Last Name',
                                    ]) !!}
                                </div>
                                <x-mobile divClass="col-md-6" name="phone" label="{{ __('Phone') }}"
                                    placeholder="{{ __('Enter employee phone') }}" id="phone" required="true">
                                </x-mobile>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('dob', __('Date of Birth'), ['class' => 'form-label']) !!}
                                        <span class="text-danger pl-1">*</span>

                                        {{ Form::text('dob', null, 
                                            [
                                                'class' => 'form-control datepicker w-100',
                                                'required',
                                                'autocomplete' => 'off',
                                                'placeholder' => 'Select Date of Birth',
                                            ]) 
                                        }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('gender', __('Gender'), ['class' => 'form-label']) !!}<span class="text-danger pl-1">*</span>
                                        <div class="d-flex radio-check">
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" id="g_male" value="Male" name="gender"
                                                    class="form-check-input" required>
                                                <label class="form-check-label"
                                                    for="g_male">{{ __('Male') }}</label>
                                            </div>
                                            <div class="custom-control custom-radio ms-1 custom-control-inline">
                                                <input type="radio" id="g_female" value="Female" name="gender"
                                                    class="form-check-input">
                                                <label class="form-check-label"
                                                    for="g_female">{{ __('Female') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    {!! Form::label('email', __('Email'), ['class' => 'form-label']) !!}<span class="text-danger pl-1">*</span>
                                    {!! Form::email('email', old('email'), [
                                    'class' => 'form-control',
                                    'required' => 'required',
                                    'placeholder' => 'Enter employee email',
                                    ]) !!}
                                </div>
                                <div class="form-group col-md-6">
                                    {!! Form::label('password', __('Password'), ['class' => 'form-label']) !!}<span class="text-danger pl-1">*</span>
                                    {!! Form::password('password', [
                                    'class' => 'form-control',
                                    'required' => 'required',
                                    'placeholder' => 'Enter employee new password',
                                    ]) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('address', __('Address'), ['class' => 'form-label']) !!}<span class="text-danger pl-1">*</span>
                                {!! Form::textarea('address', old('address'), [
                                'class' => 'form-control',
                                'rows' => 2,
                                'required' => 'required',
                                'placeholder' => 'Enter employee address',
                                ]) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card em-card">
                        <div class="card-header">
                            <h5>{{ __('Company Detail') }}</h5>
                        </div>
                        <div class="card-body employee-detail-create-body">
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
                                    <div class="form-icon-user">
                                        {{ Form::select('branch_id', $branches, null, ['class' => 'form-control branch_id', 'required' => 'required', 'placeholder' => 'Select Branch']) }}
                                    </div>
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
                                    <div class="form-icon-user">
                                        {{ Form::select('department_id', $departments, null, ['class' => 'form-control department_id', 'id' => 'department_id', 'required' => 'required', 'placeholder' => 'Select Department']) }}
                                    </div>
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
                                    <div class="form-icon-user">
                                        {{ Form::select('subdepartment_id', $subdepartments, null, ['class' => 'form-control subdepartment_id', 'id' => 'subdepartment_id', 'required' => 'required', 'placeholder' => 'Select Sub Department']) }}
                                    </div>
                                </div>

                                <div class="form-group col-md-6 ">
                                    {{ Form::label('designation_id', __('Select Designation'), ['class' => 'form-label']) }}
                                    <a href="javascript:void(0)" data-title="{{ __('Create New Designation') }}"
                                        onclick="modalShow([{'department': '{{ $departments }}'}, {'name' : ''}], 'create-designation','Create Designation','designation')"
                                        data-bs-toggle="tooltip" title="{{ __('Create New Designation') }}"
                                        class="btn btn-sm btn-primary addBtn"
                                        data-bs-original-title="{{ __('Create') }}">
                                        <i class="ti ti-plus"></i>
                                    </a>
                                    <div class="form-icon-user">
                                        {{ Form::select('designation_id', $designations, null, ['class' => 'form-control designation_id', 'id' => 'designation_id', 'required' => 'required', 'placeholder' => 'Select Designation']) }}

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
                                        {{ Form::select('shift_id', $shift, null, ['class' => 'form-control shift_id', 'id' => 'shift_id', 'required' => 'required', 'placeholder' => 'Select Shift']) }}

                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    {{ Form::label('shift_id', __('Select Employee Zone Time'), ['class' => 'form-label']) }}
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
                                            @endphp

                                            @if($shiftOptions)
                                                {{ Form::select('company_shift_time', $shiftOptions, null, [
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
                                </div>
                                <div class="form-group col-md-6">
                                    <div class="form-group ">
                                        {!! Form::label(' company_doj', __('Company Date Of Joining'), ['class'=> ' form-label']) !!}<span class="text-danger pl-1">*</span>
                                        {{ Form::date('company_doj', 
                                            !empty($employee->company_doj) 
                                                ? \Carbon\Carbon::parse($employee->company_doj)->format($company_settings['site_date_format']) 
                                                : null, 
                                            [
                                                'class' => 'form-control w-100',
                                                'required',
                                                'autocomplete' => 'off',
                                                'placeholder' => 'Select company date of joining',
                                            ]) 
                                        }}
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 ">
                        <div class="card em-card">
                            <div class="card-header">
                                <h5>{{ __('Document') }}</h6>
                            </div>
                            <div class="card-body employee-detail-create-body">
                                @foreach ($documents as $key => $document)
                                <div class="row">
                                    <div class="form-group col-12 d-flex">
                                        <div class="float-left col-4">
                                            <label for="document"
                                                class="float-left pt-1 form-label">{{ $document->name }} @if ($document->is_required == 1)
                                                <span class="text-danger">*</span>
                                                @endif
                                            </label>
                                        </div>
                                        <div class="float-right col-8">
                                            <input type="hidden" name="emp_doc_id[{{ $document->id }}]"
                                                id="" value="{{ $document->id }}">
                                            <div class="choose-files">
                                                <label for="document[{{ $document->id }}]">
                                                    <div class=" bg-primary document "> <i
                                                            class="ti ti-upload "></i>{{ __('Choose file here') }}
                                                    </div>
                                                    <input type="file"
                                                        class="form-control file  d-none @error('document') is-invalid @enderror"
                                                        @if ($document->is_required == 1) required @endif
                                                    name="document[{{ $document->id }}]"
                                                    id="document[{{ $document->id }}]"
                                                    data-filename="{{ $document->id . '_filename' }}"
                                                    onchange="document.getElementById('{{ 'blah' . $key }}').src = window.URL.createObjectURL(this.files[0])">
                                                </label>
                                                <img id="{{ 'blah' . $key }}" src="" width="50%" />

                                            </div>

                                        </div>

                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 ">
                        <div class="card em-card">
                            <div class="card-header">
                                <h5>{{ __('Bank Account Detail') }}</h5>
                            </div>
                            <div class="card-body employee-detail-create-body">
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        {!! Form::label('account_holder_name', __('Account Holder Name'), ['class' => 'form-label']) !!}
                                        {!! Form::text('account_holder_name', old('account_holder_name'), [
                                        'class' => 'form-control',
                                        'placeholder' => 'Enter account holder name',
                                        ]) !!}

                                    </div>
                                    <div class="form-group col-md-6">
                                        {!! Form::label('account_number', __('Account Number'), ['class' => 'form-label']) !!}
                                        {!! Form::number('account_number', old('account_number'), [
                                        'class' => 'form-control',
                                        'placeholder' => 'Enter account number',
                                        ]) !!}

                                    </div>
                                    <div class="form-group col-md-6">
                                        {!! Form::label('bank_name', __('Bank Name'), ['class' => 'form-label']) !!}
                                        {!! Form::text('bank_name', old('bank_name'), ['class' => 'form-control', 'placeholder' => 'Enter bank name']) !!}

                                    </div>
                                    <div class="form-group col-md-6">
                                        {!! Form::label('bank_identifier_code', __('IFSC Code'), ['class' => 'form-label']) !!}
                                        {!! Form::text('bank_identifier_code', old('bank_identifier_code'), [
                                        'class' => 'form-control',
                                        'placeholder' => 'Enter bank identifier code',
                                        ]) !!}
                                    </div>
                                    <div class="form-group col-md-6">
                                        {!! Form::label('branch_location', __('Branch Location'), ['class' => 'form-label']) !!}
                                        {!! Form::text('branch_location', old('branch_location'), [
                                        'class' => 'form-control',
                                        'placeholder' => 'Enter branch location',
                                        ]) !!}
                                    </div>
                                    <div class="form-group col-md-6">
                                        {!! Form::label('tax_payer_id', __('Tax Payer/PAN Id'), ['class' => 'form-label']) !!}
                                        {!! Form::text('tax_payer_id', old('tax_payer_id'), [
                                        'class' => 'form-control',
                                        'placeholder' => 'Enter tax payer id',
                                        ]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="float-end">
                    <button type="submit" class="btn  btn-primary">{{ 'Create' }}</button>
                </div>
            </div>

            </form>
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
    @include('layouts.dateformat')
    <script>
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                const modal = document.querySelector('.modal.show');

                if (modal) {
                    submitForm();
                } else {
                    const mainForm = document.querySelector('.employee-create-form');
                    if (mainForm) {
                        mainForm.querySelector('button[type="submit"]')?.click();
                    }
                }
            }
        });

        $('input[type="file"]').change(function(e) {
            var file = e.target.files[0].name;
            var file_name = $(this).attr('data-filename');
            $('.' + file_name).append(file);
        });
    </script>
    <script>
        $(document).ready(function() {
            var d_id = $('.department_id').val();
            getDesignation(d_id);
        });

        $(document).on('change', 'select[name=department_id]', function() {
            var department_id = $(this).val();
            getDesignation(department_id);
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
                    var emp_selct = ` <select class="form-control  designation_id" name="designation_id" id="choices-multiple"
                                            placeholder="Select Designation" >
                                            </select>`;
                    $('.designation_div').html(emp_selct);

                    $('.designation_id').append('<option value="0"> {{ __('All') }} </option>');
                    $.each(data, function(key, value) {
                        $('.designation_id').append('<option value="' + key + '">' + value +
                            '</option>');
                    });
                    new Choices('#choices-multiple', {
                        removeItemButton: true,
                    });


                }
            });
        }
    </script>
    <script>
        
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
                                                        <label class="form-label">' + key1.toUpperCase() + '</label>';

                    if (value1 != '') {
                        html += '<div class="form-icon-user">\
                                                        <select class="form-control" name="' + key1 + '">';
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
                                                            <input name="' + key1 + '" id="' + key1 + '" placeholder="' +
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