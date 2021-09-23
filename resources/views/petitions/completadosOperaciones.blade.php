@extends('layouts.octopus')
@section('content')
<header class="page-header">
    <h2>Administración de Pagos</h2>
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

        <h2 class="panel-title">Solicitudes Completadas</h2>
    </header>
    <div class="panel-body">
        <table class="table table-bordered table-striped mb-none" id="datatable-default">
            <thead style="cursor: pointer;">
                <tr>
                <th>Cliente</th>
                <th>Producto</th>
                <th>Status</th>
                <th>Cobro cpe</th>
                <th>Cobro Plan</th>
                <th>Fecha Solicitud</th>
                <th>Activado Por</th>
                <th>Fecha Activación</th>
                <th>Recibido Por</th>
                <th>Fecha Recibido</th>
                <th>Comentario</th>
                <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                @foreach($completadas as $completado)
                <tr>
                    <td>{{$completado['client']}}</td>
                    <td>{{$completado['product']}}</td>
                    <td><span class="badge label-{{$completado['badgeStatus']}}">{{$completado['status']}}</span></td>
                    <td>{{$completado['cobroPlan']}}</td>
                    <td>{{$completado['cobroCpe']}}</td>
                    <td>{{$completado['fecha_solicitud']}}</td>
                    <td>{{$completado['activadoPor']}}</td>
                    <td>{{$completado['date_activated']}}</td>
                    <td>{{$completado['recibido']}}</td>
                    <td><span class="badge label-{{$completado['badgeFecha']}}">{{$completado['dateRecibido']}}</span></td>
                    <td>{{$completado['comment']}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
<script>

</script>

@endsection