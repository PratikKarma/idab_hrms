@extends('layouts.admin')

@section('page-title')
    {{ __('Manage Attendance Requests') }}
@endsection

@php 
    $company_settings = \App\Models\Utility::settings();
@endphp
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Attendance Requests') }}</li>
@endsection

@section('action-button')
    <div class="row align-items-center m-1">
        @can('Create Attendance Request')
            <a href="javascript:void(0)" data-size="lg" data-url="{{ route('attendance-requests.create') }}" data-ajax-popup="true"
                data-bs-toggle="tooltip" title="{{ __('Create') }}" data-title="{{ __('Request Clock In/Out') }}"
                class="btn btn-sm btn-primary">
                <i class="ti ti-plus"></i>
            </a>
        @endcan
    </div>
@endsection

@section('content')
    <div class="row">
        {{-- Stats Cards --}}
        <div class="col-xl-3">
            <div class="card comp-card">
                <div class="card-body">
                    <h6>{{ __('Total Requests') }}</h6>
                    <h3>{{ $stats['total'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3">
            <div class="card comp-card">
                <div class="card-body">
                    <h6>{{ __('This Month') }}</h6>
                    <h3>{{ $stats['this_month'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3">
            <div class="card comp-card">
                <div class="card-body">
                    <h6>{{ __('This Week') }}</h6>
                    <h3>{{ $stats['this_week'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3">
            <div class="card comp-card">
                <div class="card-body">
                    <h6>{{ __('Last 30 Days') }}</h6>
                    <h3>{{ $stats['last_30days'] }}</h3>
                </div>
            </div>
        </div>

        <div class="col-sm-12">
            <div class=" mt-2 " id="multiCollapseExample1">
                <div class="card">
                    <div class="card-body">
                        {{-- Filter Form --}}
                        {{ Form::open(['route' => ['attendance-requests.index'], 'method' => 'get', 'id' => 'attendance_request_filter']) }}
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

                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 month">
                                        <div class="btn-box">
                                            {{ Form::label('month', __('Month'), ['class' => 'form-label']) }}
                                            {{ Form::month('month', isset($_GET['month']) ? $_GET['month'] : date('Y-m'), ['class' => 'month-btn form-control month-btn']) }}
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 date">
                                        <div class="btn-box">
                                            {{ Form::label('date', __('Date'), ['class' => 'form-label']) }}
                                            {{ Form::text('date', isset($_GET['date']) ? $_GET['date'] : \Carbon\Carbon::now()->format($company_settings['site_date_format']), ['class' => 'form-control month-btn datepicker w-100']) }}
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="col-auto mt-4">
                                <div class="row">
                                    <div class="col-auto">

                                        <a href="javascript:void(0)" class="btn btn-sm btn-primary me-1"
                                            onclick="document.getElementById('attendance_request_filter').submit(); return false;"
                                            data-bs-toggle="tooltip" title="{{ __('Apply') }}"
                                            data-original-title="{{ __('apply') }}">
                                            <span class="btn-inner--icon"><i class="ti ti-search"></i></span>
                                        </a>

                                        <a href="{{ route('attendance-requests.index') }}"
                                            class="btn btn-sm btn-danger me-1" data-bs-toggle="tooltip"
                                            title="{{ __('Reset') }}" data-original-title="{{ __('Reset') }}">
                                            <span class="btn-inner--icon"><i
                                                    class="ti ti-refresh text-white-off "></i></span>
                                        </a>
                                    </div>

                                </div>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="col-xl-12 mt-3">
            <div class="card table-card">
                <div class="card-header card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table mb-0" id="pc-dt-simple">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Employee') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Reason') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Requested Date') }}</th>
                                    <th>{{ __('Requested Time') }}</th>
                                    <th>{{ __('Approved By') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($requests as $req)
                                    @php
                                        $reqDateTime = \Carbon\Carbon::parse($req->requested_at);
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $req->employee?->name }}</td>
                                        <td>{{ Str::title(str_replace('_', ' ', $req->type)) }}</td>
                                        <td data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $req->reason ?? '-' }}"style="max-width: 200px;white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">{{ $req->reason ?? '-' }}</td>
                                        <td>
                                            @if ($req->status == 'pending')
                                                <span class="badge bg-warning">{{ __('Pending') }}</span>
                                            @elseif($req->status == 'approved')
                                                <span class="badge bg-success">{{ __('Approved') }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ __('Declined') }}</span>
                                            @endif
                                        </td>
                                        {{-- Separate date and time --}}
                                        <td>{{ $reqDateTime->toDateString() }}</td>
                                        <td>{{ $reqDateTime->format('h:i A') }}</td>
                                        <td>{{ $req->approver?->name }}</td>
                                        <td>
                                            @can('Approve Attendance Request')
                                                @if ($req->status == 'pending')
                                                    <form method="POST"
                                                        action="{{ route('attendance-requests.approve', $req) }}"
                                                        style="display:inline;">
                                                        @csrf
                                                        <button type="submit"
                                                            class="btn btn-sm btn-success">{{ __('Approve') }}</button>
                                                    </form>
                                                    <form method="POST"
                                                        action="{{ route('attendance-requests.decline', $req) }}"
                                                        style="display:inline;">
                                                        @csrf
                                                        <button type="submit"
                                                            class="btn btn-sm btn-danger">{{ __('Decline') }}</button>
                                                    </form>
                                                @else
                                                    -
                                                @endif
                                            @endcan
                                            {{-- @can('Delete Attendance Request')
                                                {!! Form::open([
                                                    'method' => 'DELETE',
                                                    'route' => ['attendance-requests.destroy', $req->id],
                                                    'style' => 'display:inline',
                                                ]) !!}
                                                <button type="submit"
                                                    class="btn btn-sm btn-danger">{{ __('Delete') }}</button>
                                                {!! Form::close() !!}
                                            @endcan --}}
                                        </td>
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

@push('script-page')
@include('layouts.dateformat');
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
@endpush
