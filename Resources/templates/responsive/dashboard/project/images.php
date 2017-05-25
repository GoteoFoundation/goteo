<?php $this->layout('dashboard/project/layout') ?>

<?php $this->section('dashboard-project-content') ?>

    <h1><?= $this->text('images-main-header') ?></h1>
    <blockquote><?= $this->text('dashboard-project-images-desc') ?></blockquote>

    <?php foreach($this->zones as $key => $zone): ?>
        <h4><?= $zone ?></h4>
        <ul class="list-inline image-list-sortable">
        <?php foreach($this->images[$key] as $i => $img): ?>
            <li>
                <div class="image" style="background-image:url(<?= $img->getLink(300, 300, true) ?>)"></div>
                <div class="options">
                    <a class="btn <?= $this->project->image->name == $img->getName() ? 'btn-pink' : 'btn-default' ?>"><i class="fa fa-star" title="<?= $this->text('dashboard-project-default-image') ?>"></i></a>
                    <a class="btn btn-danger" onclick="return confirm('<?= $this->ee($this->text('dashboard-project-delete-image-confirm'), 'js') ?>')"><i class="fa fa-trash" title="<?= $this->text('dashboard-project-delete-image') ?>"></i></a>
                </div>
            </li>
        <?php endforeach ?>
            <li class="dragndrop"><span><i style="font-size:2em" class="fa fa-plus"></i><br><br><?= $this->text('dashboard-project-dnd-image') ?></span></li>
        </ul>
    <?php endforeach ?>

<?php $this->replace() ?>

<?php $this->section('footer') ?>

<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt

$(function(){
    $(".image-list-sortable").each(function(){
        Sortable.create($(this).get(0), {
            group: 'project-images'
            , filter: ".dragndrop"
        });
        var dropzone = new Dropzone($(this).find('.dragndrop').get(0), {url:'/post'});
    });
})

// @license-end
</script>

<?php $this->append() ?>
