<?php

use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\NormalForm,
    Goteo\Model;

$node = $vars['node'];
$id   = $vars['id'];

$original = Model\Blog\Post::get($id);
$data     = Model\Blog\Post::get($id, $_SESSION['translate_lang']);

$bodyClass = 'admin';
?>
<script type="text/javascript" src="<?php echo SRC_URL; ?>/view/js/ckeditor/ckeditor.js"></script>

<script type="text/javascript">
$(document).ready(function(){
	// Lanza wysiwyg contenido
	CKEDITOR.replace('text_editor', {
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

<form method="post" action="/translate/node/<?php echo $node ?>/post/edit/<?php echo $id ?>" class="project" >
<?php echo new NormalForm(array(
    'level'         => 3,
    'action'        => '',
    'method'        => 'post',
    'class'         => 'aqua',
    'footer'        => array(
        'view-step-preview' => array(
            'type'  => 'submit',
            'name'  => 'save',
            'label' => Text::get('regular-save'),
            'class' => 'next'
        )
    ),
    'elements'      => array(

        'lang' => array(
            'type'      => 'hidden',
            'value'     => $_SESSION['translate_lang']
        ),

        'blog' => array(
            'type'      => 'hidden',
            'value'     => $original->blog
        ),

        'title-orig' => array(
            'type'      => 'html',
            'title'     => 'Título',
            'html'     => $original->title
        ),
        'title' => array(
            'type'      => 'textbox',
            'title'     => '',
            'class'     => 'inline',
            'value'     => $data->title
        ),

        'text-orig' => array(
            'type'      => 'html',
            'title'     => 'Texto entrada',
            'html'     => '<div style="width: 650px; height: 400px; overflow:scroll;">'.$original->text.'</div>'
        ),
        'text' => array(
            'type'      => 'textarea',
            'title'     => '',
            'class'     => 'inline',
            'value'     => $data->text
        ),

        'legend-orig' => array(
            'type'      => 'html',
            'title'     => 'Leyenda media',
            'html'     => $original->legend
        ),
        'legend' => array(
            'type'      => 'textbox',
            'title'     => '',
            'class'     => 'inline',
            'value'     => $data->legend
        )

    )

));
?>
</form>
