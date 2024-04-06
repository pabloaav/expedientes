@extends('layouts.app')

@section('content')

<style>
    .contenedor_org {
        overflow-x: auto;
        padding: 20px;
        margin-bottom: 40px;
        background-color: #fff;
        border-radius: 2px;
        color:black;
    }

</style>

<link rel="stylesheet" href="/assets/autocomplete/jquery-ui.css">
<script src="/assets/autocomplete/jquery-1.9.1.js"></script>
<script src="/assets/autocomplete/jquery-ui.js"></script>
<!-- Importacion de libreria Google Chart para crear el organigrama -->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<!-- </head> -->
<div class="content">

    <div class="page-heading">
        <h1>
            <a  href="{{ url()->previous() }}">
            <i class='icon icon-left-circled'></i>
                {{ $title }}
            </a>
        </h1>

    </div>
    
    <div class="col-md-12 contenedor_org">
            <div class="widget-content">
                <div id="chart_div">
                    <input id="chart" type="hidden" value="{{$organismo_chart}}">
                </div>
            </div>
    </div>
    
</div>

<script src="/js/organismossector/organismo_chart.js"></script>

<script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>
<script type="text/javascript" src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.10.0/localization/messages_es.js"></script>
@endsection