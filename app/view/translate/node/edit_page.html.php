<?php

use Goteo\Library\Text,
    Goteo\Library\page,
    Goteo\Model;

$node = $vars['node'];
$id   = $vars['id'];

$original = Page::get($id, $node, 'es');
$data     = Page::get($id, $node, $_SESSION['translate_lang']);

$bodyClass = 'admin';
?>
<script type="text/javascript" src="<?php echo SRC_URL; ?>/view/js/ckeditor/ckeditor.js"></script>

<script type="text/javascript">
$(document).ready(function(){
	// Lanza wysiwyg contenido
	CKEDITOR.replace('richtext_content', {
		toolbar: 'Full',
		toolbar_Full: [
				['Source','-'],
				['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print', 'SpellChecker', 'Scayt'],
				['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
				'/',
				['Bold','Italic','Underline','Strike'],
				['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
				['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
				['Link','Unlink','Anchor'],
                ['Image','Format','FontSize'],
			  ],
		skin: 'kama',
		language: 'es',
		height: '300px',
		width: '800px'
	});
});
</script>

<div class="widget board">
    <h3 class="title"><?php echo $original->name; ?></h3>

    <fieldset>
        <legend>Nombre</legend>
        <blockquote><?php echo $original->name; ?></blockquote>
    </fieldset>
    <fieldset>
        <legend>Descripción</legend>
        <blockquote><?php echo $original->description; ?></blockquote>
    </fieldset>

    <form method="post" action="/translate/node/<?php echo $node; ?>/page/edit/<?php echo $id; ?>">
        <input type="hidden" name="lang" value="<?php echo $_SESSION['translate_lang'] ?>" />
        <input type="hidden" name="node" value="<?php echo $node ?>" />

        <p>
            <label>Nombre<br />
            <input type="text" name="name" value="<?php echo $data->name ?>" style="width:350px" />
            </label>
        </p>

        <p>
            <label>Descripcion<br />
            <textarea name="description" cols="100" rows="10"><?php echo $data->description; ?></textarea><br />
            </label>
        </p>

        <p>
            <label>Contenido<br />
            <textarea id="richtext_content" name="content" cols="100" rows="20"><?php echo $data->content; ?></textarea>
            </label>
        </p>

        <input type="submit" name="save" value="Guardar" />
    </form>
</div>

<div class="widget board">
    <h3>Contenido original</h3>

    <div style="border: 1px solid black;padding: 10px;"><?php echo $original->content; ?></div>
</div>
