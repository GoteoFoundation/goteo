<?php

use Goteo\Library\Text;

?>
<script type="text/javascript" src="/view/js/ckeditor/ckeditor.js"></script>
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
		width: '730px'
	});
});
</script>

<div class="widget board">
    <form method="post" action="/admin/pages/edit/<?php echo $this['page']->id; ?>">

        <p>
            <label for="page-name">T&iacute;tulo:</label><br />
            <input type="text" name="name" id="page-name" value="<?php echo $this['page']->name; ?>" />
        </p>

        <p>
            <label for="page-description">Cabecera:</label><br />
            <textarea name="description" id="page-description" cols="60" rows="4"><?php echo $this['page']->description; ?></textarea>
        </p>

        <p>
            <label for="richtext_content">Contenido:</label><br />
            <textarea id="richtext_content" name="content" cols="100" rows="20"><?php echo $this['page']->content; ?></textarea>
        </p>

        <input type="submit" name="save" value="Guardar" />

        <p>
            <label for="mark-pending">Marcar como pendiente de traducir</label>
            <input id="mark-pending" type="checkbox" name="pending" value="1" />
        </p>

    </form>
</div>