<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div>
                <h2>Preguntas frecuentes</h2>
            </div>

            <div class="sub-menu">
                <div class="admin-menu">
                    <ul>
                        <li class="home"><a href="/admin">Mainboard</a></li>
                        <li class="checking"><a href="/admin/checking">Revisión de proyectos</a></li>
                        <li><a href="?filter=<?php echo $this['filter']; ?>&add">Añadir pregunta</a></li>
                    </ul>
                </div>
            </div>

        </div>

        <div id="main">
            <?php if (!empty($this['filter'])) : ?>
                <h3>Viendo las preguntas de la sección '<?php echo $this['sections'][$this['filter']]; ?>'</h3>
            <?php endif;?>
                
            <?php if (!empty($this['errors'])) {
                echo '<pre>' . print_r($this['errors'], 1) . '</pre>';
            } ?>

            <?php if (!empty($this['success'])) {
                echo '<pre>' . print_r($this['success'], 1) . '</pre>';
            } ?>

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
                            <td><a href="?filter=<?php echo $this['filter']; ?>&up=<?php echo $faq->id; ?>">[&uarr;]</a></td>
                            <td><a href="?filter=<?php echo $this['filter']; ?>&down=<?php echo $faq->id; ?>">[&darr;]</a></td>
                            <td><a href="?filter=<?php echo $this['filter']; ?>&edit=<?php echo $faq->id; ?>">[Editar]</a></td>
                            <td><a href="?filter=<?php echo $this['filter']; ?>&remove=<?php echo $faq->id; ?>">[Quitar]</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>
                
        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';