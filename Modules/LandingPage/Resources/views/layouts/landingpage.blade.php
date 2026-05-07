@php
    use App\Models\Utility;
    $prefix = app()->environment('local') ? '' : 'public/';
    $settings = \Modules\LandingPage\Entities\LandingPageSetting::settings();
    $logo = Utility::get_file('uploads/landing_page_image/');
    $sup_logo = Utility::get_file('uploads/logo');
    $adminSettings = Utility::settings();
    $language = \App\Models\Utility::getValByName('default_language');
    $admin_payment_setting = Utility::getAdminPaymentSetting();
    $getseo = App\Models\Utility::getSeoSetting();
    $metatitle = isset($getseo['meta_title']) ? $getseo['meta_title'] : '';
    $metadesc = isset($getseo['meta_description']) ? $getseo['meta_description'] : '';
    $meta_image = \App\Models\Utility::get_file('uploads/meta/');
    $meta_logo = isset($getseo['meta_image']) ? $getseo['meta_image'] : '';
    $enable_cookie = \App\Models\Utility::getCookieSetting('enable_cookie');
    $setting = \App\Models\Utility::colorset();
    $SITE_RTL = Utility::getValByName('SITE_RTL');
    $color = !empty($setting['theme_color']) ? $setting['theme_color'] : 'theme-2';
    if ($language == 'ar' || $language == 'he') {
        $setting['SITE_RTL'] = 'on';
    }
    if (isset($setting['color_flag']) && $setting['color_flag'] == 'true') {
        $themeColor = 'custom-color';
    } else {
        $themeColor = $color;
    }

@endphp
<!DOCTYPE html>
<html lang="en">
<html lang="en" dir="{{ $setting['SITE_RTL'] == 'on' ? 'rtl' : '' }}">

