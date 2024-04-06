@extends('layouts.app')

@section('content')

<div class="content">
  <!-- Page Heading Start -->
  <div class="page-heading">
    <h1>
      <a href="/permissions">
        <i class='fa fa-table'></i>
        {{ $title }}
      </a>
    </h1>
    <!-- <h3>Basic & Simple Sortable Tables</h3>            	 -->
  </div>
  <div class="row">
    <div class="col-sm-12 portlets">
      <div class="widget">
        <div class="widget-header transparent">
          <h2><strong>Editar</strong> </h2>
          <div class="additional-btn">
            <a href="#" class="hidden reload"><i class="icon-ccw-1"></i></a>
            <a href="#" class="widget-toggle"><i class="icon-down-open-2"></i></a>
            <!-- <a href="#" class="widget-close"><i class="icon-cancel-3"></i></a> -->
          </div>
        </div>
        @if(session()->has('error'))
        <div class="alert alert-danger"><center>{{session('error')}}</center>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>  
        @endif
        @if (session('status'))
        <div class="alert alert-success">
          {{ session('status') }}
          <?php session(['status' => '']); ?>
        </div>
        @endif
        <div class="widget-content padding">
          <div id="basic-form">
            {{ Form::open(array('url' => URL::to('permissions/' . $permiso['Id']), 'method' => 'PUT', 'class' => 'form-group', 'permission' => 'form')) }}
            <input name="id" type="hidden" value={{$permiso['Id']}}>
            <div class="form-group">
              <label for="exampleInputEmail1">Nombre</label>
              {{ Form::text('permiso', $permiso['Permiso'], array('class' => 'form-control', 'id' => 'name', 'name' => 'permiso', 'class' => 'form-control input-lg', 'placeholder' => 'Ingrese Nombre')) }}
            </div>

            <div class="form-group">
              <label for="exampleInputEmail1">Alcance o Scope</label>
              {{ Form::text('scope', $permiso['Scope'], array('class' => 'form-control', 'id' => 'slug', 'name' => 'scope', 'class' => 'form-control input-lg', 'placeholder' => 'Ingrese Scope')) }}
            </div>

            <div class="form-group">
              <label for="exampleInputEmail1">Descripcion</label>
              {{ Form::text('descriction', $permiso['Descripcion'], array('class' => 'form-control', 'id' => 'description', 'name' => 'descripcion', 'class' => 'form-control input-lg', 'placeholder' => 'Ingrese Descripcion')) }}
            </div>

            {{ Form::submit('Modificar', array('class' => 'btn btn-primary ml-auto')) }}
            {{ Form::close() }}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@stop