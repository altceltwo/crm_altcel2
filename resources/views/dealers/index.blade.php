@extends('layouts.octopus')
@section('content')
<header class="page-header">
    <h2>Distribuidores</h2>
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

@if(session('success'))
    <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4 class="alert-heading">Well done!!</h4>
        <p>{{session('message')}}</p>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger" >
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4 class="alert-heading">Oopps!!</h4>
        <p>{{session('error')}}</p>
    </div>
@endif
@if(session('warning'))
    <div class="alert alert-warning" >
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4 class="alert-heading">Oopps!!</h4>
        <p>{!! session('warning') !!}</p>
    </div>
@endif
<div class="row" id="create">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                <div class="panel-actions">
                    <a href="#" class="fa fa-caret-down"></a>
                    <a href="#" class="fa fa-times"></a>
                </div>

                <h2 class="panel-title">Crear Distribuidor</h2>
            </header>
            <div class="panel-body">
                <form class="form-horizontal form-bordered" method="POST" action="{{route('dealer.store')}}">
                @csrf

                    <div class="form-group"  style="padding-right: 1rem; padding-left: 1rem;">
                        <!-- <h3>Información personal</h3> -->
                        <div class="col md-12">
                            <div class="col-md-6 row">
                                <div class="col-md-12">
                                    <label>Usuarios</label>
                                    <select data-plugin-selectTwo class="form-control populate" id="users" name="user_id" onchange="getData()">
                                        <optgroup label="SIM's disponibles">
                                        <option value="0">Elige...</option>
                                        @foreach($users as $user)
                                        <option value="{{$user->id}}" data-name="{{$user->name}}" data-lastname="{{$user->lastname}}" data-email="{{$user->email}}">{{$user->name.' '.$user->lastname.' - '.$user->email}}</option>
                                        
                                        @endforeach
                                        </optgroup>
                                        
                                    </select>
                                </div>
                                <div class="col-md-12 mt-md d-none" id="data-user">
                                    <ul class="list-group col-md-12" id="data-user-list">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Nombre: <span class="badge label-info" id="name_user"> </span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Apellidos: <span class="badge label-info" id="lastname_user"> </span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Email: <span class="badge label-info" id="email_user"> </span>
                                        </li>
                                    </ul>

                                </div>
                            </div>
                            <input type="hidden" name="who_created" value="{{Auth::user()->id}}">
                            <input type="hidden" name="newDealerData" id="newDealerData" >
                            
                            <div class="col-md-6 ">
                                <label for="price" class="form-label">Saldo</label>
                                <input type="text" class="form-control form-control-sm mr-2" id="balance" name="balance" value="0.00">
                            </div>
                        </div>
                        <div class="col-md-12" style="margin-top: 1rem;">
                        <button type="submit" class="btn btn-success" id="savePrice">Guardar</button>
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
                @foreach($dealers as $dealer)
                <tr>
                    <td>{{$dealer['dealer']}}</td>
                    <td>{{$dealer['email']}}</td>
                    <td id="balance-{{$dealer['dealer_id']}}">${{number_format($dealer['balance'],2)}}</td>
                    <td>{{$dealer['who_created']}}</td>
                    <td>
                        <a href="{{route('dealer.show',['dealer' => $dealer['dealer_id']])}}" class="btn btn-info btn-xs" data-toggle="tooltip" data-placement="top" data-original-title="Ver detalles"><i class="fa fa-eye"></i></a>
                        <button class="btn btn-warning btn-xs editBalance" data-balance="{{$dealer['balance']}}" data-name="{{$dealer['dealer']}}" data-dealer-id="{{$dealer['dealer_id']}}" data-toggle="tooltip" data-placement="top" data-original-title="Ajustar saldo"><i class="fa fa-edit"></i></button>
                    </td>
                </tr>
                @endforeach
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
</div>
<script src="{{asset('octopus/assets/vendor/pnotify/pnotify.custom.js')}}"></script>
<script>
$('#balance, #balanceEdit').on('input', function () {
    this.value = this.value.replace(/[^0-9.]/g, '');
});

function getData(){
    let id = $('#users').val();

    if(id == 0){
        $('#data-user').addClass('d-none');
        $('#name_user').html('');
        $('#lastname_user').html('');
        $('#email_user').html('');
    }
    else{
        let name = $('#users option:selected').data('name');
        let lastname = $('#users option:selected').data('lastname');
        let email = $('#users option:selected').data('email');

        $('#name_user').html(name);
        $('#lastname_user').html(lastname);
        $('#email_user').html(email);
        $('#newDealerData').val(name+' '+lastname);
        $('#data-user').removeClass('d-none');
    }
}

$('.editBalance').click(function(){
    let balance = $(this).data('balance');
    let dealer = $(this).data('name');
    let dealer_id = $(this).data('dealer-id');
    
    $('#dealer_id').val(dealer_id);
    $('#myModalTitle').html('Saldo de '+dealer);
    $('#balanceEdit').val(balance.toFixed(2));
    $('#modalDealer').modal('show');
});

$('#add_update_balance').click(function(){
    let balance = $('#balanceEdit').val();
    let id = $('#dealer_id').val();
    let method = $('#methodUpdate').val();
    let token = $('#tokenUpdate').val();
    let url = "{{route('dealer.update',['dealer' => 'temp'])}}";
    url = url.replace('temp',id);

    $.ajax({
        url: url,
        method: 'PUT',
        data: {_token:token, _method:method, balance:balance},
        beforeSend: function(){
            Swal.fire({
                    title: 'Estamos trabajando en ello...',
                    html: 'Espera un poco, un poquito más...',
                    didOpen: () => {
                        Swal.showLoading();
                    }
            });
        },
        success: function(response){
            Swal.close();
            
            if(response.http_code == 1){
                $('#balance-'+id).html('$'+parseFloat(balance).toFixed(2));
                new PNotify({
                    title: response.message,
                    text: "<a href='{{route('dealer.index')}}' style='color: white !important;'>Click aquí para actualizar.</a>",
                    type: 'success',
                    icon: 'fa fa-check'
                });
            }else if(response.http_code == 0){
                new PNotify({
                    title: 'Oopps!',
                    text: response.message,
                    type: 'error',
                    icon: 'fa fa-times'
                });
            }
        }
    });
});
</script>
@endsection