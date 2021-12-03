@extends('layouts.octopus')
@section('content')
<header class="page-header">
    <h2>Activaciones Batch</h2>
    <div class="right-wrapper pull-right">
        <ol class="breadcrumbs">
            <li>
                <a href="{{route('home')}}">
                    <i class="fa fa-home"></i>
                </a>
            </li>
            <li><span>Dashboard</span></li>
        </ol>
        <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
    </div>
</header>
@if(session('message'))
    <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <strong>¡Éxito!</strong> {{session('message')}}
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <strong>Upps!!</strong> {{session('error')}} <br>
        <a href="#create" class="alert-link">Intentar de nuevo.</a>
    </div>
@endif
<div class="col-md-6">
    <section class="panel">
        <header class="panel-heading">
            <div class="panel-actions">
                <a href="#" class="fa fa-caret-down"></a>
            </div>

            <h2 class="panel-title">MSISDN's Disponibles</h2>
        </header>
        <div class="panel-body">
            <table class="table table-bordered table-striped mb-none" id="datatable-default">
                <thead >
                    <tr>
                    <th scope="col">ICC</th>
                    <th scope="col">MSISDN</th>
                    <th scope="col">Producto</th>
                    <th scope="col">Opciones</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($numbers as $number )
                <tr style="cursor: pointer;">
                    <td>{{ $number->icc_id }}</td>
                    <td>{{ $number->MSISDN }}</td>
                    <td>{{ $number->producto }}</td>
                    <td class="actions-hover">
                        <a>
                            <button class="btn btn-success btn-xs available" data-icc="{{$number->icc_id}}" data-msisdn="{{$number->MSISDN}}" data-producto="{{$number->producto}}">
                                <i class="fa fa-arrow-circle-right"></i>
                            </button>
                        </a>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </section>
</div>

<div class="col-md-6">
    <section class="panel">
        <header class="panel-heading">
            <div class="panel-actions">
                <a href="#" class="fa fa-caret-down"></a>
            </div>

            <h2 class="panel-title">MSISDN's Elegidos</h2>
        </header>
        <div class="panel-body">
            <table class="table table-bordered table-striped mb-none" id="datatable-default2">
                <thead >
                    <tr>
                    <th scope="col">ICC</th>
                    <th scope="col">MSISDN</th>
                    <th scope="col">Producto</th>
                    <th scope="col">Plan</th>
                    <th scope="col">Coordenadas</th>
                    <th scope="col">Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($temporaries as $temporarie)
                    <tr>
                        <td>{{$temporarie->ICC}}</td>
                        <td>{{$temporarie->MSISDN}}</td>
                        <td>{{$temporarie->Producto}}</td>
                        <td>{{$temporarie->Rate}}</td>
                        <td>{{$temporarie->Coordinates}}</td>
                        <td>
                            <button class='btn btn-danger btn-xs' data-icc='{{$temporarie->ICC}}' onclick="deleteICC(this)"><i class='fa fa-times'></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</div>

<div class="modal fade" id="coordinatesModal" tabindex="-1" aria-labelledby="coordinatesModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="referenceLabel">Datos de HBB</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            
                <div class="row">
                    <div class="col-md-12 mb-md" >
                        <label>Clientes</label>
                        <select data-plugin-selectTwo class="form-control populate" id="client" >
                            <optgroup label="Clientes">
                            <option value="0">Elige...</option>
                            @foreach($clients as $client)
                            <option value="{{$client->id}}">
                                {{$client->name.' '.$client->lastname.' - '.$client->email}}
                            </option>
                            @endforeach
                            </optgroup>
                            
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group has-success">
                            <label class="control-label">ICC</label>
                            <input type="text" name="icc" id="icc" class="form-control" disabled readonly>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group has-success">
                            <label class="control-label">MSISDN</label>
                            <input type="text" name="msisdn" id="msisdn" class="form-control" disabled readonly>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group has-success">
                            <label class="control-label">PRODUCTO</label>
                            <input type="email" name="producto" id="producto" class="form-control" disabled readonly>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="control-label">PLANES</label>
                            <select name="rates" id="rates" class="form-control" required=""></select>
                        </div>
                    </div>
                    <div class="col-sm-6" id="contentCoordinates">
                        <div class="form-group">
                            <label class="control-label">COORDENADAS</label>
                            <input type="text" name="coordinates" id="coordinates" class="form-control">
                        </div>
                    </div>
                </div>
           
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" id="addSIM">Añadir</button>
        </div>
        </div>
    </div>
