<?php

use Goteo\Library\Text,
    Goteo\Library\Content;

$bodyClass = 'admin';

$table = $this['table'];
$id = $this['id'];
$content = $this['content'];

$sizes = array(
    'title'       => 'cols="100" rows="2"',
    'name'        => 'cols="100" rows="1"',
    'description' => 'cols="100" rows="4"',
    'review'      => 'cols="100" rows="4"',
    'url'         => 'cols="100" rows="1"',
    'text'        => 'cols="100" rows="10"'
);
?>
<script type="text/javascript" src="<?php echo SRC_URL; ?>/view/js/ckeditor/ckeditor.js"></script>

<div class="widget board">
    <form action="/translate/<?php echo $table ?>/edit/<?php echo $id ?>/<?php echo $this['filter'] . '&page=' . $_GET['page'] ?>" method="post" >
        <input type="hidden" name="table" value="<?php echo $table ?>" />
        <input type="hidden" name="id" value="<?php echo $id ?>" />
        <input type="hidden" name="lang" value="<?php echo $_SESSION['translate_lang'] ?>" />
        <?php if ($table == 'post') : ?><input type="hidden" name="blog" value="<?php echo $content->blog ?>" /><?php endif; ?>


        <?php foreach (Content::$fields[$table] as $field=>$fieldName) : 
        if(($field=="text")&&($table=="post"))
            $class_field='class="ckeditor-text"';
        ?>
        <p>
            <label for="<?php echo 'id'.$field ?>"><?php echo $fieldName ?></label><br />
            <textarea id="<?php echo 'id'.$field ?>" name="<?php echo $field ?>" <?php echo $class_field; ?><?php echo $sizes[$field] ?>><?php echo $content->$field; ?></textarea>
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
            <?php echo nl2br($content->$campo); ?>
        </blockquote>
        <br />
    <?php endforeach;  ?>


</div>
