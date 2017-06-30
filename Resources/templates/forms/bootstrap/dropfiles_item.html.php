<li data-name="<?= $view->escape($file_name) ?>">
    <div class="image" style="background-image:url(<?= $file_url ?>)"></div>
    <?= $hidden_input ?>
    <div class="options">
        <!-- <a class="default-image btn <?= 1 ? 'btn-pink' : 'btn-default' ?>"><i class="fa fa-star" title="SET AS DEFAULT TEXT"></i></a> -->
        <a class="delete-image btn btn-danger"><i class="fa fa-trash" title="REMOVE IMAGE TEXT"></i></a>
    </div>

</li>