<head>
    @include('layouts.pwa_head')

    <title>{{ env('APP_NAME') }}</title>
    <!-- Meta -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />

    <meta name="title" content="{{ $metatitle }}">
    <meta name="description" content="{{ $metadesc }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ env('APP_URL') }}">
    <meta property="og:title" content="{{ $metatitle }}">
    <meta property="og:description" content="{{ $metadesc }}">
    <meta property="og:image"
        content="{{ isset($meta_logo) && !empty(asset('storage/uploads/meta/' . $meta_logo)) ? asset('storage/uploads/meta/' . $meta_logo) : 'hrmgo.png' }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ env('APP_URL') }}">
    <meta property="twitter:title" content="{{ $metatitle }}">
    <meta property="twitter:description" content="{{ $metadesc }}">
    <meta property="twitter:image"
        content="{{ isset($meta_logo) && !empty(asset('storage/uploads/meta/' . $meta_logo)) ? asset('storage/uploads/meta/' . $meta_logo) : 'hrmgo.png' }}">

    <!-- Favicon icon -->
    {{-- <link rel="icon" href="{{ $sup_logo.'/'. $adminSettings['company_favicon'] }}" type="image/x-icon" /> --}}
    <link rel="icon"
        href="{{ $sup_logo . '/' . (isset($company_favicon) && !empty($company_favicon) ? $company_favicon : 'favicon.png') }}"
        type="image/x-icon" />

    <!-- font css -->
    <link rel="stylesheet" href="{{ asset($prefix . 'modules/landingpage/fonts/tabler-icons.min.css') }}" />

    <link rel="stylesheet" href=" {{ asset($prefix . 'modules/landingpage/fonts/feather.css') }}" />
    <link rel="stylesheet" href="  {{ asset($prefix . 'modules/landingpage/fonts/fontawesome.css') }}" />
    <link rel="stylesheet" href="{{ asset($prefix . 'modules/landingpage/fonts/material.css') }}" />



    <!-- vendor css -->
    <link rel="stylesheet" href="  {{ asset($prefix . 'modules/landingpage/css/style.css') }}" />
    <link rel="stylesheet" href=" {{ asset($prefix . 'modules/landingpage/css/customizer.css') }}" />
    <link rel="stylesheet" href=" {{ asset('assets/landingpage/landing-page.css') }}" />
    <link rel="stylesheet" href=" {{ asset('assets/landingpage/custom.css') }}" />

    <style>
        :root {
            --color-customColor: <?=$color ?>;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/custom-color.css') }}">

    @if ($SITE_RTL == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/style-rtl.css') }}">
    @endif

    @if ($setting['cust_darklayout'] == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/style-dark.css') }}">
    @else
        <link rel="stylesheet" href="{{ asset($prefix . 'modules/landingpage/css/style.css') }}" id="main-style-link">
    @endif

</head>

@if ($setting['cust_darklayout'] == 'on')

    <body class="{{ $themeColor }} landing-dark">
    @else

        <body class="{{ $themeColor }}">
@endif
<!-- [ Header ] start -->
<header class="main-header">
    @if ($settings['topbar_status'] == 'on')
        <div class="announcement bg-dark text-center p-2">
            <p class="mb-0">{!! $settings['topbar_notification_msg'] !!}</p>
        </div>
    @endif
    @if ($settings['menubar_status'] == 'on')
        <div class="container">
            <nav class="navbar navbar-expand-md  default top-nav-collapse">
                <div class="header-left">
                    <a class="navbar-brand bg-transparent" href="https://idabtech.com/">
                        <img src="{{ $logo . '/' . $settings['site_logo'] . '?' . time() }}" alt="logo">
                    </a>
                </div>
                <div class="ms-auto d-flex justify-content-end gap-2">
                    <a href="{{ route('login') }}" class="btn btn-outline-dark rounded"><span
                            class="hide-mob me-2">{{ __('Login') }}</span> <i data-feather="log-in"></i></a>
                    @if (isset($adminSettings['disable_signup_button']) && $adminSettings['disable_signup_button'] == 'on')
                        <a href="{{ route('register') }}" class="btn btn-outline-dark rounded"><span
                                class="hide-mob me-2">{{ __('Register') }}</span> <i data-feather="user-check"></i></a>
                    @endif
                    <button class="navbar-toggler " type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false"
                        aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>
            </nav>
        </div>
    @endif

</header>
<!-- [ Header ] End -->
<!-- [ Banner ] start -->
<div class="position-fixed top-0 end-0 p-3" style="z-index: 99999">
    <div id="liveToast" class="toast text-white fade" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                aria-label="Close"></button>
        </div>
    </div>
</div>

@if ($settings['home_status'] == 'on')
    <section class="main-banner bg-primary" id="home">
        <div class="container">
            <div class="row gy-3 g-0 align-items-center">
                <div class="col-xxl-4 col-md-6">
                    <span class="badge py-2 px-3 bg-white text-dark rounded-pill fw-bold mb-3">
                        {{ $settings['home_offer_text'] }}</span>
                    <h1 class="mb-3">
                        {{-- <b class="fw-bold">{{ env('APP_NAME') }}</b> <br> --}}
                        {{ $settings['home_heading'] }}
                    </h1>

                    <h6 class="mb-0">{{ $settings['home_description'] }}</h6>
                    <div class="d-flex gap-3 mt-4 banner-btn">
                        @if ($settings['home_buy_now_link'])
                            <a href="{{ $settings['home_buy_now_link'] }}"
                                class="btn btn-outline-dark">{{ __('Buy Now') }} <i data-feather="lock"
                                    class="ms-2"></i></a>
                        @endif
                    </div>
                </div>
                <div class="col-xxl-8 col-md-6">
                    <div class="dash-preview">
                        <img class="img-fluid preview-img" src="{{ $logo . '/' . $settings['home_banner'] }}"
                            alt="">
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row g-0 gy-2 mt-4 align-items-center">
                <div class="col-xxl-3">
                    <p class="mb-0">{{ __('Trusted by') }} <b
                            class="fw-bold">{{ $settings['home_trusted_by'] }}</b></p>
                </div>
                <div class="col-xxl-9">
                    <div class="row gy-3 row-cols-9">
                        @foreach (explode(',', $settings['home_logo']) as $k => $home_logo)
                            <div class="col-auto">
                                <img src="{{ $logo . '/' . $home_logo }}" alt="" class="img-fluid"
                                    style="width: 130px;">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
<!-- [ Banner ] start -->
<!-- [ features ] start -->
@if ($settings['feature_status'] == 'on')
    <section class="features-section section-gap bg-dark" id="features">
        <div class="container">
            <div class="row gy-3">
                <div class="col-xxl-4">
                    <span class="d-block mb-2 text-uppercase">{{ $settings['feature_title'] }}</span>
                    <div class="title mb-4">
                        <h2><b class="fw-bold">{!! $settings['feature_heading'] !!}</b></h2>
                    </div>
                    <p class="mb-3">{!! $settings['feature_description'] !!}</p>
                    @if ($settings['feature_buy_now_link'])
                        <a href="{{ $settings['feature_buy_now_link'] }}"
                            class="btn btn-primary rounded-pill d-inline-flex align-items-center">{{ __('Buy Now') }}
                            <i data-feather="lock" class="ms-2"></i></a>
                    @endif
                </div>
                <div class="col-xxl-8">
                    <div class="row">
                        @if (is_array(json_decode($settings['feature_of_features'], true)) ||
                                is_object(json_decode($settings['feature_of_features'], true)))
                            @foreach (json_decode($settings['feature_of_features'], true) as $key => $value)
                                <div class="col-lg-4 col-sm-6 d-flex">
                                    <div class="card {{ $key == 0 ? 'bg-primary' : '' }}">
                                        <div class="card-body">
                                            <span class="theme-avtar avtar avtar-xl mb-4">
                                                <img src="{{ $logo . '/' . $value['feature_logo'] . '?' . time() }}"
                                                    alt="">
                                            </span>
                                            <h3 class="mb-3 {{ $key == 0 ? '' : 'text-white' }}">
                                                {!! $value['feature_heading'] !!}</h3>
                                            <p class=" f-w-600 mb-0 {{ $key == 0 ? 'text-body' : '' }}">
                                                {!! $value['feature_description'] !!}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="mt-5">
                    <div class="title text-center mb-4">
                        <span class="d-block mb-2 text-uppercase">{{ $settings['feature_title'] }}</span>
                        <h2 class="mb-4">{!! $settings['highlight_feature_heading'] !!}</h2>
                        <p>{!! $settings['highlight_feature_description'] !!}</p>
                    </div>
                    <div class="features-preview">
                        <img class="img-fluid m-auto d-block"
                            src="{{ $logo . '/' . $settings['highlight_feature_image'] }}" alt="">
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif

<!-- [ Screenshots ] start -->
@if ($settings['screenshots_status'] == 'on')
    <section class="screenshots section-gap">
        <div class="container">
            <div class="row mb-2 justify-content-center">
                <div class="col-xxl-6">
                    <div class="title text-center mb-4">
                        <span class="d-block mb-2 fw-bold text-uppercase">{{ __('SCREENSHOTS') }}</span>
                        <h2 class="mb-4">{!! $settings['screenshots_heading'] !!}</h2>
                        <p>{!! $settings['screenshots_description'] !!}</p>
                    </div>
                </div>
            </div>
            <div class="row gy-4 gx-4">
                @if (is_array(json_decode($settings['screenshots'], true)) || is_object(json_decode($settings['screenshots'], true)))
                    @foreach (json_decode($settings['screenshots'], true) as $value)
                        <div class="col-md-4 col-sm-6">
                            <div class="screenshot-card">
                                <div class="img-wrapper">
                                    <img src="{{ $logo . '/' . $value['screenshots'] }}"
                                        class="img-fluid header-img mb-4 shadow-sm" alt="">
                                </div>
                                <h5 class="mb-0">{!! $value['screenshots_heading'] !!}</h5>
                                {{-- <a href="javascript:void(0)" class="btn btn-primary pr-btn"> <i data-feather="search"></i> </a> --}}
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </section>
@endif
<!-- [ Screenshots ] start -->

<!-- [ Footer ] start -->
<footer class="site-footer bg-gray-100">
    <div class="container">
        <div class="footer-row">
            <div class="ftr-col cmp-detail">
                <div class="footer-logo mb-3">
                    <a href="javascript:void(0)">
                        <img src="{{ asset('storage/uploads/logo/logo-dark.png') }}" alt="logo">
                    </a>
                </div>
                <p>
                    {!! $settings['site_description'] !!}
                </p>

            </div>
            <div class="ftr-col">
                <ul class="list-unstyled">
                    @php
                        $menubarPages = json_decode($settings['menubar_page'] ?? '[]') ?? [];
                    @endphp

                    @if (!empty($menubarPages))
                        @foreach ($menubarPages as $value)
                            @if (!empty($value->footer) && $value->footer === 'on' && !empty($value->page_url) && !empty($value->menubar_page_name))
                                <li>
                                    <a href="{{ $value->page_url }}">{{ $value->menubar_page_name }}</a>
                                </li>
                            @endif
                        @endforeach
                    @endif

                </ul>
            </div>
            @if ($settings['joinus_status'] == 'on')
                <div class="ftr-col ftr-subscribe">
                    <h2>{!! $settings['joinus_heading'] !!}</h2>
                    <p>{!! $settings['joinus_description'] !!}</p>
                    <form method="post" action="{{ route('join_us_store') }}">
                        @csrf
                        <div class="input-wrapper border border-dark">
                            <input type="text" name="email" placeholder="Type your email address..." required>
                            <button type="submit" class="btn btn-dark rounded-pill">{{ __('Join Us!') }}</button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
    <div class="border-top border-dark text-center p-2">

        &copy;{{ date(' Y') }}
        {{ App\Models\Utility::getValByName('footer_text') ? App\Models\Utility::getValByName('footer_text') : config('app.name', 'IDAB TECH') }}
        {{ __(' - (HRMS)') }}

    </div>
</footer>
<!-- [ Footer ] end -->
<!-- Required Js -->

<script src="{{ asset($prefix . 'modules/landingpage/js/plugins/popper.min.js') }}"></script>
<script src="{{ asset($prefix . 'modules/landingpage/js/plugins/bootstrap.min.js') }}"></script>
<script src="{{ asset($prefix . 'modules/landingpage/js/plugins/feather.min.js') }}"></script>

<script>
    function show_toastr(type, message) {
        const toastEl = document.getElementById('liveToast');
        const toastBody = toastEl.querySelector('.toast-body');

        // Reset background color classes
        toastEl.classList.remove('bg-primary', 'bg-danger');

        // Add appropriate class
        if (type === 'success' || type === 'Success') {
            toastEl.classList.add('bg-primary');
        } else {
            toastEl.classList.add('bg-danger');
        }

        // Set message
        toastBody.innerHTML = message;

        // Show toast
        const toast = new bootstrap.Toast(toastEl);
        toast.show();
    }

    // Start [ Menu hide/show on scroll ]
    let ost = 0;
    document.addEventListener("scroll", function() {
        let cOst = document.documentElement.scrollTop;
        if (cOst == 0) {
            document.querySelector(".navbar").classList.add("top-nav-collapse");
        } else if (cOst > ost) {
            document.querySelector(".navbar").classList.add("top-nav-collapse");
            document.querySelector(".navbar").classList.remove("default");
        } else {
            document.querySelector(".navbar").classList.add("default");
            document
                .querySelector(".navbar")
                .classList.remove("top-nav-collapse");
        }
        ost = cOst;
    });
    // End [ Menu hide/show on scroll ]

    var scrollSpy = new bootstrap.ScrollSpy(document.body, {
        target: "#navbar-example",
    });
    feather.replace();
</script>

@if ($message = Session::get('success'))
    <script>
        show_toastr('Success', '{!! $message !!}');
    </script>
@endif
@if ($message = Session::get('error'))
    <script>
        show_toastr('Error', '{!! $message !!}');
    </script>
@endif

@stack('custom-scripts')
@if ($enable_cookie['enable_cookie'] == 'on')
    @include('layouts.cookie_consent')
@endif
@include('layouts.pwa_styles')
</body>

</html>
