<li data-name="<?= $view->escape($file_name) ?>">
    <div class="image<?= $file_type ? ' file-type-' . $file_type : '' ?>" <?= $type === 'image' && $file_url ? 'style="background-image:url(' . $view->escape($file_url) . ');background-size:cover"' : '' ?>></div>
    <?= $hidden_input ?>

    <div class="options">

        <a class="add-to-markdown btn btn-default<?= $markdown_link ? '' : ' hidden' ?>" data-target="<?= $view->escape($markdown_link) ?>" title="<?= $view->escape($text_send_to_markdown) ?>"><i class="fa fa-send"></i></a>

        <a class="download-url btn btn-default<?= $download_url ? '' : ' hidden' ?>" href="<?= $download_url ?>" target="_blank" title="<?= $view->escape($text_download) ?>"><i class="fa fa-external-link"></i></a>

        <a class="delete-image btn btn-default" title="<?= $view->escape($text_delete_image) ?>"><i class="fa fa-trash"></i></a>
    </div>

</li>
