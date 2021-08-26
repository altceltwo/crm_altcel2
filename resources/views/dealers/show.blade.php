@extends('layouts.app')
@extends('layouts.datatablescss')
@section('content')
<script src="https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Datos de <strong>{{$dealer->name}}</strong> <a href="{{ route('dealers.index')}}" class="badge badge-dark">Administración</a></div>
                <div class="card-body">
                <form action="{{route('addPackDealer.post')}}" method="POST">
                    @csrf
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <h3>Asignación de Paquetes a Vender</h3>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="pack_id">Paquete</label>
                            <select id="pack_id" name="pack_id" class="form-control form-control-sm">
                                <option selected value="0" data-price="0">Choose...</option>
                            @foreach($packs as $pack)
                                <option value="{{$pack->id}}" data-price="{{$pack->price}}">{{$pack->name}}</option>
                            @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="comission">Comisión por Renta</label>
                            <input type="text" class="form-control form-control-sm" id="comission" name="comission" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="comission">Ingreso esperado</label>
                            <input type="text" class="form-control form-control-sm" id="myMoney" name="myMoney" readonly required>
                        </div>
                        <input type="hidden" class="form-control form-control-sm" id="dealer_id" name="dealer_id" value="{{$dealer->id}}">
                        <input type="hidden" class="form-control form-control-sm" id="who_did_id" name="who_did_id" value="{{Auth::user()->id}}">
                    </div>
                    
                    <button type="submit" class="btn btn-primary" id="form-offer">Guardar</button>
                </form>

                    <table id="dataT" class="table table-sm table-hover" style="width:100%">
                        <thead >
                            <tr>
                            <th scope="col">Paquete</th>
                            <th scope="col">Precio</th>
                            <th scope="col">Comisión</th>
                            <th scope="col">Total</th>
                            <th scope="col">Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach( $myPacks as $myPack )
                        <tr style="cursor: pointer;">
                            <td>{{ $myPack->pack_name }}</td>
                            <td>{{ '$'.number_format($myPack->pack_price,2) }}</td>
                            <td>{{ '$'.number_format($myPack->pack_comission,2) }}</td>
                            <td>{{ '$'.number_format($myPack->pack_total,2) }}</td>
                            <td></td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                        
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.7/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.7/js/responsive.bootstrap4.min.js"></script>
<script>
    $('#dataT').DataTable({
        responsive: true,
        autoWidth: false
    });
$('#comission').keyup(function(){
    let comission = $(this).val();
    let price = $('#pack_id option:selected').attr('data-price');
    let myMoney = 0;
    comission = parseFloat(comission);
    price = parseFloat(price);

    if(price == 0){
        $('#myMoney').val(0);
    }else{
        myMoney = price - comission;
        $('#myMoney').val(myMoney);
    }
});

$('#pack_id').change(function(){
    let comission = $('#comission').val();
    let price = $('#pack_id option:selected').attr('data-price');
    let myMoney = 0;
    comission = parseFloat(comission);
    price = parseFloat(price);

    if(price == 0){
        $('#myMoney').val(0);
    }else{
        if(comission == 0 || comission.length == 0 || /^\s+$/.test(comission)){
            $('#myMoney').val(0);
        }else{
            myMoney = price - comission;
            $('#myMoney').val(myMoney);
        }
    }
});
</script>
@endsection