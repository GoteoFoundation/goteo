<?php

$channel=$this->channel;

$this->layout("layout", [
    'bodyClass' => 'channel home',
    'title' => $channel->name,
    'meta_description' => $channel->description
    ]);

$this->section('content');

?>
<div id="channel-main">

    <?= $this->insert("channel/partials/owner_info") ?>

    <div id="side">

    <?php foreach ($this->side_order as $sideitem => $sideitemName) {
            echo $this->insert("channel/partials/side/$sideitem");
    } ?>
    </div>

    <div id="content">
        <?= $this->supply('channel-content') ?>
    </div>
</div>

<?php $this->replace() ?>


<?php $this->section('footer') ?>
<script type="text/javascript">
    $(function(){
        $('#slides_side_sponsor').slides({
            container: 'slides_container',
            effect: 'fade',
            crossfade: false,
            fadeSpeed: 350,
            play: 5000,
            pause: 1
        });
    });
</script>
<?php $this->append() ?>
