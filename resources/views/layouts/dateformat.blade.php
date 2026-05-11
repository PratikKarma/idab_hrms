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
    (function() {
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
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: altFmt,
                allowInput: false,
                disableMobile: true,
                appendTo: document.body,
                onReady: function(selectedDates, dateStr, instance) {
                    if (instance.altInput) {
                        instance.altInput.placeholder = placeholder;
                        instance.altInput.className = el.className;
                    }
                },
                onChange: function(selectedDates, dateStr) {
                    el.value = dateStr;
                    el.dispatchEvent(new Event('change', {
                        bubbles: true
                    }));
                }
            });
        }

        function initAll(root) {
            (root || document).querySelectorAll('input[type="date"]').forEach(initOne);
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                initAll();
            });
        } else {
            initAll();
        }

        var observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                mutation.addedNodes.forEach(function(node) {
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

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });

        function displayDocumentIcon(filename, previewIcon, previewText) {

            const extension = filename.split('.').pop().toLowerCase();

            if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension)) {
                previewIcon.style.display = 'none';
                previewText.textContent = '';
                return;
            }

            let iconHTML = '';

            if (['xls', 'xlsx'].includes(extension)) {
                iconHTML = `
        <svg width="24" height="28">
            <rect x="2" y="2" width="20" height="24" rx="3" fill="#217346"/>
            <text x="50%" y="18" text-anchor="middle" font-size="10" fill="#fff">XLS</text>
        </svg>`;
            } else if (extension === 'pdf') {
                iconHTML = `
        <svg width="24" height="28">
            <rect x="2" y="2" width="20" height="24" rx="3" fill="#E74C3C"/>
            <text x="50%" y="18" text-anchor="middle" font-size="10" fill="#fff">PDF</text>
        </svg>`;
            } else if (['doc', 'docx'].includes(extension)) {
                iconHTML = `
        <svg width="24" height="28">
            <rect x="2" y="2" width="20" height="24" rx="3" fill="#2B579A"/>
            <text x="50%" y="18" text-anchor="middle" font-size="10" fill="#fff">DOC</text>
        </svg>`;
            } else if (['zip', 'rar'].includes(extension)) {
                iconHTML = `
        <svg width="24" height="28">
            <rect x="2" y="2" width="20" height="24" rx="3" fill="#F39C12"/>
            <text x="50%" y="18" text-anchor="middle" font-size="10" fill="#fff">ZIP</text>
        </svg>`;
            } else if (extension === 'txt') {
                iconHTML = `<span style="font-size:22px;">📄</span>`;
            } else {
                iconHTML = `<span style="font-size:22px;">📁</span>`;
            }

            previewIcon.innerHTML = iconHTML;
            previewIcon.style.display = 'inline-block';
            previewText.textContent = filename;
        }

        document.querySelectorAll('.document').forEach(function(input) {

            input.addEventListener('change', function(event) {

                const file = event.target.files[0];

                const container = input.closest('.form-group');

                const previewImg = container.querySelector('img');
                const previewText = container.querySelector('.documentPreview');
                const previewIcon = container.querySelector('.documentPreviewIcon');

                if (!file) {
                    previewImg.style.display = 'none';
                    previewText.textContent = '';
                    previewIcon.style.display = 'none';
                    return;
                }

                // Show file name
                previewText.textContent = file.name;

                displayDocumentIcon(file.name, previewIcon, previewText);
                // Show image preview
                if (file.type.startsWith('image/')) {
                    previewImg.src = URL.createObjectURL(file);
                    previewImg.style.display = 'block';
                } else {
                    previewImg.style.display = 'none';
                }

                // Show icon for non-image
                previewIcon.style.display = 'inline-block';

            });

        });
    })();
</script>