@php
$plan = App\Models\Utility::getChatGPTSettings();
@endphp

{{ Form::open(['url' => 'document-upload', 'method' => 'post', 'enctype' => 'multipart/form-data', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">

    @if ($plan->enable_chatgpt == 'on')
    <div class="text-end">
        <a href="javascript:void(0)" class="btn btn-sm btn-primary" data-size="medium" data-ajax-popup-over="true" data-url="{{ route('generate', ['document-upload']) }}"
            data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Generate') }}"
            data-title="{{ __('Generate Content With AI') }}">
            <i class="fas fa-robot"></i>{{ __(' Generate With AI') }}
        </a>
    </div>
    @endif

    <div class="row">

        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="form-group">
                {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}<x-required></x-required>
                <div class="form-icon-user">
                    {{ Form::text('name', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter Document Name')]) }}
                </div>

            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{ Form::label('document', __('Document'), ['class' => 'form-label']) }}<x-required></x-required>
                <div class="choose-file form-group ">
                    <label for="document">
                        <input type="file" class="form-control doc_data w-100" name="documents" id="documents"
                            data-filename="documents" required>
                        <hr>
                        <img id="blah" width="100" style="display: none;" />
                        <div id="documentPreviewWrapper" class="d-flex align-items-center text-muted">
                            <span id="documentPreviewIcon" class="me-2" style="display: none;"></span>
                            <span id="documentPreview"></span>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                {{ Form::label('role', __('Role'), ['class' => 'form-label']) }}<x-required></x-required>
                <div class="form-icon-user">
                    {{ Form::select('role', $roles, null, ['class' => 'form-control', 'required' => 'required']) }}
                    <div class="text-xs mt-1">
                        {{ __('Create role.') }} <a href="{{ route('roles.index') }}"><b>{{ __('Click here') }}</b></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="form-group">
                {{ Form::label('description', __('Description'), ['class' => 'form-label']) }}
                <div class="form-icon-user">
                    {{ Form::textarea('description', null, ['class' => 'form-control', 'rows' => '3', 'placeholder' => __('Enter Description')]) }}
                </div>
            </div>
        </div>


    </div>
</div>
<div class="modal-footer">
    <input type="button" value="Cancel" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
</div>
{{ Form::close() }}
<script>
    function displayDocumentIcon(filename) {
        const extension = filename.split('.').pop().toLowerCase();
        const previewIcon = document.getElementById('documentPreviewIcon');
        const previewText = document.getElementById('documentPreview');

        if (extension === 'xls' || extension === 'xlsx') {
            previewIcon.innerHTML = `
                <svg width="24" height="28" viewBox="0 0 24 28" xmlns="http://www.w3.org/2000/svg">
                    <rect x="2" y="2" width="20" height="24" rx="3" fill="#217346" />
                    <text x="50%" y="18" text-anchor="middle" font-size="10" fill="#fff" font-family="Arial">XLS</text>
                </svg>
            `;
        } else if (extension === 'pdf') {
            previewIcon.innerHTML = `
                <svg width="24" height="28" viewBox="0 0 24 28" xmlns="http://www.w3.org/2000/svg">
                    <rect x="2" y="2" width="20" height="24" rx="3" fill="#E74C3C" />
                    <text x="50%" y="18" text-anchor="middle" font-size="10" fill="#fff" font-family="Arial">PDF</text>
                </svg>
            `;
        } else if (extension === 'doc' || extension === 'docx') {
            previewIcon.innerHTML = `
                <svg width="24" height="28" viewBox="0 0 24 28" xmlns="http://www.w3.org/2000/svg">
                    <rect x="2" y="2" width="20" height="24" rx="3" fill="#2B579A" />
                    <text x="50%" y="18" text-anchor="middle" font-size="10" fill="#fff" font-family="Arial">DOC</text>
                </svg>
            `;
        } else if (extension === 'zip' || extension === 'rar') {
            previewIcon.innerHTML = `
                <svg width="24" height="28" viewBox="0 0 24 28" xmlns="http://www.w3.org/2000/svg">
                    <rect x="2" y="2" width="20" height="24" rx="3" fill="#F39C12" />
                    <text x="50%" y="18" text-anchor="middle" font-size="10" fill="#fff" font-family="Arial">ZIP</text>
                </svg>
            `;
        } else if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension)) {
            previewIcon.innerHTML = `<span style="font-size:22px;">🖼️</span>`;
        } else if (extension === 'txt') {
            previewIcon.innerHTML = `<span style="font-size:22px;">📄</span>`;
        } else {
            previewIcon.innerHTML = `<span style="font-size:22px;">📁</span>`;
        }
        previewIcon.style.display = 'inline-block';
        previewText.textContent = filename;
    }

    document.getElementById('documents').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const previewImg = document.getElementById('blah');
        const previewText = document.getElementById('documentPreview');
        const previewIcon = document.getElementById('documentPreviewIcon');

        if (!file) {
            previewImg.style.display = 'none';
            previewText.textContent = '';
            previewIcon.style.display = 'none';
            return;
        }

        displayDocumentIcon(file.name);

        if (file.type.startsWith('image/')) {
            previewImg.src = URL.createObjectURL(file);
            previewImg.style.display = 'block';
            previewText.textContent = '';
            previewIcon.style.display = 'none';
        } else {
            previewImg.style.display = 'none';
        }
    });
</script>