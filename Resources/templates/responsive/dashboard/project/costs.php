<?php $this->layout('dashboard/project/layout') ?>

<?php $this->section('dashboard-content') ?>

<div class="dashboard-content">
  <div class="inner-container">
    <h1>4. <?= $this->text('costs-main-header') ?></h1>
    <div class="auto-hide">
        <div class="inner"><?= $this->text('guide-project-costs') ?></div>
        <div class="more"><i class="fa fa-info-circle"></i> <?= $this->text('regular-help') ?></div>
    </div>

    <?= $this->insert('dashboard/project/partials/goto_first_error') ?>

    <?= $this->supply('dashboard-content-form', function() {
        $form = $this->raw('form');
        echo $this->form_start($form);

        echo $this->form_row($form['title-costs']);

        $submit = $form['submit'] ? $this->form_row($form['submit']) : '';
        echo '<div class="top-button hidden">' . $submit . '</div>';

        $min = $opt = 0;
        echo '<div class="cost-list">';
        foreach($this->costs as $cost) {
            if($cost->required) $min += $cost->amount;
            else                $opt += $cost->amount;
            echo $this->insert('dashboard/project/partials/cost_item', ['cost' => $cost, 'form' => $form]);
        }
        echo '</div>';

        echo $this->insert('dashboard/project/partials/costs_bar', ['minimum' => $min, 'optimum' => $opt]);

        echo '<div class="form-group pull-right">'.$this->form_row($form['add-cost'], [], true).'</div>';

        echo $submit;

        echo $this->form_end($form);

    }) ?>

    <?= $this->insert('dashboard/project/partials/partial_validation') ?>

  </div>
</div>

<?php $this->replace() ?>



<?php $this->section('footer') ?>

<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt

$(function(){

    $('.autoform').on('change', '.cost-item .type select', function() {
        $(this).closest('.type').find('img').attr('src', '<?= $this->ee($this->asset('img/project/needs/'), 'js') ?>' + $(this).val() + '.png');
    });

// @license-end
</script>
<?php $this->append() ?>
