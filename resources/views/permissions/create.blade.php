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
          <h2><strong>Agregar</strong> </h2>
          <div class="additional-btn">
            <a href="#" class="hidden reload"><i class="icon-ccw-1"></i></a>
            <a href="#" class="widget-toggle"><i class="icon-down-open-2"></i></a>
            <!-- <a href="#" class="widget-close"><i class="icon-cancel-3"></i></a> -->
          </div>
        </div>
        @if(session('errors')!=null && count(session('errors')) > 0)
        <div class="alert alert-danger">
          <ul>
            @foreach (session('errors') as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif
        <div class="widget-content padding">
          <div id="basic-form">
            {{ Form::open(array('route' => 'permissions.store', 'class' => 'form-group', 'permission' => 'form',  'autocomplete' => 'off')) }}
            <div class="form-group">
              <label for="exampleInputEmail1">Nombre</label>
              {{ Form::text('permiso', old('permiso'), array('class' => 'form-control', 'id' => 'name', 'name' => 'permiso', 'class' => 'form-control input-lg', 'placeholder' => 'Ingrese un nombre')) }}
            </div>

            <div class="form-group">
              <label for="exampleInputEmail1">Alcance (Scope)</label>
              {{ Form::text('scope', old('scope'), array('class' => 'form-control', 'id' => 'slug', 'name' => 'scope', 'class' => 'form-control input-lg', 'placeholder' => 'Ingrese un scope o alcance')) }}
            </div>

            <div class="form-group">
              <label for="exampleInputEmail1">Descripcion</label>
              {{ Form::text('descripcion', old('descripcion'), array('class' => 'form-control', 'id' => 'description', 'name' => 'descripcion', 'class' => 'form-control input-lg', 'placeholder' => 'Ingrese una descripcion')) }}
            </div>
            <div style="text-align: right;">

              {{ Form::submit('Crear Permiso', array('class' => 'btn btn-success float-right')) }}
            </div>
            {{ Form::close() }}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@stop