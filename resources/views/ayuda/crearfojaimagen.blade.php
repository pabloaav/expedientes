@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="row">
            <div class="col-sm-12 portlets">
                <div class="widget">
                    <div class="widget-header ">
                        <h2><strong>Crear Fojas</strong> {{ getDominioApi() }}</h2>
                        <div class="additional-btn">
                        </div>
                    </div>
                    <div class="widget-content padding">

                        <p>Permite crear fojas para un documento de tipo imagen, PDF y texto a travéz de la API RESTful de DOCO.</p>

                        <p>Las condiciones necesarias para dicha operación son las siguientes:</p>

                        <ul>
                            <li>Token de verificación</li>
                            <li>Identificador del usuario</li>
                            <li>Número del sector</li>
                            <li>Identificador del organismo</li>
                        </ul>
                        
                        <br>
                    </div>
                    <br><br>
                    <div class="col-sm-12">
						<div class="widget widget-tabbed">
							<!-- Nav tab -->
							<ul class="nav nav-tabs nav-justified">
                              <li><a href="#user-activities" data-toggle="tab">Texto</a></li>
							  <li class="active"><a href="#my-timeline" data-toggle="tab">Imagen</a></li>
							  <li><a href="#about" data-toggle="tab">PDF</a></li>
							</ul>
							<!-- End nav tab -->

							<!-- Tab panes -->
							<div class="tab-content">
								
								
								<!-- Tab timeline -->
								<div class="tab-pane animated active fadeInRight" id="my-timeline">
									<div class="user-profile-content">

                                        <p style="font-size: 15px;"><b>POST</b> /crear-foja-imagen</p>

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
                                                        <td>content []</td>
                                                        <td>archivos a subir</td>
                                                        <td>SI</td>
                                                        <td>Deben ser del formato: .jpg, .jpeg o .png</td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td>num_documento</td>
                                                        <td>número de documento</td>
                                                        <td>SI</td>
                                                        <td>Sin validacion</td>
                                                        <td>"26"</td>
                                                    </tr>
                                                    <tr>
                                                        <td>año</td>
                                                        <td>año en que se creó el documento</td>
                                                        <td>SI</td>
                                                        <td>Ser un número entero</td>
                                                        <td>"2022"</td>
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
                                                </tbody>
                                            </table>
                                        </div>
                                        <br><br>
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
                                                                <td>content []</td>
                                                                <td>file</td>
                                                                <td>Arreglo de imágenes</td>
                                                                <td><b>required</b></td>
                                                            </tr>
                                                            <tr>
                                                                <td>num_documento</td>
                                                                <td>int</td>
                                                                <td>Número de documento</td>
                                                                <td><b>required</b></td>
                                                            </tr>
                                                            <tr>
                                                                <td>año</td>
                                                                <td>int</td>
                                                                <td>Año de creación del documento</td>
                                                                <td><b>required</b></td>
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
    "content": ['imagen/foja1.jpg','imagen/foja2.png'],
    "num_documento": 26,
    "año": 2022,
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
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div> <!-- / .tab-pane -->
                                            <div class="tab-pane" id="demo4-profile-2">
                                            <pre><code class="html">
{
    "success": "Registro creado con éxito"
}
                                            </code>
                                            </pre> 
                                            </div> <!-- / .tab-pane -->
                                        </div> <!-- / .tab-content -->
									</div><!-- End div .user-profile-content -->
								</div><!-- End div .tab-pane -->
								<!-- End Tab timeline -->
								<!-- Tab about -->
								<div class="tab-pane animated fadeInRight" id="about">
                                <div class="user-profile-content">

                                        <p style="font-size: 15px;"><b>POST</b> /crear-foja-pdf</p>

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
                                                        <td>content</td>
                                                        <td>archivos a subir</td>
                                                        <td>SI</td>
                                                        <td>Deben ser del formato PDF</td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td>num_documento</td>
                                                        <td>número de documento</td>
                                                        <td>SI</td>
                                                        <td>Sin validacion</td>
                                                        <td>"26"</td>
                                                    </tr>
                                                    <tr>
                                                        <td>año</td>
                                                        <td>año en que se creó el documento</td>
                                                        <td>SI</td>
                                                        <td>Ser un número entero</td>
                                                        <td>"2022"</td>
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
                                                </tbody>
                                            </table>
                                        </div>
                                        <br><br>
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
                                                <a href="#demo5-home" data-toggle="tab">Schema</a>
                                            </li>
                                            <li class="">
                                                <a href="#demo5-profile" data-toggle="tab">Ejemplo</a>
                                            </li>
                                        </ul>
                                        <div class="tab-content">
                                            <div class="tab-pane active in" id="demo5-home">
                                                <h5 style="margin-left: 15px;">object</h5>
                                                <div class="table-responsive" style="background-color: #f0f0f0;">
                                                    <table data-sortable class="table">
                                                        
                                                        <tbody>
                                                            <tr>
                                                                <td>content</td>
                                                                <td>file</td>
                                                                <td>Archivo en formato PDF</td>
                                                                <td><b>required</b></td>
                                                            </tr>
                                                            <tr>
                                                                <td>num_documento</td>
                                                                <td>int</td>
                                                                <td>Número de documento</td>
                                                                <td><b>required</b></td>
                                                            </tr>
                                                            <tr>
                                                                <td>año</td>
                                                                <td>int</td>
                                                                <td>Año de creación del documento</td>
                                                                <td><b>required</b></td>
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
                                            <div class="tab-pane" id="demo5-profile">
                                            <pre><code class="html">
{
    "content": 'pdf/fojas.pdf',
    "num_documento": 26,
    "año": 2022,
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
                                                <a href="#demo5-home-2" data-toggle="tab">Schema</a>
                                            </li>
                                            <li class="">
                                                <a href="#demo5-profile-2" data-toggle="tab">Ejemplo</a>
                                            </li>
                                        </ul>
                                        <div class="tab-content">
                                                <div class="tab-pane active in" id="demo5-home-2">
                                                    <h5 style="margin-left: 15px;">object</h5>
                                                    <div class="table-responsive" style="background-color: #f0f0f0;">
                                                        <table data-sortable class="table">
                                                            
                                                            <tbody>
                                                                <tr>
                                                                    <td>success</td>
                                                                    <td>string</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div> <!-- / .tab-pane -->
                                            <div class="tab-pane" id="demo5-profile-2">
                                            <pre><code class="html">
{
    "success": "Registro creado con éxito"
}
                                            </code>
                                            </pre> 
                                            </div> <!-- / .tab-pane -->
                                        </div> <!-- / .tab-content -->
									</div><!-- End div .user-profile-content -->
								</div><!-- End div .tab-pane -->
								<!-- End Tab about -->
                                <!-- Tab user activities -->
								<div class="tab-pane animated fadeInRight" id="user-activities">
                                <div class="user-profile-content">

                                    <p style="font-size: 15px;"><b>POST</b> /crear-foja-texto</p>

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
                                                        <td>content</td>
                                                        <td>texto a subir</td>
                                                        <td>SI</td>
                                                        <td>Ser formato string o HTML</td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td>num_documento</td>
                                                        <td>número de documento</td>
                                                        <td>SI</td>
                                                        <td>Sin validacion</td>
                                                        <td>"26"</td>
                                                    </tr>
                                                    <tr>
                                                        <td>año</td>
                                                        <td>año en que se creó el documento</td>
                                                        <td>SI</td>
                                                        <td>Ser un número entero</td>
                                                        <td>"2022"</td>
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
                                                </tbody>
                                            </table>
                                        </div>
                                        <br><br>
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
                                                <a href="#demo6-home" data-toggle="tab">Schema</a>
                                            </li>
                                            <li class="">
                                                <a href="#demo6-profile" data-toggle="tab">Ejemplo</a>
                                            </li>
                                        </ul>
                                        <div class="tab-content">
                                            <div class="tab-pane active in" id="demo6-home">
                                                <h5 style="margin-left: 15px;">object</h5>
                                                <div class="table-responsive" style="background-color: #f0f0f0;">
                                                    <table data-sortable class="table">
                                                        
                                                        <tbody>
                                                            <tr>
                                                                <td>content</td>
                                                                <td>string/HTML</td>
                                                                <td>Archivo en formato texto o HTML</td>
                                                                <td><b>required</b></td>
                                                            </tr>
                                                            <tr>
                                                                <td>num_documento</td>
                                                                <td>int</td>
                                                                <td>Número de documento</td>
                                                                <td><b>required</b></td>
                                                            </tr>
                                                            <tr>
                                                                <td>año</td>
                                                                <td>int</td>
                                                                <td>Año de creación del documento</td>
                                                                <td><b>required</b></td>
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
                                            <div class="tab-pane" id="demo6-profile">
                                            <pre><code class="html">
{
    "content": "Los hechos ocurrieron por la madrugada del sábado: en toda la ciudad se sintió un temblor que fue causado por un
                terremoto leve. Según los informes oficiales no hubo accidentes, nadie salió herido y no hubo ningún derrumbe.
                Muchas personas aseguran que se despertaron por el temblor, pero no se asustaron porque duró muy poco.",
    "num_documento": 26,
    "año": 2022,
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
                                                <a href="#demo6-home-2" data-toggle="tab">Schema</a>
                                            </li>
                                            <li class="">
                                                <a href="#demo6-profile-2" data-toggle="tab">Ejemplo</a>
                                            </li>
                                        </ul>
                                        <div class="tab-content">
                                                <div class="tab-pane active in" id="demo6-home-2">
                                                    <h5 style="margin-left: 15px;">object</h5>
                                                    <div class="table-responsive" style="background-color: #f0f0f0;">
                                                        <table data-sortable class="table">
                                                            
                                                            <tbody>
                                                                <tr>
                                                                    <td>success</td>
                                                                    <td>string</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div> <!-- / .tab-pane -->
                                            <div class="tab-pane" id="demo6-profile-2">
                                            <pre><code class="html">
{
    "success": "Registro creado con éxito"
}
                                            </code>
                                            </pre> 
                                            </div> <!-- / .tab-pane -->
                                        </div> <!-- / .tab-content -->
									</div><!-- End div .user-profile-content -->
								</div><!-- End div .tab-pane -->
								<!-- End Tab user activities -->
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