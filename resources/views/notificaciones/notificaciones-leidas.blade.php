@extends('layouts.app')
@section('content')
<div class="content">
    <div class="page-heading">
        <h1>
            <a href="/notificaciones">
                <i class='icon icon-left-circled'></i>
                {{ $title }}
            </a>
        </h1>
    </div>
    <div class="row">
        <div class="col-md-12 portlets ui-sortable">
            <div class="widget">
                <div class="widget-header ">
                    <h2></h2>
                    <div class="additional-btn">
                        <a href="#" class="hidden reload"><i class="icon-ccw-1"></i></a>
                        <a href="#" class="widget-toggle"><i class="icon-down-open-2"></i></a>
                        <!-- <a href="#" class="widget-close"><i class="icon-cancel-3"></i></a> -->
                    </div>
                </div>       
                @if ($notifications->count())
                @foreach ($notifications as $notification)
                <div class="widget-content padding">
                <div class="alert alert-info fade in nomargin">
                <h4> <strong>Documento núm: {{getExpedienteName($notification)}}  -  Fecha : {{ date("d/m/Y", strtotime($notification->created_at))}} </strong></h4>
                <h5><strong> Observación : </strong> {{$notification->observacion}}  </h5>
                <h5><strong>Extracto:</strong>  {{$notification->expediente}}  </h5>
                @if ($notification->comentario_pase !== null)
                    <h5><strong>Comentarios:</strong>  {{$notification->comentario_pase}}  </h5>
                @else
                    <h5><strong>Comentarios:</strong>  No posee comentarios  </h5>
                @endif
                <p>
                 <br>
                 <a href="{{route('expediente.show', base64_encode($notification->expedientes_id))}}">
                <span class="label label-info">Ver documento {{getExpedienteName($notification)}} </span>             
                </a>                 
                 </div>
                </div>
                @endforeach
                @else
                &nbsp;&nbsp;&nbsp;&nbsp;<span class="label label-primary">No posee notificaciones nuevas </span>   
               @endif
               <div class="data-table-toolbar">
                {{ $notifications->links() }}
              </div>
            </div>
        </div>
    </div>
</div>
@endsection
