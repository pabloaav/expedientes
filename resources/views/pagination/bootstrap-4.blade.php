@if ($paginator->hasPages())
<ul class="pager">

  @if ($paginator->onFirstPage())
  <li class="disabled"><span>← Anterior</span></li>
  @else
  <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev">← Anterior</a></li>
  @endif



  @foreach ($elements as $element)

  @if (is_string($element))
  <li class="disabled"><span>{{ $element }}</span></li>
  @endif



  @if (is_array($element))
  @foreach ($element as $page => $url)
  @if ($page == $paginator->currentPage())
  <li class="active my-active"><span>{{ $page }}</span></li>
  @else
  <li><a href="{{ $url }}">{{ $page }}</a></li>
  @endif
  @endforeach
  @endif
  @endforeach



  @if ($paginator->hasMorePages())
  <li><a href="{{ $paginator->nextPageUrl() }}" rel="next">Siguiente →</a></li>
  @else
  <li class="disabled"><span>Siguiente →</span></li>
  @endif
</ul>
@endif