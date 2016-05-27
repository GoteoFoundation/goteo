<?php $this->layout('admin/layout') ?>

<?php $this->section('admin-content') ?>

<?php

$milestone = $this->milestone;


?>
<p><a href="/admin/milestones" class="button"><?= $this->text('admin-back') ?></a></p>
<div class="widget board">
    <form method="post" action="<?= $this->action ?>" enctype="multipart/form-data">

        <p>
            <label for="type">Tipo:</label><br />
            <select name="type" id="type">
            <?php foreach ($this->types as $key => $type ): ?>
                <option value="<?= $key ?>" <?= $key==$milestone->type ? 'selected' : '' ?> ><?= $type ?></option>  
            <?php endforeach; ?>
            </select>
        </p>

        <p>
            <label for="description">Description:</label><br />
            <textarea name="description" id="description" style="width:100%"><?= $milestone->description ?></textarea><br/>
        </p>

        <p>
            <label for="link">Tipo:</label><br />
            <select name="link" id="link">
                <option value="">Sin link</option>  
            <?php foreach ($this->links_types as $key => $link ): ?>
                <option value="<?= $key ?>" <?= $key==$milestone->link ? 'selected' : '' ?> ><?= $link ?></option>  
            <?php endforeach; ?>
            </select>
        </p>

        <p>
        <label for="image">Imagen (Opcional):</label><br/>
        <input type="file" id="image" name="image"/>
        <?php if (!empty($milestone->image)) : ?>
            <br/>
            <input type="hidden" name="prev_image" value="<?= $milestone->image->id ?>"/>
            <img src="<?= $milestone->image->getLink(700, 150, true) ?>" title="Milestone imagen" alt="falta imagen"/>
            <input type="submit" name="image-<?= $milestone->image->hash ?>-remove" value="Quitar" />
        <?php endif ?>
        </p>

        <p>
        <label for="image_emoji">Emojicono:</label><br/>
        <input type="file" id="image_emoji" name="image_emoji"/>
        <?php if (!empty($milestone->image_emoji)) : ?>
            <br/>
            <input type="hidden" name="prev_image_emoji" value="<?= $milestone->image_emoji->id ?>"/>
            <img src="<?= $milestone->image_emoji->getLink(100, 100, true) ?>" title="Milestone emojicono" alt="falta imagen"/>
            <input type="submit" name="image-emoji-<?= $milestone->image_emoji->hash ?>-remove" value="Quitar" />
        <?php endif ?>
        </p>

        <input type="submit" name="save" value="Guardar" />

    </form>
</div>
<?php $this->replace() ?>
