@php
    $company_settings = \App\Models\Utility::settings();
@endphp
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    var laravelFormat = "{{ $company_settings['site_date_format'] }}";

    var formatMap = {
        "Y-m-d": "Y-m-d",
        "d-m-Y": "d-m-Y",
        "m-d-Y": "m-d-Y",
        "d/m/Y": "d/m/Y",
        "m/d/Y": "m/d/Y",
        "M j, Y": "M j, Y"
    };

    flatpickr(".datepicker", {
        dateFormat: formatMap[laravelFormat] || "Y-m-d",
        maxDate: "today",
        defaultDate: "today"
    });
</script>