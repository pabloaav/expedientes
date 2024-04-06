@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="row">
            <div class="col-sm-12 portlets">
                <div class="widget">
                    <div class="widget-header ">
                        <h2><strong>Propiedades del Header HTTP</strong></h2>
                        <div class="additional-btn">
                        </div>
                    </div>
                    <div class="widget-content padding">
                        <p>Para poder consumir el servicio ofrecido por la API RESTful de DOCO, es necesario enviar los siguientes campos en el 'Header' HTTP.</p>

                        <p>Los campos se muestran y detallan en la tabla a continuación:</p>
                        <br>
                        
                        <div class="table-responsive" style="background-color: #f0f0f0;">
                            <table data-sortable class="table">
                                <thead>
                                    <tr>
                                        <th style="width: 110px;">Nombre</th>
                                        <th>Descripción</th>
                                        <th>Valor</th>
                                        <th>Obligatorio</th>
                                    </tr>
                                </thead>
                                
                                <tbody>
                                    <tr>
                                        <td>Accept</td>
                                        <td>Por defecto JSON es Unicode UTF-8. No se debe configurar otro charset</td>
                                        <td><span class="label label-info">application/json</span></td>
                                        <td>Si</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <br><br>
                        <p><b>Authentication: Bearer Token</b></p>

                        <p>Para cada operación de la sección de <i>Ayuda</i> es necesaria enviar por Header la propiedad Bearer Token que forma parte de la cabecera.</p>
                        <br>

                        <div class="table-responsive" style="background-color: #f0f0f0;">
                            <table data-sortable class="table">
                                <thead>
                                    <tr>
                                        <th style="width: 110px;">Nombre</th>
                                        <th>Descripción</th>
                                        <th>Obligatorio</th>
                                    </tr>
                                </thead>
                                
                                <tbody>
                                    <tr>
                                        <td>Bearer Token</td>
                                        <td>Solicitud de token de usuario</td>
                                        <td>Si</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@endsection