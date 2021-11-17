<!doctype html>
<html class="fixed">

<head>

    <!-- Basic -->
    <meta charset="UTF-8">

    <title>Altcel II</title>
    <meta name="keywords" content="HTML5 Admin Template" />
    <meta name="description" content="JSOFT Admin - Responsive HTML5 Template">
    <meta name="author" content="JSOFT.net">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Mobile Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

    <link rel="shortcut icon" type="image/png" href="{{asset('images/Altcel2_Ok@2x.png')}}">

    <!-- Web Fonts  -->
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800|Shadows+Into+Light"
        rel="stylesheet" type="text/css">

    <!-- Vendor CSS -->
    <link rel="stylesheet" href="{{asset('octopus/assets/vendor/bootstrap/css/bootstrap.css')}}" />
    <link rel="stylesheet" href="{{asset('octopus/assets/vendor/font-awesome/css/font-awesome.css')}}" />
    <link rel="stylesheet" href="{{asset('octopus/assets/vendor/magnific-popup/magnific-popup.css')}}" />
    <link rel="stylesheet" href="{{asset('octopus/assets/vendor/bootstrap-datepicker/css/datepicker3.css')}}" />
    <!-- Specific Page Vendor CSS -->
	<link rel="stylesheet" href="{{asset('octopus/assets/vendor/bootstrap-fileupload/bootstrap-fileupload.min.css')}}" />

    <!-- Specific Page Vendor CSS -->
    <link rel="stylesheet" href="{{asset('octopus/assets/vendor/jquery-ui/css/ui-lightness/jquery-ui-1.10.4.custom.css')}}" />
    <link rel="stylesheet" href="{{asset('octopus/assets/vendor/bootstrap-multiselect/bootstrap-multiselect.css')}}" />
    <link rel="stylesheet" href="{{asset('octopus/assets/vendor/morris/morris.css')}}" />
    <link rel="stylesheet" href="{{asset('octopus/assets/vendor/pnotify/pnotify.custom.css')}}" />
    <link rel="stylesheet" href="{{asset('octopus/assets/vendor/bootstrap-colorpicker/css/bootstrap-colorpicker.css')}}" />

    <!-- Estilos Calendar -->
    <link rel="stylesheet" href="{{asset('octopus/assets/vendor/fullcalendar/fullcalendar.css')}}" />
    <link rel="stylesheet" href="{{asset('octopus/assets/vendor/fullcalendar/fullcalendar.print.css')}}" media="print" />

    <link rel="stylesheet" href="{{asset('octopus/assets/vendor/select2/select2.css')}}" />
	<link rel="stylesheet" href="{{asset('octopus/assets/vendor/jquery-datatables-bs3/assets/css/datatables.css')}}" />
    <!-- Theme CSS -->
    <link rel="stylesheet" href="{{asset('octopus/assets/stylesheets/theme.css')}}" />

    <!-- Skin CSS -->
    <link rel="stylesheet" href="{{asset('octopus/assets/stylesheets/skins/default.css')}}" />

    <!-- Theme Custom CSS -->
    <link rel="stylesheet" href="{{asset('octopus/assets/stylesheets/theme-custom.c')}}ss">
        
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <!-- Head Libs -->
    <script src="{{asset('octopus/assets/vendor/modernizr/modernizr.js')}}"></script>
    <script src="{{asset('octopus/assets/vendor/jquery/jquery.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js"></script>
    <!-- <script src="{{ asset('js/app.js') }}" defer></script> -->

    <style>
    .HBB {
        background-color:#7d23fa !important; color: white !important;
    }
    .MIFI {
        background-color:#ffe352 !important; color: #33353f !important;
    }
    .MOV {
        background-color:#fa5025 !important; color: white !important;
    }
    .Telmex {
        background-color:#14a2fa !important; color: white !important;
    }
    .Conecta {
        background-color:#44d581 !important; color: white !important;
    }

    .not-action {
        pointer-events: none; 
    }

    .list-alert {
        float: left !important;
        margin-left: 4rem;
    }
        .d-none{
            display: none !important;
        }
        @media screen and (min-width: 768px) {
            #dataT_filter {
                display: flex;
                justify-content: flex-end;
            }
            #dataR_filter {
                display: flex;
                justify-content: flex-end;
            }
            #dataP_filter {
                display: flex;
                justify-content: flex-end;
            }
            #dataC_filter {
                display: flex;
                justify-content: flex-end;
            }
            #dataServices_filter {
                display: flex;
                justify-content: flex-end;
            }
        }

        .title-3 {
        margin-bottom: 10px;
        font-size: 15px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .opps {
        width: 496px; 
        border-radius: 4px;
        box-sizing: border-box;
        padding: 0 45px;
        margin: 40px auto;
        overflow: hidden;
        border: 1px solid #b0afb5;
        font-family: 'Open Sans', sans-serif;
        color: #4f5365;
    }

    .opps-reminder {
        position: relative;
        top: -1px;
        padding: 9px 0 10px;
        font-size: 11px;
        text-transform: uppercase;
        text-align: center;
        color: #ffffff;
        background: #000000;
    }

    .opps-info {
        margin-top: 26px;
        position: relative;
    }

    .opps-info:after {
        visibility: hidden;
        display: block;
        font-size: 0;
        content: " ";
        clear: both;
        height: 0;

    }

    .opps-brand {
        width: 45%;
        float: left;
    }

    .opps-brand img {
        max-width: 150px;
        margin-top: 2px;
    }

    .opps-ammount {
        width: 55%;
        float: right;
    }

    .opps-ammount h2 {
        font-size: 36px;
        color: #000000;
        line-height: 24px;
        margin-bottom: 15px;
    }

    .opps-ammount h2 sup {
        font-size: 16px;
        position: relative;
        top: -2px
    }

    .opps-ammount p {
        font-size: 10px;
        line-height: 14px;
    }

    .opps-reference {
        margin-top: 14px;
    }

    .referenceOxxoCard {
        font-size: 27px;
        color: #000000;
        text-align: center;
        margin-top: -1px;
        padding: 6px 0 7px;
        border: 1px solid #b0afb5;
        border-radius: 4px;
        background: #f8f9fa;
    }

    .opps-instructions {
        margin: 32px -45px 0;
        padding: 32px 45px 45px;
        border-top: 1px solid #b0afb5;
        background: #f8f9fa;
    }

    .instructions {
        margin: 17px 0 0 16px;
    }

    .search-oxxo {
        color: #1155cc;
    }

    .opps-footnote {
        margin-top: 22px;
        padding: 22px 20 24px;
        color: #108f30;
        text-align: center;
        border: 1px solid #108f30;
        border-radius: 4px;
        background: #ffffff;
    }
    </style>
</head>

<body>
    <section class="body">

        <!-- start: header -->
        <header class="header">
            <div class="logo-container">
                <a href="{{route('home')}}" class="logo">
                    <img src="{{asset('images/Altcel2_Ok@2x.png')}}" height="35" alt="JSOFT Admin" />
                </a>
                <div class="visible-xs toggle-sidebar-left" data-toggle-class="sidebar-left-opened" data-target="html"
                    data-fire-event="sidebar-left-opened">
                    <i class="fa fa-bars" aria-label="Toggle sidebar"></i>
                </div>
            </div>

            <!-- start: search & user box -->
            <div class="header-right">

                </form>

                <span class="separator"></span>
                <!-- Notificaciones -->
                <ul class="notifications">
                    <li>
                        <a href="#" class="dropdown-toggle notification-icon" data-toggle="dropdown">
                            <i class="fa fa-bell"></i>
                            <span class="badge">3</span>
                        </a>

                        <div class="dropdown-menu notification-menu">
                            <div class="notification-title">
                                <span class="pull-right label label-default">3</span>
                                Alerts
                            </div>

                            <div class="content">
                                <ul>
                                    <li>
                                        <a href="#" class="clearfix">
                                            <div class="image">
                                                <i class="fa fa-thumbs-down bg-danger"></i>
                                            </div>
                                            <span class="title">Server is Down!</span>
                                            <span class="message">Just now</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="clearfix">
                                            <div class="image">
                                                <i class="fa fa-lock bg-warning"></i>
                                            </div>
                                            <span class="title">User Locked</span>
                                            <span class="message">15 minutes ago</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="clearfix">
                                            <div class="image">
                                                <i class="fa fa-signal bg-success"></i>
                                            </div>
                                            <span class="title">Connection Restaured</span>
                                            <span class="message">10/10/2014</span>
                                        </a>
                                    </li>
                                </ul>

                                <hr />

                                <div class="text-right">
                                    <a href="#" class="view-more">View All</a>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
                <!-- Final Notificaciones -->

                <span class="separator"></span>
                @guest
                @else
                    @php
                        $cargo =  \App\Role::where('id',Auth::user()->role_id)->first();
                        $cargo = $cargo->rol
                    @endphp
                @endguest

                <div id="userbox" class="userbox">
                    <a href="#" data-toggle="dropdown">
                        <figure class="profile-picture">
                            <img src="{{asset('octopus/assets/images/!logged-user.jpg')}}" alt="Joseph Doe" class="img-circle"
                                data-lock-picture="assets/images/!logged-user.jpg" />
                        </figure>
                        <div class="profile-info" data-lock-name="John Doe" data-lock-email="johndoe@JSOFT.com">
                            <span class="name">{{Auth::user()->name.' '.Auth::user()->lastname}}</span>
                            <span class="role">Perfil: {{$cargo}}</span>
                        </div>

                        <i class="fa custom-caret"></i>
                    </a>

                    <!-- Dropdown del usuario -->
                    <div class="dropdown-menu">
                        <ul class="list-unstyled">
                            <li class="divider"></li>
                            <li>
                                <a role="menuitem" tabindex="-1" href="{{ route('myProfile') }}"><i
                                        class="fa fa-user"></i> Mi Perfil</a>
                            </li>
                            
                            <li>
                                <a role="menuitem" tabindex="-1" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();"><i
                                        class="fa fa-power-off"></i> Logout</a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                    </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- end: search & user box -->
        </header>
        <!-- end: header -->

        <div class="inner-wrapper">
            <!-- start: sidebar -->
            <aside id="sidebar-left" class="sidebar-left">

                <div class="sidebar-header">
                    <div class="sidebar-title text-info">
                        Navegación
                    </div>
                    <div class="sidebar-toggle hidden-xs" data-toggle-class="sidebar-left-collapsed" data-target="html"
                        data-fire-event="sidebar-left-toggle">
                        <i class="fa fa-bars" aria-label="Toggle sidebar"></i>
                    </div>
                </div>
                <!-- Barra lateral izquierda -->
                <div class="nano">
                    <div class="nano-content">
                        <nav id="menu" class="nav-main" role="navigation">
                            <ul class="nav nav-main">
                                <li class="nav-active">
                                    <a href="{{route('home')}}">
                                        <i class="fa fa-home" aria-hidden="true"></i>
                                        <span>Dashboard</span>
                                    </a>
                                </li>
                                @if(Auth::user()->role_id == 1 && Auth::user()->id != 1)
                                    <li class="nav-parent">
                                        <a>
                                            <i class="fa fa-plus-circle" aria-hidden="true"></i>
                                            <span>Clientes</span>
                                        </a>
                                        <ul class="nav nav-children">
                                            <li>
                                                <a href="{{route('activations.create')}}">Nuevo</a>
                                            </li>
                                            <li>
                                                <a href="{{route('clients-pay-all.get')}}"> Ver</a>
                                            </li>
                                            <li>
                                                <a href="{{route('clients.index')}}">Resumen</a>
                                            </li>
                                            <li>
                                                <a href="{{route('prospects.index')}}">Prospectos</a>
                                            </li>
                                            <li>
                                                <a href="{{route('operations.specials')}}">Operaciones Especiales</a>
                                            </li>
                                            <li>
                                                <a href="{{route('preactivations.index')}}">Preactivaciones</a>
                                            </li>
                                            <li>
                                                <a href="{{route('reports')}}">Reportes de Consumos</a>
                                            </li>
                                            <li>
                                                <a href="{{route('reportMoney')}}">Reportes de Dinero</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-parent">
                                        <a>
                                            <i class="fa fa-money" aria-hidden="true"></i>
                                            <span>Pagos</span>
                                        </a>
                                        <ul class="nav nav-children">
                                            <li>
                                                <a href="{{route('activations.index')}}">Resumen</a>
                                            </li>
                                            <li>
                                                <a href="{{route('webhook-openpay.get')}}">Completados</a>
                                            </li>
                                            <li>
                                                <a href="{{route('webhook-payments-pending.get')}}">Pendientes</a>
                                            </li>
                                            <li>
                                                <a href="{{route('webhook-payments-overdue.get')}}">Vencidos</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-parent">
                                        <a>
                                            <i class="fa fa-book" aria-hidden="true"></i>
                                            <span>Administración</span>
                                        </a>
                                        <ul class="nav nav-children">
                                            <li>
                                                <a href="{{route('show-users.get')}}">Usuarios</a>
                                            </li>
                                            <li class="nav-parent">
                                                <a>Productos</a>
                                                <ul class="nav nav-children">
                                                    <li class="nav-parent">
                                                        <a>Ofertas Altán</a>
                                                        <ul class="nav nav-children">
                                                            <li>
                                                                <a href="{{route('offers.index')}}">Ver</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{route('offers.create')}}">Crear</a>
                                                            </li>
                                                        </ul>
                                                    </li>
                                                    <li class="nav-parent">
                                                        <a>Planes Altán</a>
                                                        <ul class="nav nav-children">
                                                            <li>
                                                                <a href="{{route('rates.index')}}">Ver</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{route('rates.create')}}">Crear</a>
                                                            </li>
                                                        </ul>
                                                    </li>
                                                    <li class="nav-parent">
                                                        <a>Promociones</a>
                                                        <ul class="nav nav-children">
                                                            <li>
                                                                <a href="{{route('promotion.index')}}">Ver</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{route('promotion.create')}}">Crear</a>
                                                            </li>
                                                        </ul>
                                                    </li>
                                                    <li>
                                                        <a href="{{route('ethernet-admin.get')}}">Internet</a>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li>
                                                <a href="{{route('politicRate.create')}}">Políticas</a>
                                            </li>
                                            <li>
                                                <a href="{{route('devices.index')}}">Dispositivos</a>
                                            </li>
					                        <li class="nav-parent">
                                                <a>Facturación</a>
                                                <ul class="nav nav-children">
                                                    <li>
                                                        <a href="{{route('facturacion.index')}}">Facturar</a>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li class="nav-parent">
                                                <a>Promotores</a>
                                                <ul class="nav nav-children">
                                                    <li>
                                                        <a href="{{route('promoters.get')}}">Ver</a>
                                                    </li>
                                                </ul>
                                            </li>

                                            <!-- <li >
                                                <a href="{{route('dealer.index')}}">Distribuidores</a>
                                            </li> -->
                                        </ul>
                                    </li>

                                    <li class="nav-parent">
                                        <a>
                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                            <span>Agenda</span>
                                        </a>
                                        <ul class="nav nav-children">
                                            <li>
                                                <a href="{{route('schedules.create')}}">Alta</a>
                                            </li>
                                            <li>
                                                <a href="{{route('schedules.index')}}">Administración</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-parent">
                                        <a>
                                            <i class="fa fa-paper-plane" aria-hidden="true"></i>
                                            <span>Solicitudes</span>
                                        </a>
                                        <ul class="nav nav-children">
                                            <li>
                                                <a href="{{route('solicitudes')}}">Nuevas</a>
                                            </li>
                                            <li>
                                                <a href="{{route('completadas')}}">Completadas</a>
                                            </li>
                                        </ul>
                                    </li>
                                @elseif(Auth::user()->id == 1 || Auth::user()->id == 110)
                                    <li class="nav-parent">
                                        <a>
                                            <i class="fa fa-plus-circle" aria-hidden="true"></i>
                                            <span>Clientes</span>
                                        </a>
                                        <ul class="nav nav-children">
                                            <li>
                                                <a href="{{route('activations.create')}}">Nuevo</a>
                                            </li>
                                            <li>
                                                <a href="{{route('clients-pay-all.get')}}"> Ver</a>
                                            </li>
                                            <li>
                                                <a href="{{route('clients.index')}}">Resumen</a>
                                            </li>
                                            <li>
                                                <a href="{{route('prospects.index')}}">Prospectos</a>
                                            </li>
                                            <li>
                                                <a href="{{route('operations.specials')}}">Operaciones Especiales</a>
                                            </li>
                                            <li>
                                                <a href="{{route('preactivations.index')}}">Preactivaciones</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-parent">
                                        <a>
                                            <i class="fa fa-paper-plane" aria-hidden="true"></i>
                                            <span>Solicitudes</span>
                                        </a>
                                        <ul class="nav nav-children">
                                            <li>
                                                <a href="{{route('solicitudes')}}">Nuevas</a>
                                            </li>
                                            <li>
                                                <a href="{{route('completadas')}}">Completadas</a>
                                            </li>
                                        </ul>
                                    </li>
                                @elseif(Auth::user()->role_id == 2)
                                    <li class="nav-parent">
                                        <a>
                                            <i class="fa fa-plus-circle" aria-hidden="true"></i>
                                            <span>Clientes</span>
                                        </a>
                                        <ul class="nav nav-children">
                                            <li>
                                                <a href="{{route('activations.create')}}">Nuevo</a>
                                            </li>
                                            <li>
                                                <a href="{{route('clients-pay-all.get')}}">Ver</a>
                                            </li>
                                            <li>
                                                <a href="{{route('activations.index')}}">Resumen</a>
                                            </li>
                                            <li>
                                                <a href="{{route('prospects.index')}}">Prospectos</a>
                                            </li>
                                            <li>
                                                <a href="{{route('operations.specials')}}">Operaciones Especiales</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-parent">
                                        <a>
                                            <i class="fa fa-tasks" aria-hidden="true"></i>
                                            <span>Reportes</span>
                                        </a>
                                        <ul class="nav nav-children">
                                            <li>
                                                <a href="{{route('webhook-openpay.get')}}">Pagos</a>
                                            </li>
                                        </ul>
                                    </li>

                                    <li class="nav-parent">
                                        <a>
                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                            <span>Agenda</span>
                                        </a>
                                        <ul class="nav nav-children">
                                            <li>
                                                <a href="{{route('schedules.create')}}">Alta</a>
                                            </li>
                                            <li>
                                                <a href="{{route('schedules.index')}}">Administración</a>
                                            </li>
                                        </ul>
                                    </li>
                                @elseif(Auth::user()->role_id == 4)
                                    <li class="nav-parent">
                                        <a>
                                            <i class="fa fa-plus-circle" aria-hidden="true"></i>
                                            <span>Clientes</span>
                                        </a>
                                        <ul class="nav nav-children">
                                            <li>
                                                <a href="{{route('activations.create')}}">Nuevo</a>
                                            </li>
                                            <li>
                                                <a href="{{route('clients-pay-all.get')}}">Ver</a>
                                            </li>
                                            <li>
                                                <a href="{{route('clients.index')}}">Resumen</a>
                                            </li>
                                            <li>
                                                <a href="{{route('prospects.index')}}">Prospectos</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-parent">
                                        <a>
                                            <i class="fa fa-book" aria-hidden="true"></i>
                                            <span>Administración</span>
                                        </a>
                                        <ul class="nav nav-children">
                                            <li class="nav-parent">
                                                <a href="#">Productos</a>
                                                <ul class="nav nav-children">
                                                    <li class="nav-parent">
                                                        <a>Ofertas Altán</a>
                                                        <ul class="nav nav-children">
                                                            <li>
                                                                <a href="{{route('offers.index')}}">Ver</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{route('offers.create')}}">Crear</a>
                                                            </li>
                                                        </ul>
                                                    </li>
                                                    <li class="nav-parent">
                                                        <a>Planes Altán</a>
                                                        <ul class="nav nav-children">
                                                            <li>
                                                                <a href="{{route('rates.index')}}">Ver</a>
                                                            </li>
                                                        </ul>
                                                    </li>
                                                    <li>
                                                        <a href="{{route('ethernet-admin.get')}}">Internet</a>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li>
                                                <a href="{{route('politicRate.create')}}">Políticas</a>
                                            </li>
                                            <li>
                                                <a href="{{route('devices.index')}}">Dispositivos</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-parent">
                                        <a>
                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                            <span>Agenda</span>
                                        </a>
                                        <ul class="nav nav-children">
                                            <li>
                                                <a href="{{route('schedules.index')}}">Administración</a>
                                            </li>
                                        </ul>
                                    </li>
                                    
                                    <li class="nav-parent">
                                        <a>
                                            <i class="fa fa-money" aria-hidden="true"></i>
                                            <span>Pagos</span>
                                        </a>
                                        <ul class="nav nav-children">
                                            <li>
                                                <a href="{{route('activations.index')}}">Resumen</a>
                                            </li>
                                            <li>
                                                <a href="{{route('webhook-openpay.get')}}">Completados</a>
                                            </li>
                                            <li>
                                                <a href="{{route('webhook-payments-pending.get')}}">Pendientes</a>
                                            </li>
                                            <li>
                                                <a href="{{route('webhook-payments-overdue.get')}}">Vencidos</a>
                                            </li>
                                        </ul>
                                    </li>
                                    
                                @elseif(Auth::user()->role_id == 5)
                                    <li class="nav-parent">
                                        <a>
                                            <i class="fa fa-plus-circle" aria-hidden="true"></i>
                                            <span>Clientes</span>
                                        </a>
                                        <ul class="nav nav-children">
                                            <li>
                                                <a href="{{route('activations.index')}}">Resumen</a>
                                            </li>
                                            <li>
                                                <a href="{{route('clients-pay-all.get')}}">Ver</a>
                                            </li>
                                            <li>
                                                <a href="{{route('prospects.index')}}">Prospectos</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-parent">
                                        <a>
                                            <i class="fa fa-book" aria-hidden="true"></i>
                                            <span>Administración</span>
                                        </a>
                                        <ul class="nav nav-children">
                                            <li class="nav-parent">
                                                <a href="#">Productos</a>
                                                <ul class="nav nav-children">
                                                    <li class="nav-parent">
                                                        <a>Ofertas Altán</a>
                                                        <ul class="nav nav-children">
                                                            <li>
                                                                <a href="{{route('offers.index')}}">Ver</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{route('offers.create')}}">Crear</a>
                                                            </li>
                                                        </ul>
                                                    </li>
                                                    <li class="nav-parent">
                                                        <a>Planes Altán</a>
                                                        <ul class="nav nav-children">
                                                            <li>
                                                                <a href="{{route('rates.index')}}">Ver</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{route('rates.create')}}">Crear</a>
                                                            </li>
                                                        </ul>
                                                    </li>
                                                    <li>
                                                        <a href="{{route('ethernet-admin.get')}}">Internet</a>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li>
                                                <a href="{{route('politicRate.create')}}">Políticas</a>
                                            </li>
                                            <li>
                                                <a href="{{route('devices.index')}}">Dispositivos</a>
                                            </li>
                                            <li class="nav-parent">
                                                        <a href="#">Facturación</a>
                                                        <ul class="nav nav-children">
                                                            <li>
                                                                <a href="{{route('facturacion.index')}}">Facturar</a>
                                                            </li>
                                                        </ul>
                                                    </li>
                                        </ul>
                                    </li>
                                    <li class="nav-parent">
                                        <a>
                                            <i class="fa fa-money" aria-hidden="true"></i>
                                            <span>Pagos</span>
                                        </a>
                                        <ul class="nav nav-children">
                                            <li>
                                                <a href="{{route('activations.index')}}">Resumen</a>
                                            </li>
                                            <li>
                                                <a href="{{route('webhook-openpay.get')}}">Completados</a>
                                            </li>
                                            <li>
                                                <a href="{{route('webhook-payments-pending.get')}}">Pendientes</a>
                                            </li>
                                            <li>
                                                <a href="{{route('webhook-payments-pending.get')}}">Vencidos</a>
                                            </li>
                                            <li>
                                                <a href="{{route('indexConcesiones')}}">Por Concesionar</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-parent">
                                        <a>
                                            <i class="fa fa-paper-plane" aria-hidden="true"></i>
                                            <span>Solicitudes</span>
                                        </a>
                                        <ul class="nav nav-children">
                                            <li>
                                                <a href="{{route('completadas')}}">Nuevas</a>
                                            </li>
                                            <li>
                                                <a href="{{route('recibidos')}}">Completadas</a>
                                            </li>
                                        </ul>
                                    </li>
                                    
                                @elseif(Auth::user()->role_id == 6)
                                    <li class="nav-parent">
                                        <a>
                                            <i class="fa fa-users" aria-hidden="true"></i>
                                            <span>Clientes</span>
                                        </a>
                                        <ul class="nav nav-children">
                                            <li>
                                                <a href="{{route('clients-pay-all.get')}}">Ver</a>
                                            </li>
                                            <li>
                                                <a href="{{route('activations.index')}}">Resumen</a>
                                            </li>
                                            <li>
                                                <a href="{{route('prospects.index')}}">Prospectos</a>
                                            </li>
                                            <li>
                                                <a href="{{route('operations.specials')}}">Operaciones Especiales</a>
                                            </li>
                                            <li>
                                                <a href="{{route('preactivations.index')}}">Preactivaciones</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-parent">
                                        <a>
                                            <i class="fa fa-plus-circle" aria-hidden="true"></i>
                                            <span>Activaciones</span>
                                        </a>
                                        <ul class="nav nav-children">
                                            <li>
                                                <a href="{{route('activations.create')}}">Nuevo</a>
                                            </li>
                                            <li>
                                                <a href="{{route('activations.index')}}">Ver</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-parent">
                                        <a>
                                            <i class="fa fa-book" aria-hidden="true"></i>
                                            <span>Administración</span>
                                        </a>
                                        <ul class="nav nav-children">
                                            <li>
                                                <a href="{{route('show-users.get')}}">Usuarios</a>
                                            </li>
                                            <li class="nav-parent">
                                                <a>Productos</a>
                                                <ul class="nav nav-children">
                                                    <li class="nav-parent">
                                                        <a>Ofertas Altán</a>
                                                        <ul class="nav nav-children">
                                                            <li>
                                                                <a href="{{route('offers.index')}}">Ver</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{route('offers.create')}}">Crear</a>
                                                            </li>
                                                        </ul>
                                                    </li>
                                                    <li class="nav-parent">
                                                        <a>Planes Altán</a>
                                                        <ul class="nav nav-children">
                                                            <li>
                                                                <a href="{{route('rates.index')}}">Ver</a>
                                                            </li>
                                                            <li>
                                                                <a href="{{route('rates.create')}}">Crear</a>
                                                            </li>
                                                        </ul>
                                                    </li>
                                                    <li>
                                                        <a href="{{route('ethernet-admin.get')}}">Internet</a>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li>
                                                <a href="{{route('politicRate.create')}}">Políticas</a>
                                            </li>
                                            <li>
                                                <a href="{{route('devices.index')}}">Dispositivos</a>
                                            </li>
					                        <li class="nav-parent">
                                                <a>Facturación</a>
                                                <ul class="nav nav-children">
                                                    <li>
                                                        <a href="{{route('facturacion.index')}}">Facturar</a>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li class="nav-parent">
                                                <a>Promotores</a>
                                                <ul class="nav nav-children">
                                                    <li>
                                                        <a href="{{route('promoters.get')}}">Ver</a>
                                                    </li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-parent">
                                        <a>
                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                            <span>Agenda</span>
                                        </a>
                                        <ul class="nav nav-children">
                                            <li>
                                                <a href="{{route('schedules.create')}}">Alta</a>
                                            </li>
                                            <li>
                                                <a href="{{route('schedules.index')}}">Administración</a>
                                            </li>
                                        </ul>
                                    </li>
                                @elseif(Auth::user()->role_id == 7)
                                    <li class="nav-parent">
                                        <a>
                                            <i class="fa fa-plus-circle" aria-hidden="true"></i>
                                            <span>Clientes</span>
                                        </a>
                                        <ul class="nav nav-children">
                                            <li>
                                                <a href="{{route('clients.create')}}">Nuevo</a>
                                            </li>
                                        </ul>
                                    </li>
                                @endif
                                
                            </ul>
                        </nav>
                    </div>

                </div>
                <!-- Final Barra lateral izquierda -->

            </aside>
            <!-- end: sidebar -->

            <section role="main" class="content-body">
                

                <!-- start: page -->
                

                @yield('content')

                <!-- end: page -->
            </section>
        </div>

    </section>
    <!-- Vendor -->
    <script src="{{asset('octopus/assets/vendor/jquery/jquery.js')}}"></script>
    <script src="{{asset('octopus/assets/vendor/jquery-browser-mobile/jquery.browser.mobile.js')}}"></script>
    <script src="{{asset('octopus/assets/vendor/bootstrap/js/bootstrap.js')}}"></script>
    <script src="{{asset('octopus/assets/vendor/nanoscroller/nanoscroller.js')}}"></script>
    <script src="{{asset('octopus/assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js')}}"></script>
    <script src="{{asset('octopus/assets/vendor/magnific-popup/magnific-popup.js')}}"></script>
    <script src="{{asset('octopus/assets/vendor/jquery-placeholder/jquery.placeholder.js')}}"></script>

    <!-- Examples -->
    <script src="{{asset('octopus/assets/javascripts/dashboard/examples.dashboard.js')}}"></script>

    <!-- Specific Page Vendor -->
    <script src="{{asset('octopus/assets/vendor/bootstrap-fileupload/bootstrap-fileupload.min.js')}}"></script>
    <script src="{{asset('octopus/assets/vendor/bootstrap-colorpicker/js/bootstrap-colorpicker.js')}}"></script>
    
    <!-- Librerías DataTable -->
    <script src="{{asset('octopus/assets/vendor/select2/select2.js')}}"></script>
    <script src="{{asset('octopus/assets/vendor/jquery-datatables/media/js/jquery.dataTables.js')}}"></script>
    <script src="{{asset('octopus/assets/vendor/jquery-datatables/extras/TableTools/js/dataTables.tableTools.min.js')}}"></script>
    <script src="{{asset('octopus/assets/vendor/jquery-datatables-bs3/assets/js/datatables.js')}}"></script>

    <!-- Librerías Callendar -->
    <script src="{{asset('octopus/assets/vendor/jquery-ui/js/jquery-ui-1.10.4.custom.js')}}"></script>
    <script src="{{asset('octopus/assets/vendor/jquery-ui-touch-punch/jquery.ui.touch-punch.js')}}"></script>
    <script src="{{asset('octopus/assets/vendor/fullcalendar/lib/moment.min.js')}}"></script>
    <script src="{{asset('octopus/assets/vendor/fullcalendar/fullcalendar.js')}}"></script>

    <!-- Theme Base, Components and Settings -->
    <script src="{{asset('octopus/assets/javascripts/theme.js')}}"></script>

    <!-- Theme Custom -->
    <script src="{{asset('octopus/assets/javascripts/theme.custom.js')}}"></script>

    <!-- Theme Initialization Files -->
    <script src="{{asset('octopus/assets/javascripts/theme.init.js')}}"></script>
    <!-- Examples -->
    <script src="{{asset('octopus/assets/javascripts/tables/examples.datatables.default.js')}}"></script>
    

</body>

</html>