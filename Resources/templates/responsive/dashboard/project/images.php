<?php $this->layout('dashboard/layout') ?>

<?php $this->section('dashboard-content') ?>

    <h1><?= $this->text('images-main-header') ?></h1>
    <blockquote><?= $this->text('dashboard-project-images-desc') ?></blockquote>

    <?php foreach($this->zones as $key => $zone): ?>
        <h4><?= $zone ?></h4>
        <div class="image-zone" data-section="<?= $key ?>">
            <ul class="list-inline image-list-sortable"><?php
            foreach($this->images[$key] as $img) {
                echo $this->insert('dashboard/project/partials/image_list_item', [
                        'image_url' => $img->getLink(300, 300, true),
                        'image_name' => $img->getName()]);
            }
            ?></ul>
            <div class="dragndrop"><div class="dropzone"></div></div>
        </div>
        <p class="text-danger error-msg hidden"></p>
    <?php endforeach ?>

<?php $this->replace() ?>

<?php $this->section('footer') ?>

<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt

$(function(){
    var saveCurrentOrder = function() {
        var gallery = {};
        var $error = $('.image-zone+.error-msg');
        $('.image-list-sortable li').each(function(){
            var name = $(this).data('name');
            var section = $(this).closest('.image-zone').data('section') ||  '_';
            if(!$.isArray(gallery[section])) {
                gallery[section] = [];
            }
            gallery[section].push(name);
        });
        // console.log('Current order', gallery,JSON.stringify(gallery));
        $.ajax({
            url: '/api/projects/<?= $this->project->id ?>/images/reorder',
            'method': 'POST',
            data: {gallery: gallery}
        })
        .done(function(data){
            // console.log(data);
            if(!data.result) {
                $error.html(data.msg);
                $error.removeClass('hidden');
            }
        });
    };

    Dropzone.autoDiscover = false;

    $('.image-zone').each(function(){
        var $zone = $(this);
        var $list = $(this).find('.image-list-sortable');
        var $error = $zone.next();
        var element = $zone.find('.dragndrop>div').get(0);

        Sortable.create($list.get(0), {
            group: 'project-images'
            // , filter: ".dragndrop"
            // Reorder actions
            , onEnd: function (evt) {
                // evt.oldIndex;  // element's old index within parent
                // evt.newIndex;  // element's new index within parent
                // console.log(evt);
                saveCurrentOrder();
            }
        });

        var dropzone = new Dropzone(element, {
            url:'/api/projects/<?= $this->project->id ?>/images',
            uploadMultiple: true,
            createImageThumbnails: true,
            maxFiles:10,
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
            var name = file.name;
            for(var i in response.files) {
                if(response.files[i].originalName == name) {
                    name = response.files[i].name;
                }
            }

            var img = '/img/300x300c/' + name;
            li = li.replace('{URL}', img);
            li = li.replace('{NAME}', name);
            $list.append(li);
            $error.addClass('hidden');
            // console.log('success', file, response, li);
        });
        dropzone.on("complete", function(file) {
            dropzone.removeFile(file);
        });
        dropzone.on("sending", function(file, xhr, formData) {
          // Will send the section value along with the file as POST data.
          formData.append("section", $zone.data('section'));
        });
    });

    // Delete actions
    $('.image-list-sortable').on( 'click', '.delete-image', function(e) {
        e.preventDefault();
        var $li = $(this).closest('li');
        var $zone = $(this).closest('.image-zone');
        var $error = $zone.next();
        // Check if it is default
        if($li.find('.default-image').hasClass('btn-pink')) {
            alert('<?= $this->ee($this->text('dashboard-project-delete-default-image'), 'js') ?>')
            return false;
        }
        if(confirm('<?= $this->ee($this->text('dashboard-project-delete-image-confirm'), 'js') ?>')) {
            $.ajax({
                url: '/api/projects/<?= $this->project->id ?>/images/' + $li.data('name'),
                'method': 'DELETE'
            })
            .done(function(data) {
                // console.log('done',data);
                if(data.result) {
                    // OK
                    $li.remove();
                    $error.addClass('hidden');
                } else {
                    $error.html('<?= $this->ee($this->text('dashboard-project-image-delete-ko'), 'js') ?>');
                    $error.removeClass('hidden');
                }
            });
        }
    });
    $('.image-list-sortable').on( 'click', '.default-image', function(e) {
        e.preventDefault();
        var $li = $(this).closest('li');
        var $this = $(this);
        var $zone = $(this).closest('.image-zone');
        var $error = $zone.next();
        if(confirm('<?= $this->ee($this->text('dashboard-project-default-image-confirm'), 'js') ?>')) {
            $.ajax({
                url: '/api/projects/<?= $this->project->id ?>/images/' + $li.data('name'),
                'method': 'PUT'
            })
            .done(function(data) {
                // console.log('done',data);
                if(data.result) {
                    // OK
                    $('.image-list-sortable .default-image').removeClass('btn-pink').addClass('btn-default');
                    $this.addClass('btn-pink').removeClass('btn-default');
                    $error.addClass('hidden');
                    // Autoupdate images covers with class auto-project-image
                    $('#project-<?= $this->project->id ?> img.img-project').each(function() {
                        var src =  $(this).attr('src').split('/');
                        src[src.length - 1] = data.default;
                        $(this).attr('src', src.join('/'));
                    });
                } else {
                    $error.html(data.msg);
                    $error.removeClass('hidden');
                }
            });
        }

    });


})

// @license-end
</script>

<?php $this->append() ?>
