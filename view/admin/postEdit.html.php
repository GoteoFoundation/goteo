<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div>
                <h2>Entradas para la portada</h2>
            </div>

            <div class="sub-menu">
                <div class="admin-menu">
                    <ul>
                        <li class="home"><a href="/admin">Mainboard</a></li>
                        <li class="checking"><a href="/admin/checking">Revisión de proyectos</a></li>
                        <li><a href="/admin/posts">Entradas</a></li>
                    </ul>
                </div>
            </div>

        </div>

        <div id="main">
            <?php switch ($this['action']) {
                case 'add': ?>
                    <h3>Añadiendo nueva entrada para la portada</h3>
                    <?php break;
                case 'edit': ?>
                    <h3>Editando la entrada '<?php echo $this['post']->title; ?>'</h3>
                    <?php break;
            } ?>

            <?php if (!empty($this['errors'])) {
                echo '<pre>' . print_r($this['errors'], 1) . '</pre>';
            } ?>

            <div class="widget board">
                <form method="post" action="/admin/posts">

                    <input type="hidden" name="action" value="<?php echo $this['action']; ?>" />
                    <input type="hidden" name="order" value="<?php echo $this['post']->order; ?>" />
                    <input type="hidden" name="blog" value="1" />
                    <input type="hidden" name="image" value="<?php echo $this['post']->image; ?>" />
                    <input type="hidden" name="date" value="<?php echo date('Y-m-d'); ?>" />
                    <input type="hidden" name="allow" value="0" />

                    <input type="hidden" name="id" value="<?php echo $this['post']->id; ?>" />
    <br />
                    <label for="posts-title">Título:</label><br />
                    <input type="text" name="title" id="posts-title" value="<?php echo $this['post']->title; ?>" />
    <br />
                    <label for="posts-text">Descripción:</label><br />
                    <textarea name="text" id="posts-text" cols="60" rows="10"><?php echo $this['post']->text; ?></textarea>

    <br />
                    <label for="posts-media">Video:</label><br />
                    <textarea name="media" id="posts-media" cols="30" rows="5"><?php echo $this['post']->media; ?></textarea>



                    <input type="submit" name="save" value="Guardar" />
                </form>
            </div>
                    
        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';