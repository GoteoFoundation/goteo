<?php $this->layout('admin/layout') ?>

<?php $this->section('admin-content') ?>
<?php

$template = $this->edit;

?>
<p><strong><?= $template->name ?></strong>: <?= $template->purpose ?></p>

<div class="widget board">
    <form method="post" action="/admin/templates/edit/<?= $template->id ?>">
        <input type="hidden" name="group" value="<?= $template->group ?>" />
        <p>
            <label for="tpltitle"><?= $this->text('regular-title') ?>:</label><br />
            <input id="tpltitle" type="text" name="title" size="120" value="<?= $template->title ?>" />
        </p>

        <p class="newsletter">
            <label for="tpltext"><?= $this->text('regular-content') ?>:</label><br />

            <textarea id="tpltext" name="text" cols="100" rows="20"><?= $template->text ?></textarea>
        </p>

        <input type="submit" name="save" value="<?= $this->text('regular-save') ?>" />

        <p>
            <label for="mark-pending"><?= $this->text('mark-pending') ?></label>
            <input id="mark-pending" type="checkbox" name="pending" value="1" />
        </p>

        <p>
            <label for="text-type"><?= $this->text('admin-text-type') ?></label>
            <?= $this->html('select', [
                'value' => $template->type,
                'options' => [
                    'html' => $this->text('admin-text-type-html'),
                    'md' => $this->text('admin-text-type-md')],
                'name' => 'type',
                'attribs' => ['id'=>'text-type']]) ?>
        </p>

    </form>
</div>

<?php $this->replace() ?>

<?php $this->section('footer') ?>
<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
$(function(){

    $('#text-type').on('change', function() {
        $(this).closest('form').submit();
    });
    <?php if($template->type === 'md'): ?>

    var _uploadImage = function(files, url, callback) {
        callback = $.isFunction(callback) ? callback : function(){};
        var data = new FormData();
        if(!files.length) files = [files];
        $.each(files, function(index, file){
          // TODO: configurable input.file name
          data.append('file[]', file);
        });
        var _progress = function(e) {
            if(e.lengthComputable){
                // console.log('progress', e.loaded, e.total);
                callback('progress', e.loaded / e.total);
            }
        };
        $.ajax({
            url: url,
            cache: false,
            contentType: false,
            processData: false,
            data: data,
            type: 'POST',
            xhr: function() {
              var myXhr = $.ajaxSettings.xhr();
              if (myXhr.upload) myXhr.upload.addEventListener('progress',_progress, false);
              return myXhr;
            },
            success: function(result) {
              // console.log('success', result, result.files);
              if(result && result.files) {
                var files = $.map(result.files, function(file) {
                  return IMG_URL + '/700x0/' + file.name;
                });
                callback('success', files);
              } else {
                callback('error', 'No files uploaded!');
              }
            },
            error: function(data) {
              console.log('upload error', data);
              callback('error', data);
            }
        });
    };
    var simplemde = new SimpleMDE({
        element: $("#tpltext")[0],
        spellChecker: false,
        promptURLs: true
    });
    // Tweak codemirror to accept drag&drop any file
    simplemde.codemirror.setOption("allowDropFileTypes", null);

    simplemde.codemirror.on('drop', function(codemirror, event) {
        // console.log('codemirror',codemirror,'event',event);
        var loading_text = '![](loading image...)';

        if(event.dataTransfer && event.dataTransfer.files && event.dataTransfer.files.length) {
          var images = $.grep(event.dataTransfer.files, function(file,i) {
            if(file && file.type && file.type.startsWith('image/')) {
              return true;
            }
            return false;
          });
          // console.log('images', images);
          if(images.length) {
            // Do not allow predefined codemirror behaviour if are images
            event.preventDefault();
            event.stopPropagation();
            var $cm = $('#tpltext').closest('.newsletter').find('.CodeMirror.CodeMirror-wrap');
            var $up = $('<div class="uploading">');
            $cm.prepend($up);

            var coords = codemirror.coordsChar({
              left: event.pageX,
              top: event.pageY
            });

            codemirror.replaceRange("\n" + loading_text + "\n", coords);
            coords.line++;
            coords.ch = 0;
            codemirror.setCursor(coords);
            // console.log('codemirror',codemirror,'coords',coords);

            _uploadImage(images, '/api/blog/images', function(status, data) {
              // console.log('callback upload', status, data);
              if(status === 'progress') {
                $up.css('width',  (data * 100) + '%');
              } else {
                $up.remove();
              }
              if(status === 'success') {
                if(!data.length) data = [data];
                $.each(data, function(i,name){
                  codemirror.replaceRange("![](" + name + ")", coords, {line:coords.line, ch:loading_text.length});
                });
              }
              if(status === 'error') {
                alert('ERROR: ' + data);
              }
            });
          }
        }
    });

    <?php endif ?>
});
// @license-end
</script>
<?php $this->append() ?>
