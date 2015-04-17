<?php

use Goteo\Library\Text,
    Goteo\Model,
    Goteo\Core\Redirection;

$node = $vars['node'];

if (!$node instanceof Model\Node) {
    throw new Redirection('/admin');
}
?>
<a href="/admin/node/edit" class="button">Editar</a>
&nbsp;&nbsp;&nbsp;
<a href="/translate/node/<?php echo $node->id ?>/data/edit" class="button">Traducir</a>
&nbsp;&nbsp;&nbsp;
<a href="/admin/node/admins" class="button">Ver administradores</a>
<div class="widget">
    <table>
        <tr>
            <td width="140px">Nombre</td>
            <td><?php echo $node->name ?></td>
        </tr>
        <tr>
            <td>Email</td>
            <td><?php echo $node->email ?></td>
        </tr>
        <tr>
            <td>Título</td>
            <td><?php echo $node->subtitle ?></td>
        </tr>
        <tr>
            <td>Presentación</td>
            <td><?php echo $node->description ?></td>
        </tr>
        <tr>
            <td>Logo</td>
            <td><?php echo is_object($node->logo) ? '<img src="' . SITE_URL . '/image/' . $node->logo->id . '/128/128" alt="Logo" />' : ''; ?></td>
        </tr>
        <tr>
            <td>Localización</td>
            <td><?php echo $node->location ?></td>
        </tr>
    </table>
</div>
