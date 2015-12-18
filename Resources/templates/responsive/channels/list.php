<?php
$this->layout('layout', [
    'bodyClass' => '',
    'title' =>  'Canales :: Goteo.org',
    'meta_description' => "Canales de Goteo"
    ]);

$this->section('content');

?>

<div class="container">

     <div class="jumbotron channels">
        <h1>Canales de Goteo</h1>
        <p>Te presentamos a las instituciones que apoyan a nuestra plataforma con un canal propio</p>
    </div>

    <div class="channels-container" >
            <?php foreach($this->channels as $IdChannel => $channel): ?>
            <?php if(!($IdChannel%4)):  ?>
                <div class="row channels">
                <?php endif ?>
                    <div class="col-sm-6 col-md-3 channel">
                        <a class="channel-link" href="/channel/<?= $channel->id ?>">                        
                            <img class="img-responsive" src="<?php echo $channel->home_img->getLink(250, 250, true); ?>">
                            <div class="name">
                                <?= $channel->name ?>
                            </div>
                            <div class="projects">
                                <img class="eye" src="/view/channel/img/eye.png"><?= $this->text('home-channel-project', $channel->summary['projects']) ?>
                            </div>
                        </a>
                    </div>
            <?php if(($IdChannel&&!($IdChannel%3))||$IdChannel==count($this->channels)-1):  ?>
                </div>
            <?php endif ?>
            <?php endforeach ?>
    </div>

</div>


<?php $this->replace() ?>