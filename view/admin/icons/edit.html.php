<?php

use Goteo\Library\Text;

?>
<div class="widget board">
    <!-- super form -->
    <form method="post" action="/admin/icons?filter=<?php echo $this['filter']; ?>">

        <input type="hidden" name="action" value="<?php echo $this['action']; ?>" />
        <input type="hidden" name="id" value="<?php echo $this['icon']->id; ?>" />
        <input type="hidden" name="order" value="<?php echo $this['icon']->order; ?>" />

        <label for="icon-group">Agrupación:</label><br />
        <select id="icon-group" name="group">
            <option value="">Ambas</option>
            <?php foreach ($this['groups'] as $id=>$name) : ?>
            <option value="<?php echo $id; ?>"<?php if ($id == $this['icon']->group) echo ' selected="selected"'; ?>><?php echo $name; ?></option>
            <?php endforeach; ?>
        </select>
<br />
        <label for="icon-name">Nombre:</label><br />
        <input type="text" name="name" id="icon-name" value="<?php echo $this['icon']->name; ?>" />
<br />
        <label for="icon-description">Texto tooltip:</label><br />
        <textarea name="description" id="icon-description" cols="60" rows="10"><?php echo $this['icon']->description; ?></textarea>



        <input type="submit" name="save" value="Guardar" />
    </form>
</div>