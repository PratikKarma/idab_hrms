@php
    $prefix = app()->environment('local') ? '' : 'public/';

    $status = $status ?? 500;

    $titles = [
        404 => [
            'Page Ran Away 😱',
            'Oops! Nothing to see here…',
            'This page ghosted us 👻',
            'Fell into the void 🕳️',
            'Wrong turn somewhere 🚧',
            'Page not found — classic 404 🙈',
        ],
        500 => [
            'Oops! Server had a meltdown 🔥',
            'It’s not you, it’s us 💔',
            'Well, that escalated quickly…',
            'Brain unplugged! 🧠🔌',
            'Unexpected item in the bagging area 🤖',
            'Abort Mission! 🚀💥',
            'Oopsie Doopsie!',
            'Something Broke... Send Help 🙃',
            'Uh-oh, Spaghetti Code 🍝',
            'We’ve hit a snag 🪤',
            'Well, this is awkward…',
            'Let’s pretend this didn’t happen',
        ],
        403 => ['Not allowed here, bud 🙅‍♂️', 'Access Denied 🛑', 'You shall not pass! 🧙‍♂️'],
        'default' => [
            'Oops! Something went wrong 🚨',
            'Temporary setback. Permanent charm ✨',
            'Unexpected error occurred 🤷‍♀️',
        ],
    ];

    $images = [
        404 => [
            asset($prefix . 'custom_error_images/404_1.png'),
            asset($prefix . 'custom_error_images/404_2.png'),
            asset($prefix . 'custom_error_images/404_3.png'),
            asset($prefix . 'custom_error_images/404_4_gif.gif'),
            asset($prefix . 'custom_error_images/404_5.png'),
        ],
        500 => [
            asset($prefix . 'custom_error_images/image1.png'),
            asset($prefix . 'custom_error_images/image2.png'),
            asset($prefix . 'custom_error_images/image3.png'),
            asset($prefix . 'custom_error_images/image4.png'),
            asset($prefix . 'custom_error_images/image5.png'),
            asset($prefix . 'custom_error_images/image6.png'),
            asset($prefix . 'custom_error_images/image7.png'),
        ],
        'default' => [
            asset($prefix . 'custom_error_images/image5.png'),
            asset($prefix . 'custom_error_images/image6.png'),
            asset($prefix . 'custom_error_images/image7.png'),
        ],
    ];

    $themes = [
        [
            'bg' => '#f9f9f9',
            'text' => '#333',
            'btn' => 'btn-outline-primary',
            'shadow' => '0 8px 20px rgba(0,0,0,0.1)',
        ],
        [
            'bg' => '#1a1a1a',
            'text' => '#746e6e',
            'btn' => 'btn-warning',
            'shadow' => '0 8px 20px rgba(255,255,255,0.1)',
        ],
        [
            'bg' => '#fff0f6',
            'text' => '#d63384',
            'btn' => 'btn-danger',
            'shadow' => '0 8px 30px rgba(214, 51, 132, 0.2)',
        ],
        [
            'bg' => '#e7f5ff',
            'text' => '#1c7ed6',
            'btn' => 'btn-primary',
            'shadow' => '0 8px 30px rgba(28, 126, 214, 0.2)',
        ],
        [
            'bg' => '#f3f0ff',
            'text' => '#5f3dc4',
            'btn' => 'btn-outline-dark',
            'shadow' => '0 8px 25px rgba(95, 61, 196, 0.2)',
        ]
    ];

    $selectedTitleList = $titles[$status] ?? $titles['default'];
    $selectedImageList = $images[$status] ?? $images['default'];
    $theme = $themes[array_rand($themes)];

    $heading = $selectedTitleList[array_rand($selectedTitleList)];
    $image = $selectedImageList[array_rand($selectedImageList)];

@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Error - iDAB TECH</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: {{ $theme['bg'] }};
            color: {{ $theme['text'] }};
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', sans-serif;
        }

        .error-card {
            background-color: white;
            border-radius: 1rem;
            box-shadow: {{ $theme['shadow'] }};
            padding: 2rem;
            max-width: 520px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .error-image {
            max-width: 240px;
            height: auto;
            margin-bottom: 1rem;
            border-radius: 0.75rem;
        }

        .heading {
            font-weight: 600;
            font-size: 1.75rem;
        }

        .message {
            font-size: 1rem;
            margin-top: 0.75rem;
        }

        .error-code {
            font-size: 0.875rem;
            color: #999;
            margin-top: 1rem;
        }

        .back-btn {
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <img src="{{ $image }}" alt="Funny Error" class="error-image">
        <div class="heading">{{ $heading }}</div>
        <div class="message">{{ $message ?? 'Something went wrong. Please try again.' }}</div>

        @if(isset($code))
            <div class="error-code">Reference Code: <code>{{ $code }}</code></div>
        @endif

        <div class="back-btn">
            <a href="{{ url('/') }}" class="btn {{ $theme['btn'] }}">Back to Home</a>
        </div>
    </div>
</body>
</html>
