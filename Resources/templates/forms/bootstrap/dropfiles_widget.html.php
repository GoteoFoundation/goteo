<?php // echo $view['form']->block($form, 'form_widget_simple', ['type' => 'file']); ?>

<div class="dropfiles">
    <div class="image-zone" data-section="<?= $key ?>">
        <ul class="list-inline image-list-sortable" id="list-sortable-<?= $key ?>"><?php
        foreach($this->images[$key] as $img) {
            echo $this->insert('dashboard/project/partials/image_list_item', [
                    'image_url' => $img->getLink(300, 300, true),
                    'image_name' => $img->getName()]);
        }
        ?></ul>
        <div class="dragndrop"><div class="dropzone"></div></div>
    </div>
    <p class="text-danger error-msg hidden"></p>
</div>
