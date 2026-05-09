@extends('layouts.admin')
@section('page-title')
    {{ __('Manage Attendance List') }}
@endsection

@php 
    $company_settings = \App\Models\Utility::settings();
@endphp
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Attendance List') }}</li>
@endsection

@php
    use Carbon\Carbon;
@endphp

@push('script-page')
    <script>
        $('input[name="type"]:radio').on('change', function(e) {
            var type = $(this).val();

            if (type == 'monthly') {
                $('.month').addClass('d-block');
                $('.month').removeClass('d-none');
                $('.date').addClass('d-none');
                $('.date').removeClass('d-block');
            } else {
                $('.date').addClass('d-block');
                $('.date').removeClass('d-none');
                $('.month').addClass('d-none');
                $('.month').removeClass('d-block');
            }
        });

        $('input[name="type"]:radio:checked').trigger('change');
    </script>

    <script>
        $(document).ready(function() {
            var b_id = $('#branch_id').val();
            getDepartment(b_id);
        });
        $(document).on('change', 'select[name=branch]', function() {
            var branch_id = $(this).val();

            getDepartment(branch_id);
        });

        function getDepartment(bid) {

            $.ajax({
                url: '{{ route('monthly.getdepartment') }}',
                type: 'POST',
                data: {
                    "branch_id": bid,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {

                    var selected_department = $('#selected_department_id').val();

                    // Create select
                    var emp_select = `
                        <select class="form-control select department_id" name="department_id">
                            <option value="">{{ __('Select Department') }}</option>
                        </select>
                    `;

                    $('.department_div').html(emp_select);

                    // Append options
                    $.each(data, function(key, value) {

                        var selected = (key == selected_department) ? 'selected' : '';

                        $('.department_id').append(
                            '<option value="' + key + '" ' + selected + '>' + value + '</option>'
                        );
                    });

                }
            });
        }
    </script>
@endpush

@section('action-button')
@endsection

@section('content')
    @if (session('status'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {!! session('status') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="row">
        <div class="col-sm-12">
            <div class=" mt-2 " id="multiCollapseExample1">
                <div class="card">
                    <div class="card-body">
                        {{ Form::open(['route' => ['attendanceemployee.index'], 'method' => 'get', 'id' => 'attendanceemployee_filter']) }}
                        <div class="row align-items-center justify-content-end">
                            <div class="col-xl-10">
                                <div class="row">

                                    <div class="col-3">
                                        <label class="form-label">{{ __('Type') }}</label> <br>

                                        <div class="form-check form-check-inline form-group">
                                            <input type="radio" id="monthly" value="monthly" name="type"
                                                class="form-check-input"
                                                {{ isset($_GET['type']) && $_GET['type'] == 'monthly' ? 'checked' : 'checked' }}>
                                            <label class="form-check-label" for="monthly">{{ __('Monthly') }}</label>
                                        </div>
                                        <div class="form-check form-check-inline form-group">
                                            <input type="radio" id="daily" value="daily" name="type"
                                                class="form-check-input"
                                                {{ isset($_GET['type']) && $_GET['type'] == 'daily' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="daily">{{ __('Daily') }}</label>
                                        </div>

                                    </div>

                                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-12 col-12 month">
                                        <div class="btn-box">
                                            {{ Form::label('month', __('Month'), ['class' => 'form-label']) }}
                                            {{ Form::month('month', isset($_GET['month']) ? $_GET['month'] : date('Y-m'), ['class' => 'month-btn form-control month-btn']) }}
                                        </div>
                                    </div>
                                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-12 col-12 date">
                                        <div class="btn-box">
                                            {{ Form::label('date', __('Date'), ['class' => 'form-label']) }}
                                            {{ Form::text('date', isset($_GET['date']) ? $_GET['date'] : \Carbon\Carbon::now()->format($company_settings['site_date_format']), ['class' => 'form-control month-btn datepicker w-100']) }}
                                        </div>
                                    </div>
                                    @if (\Auth::user()->type != 'employee')
                                        <div class="col-xl-2 col-lg-2 col-md-6 col-sm-12 col-12">
                                            <div class="btn-box">
                                                {{ Form::label('branch', __('Branch'), ['class' => 'form-label']) }}
                                                {{ Form::select('branch', $branch, isset($_GET['branch']) ? $_GET['branch'] : '', ['class' => 'form-control select branch_id', 'id' => 'branch_id']) }}
                                            </div>
                                        </div>
                                        <div class="col-xl-2 col-lg-2 col-md-6 col-sm-12 col-12">
                                            <div class="btn-box">
                                                {{ Form::label('department', __('Department'), ['class' => 'form-label']) }}
                                                {{ Form::select('department', $department, isset($_GET['department']) ? $_GET['department'] : '', ['class' => 'form-control select department_id', 'id' => 'department_id']) }}
                                                <input type="hidden" id="selected_department_id" value="{{ isset($_GET['department']) ?  $_GET['department'] : null}}">
                                            </div>

                                            {{-- <div class="form-icon-user" id="department_div">
                                                {{ Form::label('department', __('Department'), ['class' => 'form-label']) }}
                                                <select class="form-control select department_id" name="department_id"
                                                    id="department_id" placeholder="Select Department">
                                                </select>
                                            </div> --}}

                                        </div>
                                        <div class="col-xl-2 col-lg-2 col-md-6 col-sm-12 col-12">
                                            <div class="btn-box">
                                                {{ Form::label('employee', __('Employee'), ['class' => 'form-label']) }}
                                                {{ Form::select(
                                                    'employee',
                                                    $employees,
                                                    request('employee'),
                                                    [
                                                        'class' => 'form-control select employee_id',
                                                        'id' => 'employee_id',
                                                        'placeholder' => __('All Employees')
                                                    ]
                                                ) }}
                                            </div>
                                        </div>
                                    @endif

                                </div>
                            </div>
                            <div class="col-auto mt-4">
                                <div class="row">
                                    <div class="col-auto">

                                        <a href="javascript:void(0)" class="btn btn-sm btn-primary me-1"
                                            onclick="document.getElementById('attendanceemployee_filter').submit(); return false;"
                                            data-bs-toggle="tooltip" title="{{ __('Apply') }}"
                                            data-original-title="{{ __('apply') }}">
                                            <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                        </a>

                                        <a href="{{ route('attendanceemployee.index') }}" class="btn btn-sm btn-danger me-1"
                                            data-bs-toggle="tooltip" title="{{ __('Reset') }}"
                                            data-original-title="{{ __('Reset') }}">
                                            <span class="btn-inner--icon"><i
                                                    class="ti ti-refresh text-white-off "></i></span>
                                        </a>

                                        <a href="javascript:void(0)" data-url="{{ route('attendance.file.import') }}"
                                            data-ajax-popup="true" data-title="{{ __('Import  Attendance CSV File') }}"
                                            data-bs-toggle="tooltip" title="" class="btn btn-sm btn-primary"
                                            data-bs-original-title="{{ __('Import') }}">
                                            <i class="ti ti-file"></i>
                                        </a>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>

        <div class="col-xl-12">
            <div class="card">
                <div class="card-header card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table" id="pc-dt-simple">
                            <thead>
                                <tr>
                                    @if (\Auth::user()->type != 'employee')
                                        <th>{{ __('Employee') }}</th>
                                    @endif
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Clock In') }}</th>
                                    <th>{{ __('Clock Out') }}</th>
                                    <th>{{ __('Total Break') }}</th>
                                    <th>{{ __('Late') }}</th>
                                    <th>{{ __('Early Leaving') }}</th>
                                    <th>{{ __('Overtime') }}</th>
                                    <th>{{ __('Total Hours') }}</th>
                                    @if (Gate::check('Edit Attendance') || Gate::check('Delete Attendance'))
                                        <th width="200px">{{ __('Action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($attendanceEmployee as $attendance)
                                    <tr>
                                        @php
                                            $totalHours = '—';
                                            $inProgress = false;

                                            if (!empty($attendance->clock_in) && $attendance->clock_in !== '00:00:00') {

                                                $tz = config('app.timezone') ?: 'UTC';
                                                $in  = Carbon::createFromFormat('Y-m-d H:i:s', $attendance->date . ' ' . $attendance->clock_in, $tz);

                                                if (!empty($attendance->clock_out) && $attendance->clock_out !== '00:00:00') {
                                                    $out = Carbon::createFromFormat('Y-m-d H:i:s', $attendance->date . ' ' . $attendance->clock_out, $tz);

                                                    if ($out->lessThan($in)) {
                                                        $out = $out->addDay();
                                                    }
                                                } else {
                                                    $today = Carbon::now($tz)->toDateString();

                                                    if ($attendance->date === $today) {
                                                        $out = Carbon::now($tz);
                                                        $inProgress = true;
                                                    } else {
                                                        $out = null;
                                                    }
                                                }

                                                if ($out) {
                                                    $workedSeconds = $in->diffInSeconds($out);

                                                    if (!empty($attendance->total_break) && $attendance->total_break !== '00:00:00') {
                                                        [$bH, $bM, $bS] = array_map('intval', explode(':', $attendance->total_break));
                                                        $breakSeconds = $bH * 3600 + $bM * 60 + $bS;
                                                        $workedSeconds -= $breakSeconds;
                                                    }

                                                    $workedSeconds = max(0, $workedSeconds);

                                                    $h = floor($workedSeconds / 3600);
                                                    $m = floor(($workedSeconds % 3600) / 60);
                                                    $s = $workedSeconds % 60;

                                                    $totalHours = sprintf('%02d:%02d:%02d', $h, $m, $s);
                                                } else {
                                                    $totalHours = '—';
                                                }
                                            }
                                        @endphp
                                        @if (\Auth::user()->type != 'employee')
                                            <td>{{ !empty($attendance->employee) ? $attendance->employee->name : '' }}</td>
                                        @endif
                                        <td>{{ \Auth::user()->dateFormat($attendance->date) }}</td>
                                        <td>{{ $attendance->status }}</td>
                                        <td>{{ $attendance->clock_in != '00:00:00' ? \Auth::user()->timeFormat($attendance->clock_in) : '00:00' }}
                                        </td>
                                        <td>{{ $attendance->clock_out != '00:00:00' ? \Auth::user()->timeFormat($attendance->clock_out) : '00:00' }}
                                        </td>
                                        <td>{{ $attendance->total_break ?? '00:00:00' }}</td>
                                        <td>{{ $attendance->late }}</td>
                                        <td>{{ $attendance->early_leaving }}</td>
                                        <td>{{ $attendance->overtime }}</td>
                                        <td>{{ $totalHours }}
                                            @if ($inProgress)
                                                <small class="text-muted">({{ __('In Progress') }})</small>
                                            @endif
                                        </td>
                                        @if (Gate::check('Edit Attendance') || Gate::check('Delete Attendance'))
                                            <td class="Action">
                                                        @can('Edit Attendance')
                                                            <div class="action-btn me-2">
                                                                <a href="javascript:void(0)" class="mx-3 btn btn-sm bg-info align-items-center"
                                                                    data-size="lg"
                                                                    data-url="{{ URL::to('attendanceemployee/' . $attendance->id . '/edit') }}"
                                                                    data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip"
                                                                    title="" data-title="{{ __('Edit Attendance') }}"
                                                                    data-bs-original-title="{{ __('Edit') }}">
                                                                    <span class="text-white"><i class="ti ti-pencil"></i></span>
                                                                </a>
                                                            </div>
                                                        @endcan

                                                        @can('Delete Attendance')
                                                            <div class="action-btn">
                                                                {!! Form::open([
                                                                    'method' => 'DELETE',
                                                                    'route' => ['attendanceemployee.destroy', $attendance->id],
                                                                    'id' => 'delete-form-' . $attendance->id,
                                                                ]) !!}
                                                                <a href="javascript:void(0)"
                                                                data-bs-trigger="hover"
                                                                    class=" btn btn-sm bg-danger align-items-center bs-pass-para"
                                                                    data-bs-toggle="tooltip" title=""
                                                                    data-bs-original-title="Delete" aria-label="Delete"><span class="text-white"><i
                                                                        class="ti ti-trash"></i></span></a>
                                                                </form>
                                                            </div>
                                                        @endcan
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
