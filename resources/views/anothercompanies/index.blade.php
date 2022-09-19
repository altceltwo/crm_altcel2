@extends('layouts.octopus')
@section('content')
<header class="page-header">
    <h2>Prospectos por Contactar</h2>
    <div class="right-wrapper pull-right">
        <ol class="breadcrumbs">
            <li>
                <a href="index.html">
                    <i class="fa fa-home"></i>
                </a>
            </li>
            <li><span>Dashboard</span></li>
            <li></li>
        </ol>
    </div>
</header>
@if(session('error'))
    <div class="alert alert-danger" >
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4 class="alert-heading">Upps!!</h4>
        <p>{!!session('error')!!}</p>
    </div>
@endif
@if(session('success'))
    <div class="alert alert-success" >
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4 class="alert-heading">Well done!!</h4>
        <p>{!!session('success')!!}</p>
    </div>
@endif
<div class="row">
    <div class="col-lg-12">
        <section class="panel">
           
            <div class="panel-body">
                <form class="form-horizontal form-bordered" >

                    <div class="form-group">
                        <!-- <label class="col-md-3 control-label">Date range</label> -->
                        <div class="col-md-4">
                            <label for="type">Estados:</label>
                            <select class="form-control" data-plugin-multiselect name="state" id="state">
                                @foreach($states as $state)
                                <option value="{{$state->estado}}">{{$state->estado}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="type">Prospectos a tomar:</label>
                            <input class="form-control" type="number" min="1" value="5" id="quantity" name="quantity">
                        </div>

                        <div class="col-md-12 mt-md">
                            <button class="btn btn-success btn-sm">Obtener</button>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>
<section class="panel">
    <header class="panel-heading">
        <div class="panel-actions">
            <a href="#" class="fa fa-caret-down"></a>
            <a href="#" class="fa fa-times"></a>
        </div>

        <h2 class="panel-title">Pendientes</h2>
    </header>
    <div class="panel-body" >
        <table class="table table-bordered table-striped mb-none" id="myTable">
            <thead style="cursor: pointer;">
                <tr>
                    <th>Prospecto</th>
                    <th>Teléfono</th>
                    <th>CP</th>
                    <th>Estado</th>
                    <th>Domicilio</th>
                    <th>Fecha asginación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($prospects as $prospect)
                <tr>
                    <td>{{$prospect->paterno.' '.$prospect->materno.' '.$prospect->nombres}}</td>
                    <td>{{$prospect->telefono}}</td>
                    <td>{{$prospect->cp}}</td>
                    <td>{{$prospect->estado}}</td>
                    <td>{{$prospect->domicilio}}</td>
                    <td>{{$prospect->taken_at}}</td>
                    <td>
                        <button class="btn btn-success btn-sm attend" data-name="{{$prospect->nombres}}" data-apaterno="{{$prospect->paterno}}" data-amaterno="{{$prospect->materno}}"
                        data-telefono="{{$prospect->telefono}}" data-estado="{{$prospect->estado}}" data-idprospect="{{$prospect->id}}" data-address="C.P. {{$prospect->cp}}, {{$prospect->domicilio}}"><i class="fa fa-check"></i></button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
    </div>
</section>

<section class="panel">
    <header class="panel-heading">
        <div class="panel-actions">
            <a href="#" class="fa fa-caret-down"></a>
            <a href="#" class="fa fa-times"></a>
        </div>

        <h2 class="panel-title">Contactados</h2>
    </header>
    <div class="panel-body" >
        <table class="table table-bordered table-striped mb-none" id="myTable2">
            <thead style="cursor: pointer;">
                <tr>
                    <th>Nombre</th>
                    <th>Status</th>
                    <th>Estado</th>
                    <th>Interesado</th>
                    <th>Comentarios</th>
                    <th>Fecha contactado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($contacteds as $contacted)
                <tr>
                    <td>{{$contacted->paterno.' '.$contacted->materno.' '.$contacted->nombres}}</td>
                    <td><span class="badge label-success">{{$contacted->status}}</span></td>
                    <td>{{$contacted->estado}}</td>
                    <td>{{$clientBool = $contacted->hunted == 1 ? "Sí" : "No"}}</td>
                    <td>{{$contacted->comments}}</td>
                    <td>{{$contacted->updated_at}}</td>
                    <td>
                    <a href="http://10.44.0.71/shipping/create?name={{$contacted->paterno.' '.$contacted->materno.' '.$contacted->nombres}}&phone={{$contacted->telefono}}" target="_blank" class="btn btn-success btn-sm"><i class="fa fa-check"></i></a>
                    </td>
                </tr>
                @endforeach

                @foreach($declineds as $declined)
                <tr>
                    <td>{{$declined->paterno.' '.$declined->materno.' '.$declined->nombres}}</td>
                    <td><span class="badge label-danger">{{$declined->status}}</span></td>
                    <td>{{$declined->estado}}</td>
                    <td>{{$clientBool = $declined->hunted == 1 ? "Sí" : "No"}}</td>
                    <td>{{$declined->comments}}</td>
                    <td>{{$declined->updated_at}}</td>
                    <td>
                    
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
    </div>
</section>

<section class="panel">
    <header class="panel-heading">
        <div class="panel-actions">
            <a href="#" class="fa fa-caret-down"></a>
            <a href="#" class="fa fa-times"></a>
        </div>

        <h2 class="panel-title">Completados</h2>
    </header>
    <div class="panel-body" >
        <table class="table table-bordered table-striped mb-none" id="myTable3">
            <thead style="cursor: pointer;">
                <tr>
                    <th>Nombre</th>
                    <th>Status</th>
                    <th>Estado</th>
                    <th>Fecha completado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($completeds as $completed)
                <tr>
                    <td>{{$completed->paterno.' '.$completed->materno.' '.$completed->nombres}}</td>
                    <td>{{$completed->status}}</td>
                    <td>{{$completed->estado}}</td>
                    <td>{{$completed->updated_at}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
    </div>
</section>

<div class="modal fade" id="modalDetails" tabindex="-1" aria-labelledby="modalDetailsLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetailsLabel">Detalles del Contacto</h5>
            </div>
            <div class="modal-body col-md-12">
                <div class="col-md-4 mb-md mt-md">
                    <label>Nombre: </label>
                    <strong id="nameP"></strong>
                </div>
                <div class="col-md-4 mb-md mt-md">
                    <label>Teléfono: </label>
                    <strong id="phoneP"></strong>
                </div>
                <div class="col-md-4 mb-md mt-md">
                    <label>Estado: </label>
                    <strong id="stateP"></strong>
                </div>
                <div class="col-md-6 mb-md">
                    <label for="comment">Comentarios:</label>
                    <textarea class="form-control"  id="comment" name="comment" rows="5"></textarea>
                </div>
                <div class="col-md-6 mt-xs mb-md">
                    <div class="checkbox">
                        <label class="control-label">
                            <input type="checkbox" id="client_flag">
                            Cliente
                        </label>
                    </div>
                </div>
                
                <form class="col-md-12 d-none" id="formClient" action="{{route('clients.storeFromAnotherCompany')}}" method="POST">
                    @csrf
                    <div class="form-group col-md-6">
                        <!-- <label class="control-label col-md-6">Vertical Group w/ icon</label> -->
                        <div class="col-md-12">
                            <section class="form-group-vertical">
                                <div class="input-group input-group-icon">
                                    <span class="input-group-addon">
                                        <span class="icon"><i class="fa fa-user"></i></span>
                                    </span>
                                    <input class="form-control" type="text" placeholder="Nombre" id="name" name="name" required>
                                </div>

                                <div class="input-group input-group-icon">
                                    <span class="input-group-addon">
                                        <span class="icon"><i class="fa fa-user"></i></span>
                                    </span>
                                    <input class="form-control" type="text" placeholder="Apellido" id="lastname" name="lastname" required>
                                </div>
                            </section>
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <!-- <label class="control-label col-md-6">Vertical Group w/ icon</label> -->
                        <div class="col-md-12">
                            <section class="form-group-vertical">
                                <div class="input-group input-group-icon">
                                    <span class="input-group-addon">
                                        <span class="icon"><i class="fa fa-home"></i></span>
                                    </span>
                                    <input class="form-control" type="text" placeholder="Dirección" id="address" name="address" required>
                                </div>

                                <div class="input-group input-group-icon">
                                    <span class="input-group-addon">
                                        <span class="icon"><i class="fa fa-phone"></i></span>
                                    </span>
                                    <input class="form-control" type="text" placeholder="Teléfono Contacto" id="cellphone" name="celphone" maxlength="10" required>
                                </div>
                            </section>
                        </div>
                    </div>
                    <div class="col-md-4 mt-xs mb-md">
                        <label for="interests">Producto de Interés</label>
                        <select id="interests" name="interests" class="form-control form-control-sm" required="">
                            <option value="MOV">MOV</option>
                            <option value="MIFI">MIFI</option>
                            <option value="HBB">HBB</option>
                        </select>
                    </div>
                    <input type="hidden" name="prospect_id" id="prospect_id">
                    <input type="hidden" name="user" id="user" value="{{Auth::user()->id}}">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" id="declineProspect">Rechazado</button>
                <button type="button" class="btn btn-success" id="saveProspect">Guardar</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
<script >
    $(document).ready( function () {
        $('#myTable').DataTable({
            dom: 'Bfrtip',
            buttons: [{
                extend: 'excel',
                header: true,
                title: 'PersonasPorContactar',
                exportOptions : {
                    columns: [ 0, 1, 2, 3, 4 ],
                }
            }],
        });

        $('#myTable2').DataTable({
            dom: 'Bfrtip',
            buttons: [{
                extend: 'excel',
                header: true,
                title: 'PersonasContactadas',
                exportOptions : {
                    columns: [ 0, 1, 2, 3, 4 ],
                }
            }],
        });

        $('#myTable3').DataTable({
            dom: 'Bfrtip',
            buttons: [{
                extend: 'excel',
                header: true,
                title: 'ClientesNuevos',
                exportOptions : {
                    columns: [ 0, 1, 2 ],
                }
            }],
        });
    });

    $('.attend').click(function(){
        let name = $(this).data('name');
        let paterno = $(this).data('apaterno');
        let materno = $(this).data('amaterno');
        let phone = $(this).data('telefono');
        let state = $(this).data('estado');
        let address = $(this).data('address');
        let id = $(this).data('idprospect');

        $('#nameP').html(paterno+' '+materno+' '+name);
        $('#phoneP').html(phone);
        $('#stateP').html(state);
        $('#name').val(name);
        $('#lastname').val(paterno+' '+materno);
        $('#cellphone').val(phone);
        $('#address').val(address);
        $('#prospect_id').val(id);

        $("#client_flag").prop('checked', false);
        $("#declineProspect").prop('disabled', false);
        $('#modalDetails').modal('show');
    });

    $('#client_flag').click(function(){
        if($(this).is(':checked')){
            $('#formClient').removeClass('d-none');
            $("#declineProspect").prop('disabled', true);
            $("#saveProspect").prop('disabled', false);
        }else{
            $('#formClient').addClass('d-none');
            $("#declineProspect").prop('disabled', false);
            $("#saveProspect").prop('disabled', true);
        }
    });

    $('#saveProspect').click(function(){
        
        if($('#client_flag').is(':checked')){
            $('#formClient').submit();
        }else{
        }
    });

    $('#declineProspect').click(function(){
        let id = $('#prospect_id').val();
        let comment = $('#comment').val();
        let user_id = $('#user').val();

        if(comment.length == 0 || /^\s+$/.test(comment)){
            Swal.fire({
                icon: 'error',
                title: 'Woops!!',
                text:'Debe ingresar un comentario describiendo la razón por la que se rechaza al prospecto.'
            });
            return false;
        }

        Swal.fire({
            title: 'ATENCIÓN',
            html: "¿Está seguro(a) de marcar como rechazado al prospecto?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'SÍ, ESTOY SEGURO',
            cancelButtonText: 'Cancelar',
            customClass: {
                confirmButton: 'btn btn-primary mr-md',
                cancelButton: 'btn btn-danger '
            },
            buttonsStyling: false,
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{route('declineProspect')}}",
                    method: 'POST',
                    data: {_token:'{{csrf_token()}}', id, comment, user_id},
                    beforeSend: function(){

                    },
                    success: function(response){
                        if(response == 1){
                            location.reload();
                        }
                    }
                })
                
            } else if (
                /* Read more about handling dismissals below */
                result.dismiss === Swal.DismissReason.cancel
            ) {
                Swal.fire({
                    icon: 'error',
                    title: 'Operación cancelada',
                    text: 'No se registro ningún pago.',
                    showConfirmButton: false,
                    timer: 1000
                })
            }
        })
    });
</script>
@endsection