<li data-name="<?= $view->escape($file_name) ?>">
    <div class="image<?= $file_type ? ' file-type-' . $file_type : '' ?>" <?= $file_url ? 'style="background-image:url(' . $view->escape($file_url) . ');background-size:cover"' : '' ?>></div>
    <?= $hidden_input ?>
    <div class="options">

        <a class="add-to-markdown btn btn-default<?= $markdown_link ? '' : ' hidden' ?>" data-target="<?= $view->escape($markdown_link) ?>" title="<?= $view->escape($text_send_to_markdown) ?>"><i class="fa fa-send"></i></a>

        <!-- <a class="default-image btn <?= 1 ? 'btn-cyan' : 'btn-default' ?>"><i class="fa fa-star" title="SET AS DEFAULT TEXT"></i></a> -->
        <a class="delete-image btn btn-default" title="<?= $view->escape($text_delete_image) ?>"><i class="fa fa-trash"></i></a>
    </div>

</li>
