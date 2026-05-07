<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Models\Languages;
use App\Models\Utility;
use Illuminate\Support\Facades\Auth;

class DetectUserLanguage
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if ($user) {
            if (!Session::has('user_lang') || !Session::has('currency_code')) {
                $geo = $this->getGeoData();

                $settings = Utility::settings();
                $defaultLang = $settings['default_language'] ?? config('app.locale');
                $currencyCode = $geo['currency_code'] ?? ($settings['site_currency'] ?? 'INR');
                $currencySymbol = $geo['currency_symbol'] ?? ($settings['site_currency_symbol'] ?? '₹');

                $langCode = Languages::where('code', $geo['lang'])->exists() ? $geo['lang'] : $defaultLang;

                if ($user) {
                    $user->lang = $langCode;
                    $user->currency_code = $currencyCode;
                    $user->currency_symbol = $currencySymbol;
                    $user->save();
                }

                App::setLocale($langCode);
                Session::put('user_lang', $langCode);
                Session::put('currency_code', $currencyCode);
                Session::put('currency_symbol', $currencySymbol);

            }
        }

        return $next($request);
    }

    private function getGeoData()
    {
        $detectors = [
            fn() => $this->fromIpapiCo(),
            fn() => $this->fromIpApiCom(),
            fn() => $this->fromIpwhois(),
        ];

        foreach ($detectors as $detect) {
            $data = $detect();
            if (!empty($data)) {
                return $data;
            }
        }

        return [
            'lang' => config('app.locale'),
            'currency_code' => 'INR',
            'currency_symbol' => '₹',
        ];
    }

    private function fromIpapiCo()
    {
        try {
            $res = Http::timeout(3)->get("https://ipapi.co/json/");
            if ($res->successful()) {
                $json = $res->json();
                $langCode = explode(',', $json['languages'] ?? 'en')[0];
                $lang = substr(strtolower($langCode), 0, 2);
                $currencyCode = $json['currency'] ?? 'INR';

                return [
                    'lang' => $lang,
                    'currency_code' => $currencyCode,
                    'currency_symbol' => $this->currencySymbol($currencyCode),
                ];
            }
        } catch (\Throwable $e) {
            logger()->warning("ipapi.co failed: " . $e->getMessage());
        }

        return null;
    }

    private function fromIpApiCom()
    {
        try {
            $res = Http::timeout(3)->get("http://ip-api.com/json/");
            if ($res->successful()) {
                $json = $res->json();
                $cc = strtoupper($json['countryCode'] ?? 'IN');

                return $this->getCountryData($cc);
            }
        } catch (\Throwable $e) {
            logger()->warning("ip-api.com failed: " . $e->getMessage());
        }

        return null;
    }

    private function fromIpwhois()
    {
        try {
            $res = Http::timeout(3)->get("https://ipwho.is/");
            if ($res->successful()) {
                $json = $res->json();
                $cc = strtoupper($json['country_code'] ?? 'IN');
                $currencyCode = $json['currency']['code'] ?? 'INR';
                $currencySymbol = $json['currency']['symbol'] ?? '₹';

                return [
                    'lang' => $this->languageFromCountry($cc),
                    'currency_code' => $currencyCode,
                    'currency_symbol' => $currencySymbol,
                ];
            }
        } catch (\Throwable $e) {
            logger()->warning("ipwho.is failed: " . $e->getMessage());
        }

        return null;
    }

    private function getCountryData($cc)
    {
        $currencyCode = $this->currencyFromCountry($cc);
        return [
            'lang' => $this->languageFromCountry($cc),
            'currency_code' => $currencyCode,
            'currency_symbol' => $this->currencySymbol($currencyCode),
        ];
    }

    private function languageFromCountry($cc)
    {
        $map = [
            'US' => 'en',
            'IN' => 'hi',
            'FR' => 'fr',
            'DE' => 'de',
            'ES' => 'es',
            'IT' => 'it',
            'RU' => 'ru',
            'CN' => 'zh',
            'JP' => 'ja',
            'KR' => 'ko',
            'AE' => 'ar',
            'BR' => 'pt',
            'TR' => 'tr',
            'MX' => 'es',
            'ZA' => 'en',
            'GB' => 'en',
            'CA' => 'en',
            'AU' => 'en',
            'PK' => 'ur',
            'BD' => 'bn',
            'NP' => 'ne',
            'LK' => 'si',
            'MY' => 'ms',
            'ID' => 'id',
        ];
        return strtolower($map[$cc] ?? 'en');
    }

    private function currencyFromCountry($cc)
    {
        $map = [
            'US' => 'USD',
            'IN' => 'INR',
            'FR' => 'EUR',
            'DE' => 'EUR',
            'ES' => 'EUR',
            'IT' => 'EUR',
            'RU' => 'RUB',
            'CN' => 'CNY',
            'JP' => 'JPY',
            'KR' => 'KRW',
            'AE' => 'AED',
            'BR' => 'BRL',
            'TR' => 'TRY',
            'MX' => 'MXN',
            'ZA' => 'ZAR',
            'GB' => 'GBP',
            'CA' => 'CAD',
            'AU' => 'AUD',
            'PK' => 'PKR',
            'BD' => 'BDT',
            'NP' => 'NPR',
            'LK' => 'LKR',
            'MY' => 'MYR',
            'ID' => 'IDR',
        ];
        return $map[$cc] ?? 'INR';
    }

    private function currencySymbol($code)
    {
        $map = [
            'USD' => '$',
            'INR' => '₹',
            'EUR' => '€',
            'RUB' => '₽',
            'CNY' => '¥',
            'JPY' => '¥',
            'KRW' => '₩',
            'AED' => 'د.إ',
            'BRL' => 'R$',
            'TRY' => '₺',
            'MXN' => '$',
            'ZAR' => 'R',
            'GBP' => '£',
            'CAD' => '$',
            'AUD' => '$',
            'PKR' => '₨',
            'BDT' => '৳',
            'NPR' => '₨',
            'LKR' => 'Rs',
            'MYR' => 'RM',
            'IDR' => 'Rp',
        ];
        return $map[$code] ?? '₹';
    }
}
