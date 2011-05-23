<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="main">
            <?php switch ($this['action']) {
                case 'add': ?>
                    <h2>Añadiendo nueva entrada para la portada</h2>
                    <?php break;
                case 'edit': ?>
                    <h2>Editando la entrada '<?php echo $this['post']->title; ?>'</h2>
                    <?php break;
            } ?>

            <p><a href="/admin">Volver al Menú de administración</a></p>
            <p><a href="/admin/posts">Volver a la lista de entradas para portada</a></p>

            <?php if (!empty($this['errors'])) :
                echo '<p>';
                foreach ($this['errors'] as $error) : ?>
                    <span style="color:red;"><?php echo $error; ?></span><br />
            <?php endforeach;
                echo '</p>';
                endif;
            ?>

            <form method="post" action="/admin/posts">

                <input type="hidden" name="action" value="<?php echo $this['action']; ?>" />
                <input type="hidden" name="order" value="<?php echo $this['post']->order; ?>" />

                <input type="hidden" name="id" value="<?php echo $this['post']->id; ?>" />
<br />
                <label for="posts-title">Título:</label><br />
                <input type="text" name="title" id="posts-title" value="<?php echo $this['post']->title; ?>" />
<br />
                <label for="posts-description">Descripción:</label><br />
                <textarea name="description" id="posts-description" cols="60" rows="10"><?php echo $this['post']->description; ?></textarea>

<br />
                <label for="posts-media">Video:</label><br />
                <textarea name="media" id="posts-media" cols="30" rows="5"><?php echo $this['post']->media; ?></textarea>



                <input type="submit" name="save" value="Guardar" />
            </form>

        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';