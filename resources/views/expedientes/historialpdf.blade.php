<style type="text/css">
  
    table.blueTable {
      border: 1px solid #0A210C;
      width: 100%;
      text-align: left;
      border-collapse: collapse;
    
    }
    table.blueTable td, table.blueTable th {
      border: 1px solid #AAAAAA;
      padding: 3px 2px;
    }
    table.blueTable tbody td {
      font-size: 13px;
    }
    table.blueTable thead {
     
    }
    table.blueTable thead th {
      font-size: 15px;
    
    
      border-left: 2px solid #D0E4F5;
    }
    table.blueTable thead th:first-child {
      border-left: none;
    }
    .page-break {
        page-break-after: always;
    }
    
     @page { size: 30cm 21cm landscape; }
    
    td.sinborde{
      border-right: hidden;
    
    }
    .circular--square {
    border-radius: 100%;
    }
    </style>
    
    
      <h3 align="center"><strong> HISTORIAL DE DOCUMENTO</strong></h3>
    <table class="blueTable" style="margin: 0 auto;">
    
    <tbody>
    <tr>
    <td align="center" rowspan="2"> 
      @if ($datosexpediente->organismos->logo == NULL)
      <img style="width:70px; height:70px;" src="{{$datosexpediente->organismos->logo}}"  class="circular--square">
      @else
      <img style="width:70px; height:70px;" src="storage/{{ $datosexpediente->organismos->logo }}"  class="circular--square">
      @endif
    </td>
    <td colspan="5" align="center" rowspan="1"><strong><u>    
        <?php
        $str = strtoupper($datosexpediente->organismos->organismo);
        echo $str; 
        ?>
       </u><br></strong></td>
    </tr>
      <tr rowspan="1">
                      <td cellspacing="2" bgcolor="#F4F6F6"><strong>Localidad: {{$localidad->localidad}}  </strong></td>
                      <td colspan="4" bgcolor="#F4F6F6"> <strong>Fecha hoy:  {{$fecha_actual}}  </strong></td>
                </tr>
                <tr>
                    <td align="center" bgcolor="#F4F6F6"><strong>Doc. Nº </strong></td>
                    <td align="center" colspan="5" ><strong>{{getExpedienteName($datosexpediente)}}</strong>
                    </td>
                   
                </tr>
                  <tr>
                      <td align="center" bgcolor="#F4F6F6"><strong>Tipo de Documento </strong></td>
                      <td align="center" colspan="5" ><strong>{{$datosexpediente->expedientetipo->expedientestipo}}</strong>
                      </td>
                     
                  </tr>
                   <tr>
                      <td align="center" bgcolor="#F4F6F6"><strong>Extracto</strong></td>
                      <td align="center" colspan="5" ><strong>{{$datosexpediente->expediente}}</strong></td>
                  </tr>
                  <tr>
                       <td align="center" bgcolor="#F4F6F6"><strong>Creado por:</strong></td>
                       <td align="center"> @foreach ($estados_exp as $estados) 
                        @if ($estados->expendientesestado == 'nuevo' && $estados->users !== NULL)
                         {{$estados->users->name}}
                        @elseif ($estados->expendientesestado == 'nuevo' && $estados->users == NULL)
                          API Service
                        @endif 
                         @endforeach </td>
                       <td align="center" bgcolor="#F4F6F6"><strong>Fecha: <br></strong></td>
                       <td align="center">{{ date("d/m/Y", strtotime($datosexpediente->created_at))}}</td>
                       <td align="center" bgcolor="#F4F6F6"><strong>A las </strong></td>
                       <td align="center"> {{ $datosexpediente->created_at->format('H:i A') }}</td>
                  </tr>
                  <!-- detalles historial -->
                   <tr>
                      <td align="center"  bgcolor="#F4F6F6"><strong>Historial</strong></td>
                       <td colspan="5" align="center"  bgcolor="#F4F6F6"><strong>Detalles</strong></td>
                  </tr>
                         
                   @foreach ($estados_exp as $estados)
                        
                  <tr>
                     <td colspan="1" align="center">{{ date("d/m/Y", strtotime($estados->created_at))}} : {{ $estados->created_at->format('H:i A') }}</td>
                                 <td colspan="5">{{ $estados->observacion }}</td>
                           
                    
                    </tr>
                     @endforeach 
            
    
                
                        
    </tbody>
    </table>