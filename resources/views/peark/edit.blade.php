{{ Form::model($peark, ['route' => ['peark.update', $peark->id], 'method' => 'PUT']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group">
            {{ Form::label('title', __('Title'), ['class' => 'col-form-label']) }}
            {{ Form::text('title', null, ['class' => 'form-control ', 'required' => 'required', 'placeholder' => 'Enter Title']) }}
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('amount', __('Amount'), ['class' => 'col-form-label amount_label']) }}
                {{ Form::number('amount', null, ['class' => 'form-control ', 'required' => 'required', 'step' => '0.01']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('peark_coupon', __('Peark Coupon'), ['class' => 'col-form-label amount_label']) }}
                {{ Form::number('peark_coupon', null, ['class' => 'form-control ', 'required' => 'required',]) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="Cancel" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Update') }}" class="btn btn-primary">
</div>

{{ Form::close() }}
