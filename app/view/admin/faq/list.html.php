<?php
use Goteo\Library\Text,
    Goteo\Core\ACL;

$translator = ( isset($_SESSION['user']->roles['translator']) ) ? true : false;
$filters = $vars['filters'];
?>
<a href="/admin/faq/add" class="button">Añadir pregunta</a>

<div class="widget board">
    <form id="sectionfilter-form" action="/admin/faq" method="get">
        <label for="section-filter">Mostrar las preguntas de:</label>
        <select id="section-filter" name="section" onchange="document.getElementById('sectionfilter-form').submit();">
        <?php foreach ($vars['sections'] as $sectionId=>$sectionName) : ?>
            <option value="<?php echo $sectionId; ?>"<?php if ($filters['section'] == $sectionId) echo ' selected="selected"';?>><?php echo $sectionName; ?></option>
        <?php endforeach; ?>
        </select>
    </form>
</div>

<div class="widget board">
    <?php if (!empty($vars['faqs'])) : ?>
    <table>
        <thead>
            <tr>
                <td><!-- Edit --></td>
                <th>Título</th> <!-- title -->
                <th>Posición</th> <!-- order -->
                <td><!-- Move up --></td>
                <td><!-- Move down --></td>
                <td><!-- Traducir--></td>
                <td><!-- Remove --></td>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($vars['faqs'] as $faq) : ?>
            <tr>
                <td><a href="/admin/faq/edit/<?php echo $faq->id; ?>">[Editar]</a></td>
                <td><?php echo $faq->title; ?></td>
                <td><?php echo $faq->order; ?></td>
                <td><a href="/admin/faq/up/<?php echo $faq->id; ?>">[&uarr;]</a></td>
                <td><a href="/admin/faq/down/<?php echo $faq->id; ?>">[&darr;]</a></td>
                <?php if ($translator) : ?>
                <td><a href="/translate/faq/edit/<?php echo $faq->id; ?>" >[Traducir]</a></td>
                <?php endif; ?>
                <td><a href="/admin/faq/remove/<?php echo $faq->id; ?>" onclick="return confirm('Seguro que deseas eliminar este registro?');">[Quitar]</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>
