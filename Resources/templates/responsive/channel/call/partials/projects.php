<?php $channel=$this->channel; ?>

<?php if($this->projects): ?>

<div class="section projects">
  <div class="container">
    <h2 class="title"><span class="icon icon-desktop icon-3x"></span><?= $this->text('channel-call-projects-title') ?></h2>
    <?= $this->insert('channel/call/partials/filters_block') ?>

        <?php if ($this->type !== 'available') : ?>
            <?= $this->insert('channel/partials/projects') ?>
        <?php else: ?>
            <?= $this->insert('channel/partials/projects_block') ?>
        <?php endif ?>
  </div>

  <div class="container">
    <?= $this->insert('partials/utils/paginator', ['total' => $this->total, 'limit' => $this->limit ? $this->limit : 10]) ?>
  </div>

</div>

<?php endif; ?>