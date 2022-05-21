@extends('layouts.octopus')
@section('content')
@php
use \Carbon\Carbon;
@endphp
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
<div class="alert alert-primary">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    <strong>Mostrando registros en el rango de {{$date_init}} a {{$date_final}}</strong>
</div>
<div class="row">
    <div class="col-lg-12">
        <section class="panel">
           
            <div class="panel-body">
                <form class="form-horizontal form-bordered" action="{{route('incomes.get')}}">

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
                        <button class="col-md-1 btn btn-success btn-sm">Consultar</button>
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

        <h2 class="panel-title">Pagos Completados</h2>
    </header>
    <div class="panel-body">
        <table class="table table-bordered table-striped mb-none" id="datatable-default">
            <thead style="cursor: pointer;">
                <tr>
                <th scope="col">Canal de Pago</th>
                <th scope="col">Folio/Referencia</th>
                <th scope="col">Fecha de Pago</th>
                <th scope="col">Fecha de Operación</th>
                <th scope="col">Servicio</th>
                <th scope="col">Monto Esperado</th>
                <th scope="col">Monto Recibido</th>
                <th scope="col">Comisión</th>
                </tr>
            </thead>
            <tbody>
            
            </tbody>
        </table>
      
    </div>
</section>



<script>
    $('#date-pay').click(function(){
        $.ajax({
                url: "{{ route('date-pay')}}",
                success: function(data){
                    console.log(data);
                    
                }
            });
    });
</script>
@endsection
