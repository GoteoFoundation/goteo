<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

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
        <div id="main">
            <h2>Editando la pagina '<?php echo $this['page']->name; ?>'</h2>
            <?php echo $this['page']->description; ?><br />

            <p><a href="/admin">Volver al Menú de administración</a></p>
            <p><a href="/admin/pages">Volver a la lista de páginas</a></p>

            <?php if (!empty($this['errors'])) :
                echo '<p>';
                foreach ($this['errors'] as $error) : ?>
                    <span style="color:red;"><?php echo $error; ?></span><br />
            <?php endforeach;
                echo '</p>';
                endif;
            ?>

            <form method="post" action="">
                <textarea id="richtext_content" name="content" cols="120" rows="20"><?php echo $this['page']->content; ?></textarea>
                <input type="submit" name="save" value="Guardar" />
            </form>

            <p><a href="<?php echo $this['page']->url; ?>" target="_blank">Previsualizar</a></p>

        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';