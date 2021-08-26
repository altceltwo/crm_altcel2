@extends('layouts.octopus')
@section('content')
<header class="page-header">
    <h2>Reporte de Clientes</h2>
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

<section class="panel">
    <header class="panel-heading">
        <div class="panel-actions">
            <a href="#" class="fa fa-caret-down"></a>
            <a href="#" class="fa fa-times"></a>
        </div>

        <h2 class="panel-title">Clientes</h2>
    </header>
    <div class="panel-body">
        <table class="table table-bordered table-striped mb-none" id="datatable-default">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Contacto</th>
                    <th>MSISDN</th>
                    <th>IMEI</th>
                    <th>Plan/Paquete</th>
                    <th>Servicio</th>
                    <th>Plan</th>
                    <th>CPE</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
            <!-- {{$clients}} -->
            @php
                $amount_total = 0;
                $amount_pendiente = 0;
            @endphp
            @foreach( $clients as $client )
                <tr class="{{$client->service}}">
                    <td>{{ $name = strtoupper($client->name.' '.$client->lastname) }}</td>
                    <td>{{ $client->cellphone }}</td>
                    <td>{{ $client->MSISDN }}</td>
                    <td>{{ substr($client->imei,4) }}</td>
                    <td>{{ $rate = strtoupper($client->rate_name) }}</td>
                    <td>{{ $service = strtoupper($client->service) }}</td>
                    <td>${{ number_format($client->amount_rate,2) }}</td>
                    <td>${{ number_format($client->amount_device,2) }}</td>
                    <td>{{ $client->date_activation }}</td>
                </tr>
            @endforeach
            @foreach( $clientsTwo as $clientTwo )
                <tr class="{{$clientTwo->service}}">
                    <td>{{ $name = strtoupper($clientTwo->name.' '.$clientTwo->lastname) }}</td>
                    <td>{{ $clientTwo->cellphone }}</td>
                    <td>{{ $clientTwo->number }}</td>
                    <td>N/A</td>
                    <td>{{ $rate = strtoupper($clientTwo->pack_name) }}</td>
                    <td>{{ $service = strtoupper($clientTwo->service) }}</td>
                    <td>${{ number_format($clientTwo->amount_pack,2) }}</td>
                    <td>${{ number_format($clientTwo->amount_install,2) }}</td>
                    <td>{{ $clientTwo->date_instalation }}</td>
                </tr>
            @endforeach
           
    </div>
</section>
@endsection