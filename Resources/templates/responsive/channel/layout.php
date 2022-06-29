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
    $values['navLogo'] = 'black';
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

<?= $this->insert("channel/partials/related_workshops") ?>

<?php endif; ?>

<?php if($this->channel->show_team): ?>

<?= $this->insertif('foundation/donor') ?>

<?php endif; ?>

<?= $this->insert("channel/partials/posts_section") ?>

<?= $this->insert("channel/partials/map") ?>

<?= $this->supply('channel-footer', $this->insert("channel/partials/summary_section")) ?>

<?php if($this->channel->terms): ?>

<!-- Modal -->
<div class="modal fade" id="termsModal" tabindex="-1" role="dialog" aria-labelledby="termsModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title"><?= $this->text('matcher-terms-desc') ?></h3>
      </div>
      <div class="modal-body"><?= $this->markdown($this->channel->terms) ?></div>
    </div>
  </div>
</div>

<?php endif; ?>

<?php $this->replace() ?>




<?php $this->section('footer') ?>
    <?= $this->insert('channel/partials/javascript') ?>

    <?php if($this->channel->show_team): ?>

    <script>
    $(function(){

        $('.slider-team').slick({
            dots: false,
            autoplay: true,
            infinite: true,
            speed: 2000,
            autoplaySpeed: 3000,
            fade: true,
            arrows: false,
            cssEase: 'linear'
        });
    });
    </script>

    <?php endif; ?>

    <?php if($this->channel->chatbot_url): ?>

    <!-- Chatbot code -->

    <?php $current_lang=$this->lang_current(); ?>

    <script src="<?= $this->channel->chatbot_url ?>/widget/widget.js"></script>
    <script>
        (window.goteoHelpWidget=window.goteoHelpWidget||{}).load("<?= $this->channel->chatbot_url ?>", "<?= $current_lang ?>", <?= $this->channel->chatbot_id ?>, false);
    </script>

    <!-- End Chatbot code -->

    <?php endif ?>


<?php $this->append() ?>
