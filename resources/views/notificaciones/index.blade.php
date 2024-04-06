@extends('layouts.app')
@section('content')
<div class="content">
    <div class="page-heading">
        <h1>
            <a href="/">
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
                        <a href="{{route('notificaciones.leidas')}}" class="btn btn-xs btn-default add-todo" style="background-color:#337ab7;color:white;"><i class="fa fa-check-square-o"></i> Notificaciones leídas</a>
                    </div>
                </div>     
                @if ($notifications->count())
                @foreach ($notifications as $notification)
                <div class="widget-content padding">
                <div class="alert alert-info fade in nomargin">
                <h4> <strong>Nueva notificación documento núm:  {{getExpedienteName($notification)}} Fecha : {{ date("d/m/Y", strtotime($notification->created_at))}} </strong></h4>
                <h5><strong> Observación : </strong> {{$notification->observacion}}  </h5>
                <h5><strong>Extracto:</strong>  {{$notification->expediente}}  </h5>
                @if ($notification->comentario_pase !== null)
                    <h5><strong>Comentarios:</strong>  {{$notification->comentario_pase}}  </h5>
                @else
                    <h5><strong>Comentarios:</strong>  No posee comentarios  </h5>
                @endif
                <p>
                 <br>
                <a href="/updatenotificaciones/{{$notification->id}}">
                <span class="label label-success">Marcar como leído</span>             
                </a> 
                 <a href="/notificaciones/redirect/{{$notification->id}}">
                <span class="label label-info">Ver documento {{getExpedienteName($notification)}} </span>             
                </a>                 
                 </div>
                </div>
                @endforeach
                @else
                <br>
                {{-- &nbsp;&nbsp;&nbsp;&nbsp;<span class="label label-primary">No posee notificaciones nuevas </span>    --}}
                <h5>&nbsp;&nbsp;&nbsp;&nbsp;<strong>No posee notificaciones nuevas</strong></h5>
                <br><br>
               @endif
               <div class="data-table-toolbar">
                {{ $notifications->links() }}
              </div>
            </div>
        </div>
    </div>
</div>
@endsection

