<?php

use Goteo\Library\Text,
    Goteo\Model,
    Goteo\Core\Redirection,
    Goteo\Library\NormalForm;

$post = $vars['post'];

if (!$post instanceof Model\Blog\Post) {
    throw new Redirection('/admin/blog');
}

$tags = array();

foreach ($vars['tags'] as $value => $label) {
    $tags[] =  array(
        'value'     => $value,
        'label'     => $label,
        'checked'   => isset($post->tags[$value])
        );
}

$allow = array(
    array(
        'value'     => 1,
        'label'     => 'Sí'
        ),
    array(
        'value'     => 0,
        'label'     => 'No'
        )
);


$images = array();
foreach ($post->gallery as $image) {
    $images[] = array(
        'type'  => 'html',
        'class' => 'inline gallery-image',
        'html'  => is_object($image) ?
                   $image . '<img src="'.SITE_URL.'/image/'.$image->id.'/128/128" alt="Imagen" /><button class="image-remove weak" type="submit" name="gallery-'.$image->hash.'-remove" title="Quitar imagen" value="remove"></button>' :
                   ''
    );

}

?>
<script type="text/javascript" src="<?php echo SRC_URL; ?>/view/js/ckeditor/ckeditor.js"></script>
<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
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
        width: '675px'
    });
});
// @license-end
</script>


<form method="post" action="/admin/blog/<?= $post->id ? $vars['action'].'/'.$post->id : $vars['action'] ?>" enctype="multipart/form-data">

    <?php echo new NormalForm(array(

        'action'        => '',
        'level'         => 3,
        'method'        => 'post',
        'title'         => '',
        'footer'        => array(
            'view-step-preview' => array(
                'type'  => 'submit',
                'name'  => 'save-post',
                'label' => Text::get('regular-save'),
                'class' => 'next'
            ),
            'pending' => array(
                'type'  => 'CheckBox',
                'name'  => 'pending',
                'label' => Text::get('mark-pending'),
            )
        ),
        'elements'      => array(
            'id' => array (
                'type' => 'Hidden',
                'value' => $post->id
            ),
            'author' => array (
                'type' => 'Hidden',
                'value' => $post->author
            ),
            'home' => array (
                'type' => 'Hidden',
                'value' => $post->home
            ),
            'footer' => array (
                'type' => 'Hidden',
                'value' => $post->footer
            ),
            'blog' => array (
                'type' => 'Hidden',
                'value' => $post->blog
            ),
            'title' => array(
                'type'      => 'TextBox',
                'required'  => true,
                'size'      => 20,
                'title'     => 'Título',
                'hint'      => Text::get('tooltip-updates-title'),
                'errors'    => !empty($errors['title']) ? array($errors['title']) : array(),
                'value'     => $post->title,
            ),
            'subtitle' => array(
                'type'      => 'TextBox',
                'required'  => true,
                'size'      => 20,
                'title'     => 'Subtítulo',
                'hint'      => Text::get('tooltip-updates-subtitle'),
                'errors'    => !empty($errors['subtitle']) ? array($errors['subtitle']) : array(),
                'value'     => $post->subtitle,
            ),
            'text' => array(
                'type'      => 'TextArea',
                'required'  => true,
                'cols'      => 40,
                'rows'      => 4,
                'title'     => 'Texto de la entrada',
                'value'     => $post->text
            ),
            'image' => array(
                'title'     => 'Imagen',
                'type'      => 'Group',
                'class'     => 'image',
                'children'  => array(
                    'image_upload'    => array(
                        'type'  => 'file',
                        'class' => 'inline image_upload',
                        'label' => Text::get('form-image_upload-button')
                    )
                )
            ),

            'gallery' => array(
                'type'  => 'Group',
                'title' => Text::get('overview-field-image_gallery'),
                'class' => 'inline',
                'children'  => $images
            ),

            'media' => array(
                'type'      => 'TextBox',
                'title'     => 'Vídeo',
                'class'     => 'media',
                'value'     => (string) $post->media,
                'children'  => array(
                    'media-preview' => array(
                        'title' => 'Vista previa',
                        'class' => 'media-preview inline',
                        'type'  => 'html',
                        'html'  => !empty($post->media) ? $post->media->getEmbedCode() : ''
                    )
                )
            ),
            'legend' => array(
                'type'      => 'TextArea',
                'title'     => Text::get('regular-media_legend'),
                'value'     => $post->legend,
            ),

            'tags' => array(
                'type'      => 'CheckBoxes',
                'name'      => 'tags[]',
                'title'     => 'Tags',
                'options'   => $tags
            ),

            /*'new-tag' => array(
                'type'  => 'HTML',
                'class' => 'inline',
                'html'  => '<input type="text" name="new-tag" value="" /> <input type="submit" name="new-tag_save" value="Añadir" />'
            ),*/

            'date' => array(
                'type'      => 'DateBox',
                'title'     => 'Fecha de publicación',
                'size'      => 8,
                'value'     => $post->date
            ),
            'allow' => array(
                'title'     => 'Permite comentarios',
                'type'      => 'Slider',
                'options'   => $allow,
                'class'     => 'currently cols_' . count($allow),
                'value'     => (int) $post->allow
            ),
            'publish' => array(
                'title'     => 'Publicado',
                'type'      => 'Slider',
                'options'   => $allow,
                'class'     => 'currently cols_' . count($allow),
                'value'     => (int) $post->publish
            )

        )

    ));
    ?>

</form>
