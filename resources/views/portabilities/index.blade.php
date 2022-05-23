@extends('layouts.octopus')
@section('content')
<header class="page-header">
    <h2>Mis Portabilidades Pendientes</h2>
    <div class="right-wrapper pull-right">
        <ol class="breadcrumbs">
            <li>
                <a href="index.html">
                    <i class="fa fa-home"></i>
                </a>
            </li>
            <li><span>Dashboard</span></li>
        </ol>
    </div>
</header>


<section class="panel">
    <header class="panel-heading">
        <div class="panel-actions">
            <a href="#" class="fa fa-caret-down"></a>
            <a href="#" class="fa fa-times"></a>
        </div>

        <h2 class="panel-title">Pendientes</h2>
    </header>
    <div class="panel-body" >
        <table class="table table-bordered table-striped mb-none" id="myTable">
            <thead style="cursor: pointer;">
                <tr>
                    <th>Número Portado</th>
                    <th>ICC</th>
                    <th>Número Transitorio</th>
                    <th>Fecha para Portar</th>
                    <th>NIP</th>
                    <th>Plan Activación</th>
                    <th>Cliente</th>
                    <th>Enviado por</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            @foreach($pendings as $pending)
                <tr style="cursor: pointer;" >
                    <td>{{$pending['msisdnPorted']}}</td>
                    <td>{{$pending['icc']}}</td>
                    <td>{{$pending['msisdnTransitory']}}</td>
                    <td>{{$pending['date']}}</td>
                    <td>{{$pending['nip']}}</td>
                    <td>{{$pending['rate']}}</td>
                    <td>{{$pending['client']}}</td>
                    <td>{{$pending['who_did_it']}}</td>
                    <td>
                        <button class="btn btn-success btn-xs pending-port" data-id="{{$pending['id']}}" data-msisdn="{{$pending['msisdnTransitory']}}" data-toggle="tooltip" data-placement="left" title="" data-original-title="Activar SIM"><li class="fa fa-check-circle"></li></button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
       
    </div>
</section>

<section class="panel">
    <header class="panel-heading">
        <div class="panel-actions">
            <a href="#" class="fa fa-caret-down"></a>
            <a href="#" class="fa fa-times"></a>
        </div>

        <h2 class="panel-title">Activadas</h2>
    </header>
    <div class="col-md-3" style="margin-bottom: 1rem; margin-top: 1rem; ">
        <button class="btn btn-success btn-sm" id="importAll" >Importar a Altan</button>
    </div>
    <div class="col-md-4"  style="margin-bottom: 1rem; margin-top: 1rem; margin-left: -6rem">
        <span class="btn btn-default btn-file">
            <span class="fileupload-new">Selecciona un archivo</span>
            <input type="file" accept=".csv" id="csvtAltan">
        </span>
        <button class="btn btn-primary" id="importAltan">Cargar Archivo</button>
    </div>
    <div class="panel-body" >
        <table class="table table-bordered table-striped mb-none" id="myTable2">
            <thead style="cursor: pointer;">
                <tr>
                    <th>Número Portado</th>
                    <th>ICC</th>
                    <th>Número Transitorio</th>
                    <th>Fecha para Portar</th>
                    <th>NIP</th>
                    <th>Plan Activación</th>
                    <th>Cliente</th>
                    <th>Enviado por</th>
                    <th>Atendido por</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            @foreach($activateds as $activated)
                <tr style="cursor: pointer;" >
                    <td>{{$activated['msisdnPorted']}}</td>
                    <td>{{$activated['icc']}}</td>
                    <td>{{$activated['msisdnTransitory']}}</td>
                    <td>{{$activated['date']}}</td>
                    <td>{{$activated['nip']}}</td>
                    <td>{{$activated['rate']}}</td>
                    <td>{{$activated['client']}}</td>
                    <td>{{$activated['who_did_it']}}</td>
                    <td>{{$activated['who_attended']}}</td>
                    <td>
                        <button class="btn btn-success btn-xs activated-port" data-id="{{$activated['id']}}" data-msisdn="{{$activated['msisdnPorted']}}" data-icc="{{$activated['icc']}}" data-toggle="tooltip" data-placement="left" title="" data-original-title="Importar a Altán">
                            <li class="fa fa-check-circle"></li>
                        </button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
       
    </div>
