@extends('layouts.admin')
@php
    // $profile = asset(Storage::url('uploads/avatar/'));
    $profile = \App\Models\Utility::get_file('uploads/avatar/');

@endphp

@push('script-page')
    <script>
        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300
        })
    </script>
@endpush
@section('page-title')
    {{ __('Profile') }}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block font-weight-400 mb-0"> {{ __('Profile') }}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item active">{{ __('Profile') }}</li>
@endsection
@section('action-btn')
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-xl-3">
                    <div class="card sticky-top" style="top:30px">
                        <div class="list-group list-group-flush" id="useradd-sidenav">
                            <a href="#useradd-1"
                                class="list-group-item list-group-item-action border-0">{{ __('Personal Info') }} <div
                                    class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            <a href="#useradd-2"
                                class="list-group-item list-group-item-action border-0">{{ __('Change Password') }} <div
                                    class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            <a href="#useradd-4"
                                class="list-group-item list-group-item-action border-0">{{ __('Change Passcode') }} <div
                                    class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                            @if($userDetail->type =='super admin' ||$userDetail->type =='company' || $userDetail->type =='hr')
                                <a href="#useradd-3"
                                    class="list-group-item list-group-item-action border-0">{{ __('Two Factor Authentication') }}
                                    <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-xl-9">
                    <div id="useradd-1">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">{{ __('Personal Information') }}</h5>
                                <small> {{ __('Details about your personal information') }}</small>
                            </div>
                            <div class="card-body">


                                {{ Form::model($userDetail, ['route' => ['update.account'], 'method' => 'post', 'enctype' => 'multipart/form-data', 'class' => 'needs-validation', 'novalidate']) }}
                                @csrf
                                <div class="row">
                                    <div class="col-lg-6 col-sm-6">
                                        <div class="form-group">
                                            <label
                                                class="col-form-label text-dark">{{ __('Name') }}</label><x-required></x-required>
                                            <input class="form-control @error('name') is-invalid @enderror" name="name"
                                                type="text" id="name" placeholder="{{ __('Enter Your Name') }}"
                                                value="{{ $userDetail->name }}" required autocomplete="name">
                                            @error('name')
                                                <span class="invalid-feedback text-danger text-xs"
                                                    role="alert">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-sm-6">
                                        <div class="form-group">
                                            <label for="email"
                                                class="col-form-label text-dark">{{ __('Email') }}</label><x-required></x-required>
                                            <input class="form-control @error('email') is-invalid @enderror" name="email"
                                                type="text" id="email"
                                                placeholder="{{ __('Enter Your Email Address') }}"
                                                value="{{ $userDetail->email }}" required autocomplete="email">
                                            @error('email')
                                                <span class="invalid-feedback text-danger text-xs"
                                                    role="alert">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <div class="form-group">
                                            {{ Form::label('profile', __('Avatar'), ['class' => 'col-form-label']) }}
                                            <div class="choose-files ">
                                                <label for="profile">
                                                    <div class=" bg-primary profile "> <i
                                                            class="ti ti-upload px-1"></i>{{ __('Choose file here') }}
                                                    </div>
                                                    <input type="file" class="form-control file" name="profile"
                                                        id="profile"
                                                        onchange="document.getElementById('blah').src = window.URL.createObjectURL(this.files[0])">
                                                    <img id="blah"
                                                        class="img-fluid rounded border-2 border border-primary"
                                                        width="120px" style="height: 120px"
                                                        src="{{ !empty($userDetail->avatar) ? $profile . $userDetail->avatar : $profile . 'avatar.png' }}" />
                                                </label>
                                            </div>
                                            <span
                                                class="text-xs text-muted">{{ __('Please upload a valid image file. Size of image should not be more than 2MB.') }}</span>
                                            @error('profile')
                                                <span class="invalid-feedback text-danger text-xs"
                                                    role="alert">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="modal-footer col-lg-12">
                                        <input type="submit" value="{{ __('Save Changes') }}"
                                            class="btn btn-print-invoice  btn-primary m-r-10">
                                    </div>
                                </div>
                                </form>

                            </div>

                        </div>
                    </div>

                    <div id="useradd-2">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">{{ __('Change Password') }}</h5>
                                <small> {{ __('Details about your account password change') }}</small>
                            </div>
                            <div class="card-body">
                                {{ Form::model($userDetail, ['route' => ['update.password', $userDetail->id], 'method' => 'post', 'class' => 'needs-validation', 'novalidate']) }}
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            {{ Form::label('current_password', __('Current Password'), ['class' => 'col-form-label text-dark']) }}<x-required></x-required>
                                            {{ Form::password('current_password', ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Current Password')]) }}
                                            @error('current_password')
                                                <span class="invalid-current_password" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>


                                    <div class="col-md-4">
                                        <div class="form-group">
                                            {{ Form::label('new_password', __('New Password'), ['class' => 'col-form-label text-dark']) }}<x-required></x-required>
                                            {{ Form::password('new_password', ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter New Password')]) }}
                                            @error('new_password')
                                                <span class="invalid-new_password" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            {{ Form::label('confirm_password', __('Re-type New Password'), ['class' => 'col-form-label text-dark']) }}<x-required></x-required>
                                            {{ Form::password('confirm_password', ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Re-type New Password')]) }}
                                            @error('confirm_password')
                                                <span class="invalid-confirm_password" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer pr-0">
                                    {{ Form::submit(__('Save Changes'), ['class' => 'btn  btn-primary']) }}
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>
                    </div>
                    @if($userDetail->type =='super admin' || $userDetail->type =='company' || $userDetail->type =='hr')

                    <div id="useradd-3">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">{{ __('Two Factor Authentication') }}</h5>
                            </div>
                            <div class="card-body">
                                <p>{{ __('Two factor authentication (2FA) strengthens access security by requiring two methods (also referred to as factors) to verify your identity. Two factor authentication protects against phishing, social engineering and password brute force attacks and secures your logins from attackers exploiting weak or stolen credentials.') }}
                                </p>
                                @if ($userDetail->google2fa_secret == null)
                                    <div class=" ">
                                        <form class="form-horizontal" method="POST"
                                            action="{{ route('google2fa.index') }}">
                                            @csrf <div class="col-lg-12 text-center">
                                                <button type="submit" class="btn btn-primary">
                                                    {{ __('Generate Secret Key to Enable 2FA') }}
                                                </button>
                                            </div>
                                        </form>

                                    </div>
                                @elseif ($userDetail->google2fa_secret != null && $userDetail->google2fa_enable == 0)
                                    @php
                                        $svg_base64 = base64_encode($userDetail->google2fa_url);

                                        // Create the data URI
                                        $data_uri = 'data:image/svg+xml;base64,' . $svg_base64;

                                    @endphp

                                    1. {{ __('Install "Google Authentication App" on your') }} <a
                                        href="https://apps.apple.com/us/app/google-authenticator/id388497605"
                                        target="_black"> {{ __('IOS') }}</a> {{ __('or') }} <a
                                        href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2"
                                        target="_black">{{ __('Android phone.') }}</a><br />
                                    2. {{ __('Open the Google Authentication App and scan the below QR code.') }}<br />
                                    <div class="text-start">
                                        <img src="{{ $data_uri }}" alt="QR Code" />
                                    </div>
                                    {{ __('Alternatively, you can use the code:') }}
                                    <code>{{ $userDetail->google2fa_secret }}</code>. <br>
                                    3.
                                    {{ __('Enter the 6-digit Google Authentication code from the app') }}<br /><br />
                                    <form class="form-horizontal" method="POST"
                                        action="{{ route('google2fa.enable') }}">
                                        @csrf
                                        <div class="form-group">
                                            <label for="secret"
                                                class="col-form-label">{{ __('Authenticator Code') }}</label>
                                            <input id="secret" type="password" name="secret" class="form-control"
                                                placeholder="{{ __('Enter the code from your authenticator app') }}"
                                                required>
                                            <div class="text-center">
                                                <button type="submit" class="btn btn-primary mt-3">
                                                    {{ __('Enable 2FA') }}
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                @else
                                    <div class="alert alert-success">
                                        {{ __('2FA is currently') }} <strong>{{ __('Enabled') }}</strong>
                                        {{ __('on your account.') }}
                                    </div>
                                    <p>{{ __('If you are looking to disable Two Factor Authentication. Please confirm your password and Click Disable 2FA Button.') }}
                                    </p>
                                    <form class="form-horizontal" method="POST"
                                        action="{{ route('google2fa.disable') }}">
                                        @csrf
                                        <div class="form-group">
                                            <label for="password"
                                                class="col-form-label">{{ __('Current Password') }}</label>
                                            <input id="password" type="password" name="password" class="form-control"
                                                placeholder="{{ __('Enter Your Current Password') }}" required>
                                        </div>
                                        <div class=" text-center">
                                            <button type="submit" class="btn btn-primary mt-3">
                                                {{ __('Disable 2FA') }}
                                            </button>
                                        </div>

                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>

                    @endif

                    <div id="useradd-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">{{ __('Change Passcode') }}</h5>
                                <small> {{ __('Details about your account passcode change') }}</small>
                            </div>
                            <div class="card-body">
                                {{ Form::model($userDetail, ['route' => ['update.passcode', $userDetail->id], 'method' => 'post', 'class' => 'needs-validation', 'novalidate']) }}
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            {{ Form::label('current_passcode', __('Current Passcode'), ['class' => 'col-form-label text-dark']) }}<x-required></x-required>
                                            {{ Form::number('current_passcode', $userDetail->passcode, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Current Passcode')]) }}
                                            @error('current_passcode')
                                                <span class="invalid-current_passcode" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>


                                    <div class="col-md-4">
                                        <div class="form-group">
                                            {{ Form::label('new_passcode', __('New passcode'), ['class' => 'col-form-label text-dark']) }}<x-required></x-required>
                                            {{ Form::number('new_passcode', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter New Passcode')]) }}
                                            @error('new_passcode')
                                                <span class="invalid-new_passcode" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            {{ Form::label('confirm_passcode', __('Re-type New passcode'), ['class' => 'col-form-label text-dark']) }}<x-required></x-required>
                                            {{ Form::number('confirm_passcode', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Re-type New Passcode')]) }}
                                            @error('confirm_passcode')
                                                <span class="invalid-confirm_passcode" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer pr-0">
                                    {{ Form::submit(__('Save Changes'), ['class' => 'btn  btn-primary']) }}
                                </div>
                                {{ Form::close() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
