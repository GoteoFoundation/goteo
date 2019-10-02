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


<link rel="stylesheet" type="text/css" href="<?= SRC_URL ?>/assets/vendor/slick-carousel/slick/slick.css"/>
<link rel="stylesheet" type="text/css" href="<?= SRC_URL ?>/assets/vendor/slick-carousel/slick/slick-theme.css"/>


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

    <?php if ($summary): ?>

    <?= $this->insert("channel/partials/sponsors_section") ?>

    <?= $this->insert("channel/partials/resources_section") ?>

    <?= $this->insert("channel/partials/stories_section") ?>

    <?= $this->insert("channel/partials/posts_section") ?>

    <?= $this->supply('channel-footer', $this->insert("channel/partials/summary_section")) ?>

    <?php endif ?>

<?php $this->replace() ?>


<?php $this->section('head') ?>
    <?= $this->insert('channel/partials/styles') ?>
<?php $this->append() ?>

<?php $this->section('footer') ?>

    <script src="<?= SRC_URL ?>/assets/vendor/slick-carousel/slick/slick.min.js"></script>

    <script>

    /*
    @licstart  The following is the entire license notice for the
    JavaScript code in this page.

    Copyright (C) 2010  Goteo Foundation

    The JavaScript code in this page is free software: you can
    redistribute it and/or modify it under the terms of the GNU
    General Public License (GNU GPL) as published by the Free Software
    Foundation, either version 3 of the License, or (at your option)
    any later version.  The code is distributed WITHOUT ANY WARRANTY;
    without even the implied warranty of MERCHANTABILITY or FITNESS
    FOR A PARTICULAR PURPOSE.  See the GNU GPL for more details.

    As additional permission under GNU GPL version 3 section 7, you
    may distribute non-source (e.g., minimized or compacted) forms of
    that code without the copy of the GNU GPL normally required by
    section 4, provided you include this license notice and a URL
    through which recipients can access the Corresponding Source.


    @licend  The above is the entire license notice
    for the JavaScript code in this page.
    */
        
        
    $('.slider-stories').slick({
        dots: true,
        infinite: true,
        speed: 1000,
        fade: true,
        arrows: true,
        cssEase: 'linear',
        prevArrow: '<div class="custom-left-arrow"><span class="fa fa-angle-left"></span><span class="sr-only">Prev</span></div>',
        nextArrow: '<div class="custom-right-arrow"><span class="fa fa-angle-right"></span><span class="sr-only">Prev</span></div>'
    });

    </script>

    <?= $this->insert('channel/partials/javascript') ?>
<?php $this->append() ?>
