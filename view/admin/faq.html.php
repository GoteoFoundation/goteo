<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="main">
            <h2>Preguntas frecuentes</h2>

            <p><a href="/admin">Volver al Menú de administración</a></p>

            <?php if (!empty($this['errors'])) :
                echo '<p>';
                foreach ($this['errors'] as $error) : ?>
                    <span style="color:red;"><?php echo $error; ?></span><br />
            <?php endforeach;
                echo '</p>';
                endif;?>

            <?php if (!empty($this['success'])) : ?>
                <p><span style="color:green;"><?php echo $this['success']; ?></span><br /></p>
            <?php endif;?>

            <?php if (!empty($this['filter'])) : ?>
                <p>Viendo las preguntas de la sección '<?php echo $this['sections'][$this['filter']]; ?>'</p>
            <?php endif;?>

            <form id="sectionfilter-form" action="/admin/faq" method="get">
                <label for="section-filter">Mostrar las preguntas de:</label>
                <select id="section-filter" name="filter" onchange="document.getElementById('sectionfilter-form').submit();">
                <?php foreach ($this['sections'] as $sectionId=>$sectionName) : ?>
                    <option value="<?php echo $sectionId; ?>"<?php if ($this['filter'] == $sectionId) echo ' selected="selected"';?>><?php echo $sectionName; ?></option>
                <?php endforeach; ?>
                </select>
            </form>

            <p><a href="?filter=<?php echo $this['filter']; ?>&add">Añadir pregunta</a></p>

                    <table>
                        <thead>
                            <tr>
                                <td>Título</td> <!-- title -->
                                <td>Posición</td> <!-- order -->
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
                                <td><a href="?filter=<?php echo $this['filter']; ?>&up=<?php echo $faq->id; ?>">[&uarr;]</a></td>
                                <td><a href="?filter=<?php echo $this['filter']; ?>&down=<?php echo $faq->id; ?>">[&darr;]</a></td>
                                <td><a href="?filter=<?php echo $this['filter']; ?>&edit=<?php echo $faq->id; ?>">[Editar]</a></td>
                                <td><a href="?filter=<?php echo $this['filter']; ?>&remove=<?php echo $faq->id; ?>">[Quitar]</a></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>

                    </table>
        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';