@extends('layouts.octopus')
@section('content')
<header class="page-header">
    <h2>Detalles de Distribuidor</h2>
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

<div class="row">
    <section class="panel panel-horizontal">
        <header class="panel-heading bg-success">
        <div class="panel-heading-icon">
                <i class="fa fa-dollar"></i>
            </div>
        </header>
        <div class="panel-body p-lg">
            <h3 class="text-semibold mt-sm">Saldo Disponible: ${{number_format($dealer->balance,2)}} <i class="{{$icon}}"></i></h3>
            <!-- <blockquote class="info"> -->
                <p class="text-semibold">{{$dataDealer->name.' ' .$dataDealer->lastname}}</p>
                <p class="text-semibold">{{$dataDealer->email}}</p>
                <!-- <small>A. Einstein, <cite title="Magazine X">Magazine X</cite></small> -->
            <!-- </blockquote> -->
        </div>
    </section>
</div>

<!-- <section class="panel">
    <header class="panel-heading">
        <div class="panel-actions">
            <a href="#" class="fa fa-caret-down"></a>
            <a href="#" class="fa fa-times"></a>
        </div>

        <h2 class="panel-title">Distribuidores Existentes</h2>
    </header>
    <div class="panel-body">
        <table class="table table-bordered table-striped mb-none" id="datatable-default">
            <thead >
                <tr>
                <th scope="col">Nombre</th>
                <th scope="col">Email</th>
                <th scope="col">Saldo</th>
                <th scope="col">Creado por</th>
                <th scope="col">Opciones</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>
</section>

<div class="modal fade" id="modalDealer" tabindex="-1" role="dialog" aria-labelledby="myModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title text-dark" id="myModalTitle">Saldo</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal form-bordered">
                    <input type="hidden" id="methodUpdate" name="_method" value="PUT">
                    <input type="hidden" id="tokenUpdate" name="_token" value="{{ csrf_token() }}">
                
                    <div class="form-group"  style="padding-right: 1rem; padding-left: 1rem;">
                        <div class="col-md-6 ">
                            <label for="price" class="form-label">Disponible</label>
                            <input type="text" class="form-control form-control-sm mr-2" id="balanceEdit" name="balanceEdit" >
                        </div>
                    </div>              

                    <input type="hidden" id="dealer_id">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="add_update_balance">Guardar</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div> -->

<script>

</script>
@endsection