</section>

<section class="panel">
    <header class="panel-heading">
        <div class="panel-actions">
            <a href="#" class="fa fa-caret-down"></a>
            <a href="#" class="fa fa-times"></a>
        </div>

        <h2 class="panel-title">Completadas</h2>
    </header>
    <div class="panel-body" >
        <table class="table table-bordered table-striped mb-none" id="myTable3">
            <thead style="cursor: pointer;">
                <tr>
                    <th>Número Portado</th>
                    <th>ICC</th>
                    <th>Número Transitorio</th>
                    <th>Fecha para Portar</th>
                    <th>NIP</th>
                    <th>Plan Activación</th>
                    <th>Cliente</th>
                    <th>Enviado por</th>
                    <th>Atendido por</th>
                </tr>
            </thead>
            <tbody id="body-complate">
            @foreach($completeds as $completed)
                <tr style="cursor: pointer;" >
                    <td>{{$completed['msisdnPorted']}}</td>
                    <td>{{$completed['icc']}}</td>
                    <td>{{$completed['msisdnTransitory']}}</td>
                    <td>{{$completed['date']}}</td>
                    <td>{{$completed['nip']}}</td>
                    <td>{{$completed['rate']}}</td>
                    <td>{{$completed['client']}}</td>
                    <td>{{$completed['who_did_it']}}</td>
                    <td>{{$completed['who_attended']}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</section>

<div class="modal fade" id="modalErrors" tabindex="-1" aria-labelledby="modalErrorsLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="moralPersonModalLabel">Errores encontrados en la importación</h5>
            </div>
            <div class="modal-body" id="tblErrors">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
<script >
    $(document).ready( function () {
        $('#myTable').DataTable({
            dom: 'Bfrtip',
            buttons: [{
                extend: 'excel',
                header: true,
                title: 'portabilidadesPendientes',
                exportOptions : {
                    columns: [ 0, 1, 2, 3, 4, 5, 6, 7 ],
                }
            }],
        });

        $('#myTable2').DataTable({
            dom: 'Bfrtip',
            buttons: [{
                extend: 'excel',
                header: true,
                title: 'portabilidadesActivadas',
                exportOptions : {
                    columns: [ 0, 1, 2, 3, 4, 5, 6, 7 ],
                }
            }],
        });

        $('#myTable3').DataTable({
            dom: 'Bfrtip',
            buttons: [{
                extend: 'excel',
                header: true,
                title: 'portabilidadesCompletadas',
                exportOptions : {
                    columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 8 ],
                }
            }],
        });
    });
</script>

<script>
    $('.pending-port').click(function(){
        let id = $(this).data('id');
        let msisdn = $(this).data('msisdn');

        Swal.fire({
            title: 'ATENCIÓN',
            html: "¿Está seguro de realizar la activación del número <b>"+msisdn+"</b>?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'SÍ, ESTOY SEGURO',
            cancelButtonText: 'Cancelar',
            customClass: {
                confirmButton: 'btn btn-primary mr-md',
                cancelButton: 'btn btn-danger '
            },
            buttonsStyling: false,
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{route('doActivationPort')}}",
                    method: "POST",
                    data: {_token: "{{csrf_token()}}", id: id, user_id: "{{Auth::user()->id}}"},
                    beforeSend: function(){
                        Swal.fire({
                            title: 'Activando',
                            html: 'Espera un poco, un poquito más...',
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(response){
                        if(response.http_code){
                            if(response.http_code == 200){
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Well done!!',
                                    text: response.message,
                                });
                                setTimeout(function(){ location.reload(); }, 2500);
                            }else{
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Woops!!',
                                    text: response.message,
                                })
                            }
                        }else{
                            Swal.fire({
                                icon: 'error',
                                title: 'Algo salió mal, consulta a Desarrollo.',
                            })
                        }
                    }
                });
            } else if (
                /* Read more about handling dismissals below */
                result.dismiss === Swal.DismissReason.cancel
            ) {
                Swal.fire({
                    icon: 'error',
                    title: 'Operación cancelada',
                    showConfirmButton: false,
                    timer: 1000
                })
            }
        })
        
    });
