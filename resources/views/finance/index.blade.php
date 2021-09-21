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
<input type="hidden" id="userConsigned" value="{{auth()->user()->id}}">
<section class="panel">
    <header class="panel-heading">
        <div class="panel-actions">
            <a href="#" class="fa fa-caret-down"></a>
            <a href="#" class="fa fa-times"></a>
        </div>
        <h2 class="panel-title">Cambio de Producto</h2>
    </header>
    <div class="panel-body">
        <table class="table table-bordered table-striped mb-none" id="datatable-default">
            <thead>
                <tr>
                    <th>Monto</th>
                    <th>Promotor</th>
                    <th>MSISDN</th>
                    <th>Plan</th>
                    <th>Status</th>
                    <th>Razón</th>
                    <th>Tipo</th>
                    <th>Cobrar</th>
                    <th>Ver</th>
                </tr>
            </thead>
                <tbody>
                    @foreach($changes as $change)
                        <tr>
                            <td>${{number_format($change->amount,2)}}</td>
                            <td>{{$change->client}} {{$change->lastname}}</td>
                            <td>{{$change->MSISDN}}</td>
                            <td>{{$change->name_product}}</td>
                            <td>{{$change->status}}</td>
                            <td>{{$change->reason}}</td>
                            <td><span class="badge label-warning">Cambio de Plan</span></td>
                            <td><button class="btn btn-warning cobrar" data-status="pendiente" data-type="changes" data-id="{{$change->id}}">Cobrar</button></td>
                            <td><a href="{{url('/cortes/'.$change->who_did_id)}}" class="btn btn-info btn-sm mt-xs"><i class="fa fa-eye"></i></a></td>
                        </tr>
                @endforeach     
                @foreach($purchases as $purchase)
                    <tr>
                        <td>${{number_format($purchase->amount,2)}}</td>
                        <td>{{$purchase->client}} {{$purchase->lastname}}</td>
                        <td>{{$purchase->MSISDN}}</td>
                        <td>{{$purchase->name_product}}</td>
                        <td>{{$purchase->status}}</td>
                        <td>{{$purchase->reason}}</td>
                        <td><span class="badge label-info">Recarga</span></td>
                        <td><button class="btn btn-warning cobrar" data-status="pendiente" data-type="purchases" data-id="{{$purchase->id}}">Cobrar</button></td>
                        <td><a href="{{url('/cortes/'.$purchase->who_did_id)}}" class="btn btn-info btn-sm mt-xs"><i class="fa fa-eye"></i></a></td>
                    </tr>
                @endforeach    
            
                @foreach($pays as $pay)
                    <tr>
                        <td>${{number_format($pay->amount,2)}}</td>
                        <td>{{$pay->client}} {{$pay->lastname}}</td>
                        <td>{{$pay->MSISDN}}</td>
                        <td>{{$pay->name_product}}</td>
                        <td>{{$pay->status}}</td>
                        <td>Cobro</td>
                        <td><span class="badge label-success">Mensualidad</span></td>
                        <td><button class="btn btn-warning cobrar" data-status="pendiente" data-type="monthly" data-id="{{$pay->id}}">Cobrar</button></td>
                        <td><a href="{{url('/cortes/'.$pay->who_did_id)}}" class="btn btn-info btn-sm mt-xs"><i class="fa fa-eye"></i></a></td>
                    </tr>
                @endforeach    
            </tbody>
        </table>
    </div>
</section>

<script>
    $('.cobrar').click(function(){
        let status = $(this).attr('data-status');
        let type = $(this).attr('data-type');
        let id = $(this).attr('data-id');
        let idConsigned = $('#userConsigned').val()
        console.log(status);
        console.log(type);
        console.log(id);
        $(this).closest('tr').remove();
        $.ajax({
            url: "{{route('status.update')}}",
                method: 'POST',
                data: {idpay:id, type:type, status:status, id_consigned:idConsigned},
                success:function(response){
                        Swal.fire({ 
                            icon: 'success',
                            title: 'Éxitoso',
                            text: 'Cobro exitoso',
                            showConfirmButton: false,
                            timer: 2000
                        })
                }
        })

    })
</script>

@endsection