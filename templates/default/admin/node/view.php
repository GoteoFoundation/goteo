<?php $this->layout('admin/node/layout') ?>

<?php $this->section('admin-node-content') ?>

    <table>
        <tr>
            <td width="140px">Nombre</td>
            <td><?= $this->node->name ?></td>
        </tr>
        <tr>
            <td>Email</td>
            <td><?= $this->node->email ?></td>
        </tr>
        <tr>
            <td>Título</td>
            <td><?= $this->node->subtitle ?></td>
        </tr>
        <tr>
            <td>Presentación</td>
            <td><?= $this->node->description ?></td>
        </tr>
        <tr>
            <td>Logo</td>
            <td><?php echo is_object($this->node->logo) ? '<img src="' . '/img/small/' . $this->node->logo->id . '" alt="Logo" title="' . $this->node->logo->id . '" />' : ''; ?></td>
        </tr>
        <tr>
            <td>Sello</td>
            <td><?php echo is_object($this->node->label) ? '<img src="' . '/img/small/' . $this->node->label->id . '" alt="Label" title="' . $this->node->label->id . '" />' : ''; ?></td>
        </tr>
        <tr>
            <td>Color de fondo (HEX)</td>
            <td><span style="display: inline-block;width:20px;height: 20px;vertical-align: middle;background:<?= $this->node->owner_background ?>"></span> <?= $this->node->owner_background ?></td>
        </tr>
        <tr>
            <td>Twitter</td>
            <td><?= $this->node->twitter ?></td>
        </tr>
        <tr>
            <td>Facebook</td>
            <td><?= $this->node->facebook ?></td>
        </tr>
        <tr>
            <td>Google +</td>
            <td><?= $this->node->google ?></td>
        </tr>
        <tr>
            <td>LinkedIn</td>
            <td><?= $this->node->linkedin ?></td>
        </tr>
        <tr>
            <td>Localización</td>
            <td><?= $this->node->location ?></td>
        </tr>
    </table>

<?php $this->replace() ?>
