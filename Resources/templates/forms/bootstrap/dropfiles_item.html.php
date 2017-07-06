<li data-name="<?= $view->escape($file_name) ?>">
    <div class="image<?= $file_type ? ' file-type-' . $file_type : '' ?>" <?= $file_url ? 'style="background-image:url(' . $view->escape($file_url) . ')"' : '' ?>></div>
    <?= $hidden_input ?>
    <div class="options">

        <a class="add-to-markdown btn btn-default<?= $markdown_link ? '' : ' hidden' ?>" data-target="<?= $view->escape($markdown_link) ?>"><i class="fa fa-send" title="<?= $view->escape($text_send_to_markdown) ?>"></i></a>

        <!-- <a class="default-image btn <?= 1 ? 'btn-pink' : 'btn-default' ?>"><i class="fa fa-star" title="SET AS DEFAULT TEXT"></i></a> -->
        <a class="delete-image btn btn-danger"><i class="fa fa-trash" title="<?= $view->escape($text_delete_image) ?>"></i></a>
    </div>

</li>
