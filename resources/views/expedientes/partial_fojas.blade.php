@foreach ($fojas_preview as $foja)
    <div class="recuadroFoja" id="{{ $foja->id }}">
        @if ($foja->descripcion !== NULL)
            <label class="nombreFoja">Foja N°: {{ $foja->numero }} - {{ $foja->nombre }} - <span class="label label-danger">{{ $foja->descripcion }}</span></label>
            <img src='/fojas/{{base64_encode($foja->id)}}' alt='{{ $foja->nombre }}' name='fojasDoc' />
            <br>
        @else
            <label class="nombreFoja">Foja N°: {{ $foja->numero }} - {{ $foja->nombre }}</label>
            <img src='/fojas/{{base64_encode($foja->id)}}' alt='{{ $foja->nombre }}' name='fojasDoc' />
            <br>
        @endif
    </div>
@endforeach