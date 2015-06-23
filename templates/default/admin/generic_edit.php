<?php $this->layout('admin/layout') ?>


<?php $this->section('admin-content') ?>

<div class="widget board">
    <form action="<?= $this->form['action'] ?>" method="post" enctype="multipart/form-data">
        <dl>
            <?php foreach ($this->form['fields'] as $id => $field) : ?>
                <dt><label for="<?= $id ?>"><?= $field['label'] ?></label></dt>
                <dd><?php switch ($field['type']) {
                    case 'text': ?>
                        <input type="text" id="<?= $id ?>" name="<?= $field['name'] ?>" <?= $field['properties'] ?> value="<?php $name = $field['name']; echo $this->data->$name; ?>" />
                    <?php break;
                    case 'hidden': ?>
                        <input type="hidden" name="<?= $field['name'] ?>" <?= $field['properties'] ?> value="<?php $name = $field['name']; echo $this->data->$name; ?>" />
                    <?php break;
                    case 'textarea': ?>
                        <textarea id="<?= $id ?>" name="<?= $field['name'] ?>" <?= $field['properties'] ?>><?php $name = $field['name']; echo $this->data->$name; ?></textarea>
                    <?php break;
                    case 'image':
                         $name = $field['name'];
                        ?>
                        <input type="file" id="<?= $id ?>" name="<?= $field['name'] ?>" <?= $field['properties'] ?> value="<?php $name = $field['name']; echo $this->data->$name; ?>" /> <br />
                        <?php if (!empty($this->data->$name)) : ?>
                            <img src="<?= SITE_URL?>/image/<?= $this->data->$name; ?>/110/110" alt="<?= $field['name'] ?>" /><br />
                            <input type="hidden" name="<?= $field['name'] ?>" value="<?= $this->data->$name; ?>" />
                            <input type="submit" name="image-<?= md5($this->data->$name) ?>-remove" value="Quitar" />
                        <?php endif; ?>
                    <?php break;
                } ?></dd>

            <?php endforeach; ?>
        </dl>
        <input type="submit" name="<?= $this->form['submit']['name'] ?>" value="<?= $this->form['submit']['label'] ?>" />

    <?php if($this->translator): ?>
        <p>
            <label for="mark-pending">Marcar como pendiente de traducir</label>
            <input id="mark-pending" type="checkbox" name="pending" value="1" />
        </p>
    <?php endif ?>

    </form>
</div>

<?php $this->replace() ?>
