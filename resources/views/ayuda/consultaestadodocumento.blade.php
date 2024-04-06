@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="row">
            <div class="col-sm-12 portlets">
                <div class="widget">
                    <div class="widget-header ">
                        <h2><strong>Consultar el Estado de un documento</strong> {{ getDominioApi() }}</h2>
                        <div class="additional-btn">
                        </div>
                    </div>
                    <div class="widget-content padding">
                        <p><b>POST</b> /estado-documento</p>

                        <p>Permite consultar el estado de un documento a travéz de la API RESTful de DOCO.</p>

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
                                        <td>"2"</td>
                                    </tr>
                                    <tr>
                                        <td>año_doc</td>
                                        <td>año en que se creó el documento</td>
                                        <td>SI</td>
                                        <td>Ser un número entero</td>
                                        <td>"2022"</td>
                                    </tr>
                                    <tr>
                                        <td>num_doc</td>
                                        <td>número del documento</td>
                                        <td>SI</td>
                                        <td>Ser un número entero</td>
                                        <td>"9"</td>
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
                                        <td>estado</td>
                                        <td>estado actual del documento</td>
                                    </tr>
                                    <tr>
                                        <td>recorrido</td>
                                        <td>trayectoria del documento</td>
                                    </tr>
                                    <tr>
                                        <td>sector</td>
                                        <td>nombre del sector donde se encuentra el documento</td>
                                    </tr>
                                    <tr>
                                        <td>fecha_ingreso_sector</td>
                                        <td>fecha y hora de ingreso al sector</td>
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
                                                                <td>año_doc</td>
                                                                <td>int</td>
                                                                <td>año de creación del documento</td>
                                                                <td><b>required</b></td>
                                                            </tr>
                                                            <tr>
                                                                <td>num_doc</td>
                                                                <td>int</td>
                                                                <td>número del documento</td>
                                                                <td><b>required</b></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div> <!-- / .tab-pane -->
                                            <div class="tab-pane" id="demo4-profile">
                                            <pre><code class="html">
{
    "organismo": 2,
    "año_doc" : 2022,
    "num_doc": 9
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
                                                                    <td>num_doc</td>
                                                                    <td>string</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>extracto</td>
                                                                    <td>string</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>fecha_inicio</td>
                                                                    <td>string</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>importancia</td>
                                                                    <td>string</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>estado</td>
                                                                    <td>string</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>recorrido</td>
                                                                    <td>object</td>
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
    "num_doc": "9",
    "extracto": "Ejemplo",
    "fecha_inicio": "2022-03-14 00:00:00",
    "importancia": "Media",
    "estado": "nuevo",
    "recorrido": [
        {
            "sector": "Dirección de Coordinación del Sistema Provincial de Planificación",
            "fecha_ingreso_sector": "2021-12-06 09:55:42"
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