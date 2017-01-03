<?php

use Goteo\Library\Text,
    Goteo\Model,
    Goteo\Core\Redirection,
    Goteo\Library\NormalForm;

$post = $vars['post'];

if (!$post instanceof Model\Info) {
    throw new Redirection('/admin/info');
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
        'type'  => 'HTML',
        'class' => 'inline gallery-image',
        'html'  => is_object($image) ?
                    '<img src="' . SITE_URL . '/image/' . $image->id . '/128/128" alt="Imagen" /><button class="image-remove weak" type="submit" name="gallery-'.$image->hash.'-remove" title="Quitar imagen" value="remove"></button>' :
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

<form method="post" action="/admin/info/<?= $post->id ? $vars['action'].'/'.$post->id : $vars['action'] ?>" class="project" enctype="multipart/form-data" style="margin-bottom: 20px;">

    <?php echo new NormalForm(array(

        'action'        => '',
        'level'         => 3,
        'method'        => 'post',
        'class'         => 'aqua',
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
            'order' => array (
                'type' => 'Hidden',
                'value' => $post->order
            ),
            'node' => array (
                'type' => 'Hidden',
                'value' => $post->node
            ),
            'title' => array(
                'type'      => 'TextBox',
                'size'      => 20,
                'title'     => 'Idea',
                'value'     => $post->title,
            ),
            'text' => array(
                'type'      => 'TextArea',
                'cols'      => 40,
                'rows'      => 4,
                'title'     => 'Explicación de la idea',
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
                        'label' => 'Subir',
                        'title' => 'Subir una imagen'
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
                        'type'  => 'HTML',
                        'html'  => !empty($post->media) ? $post->media->getEmbedCode() : ''
                    )
                )
            ),
            'legend' => array(
                'type'      => 'TextArea',
                'title'     => Text::get('regular-media_legend'),
                'value'     => $post->legend,
            ),

            'share_facebook' => array(
                'type'      => 'TextArea',
                'title'     => 'Difusión Facebook',
                'value'     => $post->share_facebook,
            ),

            'share_twitter' => array(
                'type'      => 'TextArea',
                'title'     => 'Difusión Twitter',
                'value'     => $post->share_twitter,
            ),

            'publish' => array(
                'title'     => 'Publicada',
                'type'      => 'slider',
                'options'   => $allow,
                'class'     => 'currently cols_' . count($allow),
                'value'     => (int) $post->publish
            )

        )

    ));
    ?>

</form>
