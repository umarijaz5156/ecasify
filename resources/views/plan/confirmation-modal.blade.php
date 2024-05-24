
<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">Confirmation</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    {{ $msg }}
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
    <a href="{{ route('upgradePlan',$code) }}" class="btn btn-primary" id="upgrade-button">{{ $btnTxt }}</a>
</div>

