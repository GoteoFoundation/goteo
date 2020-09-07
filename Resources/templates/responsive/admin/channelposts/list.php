<?php

$this->layout('admin/channelposts/layout');

$this->section('admin-search-box-addons');
?>


<?php $this->replace() ?>

<?php $this->section('admin-container-body') ?>

<div>
    <select id="nodes-filter" name="nodes-list" class="form-control" style="margin-bottom:1em;" onchange="goChannelposts()">
        <?php if (!$this->selectedNode) : ?>
        <option selected="selected" hidden></option>
        <?php endif; ?>
        <?php foreach ($this->nodes as $nodeId => $nodeName) : ?>
        <option value="<?php echo $nodeId; ?>" <?php if ($nodeId == $this->selectedNode) echo 'selected="selected"'; ?>><?php echo $nodeName; ?></option>
        <?php endforeach; ?>
    </select>
</div>
<?php if ($this->selectedNode) : ?>

<div class="btn btn-cyan" style='margin-bottom:1em;' onclick="toggle_typeahead()"><?= $this->text('admin-channelpost-add') ?></div>

<div id='typeahead_post' style="display:none;">

    <?= $this->insert('admin/partials/typeahead', ['engines' => ['post'], 'defaults' => ['post']]) ?>

    <div id='send_post' class="btn btn-cyan" data-value="" onclick="send_post()"><?= $this->text('admin-channelpost-submit') ?></div>

</div>
<?php endif; ?>


<h5><?= $this->text('admin-list-total', $this->total) ?></h5>

<?= $this->insert('admin/partials/material_table', ['list' => $this->model_list_entries($this->list, ['id', 'title', 'order', 'actions'])]) ?>

</div>
</div>



<?php $this->replace() ?>

<?php $this->section('footer') ?>
<script type="text/javascript">
    $('.admin-typeahead').on('typeahead:select', function(event, datum, name) {
        const btn = document.querySelector('#send_post');
        btn.dataset.value = datum.id;
    });

    function toggle_typeahead() {
        var x = document.getElementById("typeahead_post");
        if (x.style.display === "none") {
            x.style.display = "block";
        } else {
            x.style.display = "none";
        }
    };

    function send_post() {
        const btn = document.querySelector('#send_post');

        $.ajax({
            url: '/admin/channelposts/' + document.getElementById("nodes-filter").value + '/add',
            type: 'POST',
            data: {
                value: btn.dataset.value,
                channel: document.getElementById("nodes-filter").value
            }
        }).done(function(data) {
            location.reload();
        });
    };

    function goChannelposts() {
        var selected = document.getElementById("nodes-filter").value;
        window.location = "/admin/channelposts/" + selected;
    }

</script>
<?php $this->append() ?> 