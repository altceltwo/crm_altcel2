@extends('layouts.octopus')
@section('content')
<header class="page-header">
    <h2>Producto</h2>

    <div class="right-wrapper pull-right">
        <ol class="breadcrumbs">
            <li>
                <a href="index.html">
                    <i class="fa fa-home"></i>
                </a>
            </li>
        </ol>

        <a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
    </div>
</header>
<section class="panel">
    <div class="panel-body">
        <div class="invoice">
            <header class="clearfix">
                <div class="row">
                    <div class="col-sm-6 mt-md">
                        @if($service == 'MIFI' || $service == 'HBB' || $service == 'MOV')
                        <h4 class="card-title"></h4>
                        <h2 class="h2 mt-none mb-sm text-dark text-bold">{{$service.'/'.$DN}}</h2>
                        @elseif($service == 'Conecta' || $service == 'Telmex')
                        <h2 class="h2 mt-none mb-sm text-dark text-bold">{{$service}}</h2>
                        @endif
                        <h4 class="h4 m-none text-dark text-bold">Paquete/Plan Actual: <br>
                        {{$pack_name}} - {{'$'.number_format($pack_price,2)}}</h4>
                    </div>
                    <div class="col-sm-6 text-right mt-md mb-md">
                        <address class="ib mr-xlg">
                            Fecha de Activación: {{$date_activation}}
                            @if($service == 'MIFI' || $service == 'HBB' || $service == 'MOV')
                                <br/>
                                    @php
                                        $tag = $traffic_out == 'activo' ? 'label-success' : 'label-danger';
                                        $tagS = $traffic_out_in == 'activo' ? 'label-success' : 'label-danger'
                                    @endphp
                                Tráfico Saliente: <span class="label {{$tag}}" style="cursor: pointer;" data-msisdn="{{$DN}}" data-status="{{$traffic_out}}" data-toggle="tooltip" data-placement="left" title="Suspensión/Reanudación del Tráfico Saliente" id="traffic_out">{{$traffic_out}}</span>
                                <br/>
                                Tráfico Saliente/Entrante: <span class="label {{$tagS}}" style="cursor: pointer;" data-msisdn="{{$DN}}" data-status="{{$traffic_out_in}}" data-toggle="tooltip" data-placement="left" title="Suspensión/Reanudación del Tráfico Saliente/Entrante" id="traffic_out_in">{{$traffic_out_in}}</span>
                            </div>
                            @endif
                        </address>
                    </div>
                </div>
            </header>
            <br>
        
        </div>

        <!-- <div class="text-right mr-lg">
            <a href="#" class="btn btn-default">Submit Invoice</a>
            <a href="pages-invoice-print.html" target="_blank" class="btn btn-primary ml-sm"><i class="fa fa-print"></i> Print</a>
        </div> -->
    </div>
</section>


<script>
    var token = $('meta[name="csrf-token"]').attr('content'); 

    $('#traffic_out').click(function(){
        let status = $(this).attr('data-status');
        let msisdn = $(this).attr('data-msisdn');
        let type = 'out';
        activateDeactivateTraffic(status,type,msisdn);
    });

    $('#traffic_out_in').click(function(){
        let status = $(this).attr('data-status');
        let msisdn = $(this).attr('data-msisdn');
        let type = 'out_in';
        activateDeactivateTraffic(status,type,msisdn);
    });

    function activateDeactivateTraffic(status, type, msisdn){
        let headers = {headers: {'Content-type': 'application/json'}};
        let data = {
                _token: token, status: status, type: type, msisdn: msisdn
            };

            Swal.fire({
                title: 'Estamos trabajando en ello.',
                html: 'Espera un poco, un poquito más...',
                didOpen: () => {
                    Swal.showLoading();
                    $.ajax({
                        url:"{{route('activate-deactivate.post')}}",
                        method: "POST",
                        data: data,
                        success: function(data){
                            if(data.bool == 1){
                                    Swal.close();
                                    Swal.fire({
                                        icon: 'success',
                                        title: data.message,
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                    setTimeout(function(){ location.reload(); }, 1500);
                                    // alert(data.bool)
                                }else if(data.bool == 0){
                                    Swal.close();
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error: '+data.errorCode,
                                        text: data.description
                                    });
                                    // alert(data.bool)
                                }
                        }
                    });  
                }
            });
            
                // axios.post('/activate-deactivate/DN', data, headers).then(response => {
                        
                //         // if(response.data == 0){
                //         //     
                //         // }
                //     }).catch(e => {
                //         console.log(e);
                //     })
    }
</script>
@endsection