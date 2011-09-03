<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

// no cache para textos
define('GOTEO_ADMIN_NOCACHE', true);

$text = new stdClass();

$text->id = $this['id'];
$text->purpose = Text::getPurpose($this['id']);
$text->text = Text::getTrans($this['id']);

?>
<div class="widget board">
    <fieldset>
        <legend>Texto en español</legend>
        <blockquote><?php echo htmlentities(utf8_decode($text->purpose)); ?></blockquote>
    </fieldset>

    <form action="/translate/texts/edit/<?php echo $text->id ?>/<?php echo $this['filter'] . '&page=' . $_GET['page'] ?>" method="post" >

        <textarea name="text" cols="100" rows="10"><?php echo $text->text; ?></textarea><br />
        <input type="submit" name="save" value="Guardar" />

    </form>
</div>