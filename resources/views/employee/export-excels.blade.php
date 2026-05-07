{{-- {{ Form::open() }} --}}
<div class="modal-body">
    <div class="row">
        <div class="col-md-12 mb-6">
            <label for="file" class="form-label" style="margin-right: 8px">Export Form A</label>
            <a href="{{route("employee.export.A")}}"
                class="btn btn-sm btn-primary ">
                <i class="ti ti-download"></i> {{ __('Download') }}
            </a>
        </div>
    </div><br>
    <div class="row">
        <div class="col-md-12 mb-6">
            <label for="file" class="form-label" style="margin-right: 8px">Export Form B</label>
            <a href="{{route("employee.export.B")}}"
                class="btn btn-sm btn-primary ">
                <i class="ti ti-download"></i> {{ __('Download') }}
            </a>
        </div>
    </div><br>
    <div class="row">
        <div class="col-md-12 mb-6">
            <label for="file" class="form-label" style="margin-right: 8px">Export Form C</label>
            <a href="{{route("employee.export.C")}}"
                class="btn btn-sm btn-primary ">
                <i class="ti ti-download"></i> {{ __('Download') }}
            </a>
        </div>
    </div><br>
    <div class="row">
        <div class="col-md-12 mb-6">
            <label for="file" class="form-label" style="margin-right: 6px">Export Form 15</label>
            <a href="{{route("employee.export")}}"
                class="btn btn-sm btn-primary ">
                <i class="ti ti-download"></i> {{ __('Download') }}
            </a>
        </div>
    </div>
</div>
{{-- {{ Form::close() }} --}}