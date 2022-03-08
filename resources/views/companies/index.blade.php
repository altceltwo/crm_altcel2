@extends('layouts.octopus')
@section('content')
<header class="page-header">
    <h2>Promotores</h2>
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


<div class="row" id="create">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                <div class="panel-actions">
                    <a href="#" class="fa fa-caret-down"></a>
                    <a href="#" class="fa fa-times"></a>
                </div>

                <h2 class="panel-title">Empresas</h2>
            </header>
            <div class="panel-body">
                <div class="col-md-11 row">
                    <button type="button" class="mb-xs mt-xs mr-xs btn btn-success" data-toggle="modal" data-target="#modalAddCompany"><i class="fa fa-plus-circle"></i> <i class="fa fa-institution"></i></button>
                    <button type="button" class="mb-xs mt-xs mr-xs btn btn-success" data-toggle="modal" data-target="#modalAddDealer"><i class="fa fa-plus-circle"></i> <i class="fa fa-user"></i></button>
                </div>  
                <form class="form-horizontal form-bordered" >
                @csrf
                <div class="col-md-4" style="margin-top: 1rem;">
                    <label for="company">Promotor: </label>
                    <select class="form-control form-control-sm" id="company" name="company" >
                        <option value="0" selected>Elegir...</option>
                       @foreach($companies as $company)
                        <option value="{{$company->id}}">{{$company->name}}</option>
                       @endforeach
                    </select>
                </div>
                <div class="col-md-2 mt-md">
                    <div class="radio col-md-12">
                        <label>
                            <input type="radio" name="optionRadioAsign" id="radioSIM" value="sim">
                            SIM
                        </label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="radio col-md-12 mt-md">
                        <label>
                            <input type="radio" name="optionRadioAsign" id="radioDevice" value="device">
                            Dispositivo
                        </label>
                    </div>
                </div>
                <div class="col-md-12 mt-md">
                    <div class="form-group">
                        <div class="col-md-6">
                            <div class="fileupload fileupload-new" data-provides="fileupload"><input type="hidden">
                                <div class="input-append">
                                    <div class="uneditable-input">
                                        <i class="fa fa-file fileupload-exists"></i>
                                        <span class="fileupload-preview"></span>
                                    </div>
                                    <span class="btn btn-default btn-file">
                                        <span class="fileupload-exists">Cambiar</span>
                                        <span class="fileupload-new">Selecciona un archivo</span>
                                        <input type="file" accept=".csv" id="fileToCharge">
                                    </span>
                                    <a href="#" class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remover</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-12 mt-md">
                    <button type="button" class="mb-xs mt-xs mr-xs btn btn-success" id="add" >Cargar</button>
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

        <h2 class="panel-title">Resultados</h2>
    </header>
    <div class="panel-body">
        <table class="table table-bordered table-striped mb-none" id="datatable-csv">
            <thead >
                <tr>
                <th scope="col">Línea</th>
                <th scope="col">No. Identificación</th>
                <th scope="col">Descripción</th>
                </tr>
            </thead>
            <tbody>
           
            </tbody>
        </table>
    </div>
</section>

