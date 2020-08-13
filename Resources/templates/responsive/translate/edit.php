<?php

$this->layout("translate/layout");


$this->section('translate-content');

$fields = $this->fields;
$translator = $this->translator;
$languages = [$translator->original => $translator->original_name]+ array_diff_key($this->languages, [$translator->original => $translator->original_name]);
$query = $this->get_query() ? '?'. http_build_query($this->get_query()) : '';

?>

<div class="dashboard-content">
  <div class="inner-container">


<?php
$default_lang = $this->get_query('hl');

?>
<ul class="nav nav-tabs">
<?php
    foreach($languages as $lang => $name):
      if(!$default_lang) $default_lang = $lang;
      if(!$translator->isOriginal($lang) && !$translator->isTranslated($lang)) continue;
?>
  <li role="tab" <?= $default_lang == $lang ? ' class="active"' : '' ?>><a<?= $translator->isOriginal($lang) ? ' class="text-success" title="' . $this->text('translator-original-text') . '"' : ($translator->isPending($lang) ? ' class="text-danger" title="' . $this->text('translator-pending') . '"' : '') ?> href="?hl=<?= $lang ?>" data-target="#t-<?= $lang ?>" data-toggle="tab"><?= $name ?></a></li>
<?php endforeach ?>
  <li>
    <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"><?= $this->text('translator-more-langs') ?> <span class="caret"></span></a>
        <ul class="dropdown-menu">
<?php
  foreach($languages as $lang => $name):
    if($translator->isOriginal($lang) || $translator->isTranslated($lang)) continue;
?>
        <li role="tab" <?= $default_lang == $lang ? ' class="active"' : '' ?>><a class="text-muted" title="<?= $this->text('translator-not-translated') ?>" href="?hl=<?= $lang ?>" data-target="#t-<?= $lang ?>" data-toggle="tab"><?= $name ?></a></li>
<?php endforeach ?>
        </ul>
  </li>
</ul>
<br>

<!-- Tab panes -->
<form action="/translate/<?= $this->zone ?>/<?= $this->id . $query?>" method="post">
<div class="tab-content">
<?php foreach($languages as $lang => $name): ?>
  <div role="tabpanel" class="tab-pane<?= $default_lang == $lang ? ' active' : '' ?>" id="t-<?= $lang ?>">
    <div class="well well-sm">
      ID: <strong><?= $this->id ?></strong>
      Lang: <strong><?= $name ?></strong>
      <?php if ($translator->isOriginal($lang)): ?>
        <strong class="pull-right"><?= $this->text('translator-original-text') ?></strong>
      <?php else: ?>
        <div class="btn-group pull-right" role="group">
         <div class="btn-group">
          <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <?= $this->text('translator-copy-from') ?> <span class="caret"></span>
          </button>
          <ul class="dropdown-menu pull-right">
          <?php foreach($languages as $l => $n):
            if($l === $lang) continue;
            if(!$translator->isTranslated($l) && !$translator->isOriginal($l)) continue;
          ?>
            <li><a href="#<?= $l ?>" class="copy-from"><?= $n ?></a></li>
          <?php endforeach ?>
          </ul>
        </div>
        <div class="btn-group">
          <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <?= $this->text('translator-autotranslate-from') ?> <span class="caret"></span>
          </button>
          <ul class="dropdown-menu pull-right">
          <?php foreach($languages as $l => $n):
            if($l === $lang) continue;
            if(!$translator->isTranslated($l) && !$translator->isOriginal($l)) continue;
          ?>
            <li><a href="#<?= $l ?>" class="autotranslate-from"><?= $n ?></a></li>
          <?php endforeach ?>
          </ul>
         </div>
        </div>
        <div class="clearfix"></div>
      <?php endif ?>

    </div>

    <?php
      foreach($fields as $field => $type):
        if($field == 'pending') continue;
    ?>
      <div class="form-group">
        <label for="i-<?= $lang ?>-<?= $field ?>"><?= $this->text("translator-field-$field") ?></label>
        <?php if($type === 'text'): ?>
            <textarea rows="10" class="form-control editor" id="i-<?= $lang ?>-<?= $field ?>" name="t[<?= $lang ?>][<?= $field ?>]"><?= $this->ee($translator->getTranslation($lang, $field, true)) ?></textarea>
        <?php else: ?>
            <input class="form-control editor" id="i-<?= $lang ?>-<?= $field ?>" name="t[<?= $lang ?>][<?= $field ?>]" value="<?= $this->ee($translator->getTranslation($lang, $field, true)) ?>">
        <?php endif ?>
      </div>
    <?php endforeach ?>

    <?php if (!$translator->isOriginal($lang)): ?>
      <?php if(array_key_exists('pending', $fields)): ?>
        <div class="form-group">
          <label>
          <input type="hidden" name="t[<?= $lang ?>][pending]" value="0">
          <input type="checkbox" name="t[<?= $lang ?>][pending]" class="pending" value="1"<?= $translator->isPending($lang) || !$translator->isTranslated($lang) ? ' checked' : '' ?>> <?= $this->text('translator-pending') ?>
          </label>
        </div>
        <div class="alert alert-danger pending"<?= $translator->isPending($lang) ? '' : ' style="display:none"' ?>><?= $this->text('translator-pending-desc') ?></div>
      <?php endif ?>
      <button type="submit" class="btn btn-cyan" name="save"><?= $translator->isPending($lang) || !$translator->isTranslated($lang) ? $this->text('translator-save-draft') : $this->text('translator-save') ?></button>
      <button type="submit" name="d" value="<?= $lang ?>" onclick="return confirm('<?= $this->ee($this->text('translator-delete-sure', $name),'js') ?>')" class="btn btn-danger pull-right"><?= $this->text('translator-delete', $name) ?></button>
    <?php else: ?>
        <button type="submit" class="btn btn-cyan" name="save"><?= $this->text('translator-save-original') ?></button>
    <?php endif ?>

  </div>
