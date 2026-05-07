@extends('layouts.admin')

@section('page-title')
    {{ __('My Shifts') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('My Shifts') }}</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h5>{{ __('Current and Upcoming Shifts') }}</h5>
        </div>
        <div class="card-body">
            @if ($employee)
                <div class="row mb-4">
                    {{-- Current Shift --}}
                    <div class="col-md-6">
                        <div class="p-3 border rounded shadow-sm bg-light">
                            <h6 class="text-muted">{{ __('Current Shift') }}</h6>
                            <h4 class="text-primary">{{ optional($employee->shift)->name ?? __('Not Assigned') }}</h4>
                            @if ($employee->shift)
                                <p class="mb-0">
                                    <strong>{{ __('Start Time:') }}</strong>
                                    {{ \Carbon\Carbon::parse($employee->shift->company_start_time)->format('h:i A') }}
                                </p>
                                <p class="mb-0">
                                    <strong>{{ __('End Time:') }}</strong>
                                    {{ \Carbon\Carbon::parse($employee->shift->company_end_time)->format('h:i A') }}
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Upcoming Shift --}}
                    <div class="col-md-6">
                        <div class="p-3 border rounded shadow-sm bg-light">
                            <h6 class="text-muted">{{ __('Upcoming Shift') }}</h6>
                            <h4 class="text-success">{{ optional($employee->upcomingShift)->name ?? __('Not Assigned') }}
                            </h4>
                            @if ($employee->upcomingShift)
                                <p class="mb-0">
                                    <strong>{{ __('Start Time:') }}</strong>
                                    {{ \Carbon\Carbon::parse($employee->upcomingShift->company_start_time)->format('h:i A') }}
                                </p>
                                <p class="mb-0">
                                    <strong>{{ __('End Time:') }}</strong>
                                    {{ \Carbon\Carbon::parse($employee->upcomingShift->company_end_time)->format('h:i A') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <p class="text-center text-muted">{{ __('No shift data found for you.') }}</p>
            @endif
        </div>
    </div>
@endsection
