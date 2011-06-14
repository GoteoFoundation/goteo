<?php

use Goteo\Library\Text;

$level = (int) $this['level'] ?: 3;

?>
<div class="widget">
    <h<?php echo $level + 2?>>Escribe tu comentario</h<?php echo $level + 2?>>
    <form method="post" action="/message/post/<?php echo $this['post']; ?>/<?php echo $this['project']; ?>">
        <textarea name="message" cols="50" rows="5"></textarea>
        <input class="button" type="submit" value="Enviar" />
    </form>
</div>
