<?php

$this->layout('admin/promote/layout');

$this->section('admin-search-box-addons');
?>


<?php $this->replace() ?>

<?php $this->section('admin-container-body') ?>

<div>
    <select id="nodes-filter" name="nodes-list" class="form-control" style="margin-bottom:1em;" onchange="goChannelPromote()">
        <?php if (!$this->selectedNode) : ?>
        <option selected="selected" hidden></option>
        <?php endif; ?>
        <?php foreach ($this->nodes as $nodeId => $nodeName) : ?>
        <option value="<?php echo $nodeId; ?>" <?php if ($nodeId == $this->selectedNode) echo 'selected="selected"'; ?>><?php echo $nodeName; ?></option>
        <?php endforeach; ?>
    </select>
</div>
<?php if ($this->selectedNode) : ?>

<div class="btn btn-cyan" style='margin-bottom:1em;' onclick="toggle_typeahead()"><?= $this->text('admin-promote-add') ?></div>

<div id='typeahead_promote' style="display:none;">

    <?= $this->insert('admin/partials/typeahead', ['engines' => ['project'], 'defaults' => ['project'], 'extra' => ($this->selectedNode != $this->get_config('node')) ? ['node' => $this->selectedNode] : false ]) ?>

    <div id='send_promote' class="btn btn-cyan" data-value="" onclick="send_promote()"><?= $this->text('admin-promote-submit') ?></div>

</div>
<?php endif; ?>


<h5><?= $this->text('admin-list-total', $this->total) ?></h5>

<?= $this->insert('admin/partials/material_table', ['list' => $this->model_list_entries($this->list, ['id', 'name', 'status', 'active', 'order', 'actions'])]) ?>

</div>
</div>



<?php $this->replace() ?>

<?php $this->section('footer') ?>
<script type="text/javascript">
    $('.admin-typeahead').on('typeahead:select', function(event, datum, name) {
        const btn = document.querySelector('#send_promote');
        btn.dataset.value = datum.id;
    });

    function toggle_typeahead() {
        var x = document.getElementById("typeahead_promote");
        if (x.style.display === "none") {
            x.style.display = "block";
        } else {
            x.style.display = "none";
        }
    };

    function send_promote() {
        const btn = document.querySelector('#send_promote');

        $.ajax({
            url: '/api/promote/add',
            type: 'POST',
            data: {
                value: btn.dataset.value,
                channel: document.getElementById("nodes-filter").value
            }
        }).done(function(data) {
            location.reload();
        });
    };

    function goChannelPromote() {
        var selected = document.getElementById("nodes-filter").value;
        window.location = "/admin/promote/channel/" + selected;
    }
</script>
<?php $this->append() ?> 