<?php endforeach ?>
</div>
</form>

  </div>
</div>

<?php $this->replace() ?>

<?php $this->section('translate-head') ?>
  <link href="<?= SRC_URL ?>/assets/vendor/summernote/dist/summernote.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= SRC_URL ?>/assets/vendor/simplemde/dist/simplemde.min.css" type="text/css" />
<?php $this->append() ?>


<?php $this->section('translate-footer') ?>

  <script type="text/javascript" src="<?= SRC_URL ?>/assets/js/jquery.translator.js"></script>
  <?php if($translator->getType() === 'html'): ?>
    <script src="<?= SRC_URL ?>/assets/vendor/summernote/dist/summernote.min.js"></script>
  <?php elseif($translator->getType() === 'md'): ?>
    <script type="text/javascript" src="<?= SRC_URL ?>/assets/vendor/simplemde/dist/simplemde.min.js"></script>
  <?php endif ?>

<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
  $(function(){
    var _uploadImage = function(files, url, callback) {
      callback = $.isFunction(callback) ? callback : function(){};
      var data = new FormData();
      if(!files.length) files = [files];
      $.each(files, function(index, file){
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

  <?php if($translator->getType() === 'html'): ?>
    $('textarea.editor').each(function() {
      var summernote = $(this).summernote({
        toolbar: [
          ['tag', ['style']],
          ['style', ['bold', 'italic', 'underline', 'clear']],
          // ['font', ['strikethrough', 'superscript', 'subscript']],
          // ['fontsize', ['fontsize']],
          ['color', ['color']],
          ['para', ['ul', 'ol', 'paragraph']],
          // ['height', ['height']],
          ['insert', ['link', 'picture', 'video', 'hr', 'table']],
          ['misc', ['fullscreen', 'codeview', 'help']]
        ],
        popatmouse: false,
        callbacks: {
          onFocus: function() {
            // console.log('Editable area is focused');
            $(this).closest('.summernote').addClass('focused');
          },
          onBlur: function() {
            // console.log('Editable area loses focus');
            $(this).closest('.summernote').removeClass('focused');
          },
          onImageUpload: function(files) {
            var $sm = $(this).closest('.summernote');
            var $up = $('<div class="uploading">');
            $sm.prepend($up);
            _uploadImage(files, '/api/blog/images', function(status, data) {
              // console.log('callback upload', status, data);
              if(status === 'progress') {
                $up.css('width',  (data * 100) + '%');
              } else {
                $up.remove();
              }
              if(status === 'success') {
                if(!data.length) data = [data];
                $.each(data, function(i,name){
                  var image = $('<img>').attr('src', name);
                  summernote.summernote('insertNode', image[0]);
                });
              }
              if(status === 'error') {
                alert('ERROR: ' + data);
              }
            });
          }
        }
      });
    });
  <?php elseif($translator->getType() === 'md'): ?>
    $('textarea.editor').each(function() {
      var el = this;
      var simplemde = new SimpleMDE({
          element: this,
          spellChecker: false,
          promptURLs: true,
          forceSync: true
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
              var $cm = $(el).closest('.form-group').find('.CodeMirror.CodeMirror-wrap');
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
      simplemde.render();
    });
  <?php endif ?>
    // //
    // var hash = window.location.hash.substring(1);
    // if(hash) {
    //   $('#t-' + hash).tab('show');
    // }
    // Form submit text toggle
    $('form input[type="checkbox"].pending').on('change', function(){
      var t = '<?= $this->ee($this->text('translator-save'), 'js') ?>'
      $('form .alert.pending').hide();
      if($(this).is(':checked')) {
        t = '<?= $this->ee($this->text('translator-save-draft'), 'js') ?>';
        $('form .alert.pending').show();
      }
      $(this).closest('.tab-pane').find('button[name="save"]').text(t);
    });

    // Hack to show codemirror editor (SimpleMDE)
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
      var target = $(e.target).data('target');
      $(target + ' .CodeMirror').each(function(i, el) {
        el.CodeMirror.refresh()
      });
    });

    var setValue = function(el, val) {
        $(el).val(val);
        var $group = $(el).closest('.form-group');
        var $editor = $group.find('.CodeMirror');
        if($editor.is('.CodeMirror')) {
          $editor.get(0).CodeMirror.setValue(val);
          $editor.get(0).CodeMirror.refresh();
          // console.log('FORM_GROUP', $group, $editor[0], val)
        }
        var $editor = $group.find('.note-editor');
        if($editor.is('.note-editor')) {
          $(el).summernote('code', val);
        }
    };
    // Copy content from other tabs
    $('a.copy-from').on('click', function(e){
      e.preventDefault();
      var lang = $(this).attr('href').substr(1);
      var $tab = $(this).closest('.tab-pane');
      var skipped = false;
      $tab.find('.editor').each(function(i, el){
        var field = $(el).attr('id').substr(5);
        var val = $('#i-' + lang + '-' + field).val();

        if($.trim($(el).val()) != '') {
          skipped = true;
          return true;
        }
        setValue(el, val);
      });
      if(skipped) {
        alert('<?= $this->ee($this->text('translator-skipped-fields'), 'js') ?>');
      }
    });

    // Copy content from other tabs
    $('a.autotranslate-from').on('click', function(e){
      e.preventDefault();
      var lang = $(this).attr('href').substr(1);
      var $tab = $(this).closest('.tab-pane');
      var skipped = false;
      $tab.find('.editor').each(function(i, el){
        var field = $(el).attr('id').substr(5);
        var val = $('#i-' + lang + '-' + field).val();

        if($.trim($(el).val()) != '' || $.trim(val) == '') {
          skipped = true;
          return true;
        }
        $.Translator({pair: lang + '|' + $(el).attr('id').substr(2,2), origin: val}, function(data){
             if(data.result) {
              setValue(el, data.text);
             }
             else alert('ERROR: ' + data.text);
        });
      });
      if(skipped) {
        alert('<?= $this->ee($this->text('translator-skipped-fields'), 'js') ?>');
      }
    });
  });
// @license-end
</script>
<?php $this->append() ?>
