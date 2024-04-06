<style>
.vertical-center {
  
  position: relative;
  top: 50%;
  -ms-transform: translateY(-25%);
  transform: translateY(-25%);
}
</style>

<div class="tab-pane animated fadeInRight active" id="mymessage">
    <div class="scroll-user-widget">
        <ul class="media-list">
        
            @if ($plantillas->count())
            @php($vinculos = 0)
            @foreach ($plantillas as $plantilla)
            @if($plantilla->activo == 1 and strstr($plantilla->plantilla,("Borrador " . $userLogin->email)))
            @php($vinculos += 1)
          <li class="media">
            <a class="pull-left vertical-center"href="/plantillas/{{ $plantilla->id }}/fojas/{{ $expediente->id }}/expediente">
              <img class="media-object" src="/assets/img/plantilla.jpg" >
            </a>
            <a class="pull-right vertical-center desabilitar-borrador" style="cursor: pointer" valor="{{ $plantilla->id }}" title="Deshabilitar">
              <i class="fa fa-trash-o fa-2x fa-lg text-danger"></i> 
            </a>
            <div class="media-body vertical-center">
              <h4 class="media-heading"><a href="/plantillas/{{ $plantilla->id }}/fojas/{{ $expediente->id }}/expediente">Fecha creación:</a> <small>{{ date("d/m/Y", strtotime($plantilla->created_at))}}</small></h4>
              <h5> <a href="/plantillas/{{ $plantilla->id }}/fojas/{{ $expediente->id }}/expediente"> Título de la plantilla : {{$plantilla->plantilla}}</a></h5>
             
            </div>
           
          </li>  
          @endif
          @endforeach
          @if  ($vinculos == 0)
          <li class="media">
            <div style="text-align:center">
              No tiene borradores en el sector
            </div>
          </li>  
          @endif
          @else
          <li class="media">
            <div style="text-align:center">
              Aún no existen plantillas/borradores en el sector
            </div>
          </li>  
          @endif
        </ul>
    </div><!-- End div .scroll-user-widget -->
</div>

<script>
$('.desabilitar-borrador').click(function(e){
      e.preventDefault();
      
      var borrador_id = $(this).attr('valor')

      Swal.fire({
        title:'Vas a deshabilitar el borrador',
        text: "¿Está seguro?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí',
        cancelButtonText:'No, cancelar'
      }).then((result) => {
        if (result.isConfirmed) {

        $.ajax({
          url: '/plantillas/' + borrador_id + '/estado',
          method:"GET",
          success: function(response) {
                  setInterval(location.reload(true),1500);
               
          },
          error: function (jqXHR, exception) {
            console.log(jqXHR);
            // Your error handling logic here..
            }
        });

        }
      })
    }); 
  </script>