<div class="call-action-section" <?php if($this->channel->owner_background) echo 'style="background-color:'.$this->channel->owner_background.'"'; ?>>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-5">
                <div class="title">
                    Únete
                </div>
                <div class="description">
                    ¿Quieres participar en el canal?
                </div>
            </div>
            <div class="col-md-3 col-md-offset-4 col-button">
                <a href="/channel/<?= $this->channel->id ?>/create" class="btn btn-white">CREAR PROYECTO</a>
            </div>
        </div>
    </div>
</div>
