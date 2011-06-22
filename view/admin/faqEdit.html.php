<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

    <script type="text/javascript">

    jQuery(document).ready(function ($) {

        $('#faq-section').change(function () {
            order = $.ajax({async: false, url: '<?php echo SITE_URL; ?>/ws/get_faq_order/'+$('#faq-section').val()}).responseText;
            $('#faq-order').val(order);
            $('#faq-num').html(order);
        });
        
    });
    </script>

        <div id="sub-header">
            <div>
                <h2>Preguntas frecuentes</h2>
            </div>

            <div class="sub-menu">
                <div class="admin-menu">
                    <ul>
                        <li class="home"><a href="/admin">Mainboard</a></li>
                        <li class="checking"><a href="/admin/checking">Revisión de proyectos</a></li>
                        <li><a href="/admin/faq?filter=<?php echo $this['filter']; ?>">Preguntas frecuentes</a></li>
                    </ul>
                </div>
            </div>

        </div>

        <div id="main">
            <?php switch ($this['action']) {
                case 'add': ?>
                    <h3>Añadiendo nueva pregunta frecuente</h3>
                    <?php break;
                case 'edit': ?>
                    <h3>Editando la pregunta '<?php echo $this['faq']->title; ?>'</h3>
                    <?php break;
            } ?>

            <?php if (!empty($this['errors'])) {
                echo '<pre>' . print_r($this['errors'], 1) . '</pre>';
            } ?>

            <div class="widget board">
                <form method="post" action="/admin/faq/?filter=<?php echo $this['filter']; ?>">

                    <input type="hidden" name="action" value="<?php echo $this['action']; ?>" />
                    <input type="hidden" name="id" value="<?php echo $this['faq']->id; ?>" />

                    <p>
                    <?php if ($this['action'] == 'add') : ?>
                        <label for="faq-section">Sección:</label><br />
                        <select id="faq-section" name="section">
                            <option value="" disabled>Elige la sección</option>
                            <?php foreach ($this['sections'] as $id=>$name) : ?>
                            <option value="<?php echo $id; ?>"<?php if ($id == $this['faq']->section) echo ' selected="selected"'; ?>><?php echo $name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php else : ?>
                        <label for="faq-section">Sección: <?php echo $this['sections'][$this['faq']->section]; ?></label><br />
                        <input type="hidden" name="section" value="<?php echo $this['faq']->section; ?>" />
                    <?php endif; ?>
                    </p>

                    <p>
                        <label for="faq-title">Título:</label><br />
                        <input type="text" name="title" id="faq-title" value="<?php echo $this['faq']->title; ?>" />
                    </p>

                    <p>
                        <label for="faq-description">Descripción:</label><br />
                        <textarea name="description" id="faq-description" cols="60" rows="10"><?php echo $this['faq']->description; ?></textarea>
                    </p>

                    <p>
                        <label for="faq-order">Posición:</label><br />
                        <select name="move">
                            <option value="same" selected="selected" disabled>Tal cual</option>
                            <option value="up">Antes de </option>
                            <option value="down">Después de </option>
                        </select>&nbsp;
                        <input type="text" name="order" id="faq-order" value="<?php echo $this['faq']->order; ?>" size="4" />
                        &nbsp;de&nbsp;<span id="faq-num"><?php echo $this['faq']->cuantos; ?></span>
                    </p>


                    <input type="submit" name="save" value="Guardar" />
                </form>
            </div>

        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';