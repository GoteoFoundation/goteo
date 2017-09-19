<?php $this->layout('dashboard/project/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
  <div class="inner-container">
    <h2><?= $this->text('dashboard-menu-projects-rewards') ?></h2>


    <p class="exportcsv"><?= $this->project->inCampaign() ? $this->text('dashboard-rewards-notice') : $this->text('dashboard-rewards-investors_table', ['%URL%' => '/api/projects/' . $this->project->id . '/invests/csv']) ?></p>

    <form id="filters" class="row">
      <span class="col-xs-6">
        <label for="filter-reward"><?= $this->text('dashboard-project-filter-by-reward') ?></label>
        <?= $this->html('input',
                        ['type' => 'select',
                        'name' => 'filter[reward]',
                        'value' => $this->filter['reward'],
                        'attribs' => [
                            'id' => 'filter-reward',
                            'class' => 'form-control'
                        ],
                        'options' => $this->filters['reward']
                    ]) ?>
      </span>
      <span class="col-xs-6">
        <label for="filter-others"><?= $this->text('dashboard-project-filter-by-others') ?></label>
        <?= $this->html('input',
                        ['type' => 'select',
                        'name' => 'filter[others]',
                        'value' => $this->filter['others'],
                        'attribs' => [
                            'id' => 'filter-others',
                            'class' => 'form-control'
                        ],
                        'options' => $this->filters['others']
                    ]) ?>
      </span>
      <input type="hidden" name="order" value="<?= $this->order ?>">
    </form>

    <h5><?= $this->text('dashboard-invests-totals', ['%TOTAL_INVESTS%' => '<strong>' . $this->total_invests . '</strong>', '%TOTAL_USERS%' => '<strong>' . $this->total_users . '</strong>', '%TOTAL_AMOUNT%' => '<strong>' . amount_format($this->total_amount) . '</strong>']) ?></h5>

    <table class="-footable table">
      <thead>
        <tr>
          <th data-type="number" data-breakpoints="xs"><?= $this->insert('dashboard/partials/table_th', ['text' => '#', 'field' => 'id']) ?></th>
          <th data-type="date" data-breakpoints="xs"><?= $this->insert('dashboard/partials/table_th', ['text' => $this->text('regular-date'), 'field' => 'invested']) ?></th>
          <th data-type="html" data-breakpoints="xs"><?= $this->insert('dashboard/partials/table_th', ['text' => $this->text('admin-user'), 'field' => 'user']) ?></th>
          <th><?= $this->insert('dashboard/partials/table_th', ['text' => $this->text('invest-amount'), 'field' => 'amount']) ?></th>
          <th><?= $this->insert('dashboard/partials/table_th', ['text' => $this->text('rewards-field-individual_reward-reward'), 'field' => 'reward']) ?></th>
          <th><?= $this->insert('dashboard/partials/table_th', ['text' => $this->text('dashboard-rewards-fulfilled_status'), 'field' => 'fulfilled']) ?></th>
          <th><?= $this->text('admin-address') ?></th>
          <th><?= $this->text('regular-actions') ?></th>
        </tr>
      </thead>
      <tbody>
    <?php if($this->invests): ?>

      <?php
        foreach($this->invests as $invest):
            $resign = $invest->resign;
            $uid = $invest->getUser()->id;
            $name = $invest->getUser()->name;
            $email = $invest->getUser()->email;
            $a = $invest->getAddress();
            $address = $a->address . ', ' . $a->location . ', ' . $a->zipcode .' ' . $a->country;
            $reward = $invest->getRewards() ? $invest->getRewards()[0]->getTitle() : '';
            if($invest->resign) {
                $reward = $address = '';
                if($invest->anonymous) {
                    $uid = $name = $email = '';
                }
                $reward = '<span class="label label-info">'.$this->text('dashboard-rewards-resigns').'</span>';
            }
            if($invest->campaign) {
                $email = $address = $reward = '';
                $resign = true;
                $reward = '<span class="label label-lilac">'.$this->text('regular-matchfunding').'</span>';
            }
            if(!$resign && !$reward) {
                $reward = '<span class="label label-danger">' . $this->text('regular-unknown') . '</span>';
            }


      ?>
        <tr>
          <td><?= $invest->id ?></td>
          <td><?= date_formater($invest->invested) ?></td>
          <td><?php if($uid): ?><img src="<?= $invest->getUser()->avatar->getLink(30, 30, true) ?>" alt="<?= $name ?>" class="img-circle"> <?= $name ?><?php else: ?><?= $this->text('regular-anonymous') ?><?php endif ?> </td>
          <td><?= amount_format($invest->amount) ?></td>
          <td><?= $reward ?></td>
          <td>
              <?php if($invest->resign): ?>
                &nbsp;
              <?php elseif($invest->fulfilled): ?>
                <span class="label label-cyan"><?= $this->text('regular-yes') ?></span>
              <?php else: ?>
              <?= $this->insert('dashboard/project/partials/boolean', ['active' => $invest->fulfilled, 'name' => 'fulfilled-' . $invest->id, 'label_type' => 'cyan', 'url' => '/api/projects/' . $this->project->id . '/invests/' . $invest->id . '/fulfilled', 'confirm_yes' => $this->text('dashboard-rewards-process_alert') ]) ?>
              <?php endif ?>
          </td>
          <td><?= $address ?></td>
          <td>
            ...
          </td>
        </tr>
      <?php endforeach ?>
    <?php else: ?>
        <tr><td colspan="8"><h4><?= $this->text('dashboard-project-no-invests') ?></h4></td></tr>
    <?php endif ?>
      </tbody>
    </table>

    <?= $this->insert('partials/utils/paginator', ['total' => $this->total_invests, 'limit' => $this->limit]) ?>

  </div>
</div>

<?php $this->replace() ?>

<?php $this->section('footer') ?>

<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt

$(function(){
    $('.exportcsv a').on('click', function(){
        alert('<?= $this->ee($this->text('dashboard-investors_table-disclaimer'), 'js') ?>');
    });
    $('#filters select').on('change', function(){
        $(this).closest('form').submit();
    });
    $(document).on('form-boolean-changed', function(evt, input){
        // console.log('changed', input, $(input), $(input).closest('div'));
        if($(input).prop('checked')) {
            $(input).closest('.material-switch').replaceWith('<span class="label label-cyan"><?= $this->text('regular-yes') ?></span>');
        }
    });
})

// @license-end
</script>

<?php $this->append() ?>