</div>

<script>
    var datas;

    $('.available').click(function(){
        let msisdn = $(this).data('msisdn');
        let icc = $(this).data('icc');
        let producto = $(this).data('producto');
        producto = producto.trim();
        let token = $('meta[name="csrf-token"]').attr('content');
        let options = "<option selected value='0'>Elegir...</option>";

        $.ajax({
            url: "{{route('get-rates-alta.post')}}",
            method: "POST",
            data: {_token:token,product:producto},
            success: function(response){
                console.log(response);
                response.forEach(function(e){
                    options+="<option value='"+e.id+"' data-offerid='"+e.offerID+"'>"+e.name+" - $"+parseFloat(e.price).toFixed(2)+"</option>"
                });
                
                $('#icc').val(icc);
                $('#msisdn').val(msisdn);
                $('#producto').val(producto);
                $('#rates').html(options);
                $('#coordinatesModal').modal('show');

                if(producto == 'HBB'){
                    $('#contentCoordinates').removeClass('d-none');
                    $('#coordinates').val('');
                }else{
                    $('#contentCoordinates').addClass('d-none');
                    $('#coordinates').val('N/A');
                }
            }
        });
    });

    $('#addSIM').click(function(){
        let icc = $('#icc').val();
        let msisdn = $('#msisdn').val();
        let producto = $('#producto').val();
        let coordinates = $('#coordinates').val();
        let rate = $('#rates').val();
        let offerID = $('#rates option:selected').attr('data-offerid');
        let client = $('#client').val();

        if(client == 0){
            Swal.fire({ 
                icon: 'error',
                title: 'Woops!!',
                text: 'Debe elegir un cliente.',
                showConfirmButton: false,
                timer: 2000
            });
            return false;
        }

        if(rate == 0){
            Swal.fire({ 
                icon: 'error',
                title: 'Woops!!',
                text: 'Debe elegir un plan.',
                showConfirmButton: false,
                timer: 2000
            });
            return false;
        }

        if(coordinates.length == 0 || /^\s+$/.test(coordinates)){
            Swal.fire({ 
                icon: 'error',
                title: 'Woops!!',
                text: 'Debe ingresar coordenadas.',
                showConfirmButton: false,
                timer: 2000
            });
            return false;
        }

        $.ajax({
            url: "{{route('addDeleteNumberBulk')}}",
            data: {ICC:icc,MSISDN:msisdn,Producto:producto,Coordinates:coordinates,rate_id:rate,offerID:offerID,client_id:client,type:'add'},
            success: function(response){
                console.log(response);
            
                var datatableInit = function() {

                    var $table = $('#datatable-default2').dataTable();
                    $table.dataTable({
                        destroy: true,
                        data: response,
                        columns: [
                            {title:"ICC", data:"ICC"},
                            {title:"MSISDN", data:"MSISDN"},
                            {title:"Producto", data:"Producto"},
                            {title:"Plan", data:"Rate"},
                            {title:"Coordenadas", data:"Coordinates"},
                            {title:"Opciones", data:"Opcion"}
                        ]
                    });

                };

                $(function() {
                    datatableInit();
                });
                $('#coordinatesModal').modal('hide');
            }
        });
    });

    function deleteICC(e){
        let icc = e.getAttribute('data-icc');
        $.ajax({
            url: "{{route('addDeleteNumberBulk')}}",
            data: {ICC:icc,type:'delete'},
            success: function(response){
                console.log(response);
            
                var datatableInit = function() {

                    var $table = $('#datatable-default2').dataTable();
                    $table.dataTable({
                        destroy: true,
                        data: response,
                        columns: [
                            {title:"ICC", data:"ICC"},
                            {title:"MSISDN", data:"MSISDN"},
                            {title:"Producto", data:"Producto"},
                            {title:"Plan", data:"Rate"},
                            {title:"Coordenadas", data:"Coordinates"},
                            {title:"Opciones", data:"Opcion"}
                        ]
                    });

                };

                $(function() {
                    datatableInit();
                });
            }
        });
    }
</script>
@endsection