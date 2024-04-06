@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="row">
            <div class="col-sm-12 portlets">
                <div class="widget">
                    <div class="widget-header ">
                        <h2><strong>Solicitud de Token</strong> {{ getDominioApi() }}</h2>
                        <div class="additional-btn">
                        </div>
                    </div>
                    <div class="widget-content padding">
                        <p><b>POST</b> /login</p>

                        <p>La solicitud de token es el primer paso para ejecutar el logueo y acceder a la API RESTful de DOCO.</p>

                        <p>Ésto es necesario para:</p>

                        <ul>
                            <li>Consultar Sectores por usuario</li>
                            <li>Consultar Tipos de Documentos</li>
                            <li>Crear Carátula</li>
                            <li>Crear Fojas de tipo texto, imagen y PDF</li>
                            <li>Consultar Documentos</li>
                            <li>Vincular personas a un Documento</li>
                        </ul>
                        
                        <br>
                        <p><b>Post:</b></p>

                        <div class="table-responsive" style="background-color: #f0f0f0;">
                            <table data-sortable class="table">
                                <thead>
                                    <tr>
                                        <th style="width: 100px;">Campo</th>
                                        <th style="width: 200px;">Descripción</th>
                                        <th style="width: 100px;">Oblig</th>
                                        <th>Restricciones</th>
                                        <th>Ejemplo</th>
                                    </tr>
                                </thead>
                                
                                <tbody>
                                    <tr>
                                        <td>username</td>
                                        <td>nombre de usuario</td>
                                        <td>SI</td>
                                        <td>Correo válido que contenga un "@"</td>
                                        <td>"lucas.gil@telco.com.ar"</td>
                                    </tr>
                                    <tr>
                                        <td>password</td>
                                        <td>clave del usuario</td>
                                        <td>SI</td>
                                        <td>Debe estar compuesta por letras mayúsculas, minúsculas, caracteres especiales, números y tener al menos 8 caracteres</td>
                                        <td>"DoCo2022@"</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <br>

                        <p><b>Response:</b></p>

                        <div class="table-responsive" style="background-color: #f0f0f0">
                            <table data-sortable class="table">
                                <thead>
                                    <tr>
                                        <th>Campo</th>
                                        <th>Descripción</th>
                                    </tr>
                                </thead>
                                
                                <tbody>
                                    <tr>
                                        <td>token</td>
                                        <td>eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczpcL1wvc2llZC50ZWxjby5jb20uYXJcL2FwaVwvbG9naW4iLCJpYXQiOjE2NDcyODA2ODcsImV4cCI6MTY0NzI4NDI4NywibmJmIjoxNjQ3MjgwNjg3LCJqdGkiOiJDRlFpNHpHQ01rVklCa0JRIiwic3ViIjoyOCwicHJ2IjoiODdlMGFmMWVmOWZkMTU4MTJmZGVjOTcxNTNhMTRlMGIwNDc1NDZhYSIsIm9yZ2FuaXNtbyI6MiwidXN1YXJpbyI6MjgsImVtYWlsIjoibHVjYXMuZ2lsQHRlbGNvLmNvbS5hciJ9.Z2WcTol4NUTF8HtW2CcIGbLYNA4yW5QkyiiizHdw0w8</td>
                                    </tr>
                                    <tr>
                                        <td>organismo</td>
                                        <td>2</td>
                                    </tr>
                                    <tr>
                                        <td>usuario</td>
                                        <td>4</td>
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
                                                                <td>username</td>
                                                                <td>string</td>
                                                                <td>Correo del usuario<br></td>
                                                                <td><b>required</b></td>
                                                            </tr>
                                                            <tr>
                                                                <td>password</td>
                                                                <td>string</td>
                                                                <td>Contraseña del usuario</td>
                                                                <td><b>required</b></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div> <!-- / .tab-pane -->
                                            <div class="tab-pane" id="demo4-profile">
                                            <pre><code class="html">
{
    "username": "lucas.gil@telco.com.ar",
    "password": "DoCo2022@"
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
                                                                    <td>token</td>
                                                                    <td>string</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>organismo</td>
                                                                    <td>int</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>usuario</td>
                                                                    <td>int</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div> <!-- / .tab-pane -->
                                            <div class="tab-pane" id="demo4-profile-2">
                                            <pre><code class="html">
{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczpcL1wvc2llZC50ZWxjby5jb20uYXJcL2FwaVwvbG9naW4iLCJpYXQiOjE2NDczN
              DExNDcsImV4cCI6MTY0NzM0NDc0NywibmJmIjoxNjQ3MzQxMTQ3LCJqdGkiOiJFc1Z3M2U0MzVZakxJT1FxIiwic3ViIjoyOCwicHJ2IjoiODdlMGFmMWV
              mOWZkMTU4MTJmZGVjOTcxNTNhMTRlMGIwNDc1NDZhYSIsIm9yZ2FuaXNtbyI6MiwidXN1YXJpbyI6MjgsImVtYWlsIjoibHVjYXMuZ2lsQHRlbGNvLmNvb
              S5hciJ9.wIHnve3-AiXF8YI0nrgjF9N0tXS3U1-gFwt7W852CIM"
    "organismo": 2,
    "usuario": 4
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