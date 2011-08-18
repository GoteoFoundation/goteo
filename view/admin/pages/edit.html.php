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
		width: '800px'
	});
});
</script>
<p>'<?php echo $this['page']->name; ?>'</p>
<blockquote><?php echo $this['page']->description; ?></blockquote>

<div class="widget board">
    <form method="post" action="/admin/pages/edit/<?php echo $this['page']->id; ?>">
        <textarea id="richtext_content" name="content" cols="120" rows="20"><?php echo $this['page']->content; ?></textarea>
        <input type="submit" name="save" value="Guardar" />
    </form>
</div>