@php
    $iconPath = \Illuminate\Support\Facades\DB::table('settings')->where('name', 'pwa_icon')->value('value');
    $iconUrl = $iconPath ? asset(app()->environment('local') ? "storage/{$iconPath}" : "public/storage/{$iconPath}") : asset(app()->environment('local') ? 'logo.png' : 'public/logo.png');
@endphp

<style>
    #install-prompt {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 9999;
        display: flex;
        align-items: center;
        gap: 10px;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 10px 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease-in-out;
    }

    #install-button {
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 48px;
        width: 48px;
        border-radius: 50%;
        background-color: #4f46e5;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        transition: background-color 0.3s ease;
    }

    #install-button:hover {
        background-color: #4338ca;
    }

    #install-button img {
        height: 28px;
        width: 28px;
        filter: brightness(0) invert(1);
    }

    #install-text {
        font-size: 14px;
        font-weight: 500;
        color: #111827;
    }

    @media (max-width: 640px) {
        #install-prompt {
            bottom: 10px;
            right: 10px;
            padding: 8px 12px;
        }
    }
</style>

<div id="install-prompt" class="box-icon" style="display: none;">
    <span id="install-button" class="circle">
        <img src="{{ $iconUrl }}" alt="Install App">
    </span>
</div>

<script src="{{ app()->environment('local') ? asset('sw.js') : asset('public/sw.js') }}"></script>
<script>
    "use strict";

    if ("serviceWorker" in navigator) {
        navigator.serviceWorker.register("{{ app()->environment('local') ? asset('sw.js') : asset('public/sw.js') }}")
            .then(() => {
                // console.log("Service worker registered");
            })
            .catch(error => {
                console.error("Service worker registration failed:", error);
            });
    }

    let deferredPrompt;
    function showInstallPromotion() {
        document.getElementById("install-prompt").style.display = "block";
    }

    window.addEventListener("load", () => {
        if (window.matchMedia("(display-mode: standalone)").matches) {
            document.getElementById("install-prompt").style.display = "none";
        }
    });

    window.addEventListener("beforeinstallprompt", (e) => {
        e.preventDefault();
        deferredPrompt = e;
        showInstallPromotion();
        document.getElementById("install-button").addEventListener("click", () => {
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then(() => {
                deferredPrompt = null;
            });
        });
    });

    window.addEventListener("appinstalled", () => {
        document.getElementById("install-prompt").style.display = "none";
    });
</script>
