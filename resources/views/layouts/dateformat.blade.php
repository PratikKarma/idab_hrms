@php
    $company_settings = \App\Models\Utility::settings();
@endphp
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    var laravelFormat = "{{ $company_settings['site_date_format'] }}";

    // var formatMap = {
    //     "Y-m-d": "Y-m-d",
    //     "d-m-Y": "d-m-Y",
    //     "m-d-Y": "m-d-Y",
    //     "d/m/Y": "d/m/Y",
    //     "m/d/Y": "m/d/Y",
    //     "M j, Y": "M j, Y"
    // };

    // flatpickr(".datepicker", {
    //     dateFormat: formatMap[laravelFormat] || "Y-m-d",
    //     maxDate: "today",
    //     //defaultDate: "today"
    // });
</script>
<script>
(function () {
    var altFmt = laravelFormat || 'd-m-Y';
 
    var placeholder = altFmt
        .replace(/Y/g, 'YYYY')
        .replace(/y/g, 'YY')
        .replace(/F/g, 'Month')
        .replace(/M/g, 'Mon')
        .replace(/D/g, 'Day')
        .replace(/m/g, 'MM')
        .replace(/n/g, 'M')
        .replace(/d/g, 'DD')
        .replace(/j/g, 'D');
 
   
    function initOne(el) {
        if (el._flatpickr) return;
        if (el.dataset.noFlatpickr !== undefined) return;
 
        flatpickr(el, {
            dateFormat:    'Y-m-d',  
            altInput:      true,     
            altFormat:     altFmt, 
            allowInput:    false,
            disableMobile: true,
            appendTo:      document.body,
            onReady: function (selectedDates, dateStr, instance) {
                if (instance.altInput) {
                    instance.altInput.placeholder = placeholder;
                    instance.altInput.className = el.className;
                }
            },
            onChange: function (selectedDates, dateStr) {
                el.value = dateStr;
                el.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
    }
 
    function initAll(root) {
        (root || document).querySelectorAll('input[type="date"]').forEach(initOne);
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () { initAll(); });
    } else {
        initAll();
    }
 
    var observer = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutation) {
            mutation.addedNodes.forEach(function (node) {
                if (node.nodeType !== 1) return; 
                if (node.matches && node.matches('input[type="date"]')) {
                    initOne(node);
                }
                if (node.querySelectorAll) {
                    node.querySelectorAll('input[type="date"]').forEach(initOne);
                }
            });
        });
    });
 
    observer.observe(document.body, { childList: true, subtree: true });
})();
</script>
 