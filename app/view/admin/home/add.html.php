<?php

use Goteo\Library\Text,
    Goteo\Model\Home;

$home = $vars['home'];
$availables = $vars['availables'];
?>
<form method="post" action="/admin/home" >
    <input type="hidden" name="action" value="<?php echo $vars['action'] ?>" />
    <input type="hidden" name="type" value="<?php echo $home->type ?>" />
    <input type="hidden" name="order" value="<?php echo $home->order ?>" />

<p>
    <label for="home-item">Elemento:</label><br />
    <select id="home-item" name="item">
    <?php foreach ($availables as $item=>$name) : ?>
        <option value="<?php echo $item; ?>"><?php echo $name; ?></option>
    <?php endforeach; ?>
    </select>
</p>

    <input type="submit" name="save" value="A&ntilde;adir" />
</form>
