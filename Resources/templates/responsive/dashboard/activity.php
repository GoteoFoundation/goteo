<?php $this->layout('dashboard/layout') ?>

<?php $this->section('dashboard-content') ?>

<?php if($this->messages): ?>
<div class="dashboard-content">

  <div class="inner-container">
    <h2><?= $this->text('regular-messages') ?></h2>
    <?php foreach($this->messages as $message):
          $comments = $message->totalResponses($this->get_user());
    ?>
    <div class="panel section-content">
      <div class="panel-body">
        <h3 class="data-support"><span class="label label-default"><?= $this->text('message-'.$message->getType()) ?></span> <?= $message->getTitle() ?></h3>
          <p class="data-description"><?= nl2br($message->message) ?></p>
          <p>
              <button class="btn btn-<?= $comments ? 'lilac' : 'default' ?>" data-toggle="collapse"  data-target="#comments-<?= $message->id ?>"><i class="icon-1x icon icon-partners"></i> <?= $this->text('regular-num-comments', $comments) ?></button>

          </p>
          <div class="comments collapse" id="comments-<?= $message->id ?>">
            <?php if($comments): ?>
              <?= $this->insert('dashboard/project/partials/comments/full', [
                    'comments' => $message->getResponses($this->get_user()),
                    'thread' => $message->id,
                    'project' => $message->project
                    ]) ?>
            <?php else: ?>
                <p class="alert alert-danger"><?= $this->text('dashboard-project-support-no-responses') ?></p>
            <?php endif ?>
          </div>
      </div>
    </div>
  <?php endforeach ?>
  </div>
</div>
<?php endif ?>

<div class="dashboard-content">
  <div class="inner-container">
    <div class="projects-container" id="projects-support-container">
        <h2><?= $this->text('profile-invest_on-header') ?></h2>
        <?= $this->insert('dashboard/partials/projects_interests', [
            'projects' => $this->invested,
            'total' => $this->invested_total,
            'interests' => null,
            'auto_update' => '/dashboard/ajax/projects/invested',
            'limit' => $this->limit
            ]) ?>
    </div>
  </div>
</div>

<div class="dashboard-content cyan">
  <div class="inner-container">
    <div class="projects-container" id="projects-interests-container">
        <h2><?= $this->text('profile-suggest-projects-interest') ?></h2>
        <?= $this->insert('dashboard/partials/projects_interests', [
            'projects' => $this->favourite,
            'total' => $this->favourite_total,
            'interests' => $this->interests,
            'auto_update' => '/dashboard/ajax/projects/interests',
            'limit' => $this->limit
            ]) ?>
    </div>

  </div>
</div>

<?php $this->replace() ?>
