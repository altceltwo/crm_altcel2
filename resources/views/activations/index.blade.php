@extends('layouts.octopus')
@section('content')
<header class="page-header">
    <h2>Instalaciones/Activaciones</h2>
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
<div class="alert alert-primary">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    <strong>Mostrando registros en el rango de {{$date_init}} a {{$date_final}}</strong>
</div>

<div class="row">
    <div class="col-lg-12">
        <section class="panel">
           
            <div class="panel-body">
                <form class="form-horizontal form-bordered" action="{{route('activations.index')}}">

                    <div class="form-group">
                        <!-- <label class="col-md-3 control-label">Date range</label> -->
                        <div class="col-md-6">
                            <div class="input-daterange input-group" data-plugin-datepicker>
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </span>
                                <input autocomplete="off" type="text" class="form-control" name="start">
                                <span class="input-group-addon">a</span>
                                <input autocomplete="off" type="text" class="form-control" name="end">
                            </div>
                        </div>

                        <div class="col-md-12 mt-md">
                            <button class="btn btn-success btn-sm">Consultar</button>
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

        <h2 class="panel-title">Reporte del Mes</h2>
    </header>
    <div class="panel-body">
        <table class="table table-bordered table-striped mb-none" id="datatable-default">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>MSISDN</th>
                    <th>Plan/Paquete</th>
                    <th>Servicio</th>
                    <th>Activación</th>
                    <th>CPE</th>
                    <th>Mensualidad</th>
                    <th>Fecha</th>
                   
                </tr>
            </thead>
            <tbody>
            <!-- {{$clients}} -->
            @php
                $amount_total = 0;
                $amount_pendiente = 0;
            @endphp
            <!-- Activaciones con y sin pagos mensuales -->
            @foreach( $clients as $client )
                <tr class="get-data-payment {{$client->pack_service}}" style="cursor: pointer;"  data-id="{{$client->id}}" data-type="activation">
                    <td>{{ $name = strtoupper($client->name.' '.$client->lastname) }}</td>
                    <td>{{ $client->MSISDN }}</td>
                    <td>{{ $rate = strtoupper($client->name_rate) }}</td>
                    <td>{{ $service = strtoupper($client->pack_service) }}</td>
                    <!-- Información de TARIFA -->
                    <td>${{ number_format($client->amount_rate,2) }}
                    @php
                        $span_text = $client->received_amount_rate < $client->amount_rate ? 'PENDIENTE' : 'OK';
                        $span_class = $client->received_amount_rate < $client->amount_rate ? 'danger' : 'success';
                        $restante = $client->received_amount_rate < $client->amount_rate ? $client->amount_rate-$client->received_amount_rate : 0;
                        $restante = '$'.number_format($restante,2);
                        $span_icon = $client->received_amount_rate < $client->amount_rate ? $restante : '<li class="fa fa-check"></li>';
                    @endphp
                    <span class="label label-{{$span_class}} col-md-12" >{{ $span_text }} {!! $span_icon !!}</span>
                    </td>
                    <!-- Información de CPE -->
                    <td>${{ number_format($client->amount_device,2) }}
                    @php
                        $span_text = $client->received_amount_device < $client->amount_device ? 'PENDIENTE' : 'OK';
                        $span_class = $client->received_amount_device < $client->amount_device ? 'danger' : 'success';
                        $restante = $client->received_amount_device < $client->amount_device ? $client->amount_device-$client->received_amount_device : 0;
                        $restante = '$'.number_format($restante,2);
                        $span_icon = $client->received_amount_device < $client->amount_device ? $restante : '<li class="fa fa-check"></li>';
                    @endphp
                    <span class="label label-{{$span_class}} col-md-12" >{{ $span_text }} {!! $span_icon !!}</span>
                    </td>
                    <td>${{number_format($client->payment_amount)}}
                    @php
                        $span_text = $client->payment_status == 'pendiente' ? 'PENDIENTE' : 'OK';
                        $span_class = $client->payment_status == 'pendiente' ? 'warning' : 'success';
                        $span_icon = $client->payment_status == 'pendiente' ? ' ' : '<li class="fa fa-check"></li>';
                    @endphp
                    <span class="label label-{{$span_class}} col-md-12" >{{ $span_text }} {!! $span_icon !!}</span>
                    </td>
                    <td>{{ $client->payment_date }}</td>
                    
                </tr>
                
            @endforeach
            @foreach( $clients2 as $client )
                <tr class="get-data-payment {{$client->pack_service}}" style="cursor: pointer;"  data-id="{{$client->id}}" data-type="activation">
                    <td>{{ $name = strtoupper($client->name.' '.$client->lastname) }}</td>
                    <td>{{ $client->MSISDN }}</td>
                    <td>{{ $rate = strtoupper($client->name_rate) }}</td>
                    <td>{{ $service = strtoupper($client->pack_service) }}</td>
                    <!-- Información de TARIFA -->
                    <td>${{ number_format($client->amount_rate,2) }}
                    @php
                        $span_text = $client->received_amount_rate < $client->amount_rate ? 'PENDIENTE' : 'OK';
                        $span_class = $client->received_amount_rate < $client->amount_rate ? 'danger' : 'success';
                        $restante = $client->received_amount_rate < $client->amount_rate ? $client->amount_rate-$client->received_amount_rate : 0;
                        $restante = '$'.number_format($restante,2);
                        $span_icon = $client->received_amount_rate < $client->amount_rate ? $restante : '<li class="fa fa-check"></li>';
                    @endphp
                    <span class="label label-{{$span_class}} col-md-12" >{{ $span_text }} {!! $span_icon !!}</span>
                    </td>
                    <!-- Información de CPE -->
                    <td>${{ number_format($client->amount_device,2) }}
                    @php
                        $span_text = $client->received_amount_device < $client->amount_device ? 'PENDIENTE' : 'OK';
                        $span_class = $client->received_amount_device < $client->amount_device ? 'danger' : 'success';
                        $restante = $client->received_amount_device < $client->amount_device ? $client->amount_device-$client->received_amount_device : 0;
                        $restante = '$'.number_format($restante,2);
                        $span_icon = $client->received_amount_device < $client->amount_device ? $restante : '<li class="fa fa-check"></li>';
                    @endphp
                    <span class="label label-{{$span_class}} col-md-12" >{{ $span_text }} {!! $span_icon !!}</span>
                    </td>
                    <td>${{number_format($client->payment_amount)}}
                        <span class="label label-primary col-md-12" >SIN GENERAR</span>
                    </td>
                    <td>{{ $client->date_activation }}</td>
                    
                </tr>
                
            @endforeach
            <!-- END Activaciones con y sin pagos mensuales -->

            <!-- Instalaciones con y sin pagos mensuales -->
            @foreach( $instalations as $instalation )
                <tr class="get-data-payment Conecta" style="color: white !important; cursor: pointer;" data-id="{{$instalation->id}}" data-type="instalation">
                    <td>{{ $name = strtoupper($instalation->client_name.' '.$instalation->client_lastname) }}</td>
                    <td>N/A</td>
                    <td>{{ $pack = strtoupper($instalation->pack_name) }}</td>
                    <td>{{ $service = strtoupper($instalation->pack_service) }}</td>
                    <!-- Información de TARIFA -->
                    <td>${{ number_format($instalation->amount,2) }}
                    @php
                        $span_text = $instalation->received_amount < $instalation->amount ? 'PENDIENTE' : 'OK';
                        $span_class = $instalation->received_amount < $instalation->amount ? 'danger' : 'success';
                        $restante = $instalation->received_amount < $instalation->amount ? $instalation->amount-$instalation->received_amount : 0;
                        $restante = '$'.number_format($restante,2);
                        $span_icon = $instalation->received_amount < $instalation->amount ? $restante : '<li class="fa fa-check"></li>';
                    @endphp
                    <span class="label label-{{$span_class}} col-md-12" >{{ $span_text }} {!! $span_icon !!}</span>
                    </td>
                    <!-- Información de CPE -->
                    <td>${{ number_format($instalation->amount_install,2) }}
                    @php
                        $span_text = $instalation->received_amount_install < $instalation->amount_install ? 'PENDIENTE' : 'OK';
                        $span_class = $instalation->received_amount_install < $instalation->amount_install ? 'danger' : 'success';
                        $restante = $instalation->received_amount_install < $instalation->amount_install ? $instalation->amount_install-$instalation->received_amount_install : 0;
                        $restante = '$'.number_format($restante,2);
                        $span_icon = $instalation->received_amount_install < $instalation->amount_install ? $restante : '<li class="fa fa-check"></li>';
                    @endphp
                    <span class="label label-{{$span_class}} col-md-12" >{{ $span_text }} {!! $span_icon !!}</span>
                    </td>
                    <td>${{number_format($instalation->payment_amount)}}
                    @php
                        $span_text = $instalation->payment_status == 'pendiente' ? 'PENDIENTE' : 'OK';
                        $span_class = $instalation->payment_status == 'pendiente' ? 'warning' : 'success';
                        $span_icon = $instalation->payment_status == 'pendiente' ? ' ' : '<li class="fa fa-check"></li>';
                    @endphp
                    <span class="label label-{{$span_class}} col-md-12" >{{ $span_text }} {!! $span_icon !!}</span>
                    </td>
                    <td>{{ $instalation->payment_date }}</td>
                    
                </tr>
            @endforeach
            @foreach( $instalations2 as $instalation )
                <tr class="get-data-payment Conecta" style="color: white !important; cursor: pointer;" data-id="{{$instalation->id}}" data-type="instalation">
                    <td>{{ $name = strtoupper($instalation->client_name.' '.$instalation->client_lastname) }}</td>
                    <td>N/A</td>
                    <td>{{ $pack = strtoupper($instalation->pack_name) }}</td>
                    <td>{{ $service = strtoupper($instalation->pack_service) }}</td>
                    <!-- Información de TARIFA -->
                    <td>${{ number_format($instalation->amount,2) }}
                    @php
                        $span_text = $instalation->received_amount < $instalation->amount ? 'PENDIENTE' : 'OK';
                        $span_class = $instalation->received_amount < $instalation->amount ? 'danger' : 'success';
                        $restante = $instalation->received_amount < $instalation->amount ? $instalation->amount-$instalation->received_amount : 0;
                        $restante = '$'.number_format($restante,2);
                        $span_icon = $instalation->received_amount < $instalation->amount ? $restante : '<li class="fa fa-check"></li>';
                    @endphp
                    <span class="label label-{{$span_class}} col-md-12" >{{ $span_text }} {!! $span_icon !!}</span>
                    </td>
                    <!-- Información de CPE -->
                    <td>${{ number_format($instalation->amount_install,2) }}
                    @php
                        $span_text = $instalation->received_amount_install < $instalation->amount_install ? 'PENDIENTE' : 'OK';
                        $span_class = $instalation->received_amount_install < $instalation->amount_install ? 'danger' : 'success';
                        $restante = $instalation->received_amount_install < $instalation->amount_install ? $instalation->amount_install-$instalation->received_amount_install : 0;
                        $restante = '$'.number_format($restante,2);
                        $span_icon = $instalation->received_amount_install < $instalation->amount_install ? $restante : '<li class="fa fa-check"></li>';
                    @endphp
                    <span class="label label-{{$span_class}} col-md-12" >{{ $span_text }} {!! $span_icon !!}</span>
                    </td>
                    <td>${{number_format($instalation->payment_amount)}}
                        <span class="label label-primary col-md-12" >SIN GENERAR</span>
                    </td>
                    <td>{{ $instalation->date_instalation }}</td>
                    
                </tr>
            @endforeach
            @foreach( $TELMEXinstalations as $TELMEXinstalation )
                <tr class="get-data-payment Telmex" style="color: white !important; cursor: pointer;" data-id="{{$TELMEXinstalation->id}}" data-type="instalation">
                    <td>{{ $name = strtoupper($TELMEXinstalation->client_name.' '.$TELMEXinstalation->client_lastname) }}</td>
                    <td>{{ $number = $TELMEXinstalation->number != null ? $TELMEXinstalation->number : 'N/A' }}</td>
                    <td>{{ $pack = strtoupper($TELMEXinstalation->pack_name) }}</td>
                    <td>{{ $service = strtoupper($TELMEXinstalation->pack_service) }}</td>
                    <!-- Información de TARIFA -->
                    <td>${{ number_format($TELMEXinstalation->amount,2) }}
                    @php
                        $span_text = $TELMEXinstalation->received_amount < $TELMEXinstalation->amount ? 'PENDIENTE' : 'OK';
                        $span_class = $TELMEXinstalation->received_amount < $TELMEXinstalation->amount ? 'danger' : 'success';
                        $restante = $TELMEXinstalation->received_amount < $TELMEXinstalation->amount ? $TELMEXinstalation->amount-$TELMEXinstalation->received_amount : 0;
                        $restante = '$'.number_format($restante,2);
                        $span_icon = $TELMEXinstalation->received_amount < $TELMEXinstalation->amount ? $restante : '<li class="fa fa-check"></li>';
                    @endphp
                    <span class="label label-{{$span_class}} col-md-12" >{{ $span_text }} {!! $span_icon !!}</span>
                    </td>
                    <!-- Información de CPE -->
                    <td>${{ number_format($TELMEXinstalation->amount_install,2) }}
                    @php
                        $span_text = $TELMEXinstalation->received_amount_install < $TELMEXinstalation->amount_install ? 'PENDIENTE' : 'OK';
                        $span_class = $TELMEXinstalation->received_amount_install < $TELMEXinstalation->amount_install ? 'danger' : 'success';
                        $restante = $TELMEXinstalation->received_amount_install < $TELMEXinstalation->amount_install ? $TELMEXinstalation->amount_install-$TELMEXinstalation->received_amount_install : 0;
                        $restante = '$'.number_format($restante,2);
                        $span_icon = $TELMEXinstalation->received_amount_install < $TELMEXinstalation->amount_install ? $restante : '<li class="fa fa-check"></li>';
                    @endphp
                    <span class="label label-{{$span_class}} col-md-12" >{{ $span_text }} {!! $span_icon !!}</span>
                    </td>
                    <td>${{number_format($TELMEXinstalation->payment_amount)}}
                    @php
                        $span_text = $TELMEXinstalation->payment_status == 'pendiente' ? 'PENDIENTE' : 'OK';
                        $span_class = $TELMEXinstalation->payment_status == 'pendiente' ? 'warning' : 'success';
                        $span_icon = $TELMEXinstalation->payment_status == 'pendiente' ? ' ' : '<li class="fa fa-check"></li>';
                    @endphp
                    <span class="label label-{{$span_class}} col-md-12" >{{ $span_text }} {!! $span_icon !!}</span>
                    </td>
                    <td>{{ $TELMEXinstalation->payment_date }}</td>            
                </tr>
            @endforeach
            @foreach( $TELMEXinstalations2 as $TELMEXinstalation )
                <tr class="get-data-payment Telmex" style="color: white !important; cursor: pointer;" data-id="{{$TELMEXinstalation->id}}" data-type="instalation">
                    <td>{{ $name = strtoupper($TELMEXinstalation->client_name.' '.$TELMEXinstalation->client_lastname) }}</td>
                    <td>{{ $number = $TELMEXinstalation->number != null ? $TELMEXinstalation->number : 'N/A' }}</td>
                    <td>{{ $pack = strtoupper($TELMEXinstalation->pack_name) }}</td>
                    <td>{{ $service = strtoupper($TELMEXinstalation->pack_service) }}</td>
                    <!-- Información de TARIFA -->
                    <td>${{ number_format($TELMEXinstalation->amount,2) }}
                    @php
                        $span_text = $TELMEXinstalation->received_amount < $TELMEXinstalation->amount ? 'PENDIENTE' : 'OK';
                        $span_class = $TELMEXinstalation->received_amount < $TELMEXinstalation->amount ? 'danger' : 'success';
                        $restante = $TELMEXinstalation->received_amount < $TELMEXinstalation->amount ? $TELMEXinstalation->amount-$TELMEXinstalation->received_amount : 0;
                        $restante = '$'.number_format($restante,2);
                        $span_icon = $TELMEXinstalation->received_amount < $TELMEXinstalation->amount ? $restante : '<li class="fa fa-check"></li>';
                    @endphp
                    <span class="label label-{{$span_class}} col-md-12" >{{ $span_text }} {!! $span_icon !!}</span>
                    </td>
                    <!-- Información de CPE -->
                    <td>${{ number_format($TELMEXinstalation->amount_install,2) }}
                    @php
                        $span_text = $TELMEXinstalation->received_amount_install < $TELMEXinstalation->amount_install ? 'PENDIENTE' : 'OK';
                        $span_class = $TELMEXinstalation->received_amount_install < $TELMEXinstalation->amount_install ? 'danger' : 'success';
                        $restante = $TELMEXinstalation->received_amount_install < $TELMEXinstalation->amount_install ? $TELMEXinstalation->amount_install-$TELMEXinstalation->received_amount_install : 0;
                        $restante = '$'.number_format($restante,2);
                        $span_icon = $TELMEXinstalation->received_amount_install < $TELMEXinstalation->amount_install ? $restante : '<li class="fa fa-check"></li>';
                    @endphp
                    <span class="label label-{{$span_class}} col-md-12" >{{ $span_text }} {!! $span_icon !!}</span>
                    </td>
                    <td>${{number_format($TELMEXinstalation->payment_amount)}}
                        <span class="label label-primary col-md-12" >SIN GENERAR</span>
                    </td>
                    <td>{{ $TELMEXinstalation->date_instalation }}</td>            
                </tr>
            @endforeach
            <!-- END Instalaciones con y sin pagos mensuales -->
            </tbody>
        </table>
        
    </div>
