@extends('layouts.app')
@extends('layouts.datatablescss')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Distribuidores <a href="{{ route('dealers.create')}}" class="badge badge-dark">Crear</a></div>
                <div class="card-body">
                    <table id="dataT" class="table table-sm table-hover" style="width:100%">
                        <thead >
                            <tr>
                            <th scope="col">Distribuidor</th>
                            <th scope="col">RFC</th>
                            <th scope="col">Teléfono</th>
                            <th scope="col">Encargado</th>
                            <th scope="col">Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach( $dealers as $dealer )
                        <tr style="cursor: pointer;">
                            <td>{{ $dealer->name }}</td>
                            <td>{{ $dealer->rfc }}</td>
                            <td>{{ $dealer->phone }}</td>
                            <td>{{ $dealer->user_name }}</td>
                            <td><a href="{{url('/dealers/'.$dealer->id)}}" class="btn btn-warning btn-sm"><i class="fas fa-eye"></a></td>
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
    </script>
@endsection