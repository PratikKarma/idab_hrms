{{ Form::open(['url' => 'pension', 'method' => 'post']) }}
{{ Form::hidden('employee_id', $employee->id, []) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-12">
            {{ Form::label('title', __('Title'), ['class' => 'col-form-label amount_label']) }}
            {{ Form::text('title', null, ['class' => 'form-control ', 'required' => 'required', 'placeholder' => 'Enter Title']) }}
        </div>
        <div class="form-group col-md-12">
            {{ Form::label('amount', __('Amount'), ['class' => 'col-form-label amount_label']) }}
            {{ Form::number('amount', null, ['class' => 'form-control ', 'required' => 'required', 'placeholder' => 'Enter Amount']) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="Cancel" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
</div>
{{ Form::close() }}
