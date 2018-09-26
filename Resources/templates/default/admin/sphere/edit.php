<?php $this->layout('admin/layout') ?>

<?php $this->section('admin-content') ?>

<?php

$sphere = $this->sphere;

?>
<p><a href="/admin/sphere" class="button"><?= $this->text('admin-back') ?></a></p>
<div class="widget board">
    <form method="post" action="<?= $this->action ?>" enctype="multipart/form-data">

        <p>
            <label for="name">Nombre:</label><br />
            <input type="text" name="name" id="name" value="<?= $sphere->name ?>" maxlength="24" style="width:150px;" />
        </p>

        <p>
        <label for="image">Imagen (Opcional):</label><br/>
        <input type="file" id="image" name="image"/>
        <?php if (!empty($sphere->image)) : ?>
            <br/>
            <input type="hidden" name="prev_image" value="<?= $sphere->image->id ?>"/>
            <img src="<?= $sphere->image->getLink(700, 150, true) ?>" title="Sphere imagen" alt="falta imagen"/>
            <input type="submit" name="image-<?= $sphere->image->hash ?>-remove" value="Quitar" />
        <?php endif ?>
        </p>
        <p>
            <label>Landing matchfunding:</label><br />
            <label>
                <input type="radio" name="landing_match" id="landing_match" value="1"<?php if ($sphere->landing_match) echo ' checked="checked"'; ?> /> SÍ</label>
            &nbsp;&nbsp;&nbsp;
            <label>
                <input type="radio" name="landing_match" id="landing_match" value="0" <?php if (!($sphere->landing_match)) echo ' checked="checked"'; ?> />NO
            </label>
        </p>
         <p>
            <label for="name">Orden:</label><br />
            <input type="text" name="order" id="order" value="<?= $sphere->order ?>" maxlength="5" style="width:50px;" />
        </p>


        <input type="submit" name="save" value="Guardar" />

    </form>
</div>
<?php $this->replace() ?>
