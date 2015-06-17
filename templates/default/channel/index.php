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
    
    <?php foreach ($this->side_order as $sideitem=>$sideitemName) {
            echo $this->insert("channel/partials/side/$sideitem");
    } ?>
    </div>

    <div id="content">
        <?php
        // primero los ocultos, los destacados si esta el buscador lateral lo ponemos anyway
        if (isset($this->side_order['searcher'])) echo $this->insert("channel/partials/home/discover");
        if (isset($this->side_order['categories'])) echo $this->insert("channel/partials/home/discat");

        foreach ($this->order as $item=>$itemName)
        {
            echo $this->insert("channel/partials/home/$item");
        }
        ?>
    </div>
</div>

<?php $this->replace() ?>

