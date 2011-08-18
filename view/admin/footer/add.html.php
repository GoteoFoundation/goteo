<?php

use Goteo\Library\Text;

?>
<div class="widget board">
    <form method="post" action="/admin/posts">

        <input type="hidden" name="action" value="<?php echo $this['action']; ?>" />
        <input type="hidden" name="order" value="<?php echo $this['post']->order; ?>" />
        <input type="hidden" name="blog" value="1" />
        <input type="hidden" name="image" value="<?php echo $this['post']->image; ?>" />
        <input type="hidden" name="date" value="<?php echo date('Y-m-d'); ?>" />
        <input type="hidden" name="allow" value="0" />

        <input type="hidden" name="id" value="<?php echo $this['post']->id; ?>" />

        <p>
            <label for="posts-title">Título:</label><br />
            <input type="text" name="title" id="posts-title" value="<?php echo $this['post']->title; ?>" />
        </p>

        <p>
            <label>Aparece en:</label><br />
            <input type="checkbox" name="home" value="1" <?php if ($this['type'] == 'home') echo 'selected="selected"'; ?> /> Portada<br />
            <input type="checkbox" name="footer" value="1" <?php if ($this['type'] == 'footer') echo 'selected="selected"'; ?> /> Pie<br />
        </p>

        <p>Solo entradas rápidas para portada/pie, para gestionar media/imagenes ir a la gestión de blog.</p>


        <input type="submit" name="save" value="Guardar" />
    </form>
</div>