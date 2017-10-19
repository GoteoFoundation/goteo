<?php $this->layout('dashboard/project/translate/layout') ?>

<?php $this->section('dashboard-translate-project') ?>

  <?= $this->form_start($this->raw('form')) ?>

  <h2><?= $this->text('rewards-fields-individual_reward-title') ?></h2>

  <?php foreach($this->rewards as $reward): ?>
      <div class="panel section-content" data-id="<?= $reward->id ?>">
        <div class="panel-body">
          <div class="pull-left" style="width: 85%;">
            <?= $this->insert('dashboard/project/translate/partials/reward_form', [
              'form' => $this->raw('form'),
              'reward' => $reward,
              'lang' => $this->lang
              ]) ?>
          </div>
          <div class="pull-right text-right" style="width: 15%;">
            <h4>
              <?= amount_format($reward->amount) ?><br>
            </h4>
          </div>
        </div>
      </div>
  <?php endforeach ?>

<?php if($this->social): ?>
  <h2><?= $this->text('rewards-fields-social_reward-title') ?></h2>

  <?php foreach($this->social as $reward): ?>
      <div class="panel section-content" data-id="<?= $reward->id ?>">
        <div class="panel-body lilac">
          <div class="pull-left" style="width: 85%;">
            <?= $this->insert('dashboard/project/translate/partials/reward_form', [
              'form' => $this->raw('form'),
              'reward' => $reward,
              'lang' => $this->lang
              ]) ?>
          </div>
          <div class="pull-right text-right" style="width: 15%;">
            <h4>
              <?= amount_format($reward->amount) ?><br>
            </h4>
          </div>
        </div>
      </div>
  <?php endforeach ?>
<?php endif ?>

  <?= $this->form_end($this->raw('form')) ?>

<?php $this->replace() ?>


