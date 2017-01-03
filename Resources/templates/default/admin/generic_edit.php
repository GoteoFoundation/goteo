<?php $this->layout('admin/layout') ?>


<?php $this->section('admin-content') ?>

<div class="widget board">
    <form action="<?= $this->form['action'] ?>" method="post" enctype="multipart/form-data">
        <dl>
            <?php foreach ($this->raw('form')['fields'] as $id => $field) : ?>
                <dt><label for="<?= $id ?>"><?= $field['label'] ?></label></dt>
                <dd><?php switch ($field['type']) {
                    case 'text': ?>
                        <input type="text" id="<?= $id ?>" name="<?= $field['name'] ?>" <?= $field['properties'] ?> value="<?= $this->data->{$field['name']}; ?>" />
                    <?php break;
                    case 'hidden': ?>
                        <input type="hidden" name="<?= $field['name'] ?>" <?= $field['properties'] ?> value="<?= $this->data->{$field['name']}; ?>" />
                    <?php break;
                    case 'textarea': ?>
                        <textarea id="<?= $id ?>" name="<?= $field['name'] ?>" <?= $field['properties'] ?>><?= $this->data->{$field['name']}; ?></textarea>
                    <?php break;
                    case 'date': ?>
                        <input type="text" id="<?= $id ?>" name="<?= $field['name'] ?>" <?= $field['properties'] ?> value="<?= $this->data->{$field['name']}; ?>" />
                    <?php break;
                    case 'select': ?>
                        <select type="text" id="<?= $id ?>" name="<?= $field['name'] ?>" <?= $field['properties'] ?>>
                        <?php foreach($field['values'] as $key => $val) :?>
                            <option value="<?= $key ?>"><?= $val ?></option>
                        <?php endforeach ?>
                        </select>
                    <?php break;
                    case 'image':
                         $name = $field['name'];
                        ?>
                        <input type="file" id="<?= $id ?>" name="<?= $field['name'] ?>" <?= $field['properties'] ?> value="<?= $this->data->{$field['name']}; ?>" /> <br />
                        <?php if (!empty($this->data->$name)) : ?>
                            <img src="<?= SITE_URL?>/image/<?= $this->data->$name; ?>/110/110" alt="<?= $field['name'] ?>" /><br />
                            <input type="hidden" name="<?= $field['name'] ?>" value="<?= $this->data->$name; ?>" />
                            <input type="submit" name="image-<?= md5($this->data->$name) ?>-remove" value="Quitar" />
                        <?php endif; ?>
                    <?php break;
                } ?></dd>

            <?php endforeach; ?>

        <?php
        if($this->location) {
            echo $this->insert('admin/partials/generic_location');
        }
        ?>
        </dl>
    <?php if($this->translator): ?>
        <dt>
            <label for="mark-pending">Marcar como pendiente de traducir</label>
        </dt>
        <dl>
            <input id="mark-pending" type="checkbox" name="pending" value="1" />
        </dl>
    <?php endif ?>

        <input type="submit" name="<?= $this->form['submit']['name'] ?>" value="<?= $this->form['submit']['label'] ?>" />


    </form>
</div>

<?php $this->replace() ?>
