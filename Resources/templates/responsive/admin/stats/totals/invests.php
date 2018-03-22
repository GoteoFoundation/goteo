<?php

$this->layout('admin/stats/layout');

// $query = http_build_query($this->filters);
$query = $value = '';
if($this->has_query('project')) {
    $value = $this->get_query('project');
    $query = http_build_query(['project' => $value]);
}
elseif($this->has_query('call')) {
    $value = $this->get_query('call');
    $query = http_build_query(['call' => $value]);
}
elseif($this->has_query('matcher')) {
    $value = $this->get_query('matcher');
    $query = http_build_query(['matcher' => $value]);
} 
// Nice name
if($this->has_query('text')) $value = $this->get_query('text');
?>

<?php $this->section('admin-container-body') ?>

<?= $this->insert('admin/partials/typeahead', ['value' => $value]) ?>

<div class="panel">
  <div class="panel-body">
    
    <h5><?= $this->text('admin-stats-invest-totals') ?></h5>
    
    <?= $this->insert('admin/stats/totals/partials/invests', ['query' => $query]) ?>
    
    <h5><?= $this->text('admin-stats-commission-totals') ?></h5>
    <?= $this->insert('admin/stats/totals/partials/commissions', ['query' => $query]) ?>

  </div>
</div>

<?php $this->replace() ?>

<?php $this->section('footer') ?>
<script type="text/javascript">
$('.admin-typeahead').on('typeahead:select', function(event, datum, name) {
    console.log('search for', event, datum, name);
    adminProntoLoad(location.pathname + '?' + name + '=' + datum.id + '&text=' + datum.name );
});
</script>
<?php $this->append() ?>