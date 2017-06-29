<?php

            // echo $this->insert('dashboard/project/partials/image_list_item', [
            //         'image_url' => $img->getLink(300, 300, true),
            //         'image_name' => $img->getName()]);
 ?>

<div class="dropfiles" data-text-upload="<?= $view->escape($text_upload) ?>" data-limit="<?= $limit ?>" data-auto-process="<?= $auto_process ?>" data-multiple="<?= $multiple ?>" data-url="<?= $view->escape($url) ?>" data-name="<?= $view->escape($full_name) ?>">
    <div class="image-zone" data-section="<?= $key ?>">
        <ul class="list-inline image-list-sortable" id="list-sortable-<?= $key ?>">
          <?php foreach($value as $img): ?>
            <li data-name="<?= $img->getName() ?>">
                <div class="image" style="background-image:url(<?= $img->getLink(300, 300, true) ?>)"></div>
            </li>
          <?php endforeach ?>
        </ul>
        <div class="dragndrop"><div class="dropzone"></div></div>
    </div>
    <p class="text-danger error-msg hidden"></p>
</div>