<div class="modal fade" id="modalAddCompany" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title text-dark">Add Company</h4>
            </div>
            <div class="modal-body">
                <div class="validation-message">
                    <ul></ul>
                </div>
                <form action="{{route('companies.store')}}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Nombre</label>
                        <div class="col-sm-9">
                            <input type="text" name="name" class="form-control" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Tipo Persona</label>
                        <div class="col-sm-9">
                            <select name="type_person" class="form-control" required="">
                                <option value="moral">Moral</option>
                                <option value="fisica">Física</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Email</label>
                        <div class="col-sm-9">
                            <input type="email" name="email" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Teléfono</label>
                        <div class="col-sm-9">
                            <input type="phone" name="phone" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">RFC</label>
                        <div class="col-sm-9">
                            <input type="text" name="rfc" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Dirección</label>
                        <div class="col-sm-9">
                            <input type="text" name="address" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-12 mt-md">
                        <button type="submit" class="mb-xs mt-xs mr-xs btn btn-success" >Add</button>
                    </div> 
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAddDealer" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title text-dark">Add Distribuidor</h4>
            </div>
            <div class="modal-body">
                <div class="validation-message">
                    <ul></ul>
                </div>
                <form action="{{route('store.dealer')}}" method="POST">
                    @csrf
                    <div class="form-group" >
                        <label for="company">Compañía: </label>
                        <select class="form-control form-control-sm" id="company" name="company_id" >
                            <option value="0" selected>Elegir...</option>
                        @foreach($companies as $company)
                            <option value="{{$company->id}}">{{$company->name}}</option>
                        @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Nombre</label>
                        <div class="col-sm-9">
                            <input type="text" name="name" class="form-control" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Apellidos</label>
                        <div class="col-sm-9">
                            <input type="text" name="lastname" class="form-control" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Email</label>
                        <div class="col-sm-9">
                            <input type="email" name="email" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Password</label>
                        <div class="col-sm-9">
                            <input type="password" name="password" class="form-control">
                        </div>
                    </div>
                    
                    <div class="col-md-12 mt-md">
                        <button type="submit" class="mb-xs mt-xs mr-xs btn btn-success" >Add</button>
                    </div> 
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    $('#add').click(function(){
        let company = $('#company').val();
        let myCSV = $('#fileToCharge').val();
        let records, badRecords, goodRecords;
        
        if(company == 0){
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Por favor elige una empresa a la cual cargar el inventario.',
                showConfirmButton: false,
                timer: 2000
            });
            return false;
        }

        if(myCSV.length == 0 || /^\s+$/.test(myCSV)){
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Por favor cargue un fichero con extensión CSV.',
                showConfirmButton: false,
                timer: 2000
            });
            return false;
        }

        if(!$('input[name="optionRadioAsign"]').is(':checked')){
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Por favor elija una opción: SIM o DISPOSITIVO.',
                showConfirmButton: false,
                timer: 2000
            });
            return false;
        }

        let type = $('input[name="optionRadioAsign"]:checked').val()
        
        let file_data = $('#fileToCharge').prop('files')[0];
        let form_data = new FormData();
        form_data.append('file', file_data);
        form_data.append('type', type);
        form_data.append('company', company);

        $.ajax({
            url: "{{route('chargeCSVInventory')}}",
            dataType: 'text',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'POST',
            beforeSend: function(){
                Swal.fire({
                    title: 'Verificando los datos del fichero...',
                    html: 'Espera un poco, un poquito más...',
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function(response){
                Swal.close();
                response = JSON.parse(response);
                records = response.records;
                badRecords = response.badRecords;
                goodRecrods = response.goodRecrods;
                console.log(records);

                let numberLine, identifier, description, rows = new Array();

                records.forEach(function(e){
                    numberLine = e.number_line;
                    if(e.length == 'good'){
                        if(e.booleanExists == true){
                            identifier = "<span class='label label-success'>"+e.identifier+"</span>";
                        }else{
                            identifier = "<span class='label label-danger'>"+e.identifier+" no existe</span>";
                        }

                        if(e.description == 0){
                            description = "<span class='label label-success'>Done</span>";
                        }else{
                            description = "<span class='label label-danger'>"+e.description+"</span>";
                        }

                    }else{
                        identifier = "<span class='label label-danger'>"+e.identifier+" inválido</span>";
                        description = "<span class='label label-danger'>"+e.description+"</span>";
                    }

                    rows.push({
                        numberLine: "<span class='label label-primary'>"+numberLine+"</span>",
                        identifier: identifier,
                        description: description
                    });
                });

                var datatableInit = function() {
                    var $table = $('#datatable-csv');

                    $table.DataTable({
                        dom: 'Bfrtip',
                        destroy: true,
                        data: rows,
                        columns: [
                            {title: "Línea",data:"numberLine"},
                            {title: "No. Identificación",data:"identifier"},
                            {title: "Descripción",data:"description"}
                        ],
                    });

                };

                $(function() {
                    datatableInit();
                });
            }
        });
    });
</script>
@endsection