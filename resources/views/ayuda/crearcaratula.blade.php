@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="row">
            <div class="col-sm-12 portlets">
                <div class="widget">
                    <div class="widget-header ">
                        <h2><strong>Crear Carátula</strong> {{ getDominioApi() }}</h2>
                        <div class="additional-btn">
                        </div>
                    </div>
                    <div class="widget-content padding">
                        <p><b>POST</b> /documento</p>

                        <p>Permite crear un documento con su carátula correspondiente a travéz de la API RESTful de DOCO.</p>

                        <p>Las condiciones necesarias para dicha operación son las siguientes:</p>

                        <ul>
                            <li>Token de verificación</li>
                            <li>Identificador del usuario</li>
                            <li>Número del sector</li>
                            <li>Identificador del organismo</li>
                        </ul>
                        
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
                                        <td>usuario</td>
                                        <td>identificador del usuario</td>
                                        <td>SI</td>
                                        <td>Ser un número entero</td>
                                        <td>"4"</td>
                                    </tr>
                                    <tr>
                                        <td>sector</td>
                                        <td>número del sector</td>
                                        <td>SI</td>
                                        <td>Ser un número entero</td>
                                        <td>"3"</td>
                                    </tr>
                                    <tr>
                                        <td>importancia</td>
                                        <td>importancia del documento</td>
                                        <td>SI</td>
                                        <td>Sin validacion</td>
                                        <td>"Baja", "Media", "Alta", "Urgente"</td>
                                    </tr>
                                    <tr>
                                        <td>extracto</td>
                                        <td>extracto del documento</td>
                                        <td>SI</td>
                                        <td>Sin validacion</td>
                                        <td>"Documentos prueba con primera foja en minio 10"</td>
                                    </tr>
                                    <tr>
                                        <td>tipo_documento</td>
                                        <td>número del tipo de documento</td>
                                        <td>SI</td>
                                        <td>Ser un número entero</td>
                                        <td>"1"</td>
                                    </tr>
                                    <tr>
                                        <td>num_documento</td>
                                        <td>número del documento</td>
                                        <td>SI</td>
                                        <td>Sin validacion</td>
                                        <td>"26"</td>
                                    </tr>
                                    <tr>
                                        <td>fecha_inicio</td>
                                        <td>fecha de creación del documento</td>
                                        <td>SI</td>
                                        <td>Ser de tipo fecha</td>
                                        <td>"2022-03-15 00:00:00"</td>
                                    </tr>
                                    <tr>
                                        <td>ref_siff</td>
                                        <td>referencia SIFF</td>
                                        <td>NO</td>
                                        <td>Sin validacion</td>
                                        <td>"4432"</td>
                                    </tr>
                                    <tr>
                                        <td>organismo</td>
                                        <td>identificador del organismo</td>
                                        <td>SI</td>
                                        <td>Ser un número entero</td>
                                        <td>"2"</td>
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
                                        <td>success</td>
                                        <td>mensaje de éxito</td>
                                    </tr>
                                    <tr>
                                        <td>documento</td>
                                        <td>dirección donde se aloja el documento</td>
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
                                                                <td>usuario</td>
                                                                <td>int</td>
                                                                <td>Identificador del usuario</td>
                                                                <td><b>required</b></td>
                                                            </tr>
                                                            <tr>
                                                                <td>sector</td>
                                                                <td>int</td>
                                                                <td>Identificador del sector</td>
                                                                <td><b>required</b></td>
                                                            </tr>
                                                            <tr>
                                                                <td>importancia</td>
                                                                <td>string</td>
                                                                <td>Importancia del documento</td>
                                                                <td><b>required</b></td>
                                                            </tr>
                                                            <tr>
                                                                <td>extracto</td>
                                                                <td>string</td>
                                                                <td>Extracto del documento</td>
                                                                <td><b>required</b></td>
                                                            </tr>
                                                            <tr>
                                                                <td>tipo_documento</td>
                                                                <td>int</td>
                                                                <td>Número del tipo de documento</td>
                                                                <td><b>required</b></td>
                                                            </tr>
                                                            <tr>
                                                                <td>num_documento</td>
                                                                <td>string</td>
                                                                <td>Número del documento</td>
                                                                <td><b>required</b></td>
                                                            </tr>
                                                            <tr>
                                                                <td>fecha_inicio</td>
                                                                <td>date</td>
                                                                <td>Fecha en que se crea el documento</td>
                                                                <td><b>required</b></td>
                                                            </tr>
                                                            <tr>
                                                                <td>ref_siff</td>
                                                                <td>string</td>
                                                                <td>Referencia SIFF del documento</td>
                                                                <td>optional</td>
                                                            </tr>
                                                            <tr>
                                                                <td>organismo</td>
                                                                <td>int</td>
                                                                <td>Identificador del organismo</td>
                                                                <td><b>required</b></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div> <!-- / .tab-pane -->
                                            <div class="tab-pane" id="demo4-profile">
                                            <pre><code class="html">
{
    "usuario": 4,
    "sector": 3,
    "importancia": "Media",
    "extracto": "Documentos prueba con primera foja en minio 10",
    "tipo_documento": 1,
    "num_documento": 26,
    "fecha_inicio": "2022-03-15 00:00:00",
    "ref_siff": "4432",
    "organismo": 2
}
                                            </code>
                                            </pre> 
                                            </div> <!-- / .tab-pane -->
                                        </div> <!-- / .tab-content -->
                                        <br><br>

                                        <h4>Responses &nbsp application/json</h4>
                                        <ul id="demo4" class="nav nav-tabs nav-simple">
                                            <li class="">
                                                <a href="" data-toggle="tab"><i class="fa fa-circle" style="color: green"></i> 201</a>
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
                                                                    <td>success</td>
                                                                    <td>string</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>documento</td>
                                                                    <td>string</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div> <!-- / .tab-pane -->
                                            <div class="tab-pane" id="demo4-profile-2">
                                            <pre><code class="html">
{
    "success": "Registro creado con éxito",
    "documento": "https://sied.telco.com.ar/expediente/NTI%3D"
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