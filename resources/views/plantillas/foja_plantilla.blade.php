@extends('layouts.app')
@section('content')

  <div class="page-heading">
    <h1>
      <a  href="{{ URL::previous() }}">
        <i class='icon icon-left-circled'></i>
        {{ $title }}
      </a>
    </h1> 	
  </div>

  {{-- Imprimir errores de validacion --}}
  @if(session('errors')!=null && count(session('errors')) > 0)
  <div class="alert alert-danger">
    <ul>
      @foreach (session('errors') as $error)
      <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
  @endif

  	{{-- notificacion en pantalla  --}}
    @if(session('error'))
    <div class="alert alert-danger alert-dismissable">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
     <center>{{ session('error') }} </center> <a href="#" class="alert-link"></a>.
    </div>
    @endif 

    <div class="box-info box-messages animated fadeInDown">
      <div class="row">
        <div class="col-md-3">
          <!-- Sidebar Message -->

          <div class="btn-group new-message-btns stacked">
            <a href="inbox.html" class="btn btn-primary btn-lg btn-block text-left"><i class="icon-left-open-1"></i> Volver a documentos </a>
          </div>
          <div class="list-group menu-message">
            <a href="inbox.html" class="list-group-item"><i class="icon-inbox"></i> Inbox <span class="badge pull-right">4</span></a>
            <a href="#fakelink" class="list-group-item"><i class="icon-pencil"></i> Draft <span class="badge bg-green-1 pull-right">1</span></a>
            <a href="#fakelink" class="list-group-item"><i class="icon-star"></i> Important <span class="badge bg-red-1 pull-right">2</span></a>
          </div>
        </div><!-- ENd div .col-md-2 -->
        
        <div class="col-md-9">
          <div class="widget">
            <div class="widget-content padding">
              <form role="form" class="form-horizontal">
                <div class="form-group">
                  <label class="control-label col-sm-1 col-xs-1">To:</label>
                  <div class="col-sm-10 col-xs-8">
                    <input type="text" class="form-control input-invis" placeholder="someone@company.com">
                  </div>
                  <div class="col-sm-1 col-xs-3 text-right">
                    <div class="btn-group">
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-sm-12">
                    @include("fojas/foja_plantilla")
                  </div>
                </div>
                <div class="row">
                  <div class="col-xs-8">
                    <button type="submit" class="btn btn-success"></i>Guardar</button>
                  </div>
                </div>	
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>




@section('scripts')
    <script src="/js/expedientes/pase.js"> </script>
@endsection

@endsection




