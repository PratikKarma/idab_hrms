@php
    $plan = App\Models\Utility::getChatGPTSettings();
@endphp

{{ Form::open(['url' => 'training', 'method' => 'post', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">

    @if ($plan->enable_chatgpt == 'on')
    <div class="card-footer text-end">
        <a href="javascript:void(0)" class="btn btn-sm btn-primary" data-size="medium" data-ajax-popup-over="true"
            data-url="{{ route('generate', ['training']) }}" data-bs-toggle="tooltip" data-bs-placement="top"
            title="{{ __('Generate') }}" data-title="{{ __('Generate Content With AI') }}">
            <i class="fas fa-robot"></i>{{ __(' Generate With AI') }}
        </a>
    </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('branch', __('Branch'), ['class' => 'col-form-label']) }}<x-required></x-required>
                {{ Form::select('branch', $branches, null, ['class' => 'form-control select2', 'required' => 'required', 'id' => 'branch']) }}
                <div class="text-xs mt-1">
                    {{ __('Create branch.') }} <a href="{{ route('branch.index') }}"><b>{{ __('Click here') }}</b></a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('trainer_option', __('Trainer Option'), ['class' => 'col-form-label']) }}<x-required></x-required>
                {{ Form::select('trainer_option', $options, null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Select Trainer Option')]) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('training_type', __('Training Type'), ['class' => 'col-form-label']) }}<x-required></x-required>
                {{ Form::select('training_type', $trainingTypes, null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Select Training Type')]) }}
                <div class="text-xs mt-1">
                    {{ __('Create training type.') }} <a href="{{ route('trainingtype.index') }}"><b>{{ __('Click here') }}</b></a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('trainer', __('Trainer'), ['class' => 'col-form-label']) }}<x-required></x-required>
                {{ Form::select('trainer', $trainers, null, ['class' => 'form-control select2', 'required' => 'required']) }}
                <div class="text-xs mt-1">
                    {{ __('Create trainer.') }} <a href="{{ route('trainer.index') }}"><b>{{ __('Click here') }}</b></a>
                </div>

            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('training_cost', __('Training Cost'), ['class' => 'col-form-label']) }}<x-required></x-required>
                {{ Form::number('training_cost', null, ['class' => 'form-control', 'step' => '0.01', 'required' => 'required', 'placeholder' => __('Enter Training Cost')]) }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('employee', __('Employee'), ['class' => 'form-label']) }}<x-required></x-required>
                <div class="employee_div">
                    <select name="employee" id="employee" class="form-control " required>
                    </select>
                    <div class="text-xs mt-1">
                    {{ __('Create employee.') }} <a href="{{ route('employee.index') }}"><b>{{ __('Click here') }}</b></a>
                    </div>
                </div>
            </div>

        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('start_date', __('Start Date'), ['class' => 'col-form-label']) }}
                {{ Form::text('start_date', null, ['class' => 'form-control w-100', 'autocomplete' => 'off']) }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('end_date', __('End Date'), ['class' => 'col-form-label']) }}
                {{ Form::text('end_date', null, ['class' => 'form-control w-100', 'autocomplete' => 'off']) }}
            </div>
        </div>
        <div class="form-group col-lg-12">
            {{ Form::label('description', __('Description'), ['class' => 'col-form-label']) }}
            {{ Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => __('Description'), 'rows' => '3']) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="Cancel" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
</div>
{{ Form::close() }}

<script>
    $(document).ready(function() {
        var now = new Date();
        var month = (now.getMonth() + 1);
        var day = now.getDate();
        if (month < 10) month = "0" + month;
        if (day < 10) day = "0" + day;
        var today = now.getFullYear() + '-' + month + '-' + day;
        $('.current_date').val(today);
    });
</script>

<script>
    $('#branch').on('change', function() {
        var branch_id = this.value;

        $.ajax({
            url: "{{ route('getemployee') }}",
            type: "post",
            data: {
                "branch_id": branch_id,
                "_token": "{{ csrf_token() }}",
            },

            cache: false,
            success: function(data) {

                $('#employee').html('<option value="">{{ __('Select Employee') }}</option>');
                $.each(data.employee, function(key, value) {
                    $("#employee").append('<option value="' + value.id + '">' + value.name +
                        '</option>');
                });

            }
        })
    });
</script>
