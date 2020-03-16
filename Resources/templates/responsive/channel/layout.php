<?php
$premium = $this->channel->premium;

$values = [
    'bodyClass' => 'channel',
    'premium' => $premium,
    'title' =>  $this->text('regular-channel').' '.$this->channel->name,
    'meta_description' => $this->channel->description,
    'tw_image' =>  $this->channel->logo ? $this->channel->logo->getlink(300,0, false, true) : '',
];

if ($premium) {
    $values['premium'] = $premium;
    $values['background'] = $this->channel->owner_background;
    $values['call_for_action_background'] = $call_for_action_background;
    $values['powered'] = true;
} else {
    $values['navClass'] = 'white';
}

$this->layout('layout', $values);

$this->section('head');

?>


<?= $this->insert('channel/partials/styles') ?>

<?php

$this->append();

$this->section('content');

$summary = ($this->summary) ? $this->summary: false;

$background = $this->channel->owner_background;
?>

    <div class="heading-section">
        <div class="owner-section"<?php if($background) echo ' style="background-color:' . $background . '"'; ?>>
            <?php if ($premium): ?>
                <?= $this->insert("channel/partials/owner_info_premium") ?>
            <?php else: ?>
                <?= $this->insert("channel/partials/owner_info") ?>
            <?php endif ?>
        </div>

        <?= $this->supply('channel-header', $this->insert("channel/partials/join_action", ['main_color' => $background])) ?>

    </div>
    
    <div class="projects-section">
        <div class="container-fluid">
            <div id="content">
                <?= $this->supply('channel-content') ?>
            </div>

        </div>
    </div>

<?php if(!$this->discover_module): ?>

<?= $this->insert("channel/partials/sponsors_section") ?>

<?= $this->insert("channel/partials/resources_section") ?>

<?= $this->insert("channel/partials/stories_section") ?>

<?= $this->insert("channel/partials/posts_section") ?>

<?= $this->insert("channel/partials/related_workshops") ?>

<?= $this->supply('channel-footer', $this->insert("channel/partials/summary_section")) ?>

<?php endif; ?>

<?php if (isset($this->channel->iframe)): ?>
    <section class="influence-map">
        <div class="container">
            <h2 class="title"><?= $this->text('node-iframe-title') ?></h2>
            <div class="map-container">
                <iframe src="<?= $this->channel->iframe ?>" width="100%" height="500" style="border:none;" allowfullscreen></iframe>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php $this->replace() ?>




<?php $this->section('footer') ?>
    <?= $this->insert('channel/partials/javascript') ?>
<?php $this->append() ?>
