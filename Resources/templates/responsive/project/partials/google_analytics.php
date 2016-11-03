<?php if($this->project->analytics_id): ?>
<!-- Project Google analytics -->
<script>
ga('create', '<?= $this->project->analytics_id ?>', 'auto', '<?= $this->project->id ?>');
ga('<?= $this->project->id ?>.send', 'pageview');
</script>

<?php endif; ?>
