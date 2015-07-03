<?php $this->layout('admin/projects/edit_layout') ?>

<?php $this->section('admin-project-content') ?>
<?php

$project = $this->project;
$images = $this->images;
$sections = $this->image_sections;


?>
    <?php if ($images) : ?>
    <form id="images_form" action="/admin/projects/images/<?= $project->id ?>" method="post">
    <table>
        <tbody>
        <?php foreach ($sections as $sec => $secName) :
            if (!$images[$sec]) continue;
            ?>
            <tr>
                <td colspan="3" style="text-align: left;"><h3><?= $secName ?></h3></td>
            </tr>
            <tr>
                <th>Orden</th>
                <th></th>
                <th></th>
            </tr>
            <?php foreach ($images[$sec] as $image) : ?>
            <tr>
                <td style="width:100px;text-align: center;">
                    <?= $this->html('input', ['type' => 'text', 'name' => 'order[' . $image->image . ']', 'value' => $image->order, 'attribs' => ['style' => 'width:20px']]) ?>
                </td>
                <td style="width:105px;text-align: left;"><img src="<?= $image->imageData->getLink(175, 100) ?>" alt="image" /></td>
                <td>
                    <p>
                        <label>Sección:</label>
                        <?= $this->html('select', ['name' => 'section[' . $image->image . ']', 'value' => $sec, 'options' => $sections]) ?>
                    </p>
                    <p>
                        <label>Enlace:<br /><?= $this->html('input', ['type' => 'text', 'name' => 'url[' . $image->image . ']', 'value' => $image->url, 'attribs' => ['style' => 'width:100%']]) ?></label>
                    </p>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <?php endforeach ?>
        <?php endforeach ?>
        </tbody>

    </table>
        <input type="submit" name="apply_changes" value="Aplicar" />
    </form>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif ?>


<?php $this->replace() ?>
