
<form method="POST" action="{{route('subdepartment-create')}}"class="simple-example">
    @csrf
    <div class="modal-body">

        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="form-group">
                    {{ Form::label('department', __('Department'), ['class' => 'form-label']) }}
                    <a href="javascript:void(0)" data-title="{{ __('Create New Department') }}" onclick="modalShow([{'branch': '{{$branch}}'}, {'name' : ''},{'slug' : ''}], 'create-department','Create Department','department')" data-bs-toggle="tooltip"
                    title="{{ __('Create New Department') }}" style="float: right" class="btn btn-sm btn-primary addBtn "
                    data-bs-original-title="{{ __('Create') }}">
                    <i class="ti ti-plus"></i>
                    </a>
                    <div class="form-icon-user">
                        {{ Form::select('department', $department, null, ['class' => 'form-control department_id','placeholder' => __('Select Department')]) }}
                    </div>
                </div>
            </div>

            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="form-group">
                    {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}
                    <div class="form-icon-user">
                        {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter SubDepartment Name')]) }}
                    </div>
                    @error('name')
                        <span class="invalid-name" role="alert">
                            <strong class="text-danger">{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

        </div>
    </div>
    {{-- <input type="hidden" name="id" value="@if(isset($subdepartment)) {{$subdepartment->id}}  @endif"> --}}
    <div class="modal-footer">
        <input type="button" value="Cancel" class="btn btn-light" data-bs-dismiss="modal">
        <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
    </div>
</form>
<div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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
    var globalDepartment = '';
    var newData = "";
    function modalShow(data, route, title,section){
        html = "";
        globalRoute = route;
        newData = "";
        $('#staticBackdropLabel').text(title);

        html += '<div class="row">';
        $.each(data, function (key, value) {
            $.each(value, function(key1, value1){
                html += '<div class="col-lg-12 col-md-12 col-sm-12">\
                        <div class="form-group">\
                            <label class="form-label">'+key1.toUpperCase()+'</label>';
                            // console.log(value1);

                if(value1 != ''){
                    html += '<div class="form-icon-user">\
                            <select class="form-control" name="'+key1+'">';
                                value1 = JSON.parse(value1);
                                if(newData.length != ''){
                                    value1 = newData;
                                }
                                $.each(value1, function(key2, value2){
                                    html += '<option value="'+key2+'">'+value2+'</option>';
                                })
                    html += '</select>\
                        </div>';
                }else{
                    html +=  '<div class="form-icon-user">\
                                <input name="'+key1+'" id="'+key1+'" placeholder="'+key1.toUpperCase()+'" class="form-control">\
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

    function submitForm(){
        var input = [];
        $("#myForm :input").each(function(){
            var value = $(this).val();
            var name = $(this).attr('name');
            input.push({
                'name' : name,
                'value' : value
            });
        });
        var url = "<?= url('') ?>/" + globalRoute;

        $.ajax({
            type: "post",
            url: url,
            data: input,
            success: function (response) {
                closeModal();
                response = JSON.parse(response);
                switch (response.section) {
                    case 'department':
                        $('.department_id').html(response.output);
                        break;
                    default:
                        break;
                }
            }
        });
    }

    function closeModal(){
        $('#staticBackdrop').modal('hide');
        html = "";
    }
</script>
