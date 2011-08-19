<?php
use Goteo\Library\Text,
    Goteo\Core\ACL;

$translator = ACL::check('/translate') ? true : false;
?>
<a href="/admin/criteria/add/?filter=<?php echo $this['filter']; ?>" class="button red">Añadir criterio</a>

<div class="widget board">
    <form id="sectionfilter-form" action="/admin/criteria" method="get">
        <label for="section-filter">Mostrar los criterios de la sección:</label>
        <select id="section-filter" name="filter" onchange="document.getElementById('sectionfilter-form').submit();">
        <?php foreach ($this['sections'] as $sectionId=>$sectionName) : ?>
            <option value="<?php echo $sectionId; ?>"<?php if ($this['filter'] == $sectionId) echo ' selected="selected"';?>><?php echo $sectionName; ?></option>
        <?php endforeach; ?>
        </select>
    </form>
</div>

<div class="widget board">
    <?php if (!empty($this['criterias'])) : ?>
    <table>
        <thead>
            <tr>
                <td><!-- Edit --></td>
                <th>Título</th> <!-- title -->
                <th>Posición</th> <!-- order -->
                <td><!-- Move up --></td>
                <td><!-- Move down --></td>
                <th><!-- Traducir--></th>
                <td><!-- Remove --></td>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this['criterias'] as $criteria) : ?>
            <tr>
                <td><a href="/admin/criteria/edit/<?php echo $criteria->id; ?>/?filter=<?php echo $this['filter']; ?>">[Editar]</a></td>
                <td><?php echo $criteria->title; ?></td>
                <td><?php echo $criteria->order; ?></td>
                <td><a href="/admin/criteria/up/<?php echo $criteria->id; ?>/?filter=<?php echo $this['filter']; ?>">[&uarr;]</a></td>
                <td><a href="/admin/criteria/down/<?php echo $criteria->id; ?>/?filter=<?php echo $this['filter']; ?>">[&darr;]</a></td>
                <?php if ($translator) : ?>
                <td><a href="/translate/contents/edit/criteria-<?php echo $criteria->id; ?>" target="_blank">[Traducir]</a></td>
                <?php endif; ?>
                <td><a href="/admin/criteria/remove/<?php echo $criteria->id; ?>/?filter=<?php echo $this['filter']; ?>" onclick="return confirm('Seguro que deseas eliminar este registro?');">[Quitar]</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
    <?php else : ?>
    <p>No se han encontrado registros</p>
    <?php endif; ?>
</div>