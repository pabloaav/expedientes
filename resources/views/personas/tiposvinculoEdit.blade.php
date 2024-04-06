@extends('layouts.app')

@section('content')

<style>
    .col-xs-6 {
        padding-left: 15px;
        padding-top: 15px;
    }

    @media (max-width: 950px) {
        label {
        margin-left: 30px;
        }
    }
</style>

<div class="content">

    <div class="page-heading">
        <h1>
      <a href="/organismos/{{ $organismo->id }}/tiposvinculo">
        <i class='icon-home-circled'></i>
           {{ $title }}
      </a>
      </h1>       	 
    </div>

    <div class="row">
        <div class="col-md-12 portlets ui-sortable">
            <div class="widget">
                <div class="widget-header transparent">
                    @if(session('errors')!=null && count(session('errors')) > 0)
                        <div class="alert alert-danger">
                        <ul>
                            @foreach (session('errors') as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        </div>
                    @endif
                </div>
                <div class="widget-content">
                    <form method="POST" action="{{url("organismos/{$organismo->id}/tiposvinculo/update")}}">
                        {{method_field('PUT')}}
                        {!!csrf_field()!!}

                        <input type="hidden" id="organismos_id" name="organismos_id" value={{$organismo->id}}>
                        <input type="hidden" id="tipos_id" name="tipos_id" value="{{ $tiposvinculo->id }}">
                        <div class="widget">
                            <div class="widget-content padding">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <div class="row">
                                            <div class="col-xs-6">
                                                <input type="text" id="tipovinculo" name="tipovinculo" class="form-control" maxlength="45" placeholder="Nombre del tipo de vinculo *"
                                                    value="{{ old('vinculo', $tiposvinculo->vinculo) }}">
                                            </div>
                                            @if (session('permission')->contains('expediente.crearips'))
                                            <div class="col-xs-6">
                                                <label for="input-text" class="control-label" style="padding-top: 5px;"> Titular</label>
                                                <div class="col-xs-2" style="margin-top: 5px;"><input type="checkbox" class="ios-switch ios-switch-success ios-switch-sm" name="titular" id="titular"
                                                @if ($tiposvinculo->titular != 0) checked @endif /></div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <button type="submit" class="btn btn-success" style="float: right;">Editar</button>
                                    </div>
                                </div>
                    </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
@endsection