</section>

<div class="modal fade" id="modalDataPayment" tabindex="-1" role="dialog" aria-labelledby="myModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalTitle"></h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal form-bordered" >
                    <div class="form-group">
                        <div class="col-sm-12">
                            <div class="col-md-12">
                                <h4 class="text-success text-bold">Montos Esperados y a Ingresar</h4>
                            </div>

                            <div class="col-md-12">
                                <div class="alert alert-info fade in nomargin">
                                    <h4>Información de la Venta</h4>
                                    <ul>
                                        <li id="client"></li>
                                        <li id="service"></li>
                                        <li id="data_rate"></li>
                                        <li id="data_device"></li>
                                    </ul>
                                    <p class="text-bold" id="amount_total_tag"></p>
                                </div>
                            </div>

                            <input type="hidden" id="id_sale">
                            <input type="hidden" id="type">
                            <input type="hidden" id="attrID">
                            <input type="hidden" id="amount_rate">
                            <input type="hidden" id="amount_extra">
                            <input type="hidden" id="amount_total">

                            <div class="form-group col-md-6">
                                <div class="col-md-12">
                                    <section class="form-group-vertical">
                                        <div class="input-group input-group-icon">
                                            <label for="received_amount_device">Monto Recibido</label>
                                            <input class="form-control" type="text" id="received_amount" name="received_amount" autocomplete="off">
                                        </div>
                                    </section>
                                </div>
                            </div>
                        </div>
                    </div>        

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="save_payment">Guardar</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script src="{{asset('octopus/assets/vendor/pnotify/pnotify.custom.js')}}"></script>
<script>
    $('.get-data-payment').click(function(){
        let id = $(this).attr('data-id');
        let type = $(this).attr('data-type');
        // let attrID = $(this).attr('id');
        console.log(id);
        console.log(type);
        return false;
        let amount_total = 0;

        $.ajax({
            url: "{{route('getDataPayment.get')}}",
            data: {id:id, type:type},
            success: function(response){
                response = response[0];
                // console.log(response);

                if(type == 'activation'){
                    $('#client').html('<b>Cliente:</b> '+response.name+' '+response.lastname);
                    $('#service').html('<b>Servicio:</b> '+response.service);
                    $('#data_rate').html('<b>Plan:</b> '+response.rate_name+' $'+response.amount_rate);
                    $('#data_device').html('<b>Dispositivo:</b> '+response.device_description+' $'+response.amount_device);
                    amount_total = parseFloat(response.amount_rate)+parseFloat(response.amount_device);
                    $('#amount_total_tag').html('Total: $'+amount_total);
                    $('#amount_total').val(amount_total);
                    $('#amount_rate').val(response.amount_rate);
                    $('#amount_extra').val(response.amount_device);
                }else if(type == 'instalation'){
                    $('#client').html('<b>Cliente:</b> '+response.name+' '+response.lastname);
                    $('#service').html('<b>Servicio:</b> '+response.service);
                    $('#data_rate').html('<b>Paquete:</b> '+response.pack_name+' $'+response.amount_pack);
                    $('#data_device').html('<b>Instalación:</b> $'+response.amount_install);
                    amount_total = parseFloat(response.amount_pack)+parseFloat(response.amount_install);
                    $('#amount_total_tag').html('Total: $'+amount_total);
                    $('#amount_total').val(+amount_total);
                    $('#amount_rate').val(response.amount_pack);
                    $('#amount_extra').val(response.amount_install);
                }
                // console.log(amount_total);

                $('#received_amount').val('');
                $('#id_sale').val(id);
                $('#type').val(type);
                // $('#attrID').val(attrID);
                $('#modalDataPayment').modal('show');
            }
        });
    });

    $('#received_amount_rate, #received_amount_device').on('input', function () {
        this.value = this.value.replace(/[^0-9.]/g, '');
    });

    $('#save_payment').click(function(){
        let id = $('#id_sale').val();
        let type = $('#type').val();
        let attrID = $('#attrID').val();
        let amount_total = $('#amount_total').val();
        let amount_rate = $('#amount_rate').val();
        let amount_extra = $('#amount_extra').val();
        let received_amount = $('#received_amount').val();

        let received_amount_rate = 0;
        let received_amount_extra = 0;

        amount_total = parseFloat(amount_total);
        amount_rate = parseFloat(amount_rate);
        amount_extra = parseFloat(amount_extra);
        received_amount = parseFloat(received_amount);
        let data;
        
        if(received_amount.length == 0 || /^\s+$/.test(received_amount)){
            Swal.fire({
                icon: 'error',
                title: 'Ingrese monto recibido.',
                showConfirmButton: false,
                timer: 1500
            })
            return false;
        }

        if(received_amount < amount_total){
            if(received_amount < amount_rate){
                received_amount_rate = received_amount;
                received_amount_extra = 0;
            }

            if(received_amount >= amount_rate){
                received_amount_rate = amount_rate;
                received_amount = received_amount-amount_rate;
                received_amount_extra = received_amount;
            }
        }

        if(received_amount >= amount_total){
            received_amount_rate = amount_rate;
            received_amount_extra = amount_extra;
        }

        console.log('Monto recibido del plan: '+received_amount_rate+' - Monto recibido extra: '+received_amount_extra);

        
        // return false;


        if(type == 'activation'){
            data = {id:id, type:type, received_amount_rate:received_amount_rate, received_amount_device:received_amount_extra}
        }else if(type == 'instalation'){
            data = {id:id, type:type, received_amount:received_amount_rate, received_amount_install:received_amount_extra}
        }
        
        $.ajax({
            url: "{{route('setPaymentStatus.get')}}",
            data: data,
            success: function(response){
                
                if(response == 1){
                    $('#'+attrID).addClass('d-none');
                    new PNotify({
                        title: 'Hecho.',
                        text: "<a href='{{route('activations.index')}}' style='color: white !important;'>Click aquí para actualizar.</a>",
                        type: 'success',
                        icon: 'fa fa-home'
                    });
                }else if(response == 0){
                    new PNotify({
                        title: 'Ooops!',
                        text: "<a href='{{route('activations.index')}}' style='color: white !important;'>Click aquí para actualizar.</a>",
                        type: 'error',
                        icon: 'fa fa-times'
                    });
                }
            }
        });
           
    });

    $('.location-reload').click(function(){
        location.reload();
    })
</script>
@endsection