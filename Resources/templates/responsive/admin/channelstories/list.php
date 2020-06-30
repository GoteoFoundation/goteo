<?php

$this->layout('admin/channelstories/layout');

$this->section('admin-search-box-addons');
?>


<?php $this->replace() ?>

<?php $this->section('admin-container-body') ?>

<div>
    <select id="nodes-filter" name="nodes-list" class="form-control" style="margin-bottom:1em;" onchange="goChannelStories()">
        <?php if (!$this->selectedNode) : ?>
        <option selected="selected" hidden></option>
        <?php endif; ?>
        <?php foreach ($this->nodes as $nodeId => $nodeName) : ?>
        <option value="<?php echo $nodeId; ?>" <?php if ($nodeId == $this->selectedNode) echo 'selected="selected"'; ?>><?php echo $nodeName; ?></option>
        <?php endforeach; ?>
    </select>
</div>
<?php if ($this->selectedNode) : ?>

<div class="btn btn-cyan" style='margin-bottom:1em;' onclick="toggle_typeahead()"><?= $this->text('admin-channelstory-add') ?></div>

<div id='typeahead_story' style="display:none;">

    <?= $this->insert('admin/partials/typeahead', ['engines' => ['story'], 'defaults' => ['story']]) ?>

    <div id='send_story' class="btn btn-cyan" data-value="" onclick="send_story()"><?= $this->text('admin-channelstory-submit') ?></div>

</div>
<?php endif; ?>


<h5><?= $this->text('admin-list-total', $this->total) ?></h5>

<?= $this->insert('admin/partials/material_table', ['list' => $this->model_list_entries($this->list, ['id', 'image', 'title', 'order', 'actions'])]) ?>

</div>
</div>



<?php $this->replace() ?>

<?php $this->section('footer') ?>
<script type="text/javascript">
    $('.admin-typeahead').on('typeahead:select', function(event, datum, name) {
        const btn = document.querySelector('#send_story');
        btn.dataset.value = datum.id;
    });

    function toggle_typeahead() {
        var x = document.getElementById("typeahead_story");
        if (x.style.display === "none") {
            x.style.display = "block";
        } else {
            x.style.display = "none";
        }
    };

    function send_story() {
        const btn = document.querySelector('#send_story');

        $.ajax({
            url: '/admin/channelstory/' + document.getElementById("nodes-filter").value + '/add',
            type: 'POST',
            data: {
                value: btn.dataset.value,
                channel: document.getElementById("nodes-filter").value
            }
        }).done(function(data) {
            location.reload();
        });
    };

    function goChannelStories() {
        var selected = document.getElementById("nodes-filter").value;
        window.location = "/admin/channelstory/" + selected;
    }

</script>
<?php $this->append() ?> 