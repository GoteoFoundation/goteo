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
                    <h3>Añadiendo nueva entrada para <?php if($this['type'] == 'home') echo 'la portada'; else echo 'el pie'; ?></h3>
                    <?php break;
                case 'edit': ?>
                    <h3>Editando la entrada '<?php echo $this['post']->title; ?>'</h3>
                    <?php break;
            } ?>

            <?php if (!empty($this['errors']) || !empty($this['success'])) : ?>
                <div class="widget">
                    <p>
                        <?php echo implode(',', $this['errors']); ?>
                        <?php echo implode(',', $this['success']); ?>
                    </p>
                </div>
            <?php endif; ?>

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
                    <label>Aparece en:</label><br />
                    <input type="checkbox" name="home" value="1" <?php if ($this['type'] == 'home') echo 'selected="selected"'; ?> /> Portada<br />
                    <input type="checkboz" name="footer" value="1" <?php if ($this['type'] == 'footer') echo 'selected="selected"'; ?> /> Pie<br />
    <br />
                    <label for="posts-media">Video:</label><br />
                    Solo entradas nuevas, para gestionar media ir a la gestión de blog.



                    <input type="submit" name="save" value="Guardar" />
                </form>
            </div>
                    
        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';