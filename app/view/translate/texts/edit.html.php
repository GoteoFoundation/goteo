<?php
use Goteo\Library\Text;

$bodyClass = 'admin';

$text = $this['text'];
?>
<div class="widget board">
    <fieldset>
        <legend>Texto en español</legend>
        <blockquote><?php echo htmlentities($text->purpose); ?></blockquote>
    </fieldset>

    <form action="/translate/texts/edit/<?php echo $text->id ?>/<?php echo $this['filter'] . '&page=' . $_GET['page'] ?>" method="post">
        <input type="hidden" name="lang" value="<?php echo $_SESSION['translate_lang'] ?>"/>
        <textarea name="text" cols="100" rows="10"><?php echo $text->text; ?></textarea><br/>
        <input type="submit" name="save" value="Guardar"/>

    </form>
</div>