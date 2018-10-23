
<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Elección de Jugabilidad</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>  
      <div class="modal-body">
       <button type="button" class="btn btn-dangerous"  data-dismiss="modal">Cancelar</button>&nbsp&nbsp
        <button type="button" class="btn btn-success" onclick="setTipoJuego(0)" >Multiplayer</button> &nbsp&nbsp
        
        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">Jugador Automatico
        </button>
            <div class="dropdown-menu">
                <button class="dropdown-item" onclick="setTipoJuego(11)">Fácil</button>
                <button class="dropdown-item" onclick="setTipoJuego(12)">Medio</button>
                <button class="dropdown-item" onclick="setTipoJuego(13)">Dificil</button>
            </div>
      </div> 
     <!-- <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div> -->
    </div> 
  </div>
</div>
<!-- Modal -->
