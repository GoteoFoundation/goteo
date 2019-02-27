<?php

$this->layout('admin/promote/layout');

$this->section('admin-search-box-addons');
?>

<?php $this->replace() ?>


<?php $this->section('admin-container-body') ?>

<div>
    <select id="nodes-filter" name="nodes-list"  style="margin-bottom:1em;" class="form-control">
    <?php foreach ($this->nodes as $nodeId=>$nodeName) : ?>
        <option value="<?php echo $nodeId;?>"><?php echo $nodeName; ?></option>
    <?php endforeach; ?>
    </select>

    <div class="btn btn-cyan" onclick="goChannelPromote()"> <?= $this->text('form-next-button')?></div>
</div>

<?php $this->replace() ?>

<?php $this->section('footer') ?>

<script type="text/javascript">
function goChannelPromote()
{
    var selected = document.getElementById("nodes-filter").value;
    window.location = "/admin/promote/channel/"+selected;
}
</script>

<?php $this->append() ?>
