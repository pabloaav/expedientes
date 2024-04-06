@extends('layouts.app')

@section('content')
    <div class="content">
        <div class="row">
            <div class="col-sm-12 portlets">
                <div class="widget">
                    <div class="widget-header ">
                        <h2><strong>Vincular Personas a Documentos</strong> {{ getDominioApi() }}</h2>
                        <div class="additional-btn">
                        </div>
                    </div>
                    <div class="widget-content padding">
                        <p><b>POST</b> /documento-personas</p>

                        <p>Permite vincular personas documentos que estén creados a travéz de la API RESTful de DOCO.</p>

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
                                        <td>año_doc</td>
                                        <td>año en que se creó el documento</td>
                                        <td>SI</td>
                                        <td>Ser un campo de tipo entero</td>
                                        <td>2022</td>
                                    </tr>
                                    <tr>
                                        <td>num_doc</td>
                                        <td>número del documento a vincular</td>
                                        <td>SI</td>
                                        <td>Ser un campo de tipo entero</td>
                                        <td>30</td>
                                    </tr>
                                    <tr>
                                        <td>dni</td>
                                        <td>número de documento de la persona a vincular</td>
                                        <td>SI</td>
                                        <td>Ser un campo de tipo string</td>
                                        <td>"34397247"</td>
                                    </tr>
                                    <tr>
                                        <td>sexo</td>
                                        <td>sexo de la persona a vincular</td>
                                        <td>SI</td>
                                        <td>Ser un campo de tipo string</td>
                                        <td>"M"</td>
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
                                                                <td>número del documento a vincular</td>
                                                                <td><b>required</b></td>
                                                            </tr>
                                                            <tr>
                                                                <td>dni</td>
                                                                <td>string</td>
                                                                <td>número de documento de la persona a vincular</td>
                                                                <td><b>required</b></td>
                                                            </tr>
                                                            <tr>
                                                                <td>sexo</td>
                                                                <td>string</td>
                                                                <td>sexo de la persona a vincular</td>
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
    "num_doc": 31,
    "dni": "34397247",
    "sexo": "M"
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
    "success": "El vinculo persona documento se creo con éxito"
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