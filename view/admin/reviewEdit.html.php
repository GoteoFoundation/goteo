<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

$filters = $this['filters'];

//arrastramos los filtros
$filter = "?status={$filters['status']}&checker={$filters['checker']}";

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div>
                <h2>Revisión de proyectos</h2>
            </div>

            <div class="sub-menu">
                <div class="admin-menu">
                    <ul>
                        <li class="home"><a href="/admin">Mainboard</a></li>
                        <li><a href="/admin/checking/?filter=<?php echo $this['filter']; ?>">Revisiones</a></li>
                    </ul>
                </div>
            </div>

        </div>

        <div id="main">
            <?php switch ($this['action']) {
                case 'add': ?>
                    <h3>Iniciando nueva revisión para el proyecto '<?php echo $this['project']->name; ?>'</h3>
                    <?php break;
                case 'edit': ?>
                    <h3>Editando la revisión para el proyecto '<?php echo $this['project']->name; ?>'</h3>
                    <?php break;
            } ?>

            <?php if (!empty($this['errors'])) {
                echo '<pre>' . print_r($this['errors'], 1) . '</pre>';
            } ?>

            <div class="widget board">
                <p>
                    <label for="review-comment">Comentario del creador:</label><br />
                    <textarea id="review-comment" cols="60" rows="10"><?php echo $this['project']->comment; ?></textarea>
                </p>

                <form method="post" action="/admin/checking/edit/?filter=<?php echo $this['filter']; ?>">

                    <input type="hidden" name="action" value="<?php echo $this['action']; ?>" />
                    <input type="hidden" name="id" value="<?php echo $this['review']->id; ?>" />
                    <input type="hidden" name="project" value="<?php echo $this['project']->id; ?>" />

                    <p>
                        <label for="faq-description">Descripción:</label><br />
                        <textarea name="description" id="faq-description" cols="60" rows="10"><?php echo $this['faq']->description; ?></textarea>
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