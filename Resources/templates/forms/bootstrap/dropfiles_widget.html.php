<?php

$current_name = $form['current']->vars['full_name'];
$uploads_name = $form['uploads']->vars['full_name'];
// $current_files = $form['current']->vars['data'];
$current_files = $value;


// var_dump($form['current']->vars);die;
?>
<div class="dropfiles" data-text-upload="<?= $view->escape($text_upload) ?>" data-limit="<?= $limit ?>" data-auto-process="<?= $auto_process ?>" data-multiple="<?= $multiple ?>" data-url="<?= $view->escape($url) ?>" data-name="<?= $view->escape($uploads_name) ?>">
    <div class="image-zone" data-section="<?= $key ?>">
        <ul class="list-inline image-list-sortable" id="list-sortable-<?= $key ?>">
          <?php foreach($current_files as $img) {
            if($img) {
                echo $view->render('bootstrap/dropfiles_item.html.php', [
                    'file_url' => $img->getLink(300, 300, true),
                    'file_name' => $img->getName(),
                    'hidden_input' => '<input type="hidden" name="' . $current_name . '" value="' . $view->escape($img->getName()) . '">'
                ]);
            }
          } ?>
        </ul>
        <div class="dragndrop"><div class="dropzone"></div></div>
    </div>
    <p class="text-danger error-msg hidden"></p>
</div>
