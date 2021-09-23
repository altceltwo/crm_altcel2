@extends('layouts.octopus')
@section('content')
<header class="page-header">
    <h2>Administración de Solicitudes</h2>
    <div class="right-wrapper pull-right">
        <ol class="breadcrumbs">
            <li>
                <a href="index.html">
                    <i class="fa fa-home"></i>
                </a>
            </li>
            <li><span>Dashboard</span></li>
        </ol>
        <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
    </div>
</header>


<section class="panel">
    <header class="panel-heading">
        <div class="panel-actions">
            <a href="#" class="fa fa-caret-down"></a>
            <a href="#" class="fa fa-times"></a>
        </div>

        <h2 class="panel-title">Nuevas Solicitudes</h2>
    </header>
    <div class="panel-body">
        <table class="table table-bordered table-striped mb-none" id="datatable-default">
            <thead style="cursor: pointer;">
                <tr>
                <th scope="col">Enviado Por</th>
                <th scope="col">Status</th>
                <th scope="col">Fecha de envio</th>
                <th scope="col">Cliente</th>
                <th scope="col">producto</th>
                <th scope="col">Comentario</th>
                <th scope="col">Acción</th>
                </tr>
            </thead>
            <tbody>
                @foreach($solicitudes as $solicitud)
                <tr>
                    <td>{{$solicitud['name_sender']}}</td>
                    <td><span class="badge label-warning">{{$solicitud['status']}}</span></td>
                    <td>{{$solicitud['date_sent']}}</td>
                    <td>{{$solicitud['client']}}</td>
                    <td>{{$solicitud['product']}}</td>
                    <td>{{$solicitud['comment']}}</td>
                    <td><button class="btn btn-warning solicitud" data-id="{{$solicitud['id']}}" data-client="{{$solicitud['id_client']}}">Enviar</button></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
<script>
    $('.solicitud').click(function(){
        let idClient = $(this).attr('data-client');
        let id_petition = $(this).attr('data-id');
        let route = "{{route('activations.create')}}"
        $.ajax({
            url:"{{route('activation.get')}}",
            method:'GET',
            data:{idClient:idClient},
            success:function(response){
                let name = response[0].name;
                let lastname = response[0].lastname;
                let rfc = response[0].rfc;
                let email = response[0].email;
                let cellphone = response[0].cellphone;
                let address = response[0].address;
                let date_born = response[0].date_born;
                let ine_code = response[0].ine_code;
                let id = id_petition;
                
                let url = route+'?from=petition&name='+name+'&lastname='+lastname+'&rfc='+rfc+'&date_born='+date_born+'&address='+address+'&email='+email+'&ine_code='+ine_code+'&cellphone='+cellphone+'&petition='+id;
                // console.log(url);
                location.href=url;
            }
        })
    })
</script>
@endsection