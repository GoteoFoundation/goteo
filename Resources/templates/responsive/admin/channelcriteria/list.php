<?php

$this->layout('admin/channelcriteria/layout');

$this->section('admin-search-box-addons');
?>


<?php $this->replace() ?>

<?php $this->section('admin-container-body') ?>

<div class="row">
    <div class="col-md-8">
        <select id="nodes-filter" name="nodes-list" class="form-control" style="margin-bottom:1em;" onchange="goChannelCriteria()">
            <?php if (!$this->current_node) : ?>
            <option selected="selected" hidden></option>
            <?php endif; ?>
            <?php foreach ($this->nodes as $nodeId => $nodeName) : ?>
            <option value="<?= $nodeId; ?>" <?= ($nodeId == $this->current_node)? 'selected="selected"' : ''; ?>><?= $nodeName; ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-4">
        <a class="btn btn-cyan" href="/admin/channelcriteria/<?= $this->current_node ?>/export"><i class="fa fa-plus"></i>Exportar respuestas a CSV</a>
    </div>
</div>



<div class="dashboard-content">
  <div class="inner-container">
    <div class="inner">
        <?php echo $this->text('admin-channelcriteria-subtitle') ?>
    </div>

    <?php
        $form = $this->raw('form');
        $submit = $this->form_row($form['submit']);
    ?>

    <?php echo $this->form_start($form); ?>

    <div id="question-list" class="question-list">
        <?php foreach((array) $this->questionnaire->questions as $question) : ?>
            <?php echo $this->insert('dashboard/partials/question_item', ['question' => $question, 'form' => $form]) ?>
        <?php endforeach ?>
    </div>

    <div class="form-group pull-right"> <?php echo $this->form_row($form['add-question'], [], true) ?> </div>
    
    <?php echo $submit ?>

    <?php echo $this->form_end($form) ?>
    
  </div>
</div>

<?php $this->replace() ?>

<?php $this->section('footer') ?>
<script type="text/javascript" src="<?php echo SRC_URL ?>/assets/js/dashboard/questions.js"></script>

<script type="text/javascript">
    function goChannelCriteria() {
        var selected = document.getElementById("nodes-filter").value;
        window.location = "/admin/channelcriteria/" + selected;
    }

</script>
<?php $this->append() ?> 