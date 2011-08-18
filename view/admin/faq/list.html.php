<?php

use Goteo\Library\Text;

?>
<a href="/admin/faq/add/?filter=<?php echo $this['filter']; ?>" class="button red">Añadir pregunta</a>

<div class="widget board">
    <form id="sectionfilter-form" action="/admin/faq" method="get">
        <label for="section-filter">Mostrar las preguntas de:</label>
        <select id="section-filter" name="filter" onchange="document.getElementById('sectionfilter-form').submit();">
        <?php foreach ($this['sections'] as $sectionId=>$sectionName) : ?>
            <option value="<?php echo $sectionId; ?>"<?php if ($this['filter'] == $sectionId) echo ' selected="selected"';?>><?php echo $sectionName; ?></option>
        <?php endforeach; ?>
        </select>
    </form>
</div>

<div class="widget board">
    <table>
        <thead>
            <tr>
                <th>Título</th> <!-- title -->
                <th>Posición</th> <!-- order -->
                <td><!-- Move up --></td>
                <td><!-- Move down --></td>
                <td><!-- Edit --></td>
                <td><!-- Remove --></td>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this['faqs'] as $faq) : ?>
            <tr>
                <td><?php echo $faq->title; ?></td>
                <td><?php echo $faq->order; ?></td>
                <td><a href="/admin/faq/up/<?php echo $faq->id; ?>/?filter=<?php echo $this['filter']; ?>">[&uarr;]</a></td>
                <td><a href="/admin/faq/down/<?php echo $faq->id; ?>/?filter=<?php echo $this['filter']; ?>">[&darr;]</a></td>
                <td><a href="/admin/faq/edit/<?php echo $faq->id; ?>/?filter=<?php echo $this['filter']; ?>">[Editar]</a></td>
                <td><a href="/admin/faq/remove/<?php echo $faq->id; ?>/?filter=<?php echo $this['filter']; ?>">[Quitar]</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
</div>