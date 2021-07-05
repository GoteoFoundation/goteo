<?php

if ($this->channel->getResources()):

  $section = current($this->channel->getSections('resources'));
?>

<div class="section resources">
  <div class="container">
    <div class="row">
      <div class="col-md-6">
        <img loading="lazy" class="img-responsive" src="<?= $section->main_image? $section->getImage()->getLink(500, 300, false) : "/assets/img/channel/call/resources.png" ?>">
      </div>
      <div class="col-md-6">
        <div class="info">
          <div class="title">
            <?= $section->main_title? $section->main_title : $this->t('channel-call-resources-title') ?>
          </div>
          <div class="description">
            <?= $section->main_description? $section->main_description : $this->t('channel-call-resources-description') ?>
          </div>
          <div class="col-button">
            <a href="<?= '/channel/'.$this->channel->id.'/resources' . $this->lang_url_query($this->lang_current()) ?>" class="btn btn-transparent"><i class="icon icon-plus icon-2x"></i><?= $section->main_button? $section->main_button: $this->text('channel-call-resources-button') ?></a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php endif; ?>