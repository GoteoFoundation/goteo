<?php

use Goteo\Library\Text;

$bodyClass = 'project-show';

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

        <div id="sub-header">
            <div>
                <h2>Gestión de páginas institucionales</h2>
            </div>

            <div class="sub-menu">
                <div class="project-menu">
                    <ul>
                        <li class="home"><a href="/admin">Mainboard</a></li>
                        <li><a href="/admin/pages">Páginas</a></li>
                    </ul>
                </div>
            </div>

        </div>

        <div id="main">
            <h3>Editando la pagina '<?php echo $this['page']->name; ?>'</h3>
            
            <p><?php echo $this['page']->description; ?></p>

            <div class="widget">
                <form method="post" action="/admin/pages/edit/<?php echo $this['page']->id; ?>">
                    <textarea id="richtext_content" name="content" cols="120" rows="20"><?php echo $this['page']->content; ?></textarea>
                    <input type="submit" name="save" value="Guardar" />
                </form>
            </div>

        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';