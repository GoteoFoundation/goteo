<?php

use Goteo\Library\Text,
    Goteo\Library\Content;

$bodyClass = 'admin';

list($table, $id) = explode('-', $this['id']);

$content = Content::get($table, $id, $_SESSION['translator_lang']);

$sizes = array(
    'title'       => 'cols="120" rows="2"',
    'name'        => 'cols="120" rows="1"',
    'description' => 'cols="120" rows="4"',
    'url'         => 'cols="120" rows="1"',
    'text'        => 'cols="120" rows="10"'
);
?>
<div class="widget board">
    <h3 class="title">Editando el registro '<?php echo $id ?>' de la tabla '<?php echo Content::$tables[$table] ?>'</h3>

    <form action="/translate/contents/edit/<?php echo $text->id ?>/<?php echo $this['filter'] ?>" method="post" >
        <input type="hidden" name="table" value="<?php echo $table ?>" />
        <input type="hidden" name="id" value="<?php echo $id ?>" />
        <input type="hidden" name="lang" value="<?php echo $_SESSION['translator_lang'] ?>" />


        <?php foreach (Content::$fields[$table] as $field=>$fieldName) : ?>
        <p>
            <label for="<?php echo 'id'.$field ?>"><?php echo $fieldName ?></label><br />
            <textarea id="<?php echo 'id'.$field ?>" name="<?php echo $field ?>" <?php echo $sizes[$field] ?>><?php echo $content->$field; ?></textarea>
        </p>
        <?php endforeach;  ?>
        <input type="submit" name="save" value="Guardar" />

    </form>
</div>

<div class="widget board">
    <h3>Contenido original</h3>

    <?php foreach (Content::$fields[$table] as $field=>$fieldName) :
        $campo = 'original_'.$field; ?>
        <label for="<?php echo 'id'.$field ?>"><?php echo $fieldName ?>:</label><br />
        <blockquote>
            <?php echo nl2br(htmlentities($content->$campo)); ?>
        </blockquote>
        <br />
    <?php endforeach;  ?>


</div>