</script>

<script>
    $('#importAll').click(function(){
        Swal.fire({
            title: 'ATENCIÓN',
            html: "¿Está seguro de realizar la importación de números a portar en Altan?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'SÍ, ESTOY SEGURO',
            cancelButtonText: 'Cancelar',
            customClass: {
                confirmButton: 'btn btn-primary mr-md',
                cancelButton: 'btn btn-danger '
            },
            buttonsStyling: false,
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{route('importAllPorts')}}",
                    method: "POST",
                    data: {_token:"{{csrf_token()}}"},
                    beforeSend: function(){
                        Swal.fire({
                            title: 'Importando datos a Altan',
                            html: 'Espera un poco, un poquito más...',
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(response){
                        Swal.close();
                        let message = response.message;
                        let error = response.error;
                        let errors = response.errors;
                        let tbl = "<table class='table table-bordered table-striped mb-none'><thead> <tr> <th>Número a Portar</th> <th>Descripción</th> <th>Código Error</th> </tr> </thead><tbody>";

                        if(error == 1){
                            errors.forEach(function(element){
                                tbl+="<tr><td><span class='badge label-danger'>"+element.msisdn.msisdnPorted+"</span></td><td><span class='badge label-danger'>"+element.msisdn.response.description+"</span></td><td><span class='badge label-danger'>"+element.msisdn.response.errorCode+"</span></td></tr>";
                            });

                            tbl+="</tbody></table>";
                            $('#tblErrors').html(tbl);
                            $('#modalErrors').modal('show');
                        }else{
                            Swal.fire({
                                icon: 'success',
                                title: 'Well Done!!',
                                text: 'La importación se realizó sin errores.'
                            });
                        }
                        
                        
                    }
                });
            } else if (
                /* Read more about handling dismissals below */
                result.dismiss === Swal.DismissReason.cancel
            ) {
                Swal.fire({
                    icon: 'error',
                    title: 'Operación cancelada',
                    showConfirmButton: false,
                    timer: 1000
                })
            }
        });
    });
</script>

<script>
    $('#importAltan').click(function(){
        let firstCSV = $('#csvtAltan').val();


        if(firstCSV.length == 0 || /^\s+$/.test(firstCSV)){
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Por favor cargue un fichero con extensión CSV.',
                showConfirmButton: false,
                timer: 2000
            });
            return false;

            console.log('CSV')
        }

        let file_data = $('#csvtAltan').prop('files')[0];
        let form_data = new FormData();
        form_data.append('file', file_data);
        form_data.append('_token', '{{csrf_token()}}');
        $.ajax({
            url: "{{route('csvAltan')}}",
            dataType: 'text',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type:'POST',
           success:function(response){
               if (response) {
                window.location.reload();
               }
            // x = JSON.parse(response)
            // let body = ''
            // x.forEach(data =>
            //     body+= "<tr>"+"<td>"+data.msisdnPorted+"</td>"+"<td>"+data.icc+"</td>"+"<td>"+data.msisdnTransitory+"</td>"+"<td>"+data.date+"</td>"+"<td>"+data.nip+"</td>"+"<td>"+data.rate+"</td>"+"<td>"+data.client+"</td>"+"<td>"+data.who_did_it+"</td>"+"<td>"+data.who_attended+"</td>"+"</tr>"
            // );
            // $('#body-complate').html(body)

           }
        })
    })
</script>
@endsection