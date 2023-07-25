<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create Categoties</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">

</head>

<body>
    <!-- Modal -->
    <div class="modal fade ajax-modal" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <form id="ajaxForm">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-title"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="category_id" id="category_id">
                        <div class="form-group mb-3">
                            <label for="">Name</label>
                            <input type="text" name="name" id="name" class="form-control">
                            <span id="nameError" class="text-danger error-messages"></span>
                        </div>
                        <div class="form-group mb-1">
                            <label for="">Type</label>
                            <select name="type" id="type" class="form-control">
                                <option disabled selected>Choose Option</option>
                                <option value="electronic">Electronic</option>
                            </select>
                            <span id="typeError" class="text-danger error-messages"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="saveBtn"></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="row">
        <div class="col-md-6 offset-3" style="margin-top: 100px">
            <a class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#exampleModal" id="add_category">Add Category</a>
            <table id="category-table" class="table">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Type</th>
                    <th scope="col">Action</th>
                  </tr>
                </thead>
                <tbody>

                </tbody>
              </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
     <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var table = $('#category-table').DataTable({
                processing: true,
                serverSide: true,

                ajax: "{{route('index')}}",
                columns: [
                    { data: 'id'},
                    { data: 'name'},
                    { data: 'type'},
                    { data: 'action',name: 'action',orderable: false,searchable: false},
                ]
            });
            $('#modal-title').html('Create Category');
            $('#saveBtn').html('Save Category');
            var form = $('#ajaxForm')[0];
            $('#saveBtn').click(function() {
                $('#saveBtn').html('soving...');
                $('#saveBtn').attr('disabled' ,true)
                $('.error-messages').html('');

                var formData = new FormData(form);

                $.ajax({
                    url: '{{ route('store') }}',
                    method: 'POST',
                    processData: false,
                    contentType: false,
                    data: formData,

                    success: function(response) {
                        table.draw();

                        $('#saveBtn').attr('disabled' ,false)
                        $('#saveBtn').html('save Category');
                        $('#name').val('');
                        $('#type').val('');
                        $('#category_id').val('');

                        $('.ajax-modal').modal('hide');
                        if (response) {
                            swal("Success!",response.success, "success");

                        }
                    },
                    error: function(error) {
                        $('#saveBtn').attr('disabled' ,false)
                        $('#saveBtn').html('save Category');
                        if (error) {
                            console.log(error.responseJSON.errors.name)
                            $('#nameError').html(error.responseJSON.errors.name);
                            $('#typeError').html(error.responseJSON.errors.type);
                        }
                    },
                });
            });

            //Edit Button
            $('body').on('click','.editButton',function()
            {
                var id = $(this).data('id');

                $.ajax({
                    url: '{{route("edit",'')}}' + '/' + id,
                    method: 'GET',
                    success: function(response){
                       $('.ajax-modal').modal('show');
                       $('#modal-title').html('Edit Category');
                       $('#saveBtn').html('Update Category');

                       $('#category_id').val(response.id);
                       $('#name').val(response.name);
                       var type = capitalizeFirstLetter(response.type);
                       $('#type').empty().append('<option selected value="'+response.type+'">'+ type +'</option>');
                        console.log(response.type)
                    },
                    error: function(error){
                        console.log(error)
                    }
                });
            });

            //Delete Button
            $('body').on('click', '.deleteButton',function(){
                var id = $(this).data('id');

                if(confirm('Are you sure to delete this this Item'))
                {
                        $.ajax({
                        url: '{{route("delete",'')}}' + '/' + id,
                        method: 'DELETE',
                        success: function(response){
                            table.draw();
                            swal("Success!",response.success, "success");

                        },
                        error: function(error){
                            console.log(error)
                        }
                    });
                }

            });
            $('#add_category').click(function(){
                $('#modal-title').html('Create Category');
                $('#saveBtn').html('save Category');

            });
            function capitalizeFirstLetter(string){
            return string.charAt(0).toUpperCase() + string.slice(1);
            }

            $('.ajax-modal').on('hidden.bs.modal', function () {
                $('.error-messages').html('');
             })
        });
    </script>
</body>

</html>
