<?php $this->layout('dashboard/project/layout') ?>

<?php $this->section('dashboard-project-content') ?>

    <h1><?= $this->text('images-main-header') ?></h1>
    <blockquote><?= $this->text('dashboard-project-images-desc') ?></blockquote>

    <?php foreach($this->zones as $key => $zone): ?>
        <h4><?= $zone ?></h4>
        <ul class="list-inline image-list-sortable" data-section="<?= $key ?>">
        <?php foreach($this->images[$key] as $img): ?>
            <?= $this->insert('dashboard/project/partials/image_list_item', ['image_url' => $img->getLink(300, 300, true), 'image_name' => $img->getName()]) ?>
        <?php endforeach ?>
            <li class="dragndrop"><div class="dropzone"></div></li>
        </ul>
        <p class="text-danger error-msg hidden"></p>
    <?php endforeach ?>

<?php $this->replace() ?>

<?php $this->section('footer') ?>

<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt

$(function(){
    Dropzone.autoDiscover = false;
    $(".image-list-sortable").each(function(){
        Sortable.create($(this).get(0), {
            group: 'project-images'
            , filter: ".dragndrop"
        });
        var $list = $(this);
        var $error = $list.next();
        var element = $list.find('.dragndrop>div').get(0);
        var dropzone = new Dropzone(element, {
            url:'/api/projects/<?= $this->project->id ?>/images',
            uploadMultiple: true,
            createImageThumbnails: true,
            maxFiles:null,
            autoProcessQueue: true,
            dictDefaultMessage: '<i style="font-size:2em" class="fa fa-plus"></i><br><br><?= $this->ee($this->text('dashboard-project-dnd-image'), 'js') ?>'
        });
        dropzone.on('error', function(file, error) {
            $error.html(error.error);
            $error.removeClass('hidden');
            // console.log('error', error);
        });
        dropzone.on('success', function(file, response) {
            // see if all files are uploaded ok in response
            if(response && !response.success) {
                $error.html(response.msg);
                $error.removeClass('hidden');
                for(var i in response.files) {
                    if(!response.files[i].success)
                        $error.append('<br>' + response.files[i].msg);
                }
                // return;
            }
            // Add to list
            var li = '<?= $this->ee($this->insert('dashboard/project/partials/image_list_item', ['image_url' => '{URL}', 'image_name' => '{NAME}']), 'js') ?>';
            var img = '/img/300x300c/' + file.name;
            li = li.replace('{URL}', img);
            $list.find('.dragndrop').before(li);
            console.log('success', file, response, li);
        });
        dropzone.on("complete", function(file) {
            dropzone.removeFile(file);
        });
        dropzone.on("sending", function(file, xhr, formData) {
          // Will send the section value along with the file as POST data.
          formData.append("section", $list.data('section'));
        });
    });
})

// @license-end
</script>

<?php $this->append() ?>
