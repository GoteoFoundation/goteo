<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

// no cache para textos
define('GOTEO_ADMIN_NOCACHE', true);

$text = new stdClass();

$text->id = $this['id'];
$text->purpose = Text::getPurpose($this['id']);
$text->text = Text::get($this['id']);

?>
<div class="widget board">
    <h3 class="title">Editando el texto '<?php echo $text->id; ?>'</h3>

    <fieldset>
        <legend>Propósito de este texto</legend>
        <blockquote><?php echo $text->purpose; ?></blockquote>
    </fieldset>

    <form action="/translate/texts/edit/<?php echo $text->id ?>/<?php echo $this['filter'] ?>" method="post" >

        <textarea name="text" cols="120" rows="10"><?php echo $text->text; ?></textarea><br />
        <input type="submit" name="save" value="Guardar" />

    </form>
</div>