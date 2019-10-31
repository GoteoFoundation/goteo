<?php
$this->layout('channel/layout', [
    'title' => $this->title_text.' :: '.$this->channel->name,
    'meta_description' => $this->title_text.'. '.$this->channel->description,
    'bodyClass' => 'discover', 
    'stats' => false,
    'summary' => false
    ]);

$this->section('channel-content');

?>

<div class="section main-info">
    <div class="container">

        <h2 class="title">
            <?=$this->text('discover-searcher-header')?>
        </h2>
        <?= $this->supply('search-box', $this->insert('discover/partials/search_box', ['link' => '/channel/'.$this->channel->id])) ?>
    </div>
</div>

<?= $this->insert('discover/partials/projects_list') ?>

<?php $this->replace() ?>

<?php $this->section('footer') ?>
    <?= $this->insert('discover/partials/javascript') ?>
<?php $this->append() ?>

