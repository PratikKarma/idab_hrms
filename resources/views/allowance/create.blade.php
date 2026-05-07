{{ Form::open(['url' => 'allowance', 'method' => 'post']) }}
{{ Form::hidden('employee_id', $employee->id, []) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group">
            {{ Form::label('allowance_option', __('Allowance Options*'), ['class' => 'col-form-label']) }}
            <a href="javascript:void(0)" data-title="{{ __('Create New Allowance Options') }}"
                onclick="modalShow([{'name' : ''}], 'create-allowance-options','Create Allowance Options','allowanceoption')"
                data-bs-toggle="tooltip" title="{{ __('Create New Allowance Options') }}" style="float: right"
                class="btn btn-sm btn-primary addBtn " data-bs-original-title="{{ __('Create') }}">
                <i class="ti ti-plus"></i>
            </a>
            {{ Form::select('allowance_option', $allowance_options, null, ['class' => 'form-control allowance_option', 'required' => 'required', 'placeholder' => 'Select Allowance Option']) }}
        </div>
        <div class="form-group">
            {{ Form::label('title', __('Title'), ['class' => 'col-form-label']) }}
            {{ Form::text('title', null, ['class' => 'form-control allowance_option', 'required' => 'required', 'placeholder' => 'Enter Title']) }}
        </div>
        <div class="form-group">
            {{ Form::label('type', __('Type'), ['class' => 'col-form-label']) }}
            {{ Form::select('type', $Allowancetypes, null, ['class' => 'form-control select2 amount_type', 'required' => 'required']) }}
        </div>
        <div class="form-group">
            {{ Form::label('amount', __('Amount'), ['class' => 'col-form-label amount_label']) }}
            {{ Form::number('amount', null, ['class' => 'form-control ', 'required' => 'required', 'step' => '0.01', 'placeholder' => 'Enter Amount', 'autocomplete' => 'off']) }}
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
    var globalAllowanceOption = '';
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

                if (value1 != '') {
                    html += '<div class="form-icon-user">\
                            <select class="form-control" name="' + key1 + '">';
                    value1 = JSON.parse(value1);
                    if (ne8wData.length != '') {
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
                    case 'allowanceoption':
                        $('.allowance_option').html(response.output);
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
