@php
    $plan = App\Models\Utility::getChatGPTSettings();
@endphp

{{ Form::open(['url' => 'loan', 'method' => 'post', 'class' => 'needs-validation', 'novalidate']) }}
{{ Form::hidden('employee_id', $employee->id, []) }}
<div class="modal-body">

    @if ($plan->enable_chatgpt == 'on')
        <div class="card-footer text-end">
            <a href="javascript:void(0)" class="btn btn-sm btn-primary" data-size="medium" data-ajax-popup-over="true"
                data-url="{{ route('generate', ['loan']) }}" data-bs-toggle="tooltip" data-bs-placement="top"
                title="{{ __('Generate') }}" data-title="{{ __('Generate Content With AI') }}">
                <i class="fas fa-robot"></i>{{ __(' Generate With AI') }}
            </a>
        </div>
    @endif

    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('title', __('Title'), ['class' => 'col-form-label']) }}<x-required></x-required>
            {{ Form::text('title', null, ['class' => 'form-control ', 'required' => 'required', 'placeholder' => __('Enter Title')]) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('loan_option', __('Loan Options'), ['class' => 'col-form-label']) }}<x-required></x-required>
            <a href="javascript:void(0)" data-title="{{ __('Create New Loan Options') }}"
                onclick="modalShow([{'name' : ''}], 'create-loan-options', 'Create Loan Options','loan-options')"
                data-bs-toggle="tooltip" title="{{ __('Create New Loan Options') }}" style="float: right"
                class="btn btn-sm btn-primary addBtn" data-bs-original-title="{{ __('Create') }}">
                <i class="ti ti-plus"></i>
            </a>
            {{ Form::select('loan_option', $loan_options, null, ['class' => 'form-control loan_option', 'required' => 'required', 'placeholder' => __('Select Loan Option')]) }}
            {{-- <div class="text-xs mt-1">
                {{ __('Create loan options.') }} <a href="{{ route('loanoption.index') }}"><b>{{ __('Click here') }}</b></a>
            </div> --}}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('type', __('Type'), ['class' => 'col-form-label']) }}<x-required></x-required>
            {{ Form::select('type', $loan, null, ['class' => 'form-control amount_type', 'required' => 'required', 'placeholder' => __('Select Type')]) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('amount', __('Loan Amount'), ['class' => 'col-form-label amount_label']) }}<x-required></x-required>
            {{ Form::number('amount', null, ['class' => 'form-control ', 'required' => 'required', 'step' => '0.01', 'placeholder' => __('Enter Amount')]) }}
        </div>
        <div class="form-group">
            {{ Form::label('reason', __('Reason'), ['class' => 'col-form-label']) }}<x-required></x-required>
            {{ Form::textarea('reason', null, ['class' => 'form-control', 'rows' => 3, 'required' => 'required']) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="button" value="Cancel" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
</div>
{{ Form::close() }}

<div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel"></h5>
                {{-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button> --}}
            </div>
            <form id="myForm">
                <div class="modal-body" id="newModalBody">

                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Close</button>
                <button type="button" class="btn btn-primary" onclick="submitForm()">Submit</button>
            </div>
        </div>
    </div>
</div>
<script>
    var globalRoute = '';
    var html = "";
    var newData = '';

    function modalShow(data, route, title, section) {
        globalRoute = route;
        html = '';
        newData = '';
        $('#staticBackdropLabel').text(title);

        html += '<div class="row">';
        $.each(data, function(key, value) {
            $.each(value, function(key1, value1) {
                html += '<div class="col-lg-12 col-md-12 col-sm-12">\
                        <div class="form-group">\
                            <label class="form-label">' + key1.toUpperCase() + '</label>';
                // console.log(value1);

                if (value1 != '') {
                    html += '<div class="form-icon-user">\
                            <select class="form-control" name="' + key1 + '">';
                    value1 = JSON.parse(value1);
                    if (newData.length != '') {
                        value1 = newData;
                    }
                    $.each(value1, function(key2, value2) {
                        html += '<option value="' + key2 + '">' + value2 + '</option>';
                    })
                    html += '</select>\
                        </div>';
                } else {
                    html += '<div class="form-icon-user">\
                                <input name="' + key1 + '" id="' + key1 + '" placeholder="' + key1.toUpperCase() + '" class="form-control">\
                            </div>';
                }

                html += '   </div>\
                        </div>';
            })
        });
        html += '</div>';
        $('#newModalBody').html(html);
        $('#staticBackdrop').modal('show');
    }

    function submitForm() {
        var input = [];
        $("#myForm :input").each(function() {
            var value = $(this).val();
            var name = $(this).attr('name');
            input.push({
                'name': name,
                'value': value
            });
        });
        var url = "<?= url('') ?>/" + globalRoute;

        $.ajax({
            type: "post",
            url: url,
            data: input,
            success: function(response) {
                closeModal();
                response = JSON.parse(response);
                switch (response.section) {
                    case 'loan-options':
                        $('.loan_option').html(response.output);
                        break;
                    default:
                        break;
                }
            }
        });
    }

    function closeModal() {
        $('#staticBackdrop').modal('hide');
        html = "";
    }
</script>
