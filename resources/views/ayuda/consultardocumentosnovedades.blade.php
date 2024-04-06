@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="row">
            <div class="col-sm-12 portlets">
                <div class="widget">
                    <div class="widget-header ">
                        <h2><strong>Consultar Documentos con novedades</strong> {{ getDominioApi() }}</h2>
                        <div class="additional-btn">
                        </div>
                    </div>
                    <div class="widget-content padding">
                        <p><b>POST</b> /documentos-novedades</p>

                        <p>Permite consultar documentos por un estado determinado y que estén en un rango de fechas determinado y que no hayan sido reportados como "leidos" a travéz de la API RESTful de DOCO.</p>

                        <p>El token de verificación es una condición necesaria para dicha operación.</p>
                        
                        <br>
                        <p><b>Post:</b></p>

                        <div class="table-responsive" style="background-color: #f0f0f0;">
                            <table data-sortable class="table">
                                <thead>
                                    <tr>
                                        <th>Campo</th>
                                        <th>Descripción</th>
                                        <th style="width: 100px;">Oblig</th>
                                        <th>Restricciones</th>
                                        <th>Ejemplo</th>
                                    </tr>
                                </thead>
                                
                                <tbody>
                                    <tr>
                                        <td>organismo</td>
                                        <td>identificador del organismo</td>
                                        <td>SI</td>
                                        <td>Ser un número entero</td>
                                        <td>2</td>
                                    </tr>
                                    <tr>
                                        <td>fecha_desde</td>
                                        <td>fecha inicio para el filtrado</td>
                                        <td>SI</td>
                                        <td>Ser un campo de tipo fecha</td>
                                        <td>"2022-03-01"</td>
                                    </tr>
                                    <tr>
                                        <td>fecha_hasta</td>
                                        <td>fecha fin para el filtrado</td>
                                        <td>SI</td>
                                        <td>Ser un campo de tipo fecha</td>
                                        <td>"2022-03-17"</td>
                                    </tr>
                                    <tr>
                                        <td>filtro</td>
                                        <td>estado actual de los documentos para el filtrado</td>
                                        <td>NO</td>
                                        <td>Ser un campo de tipo string</td>
                                        <td>"nuevo", "pasado", "procesando", "archivado", "anulado", "fusionado"</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <br>

                        <p><b>Response:</b></p>

                        <div class="table-responsive" style="background-color: #f0f0f0;">
                            <table data-sortable class="table">
                                <thead>
                                    <tr>
                                        <th>Campo</th>
                                        <th>Descripción</th>
                                    </tr>
                                </thead>
                                
                                <tbody>
                                    <tr>
                                        <td>identificador</td>
                                        <td>identificador del documento</td>
                                    </tr>
                                    <tr>
                                        <td>num_doc</td>
                                        <td>número del documento</td>
                                    </tr>
                                    <tr>
                                        <td>extracto</td>
                                        <td>nombre del documento</td>
                                    </tr>
                                    <tr>
                                        <td>fecha_inicio</td>
                                        <td>fecha en que se creó el documento</td>
                                    </tr>
                                    <tr>
                                        <td>importancia</td>
                                        <td>nivel de importancia del documento</td>
                                    </tr>
                                    <tr>
                                        <td>estado_actual</td>
                                        <td>estado actual del documento</td>
                                    </tr>
                                    <tr>
                                        <td>sector_actual</td>
                                        <td>sector actual del documento</td>
                                    </tr>
                                    <tr>
                                        <td>usuario_actual</td>
                                        <td>datos del usuario que posee el documento</td>
                                    </tr>
                                    <tr>
                                        <td>id</td>
                                        <td>identificador del usuario que posee el documento</td>
                                    </tr>
                                    <tr>
                                        <td>email</td>
                                        <td>email del usuario que posee el documento</td>
                                    </tr>
                                    <tr>
                                        <td>recorrido</td>
                                        <td>arreglo que contiene los sectores por los que pasó el documento</td>
                                    </tr>
                                    <tr>
                                        <td>sector</td>
                                        <td>sector por el que pasó el documento</td>
                                    </tr>
                                    <tr>
                                        <td>fecha_ingreso_sector</td>
                                        <td>fecha y hora en que fue pasado a ese sector</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <br><br>
                    <div class="col-sm-12">
						<div class="widget widget-tabbed">
							<!-- Nav tab -->
							<ul class="nav nav-tabs nav-justified">
							  <li class="active"><a href="#my-timeline" data-toggle="tab">Definición</a></li>
							  <!-- <li><a href="#about" data-toggle="tab">Try it out</a></li> -->
							</ul>
							<!-- End nav tab -->

							<!-- Tab panes -->
							<div class="tab-content">
								
								
								<!-- Tab timeline -->
								<div class="tab-pane animated active fadeInRight" id="my-timeline">
									<div class="user-profile-content">
                                        <h4>Headers</h4>
                                            <div class="table-responsive" style="background-color: #f0f0f0;">
                                                <table data-sortable class="table">
                                                    
                                                    <tbody>
                                                        <tr>
                                                            <td>Accept</td>
                                                            <td>1 validación<br>
                                                                <b>Valores permitidos:</b> application/json</td>
                                                            <td><b>required</b></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <br><br>
                                        <h4>Request Body &nbsp application/json</h4>
                                        <ul id="demo4" class="nav nav-tabs nav-simple">
                                            <li class="active">
                                                <a href="#demo4-home" data-toggle="tab">Schema</a>
                                            </li>
                                            <li class="">
                                                <a href="#demo4-profile" data-toggle="tab">Ejemplo</a>
                                            </li>
                                        </ul>
                                        <div class="tab-content">
                                            <div class="tab-pane active in" id="demo4-home">
                                                <h5 style="margin-left: 15px;">object</h5>
                                                <div class="table-responsive" style="background-color: #f0f0f0;">
                                                    <table data-sortable class="table">
                                                        
                                                        <tbody>
                                                            <tr>
                                                                <td>organismo</td>
                                                                <td>int</td>
                                                                <td>identificador del organismo</td>
                                                                <td><b>required</b></td>
                                                            </tr>
                                                            <tr>
                                                                <td>fecha_desde</td>
                                                                <td>date</td>
                                                                <td>fecha inicio para el filtrado</td>
                                                                <td><b>required</b></td>
                                                            </tr>
                                                            <tr>
                                                                <td>fecha_hasta</td>
                                                                <td>date</td>
                                                                <td>fecha fin para el filtrado</td>
                                                                <td><b>required</b></td>
                                                            </tr>
                                                            <tr>
                                                                <td>filtro</td>
                                                                <td>string</td>
                                                                <td>estado actual para el filtrado</td>
                                                                <td>optional</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div> <!-- / .tab-pane -->
                                            <div class="tab-pane" id="demo4-profile">
                                            <pre><code class="html">
{
    "organismo": 2,
    "fecha_desde": "2022-09-29",
    "fecha_hasta": "2022-10-13",
    "filtro": "pasado"
}
                                            </code>
                                            </pre> 
                                            </div> <!-- / .tab-pane -->
                                        </div> <!-- / .tab-content -->
                                        <br><br>

                                        <h4>Responses &nbsp application/json</h4>
                                        <ul id="demo4" class="nav nav-tabs nav-simple">
                                            <li class="">
                                                <a href="" data-toggle="tab"><i class="fa fa-circle" style="color: green"></i> 200</a>
                                            </li>
                                            <li class="active">
                                                <a href="#demo4-home-2" data-toggle="tab">Schema</a>
                                            </li>
                                            <li class="">
                                                <a href="#demo4-profile-2" data-toggle="tab">Ejemplo</a>
                                            </li>
                                        </ul>
                                        <div class="tab-content">
                                                <div class="tab-pane active in" id="demo4-home-2">
                                                    <h5 style="margin-left: 15px;">object</h5>
                                                    <div class="table-responsive" style="background-color: #f0f0f0;">
                                                        <table data-sortable class="table">
                                                            
                                                            <tbody>
                                                                <tr>
                                                                    <td>identificador</td>
                                                                    <td>int</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>num_doc</td>
                                                                    <td>string</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>extracto</td>
                                                                    <td>string</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>fecha_inicio</td>
                                                                    <td>date</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>importancia</td>
                                                                    <td>string</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>estado_actual</td>
                                                                    <td>string</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>sector_actual</td>
                                                                    <td>string</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>usuario_actual</td>
                                                                    <td>object</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>id</td>
                                                                    <td>int</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>email</td>
                                                                    <td>string</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>recorrido</td>
                                                                    <td>array</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>sector</td>
                                                                    <td>string</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>fecha_ingreso_sector</td>
                                                                    <td>string</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div> <!-- / .tab-pane -->
                                            <div class="tab-pane" id="demo4-profile-2">
                                            <pre><code class="html">
{
    "data": [
        {
            "identificador": 207,
            "num_doc": "115572",
            "extracto": "Causas judiciales",
            "fecha_inicio": "2022-03-04 00:00:00",
            "importancia": "Media",
            "estado_actual": "nuevo",
            "sector_actual": "Administración",
            "usuario_actual": {
                "id": 4,
                "email": "lucas.gil@telco.com.ar"
            },
            "recorrido": [
                {
                    "sector": "Ingeniería Vial",
                    "fecha_ingreso_sector": "2022-03-04 15:49:09"
                }
            ]
        },
        {
            "identificador": 208,
            "num_doc": "115573",
            "extracto": "Solicitudes",
            "fecha_inicio": "2022-03-08 00:00:00",
            "importancia": "Media",
            "estado_actual": "procesando",
            "sector_actual": "Administración",
            "usuario_actual": {
                "id": 4,
                "email": "lucas.gil@telco.com.ar"
            },
            "recorrido": []
        }
    ]
}
                                            </code>
                                            </pre> 
                                            </div> <!-- / .tab-pane -->
                                        </div> <!-- / .tab-content -->
									</div><!-- End div .user-profile-content -->
								</div><!-- End div .tab-pane -->
								<!-- End Tab timeline -->
								
							</div><!-- End div .tab-content -->
						</div><!-- End div .box-info -->
					</div>
                </div>
            </div>
         </div>
    </div>
@endsection

@section('scripts')
@endsection