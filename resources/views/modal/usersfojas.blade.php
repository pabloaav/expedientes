<div id="myModalUsersFojas" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><b>Usuarios digitalizadores</b></h4>
            </div>
            <div class="modal-body">
                @if (count($users) > 0)
                    <ul>
                    @foreach ($users as $user)
                        <li>{{ $user->name .' - '. $user->email}}</li>
                    @endforeach
                    </ul>
                @else
                    <p>No hay usuarios con rol para digitalizar</p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Aceptar</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->