{{ Form::model($shift, ['route' => ['shift.update', $shift->id], 'method' => 'PUT']) }}
<div class="modal-body">

    <div class="row">

        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="form-group">
                {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}
                <div class="form-icon-user">
                    {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter Shit Name')]) }}
                </div>
                @error('name')
                    <span class="invalid-name" role="alert">
                        <strong class="text-danger">{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group">
                {{ Form::label('company_start_time', __('Company Start Time'), ['class' => 'form-label']) }}
                <div class="form-icon-user">
                    {{ Form::time('company_start_time', null, ['class' => 'form-control']) }}
                </div>
                @error('company_start_time')
                    <span class="invalid-company_start_time" role="alert">
                        <strong class="text-danger">{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group">
                {{ Form::label('company_end_time', __('Company End Time'), ['class' => 'form-label']) }}
                <div class="form-icon-user">
                    {{ Form::time('company_end_time', null, ['class' => 'form-control']) }}
                </div>
                @error('company_end_time')
                    <span class="invalid-company_end_time" role="alert">
                        <strong class="text-danger">{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

    </div>
</div>
<div class="modal-footer">
    <input type="button" value="Cancel" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Update') }}" class="btn btn-primary">
</div>
{{ Form::close() }}
