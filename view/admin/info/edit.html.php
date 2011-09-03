<?php

use Goteo\Library\Text,
    Goteo\Model,
    Goteo\Core\Redirection,
    Goteo\Library\SuperForm;

define('ADMIN_NOAUTOSAVE', true);

$post = $this['post'];

if (!$post instanceof Model\Info) {
    throw new Redirection('/admin/info');
}

// Superform
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
                       $image . '<img src="' . htmlspecialchars($image->getLink(128, 128)) . '" alt="Imagen" /><button class="image-remove weak" type="submit" name="gallery-'.$image->id.'-remove" title="Quitar imagen" value="remove"></button>' :
                       ''
        );

    }

?>

<form method="post" action="/admin/info/<?php echo $this['action']; ?>/<?php echo $post->id; ?>" class="project" enctype="multipart/form-data">

    <?php echo new SuperForm(array(

        'action'        => '',
        'level'         => 3,
        'method'        => 'post',
        'title'         => '',
        'hint'          => 'Idea de about: descripción, imágenes y media (vimeo, youtube, presi, slideshare), publiar',
        'class'         => 'aqua',
        'footer'        => array(
            'view-step-preview' => array(
                'type'  => 'submit',
                'name'  => 'save-post',
                'label' => 'Guardar',
                'class' => 'next'
            )
        ),
        'elements'      => array(
            'id' => array (
                'type' => 'hidden',
                'value' => $post->id
            ),
            'order' => array (
                'type' => 'hidden',
                'value' => $post->order
            ),
            'node' => array (
                'type' => 'hidden',
                'value' => $post->node
            ),
            'title' => array(
                'type'      => 'textbox',
                'required'  => true,
                'size'      => 20,
                'title'     => 'Idea',
                'value'     => $post->title,
            ),
            'text' => array(
                'type'      => 'textarea',
                'required'  => true,
                'cols'      => 40,
                'rows'      => 4,
                'title'     => 'Explicación de la idea',
                'value'     => $post->text
            ),
            'image' => array(
                'title'     => 'Imagen',
                'type'      => 'group',
                'hint'      => Text::get('tooltip-updates-image'),
                'errors'    => !empty($errors['image']) ? array($errors['image']) : array(),
                'class'     => 'image',
                'children'  => array(
                    'image_upload'    => array(
                        'type'  => 'file',
                        'class' => 'inline image_upload',
                        'title' => 'Subir una imagen',
                        'hint'  => Text::get('tooltip-updates-image_upload'),
                    )
                )
            ),

            'gallery' => array(
                'type'  => 'group',
                'title' => Text::get('overview-field-image_gallery'),
                'class' => 'inline',
                'children'  => $images
            ),

            'media' => array(
                'type'      => 'textbox',
                'title'     => 'Vídeo',
                'class'     => 'media',
                'hint'      => Text::get('tooltip-updates-media'),
                'errors'    => !empty($errors['media']) ? array($errors['media']) : array(),
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
                'type'      => 'textarea',
                'title'     => 'Leyenda',
                'value'     => $post->legend,
            ),

            'publish' => array(
                'title'     => 'Publicada',
                'type'      => 'slider',
                'options'   => $allow,
                'class'     => 'currently cols_' . count($allow),
                'hint'      => Text::get('tooltip-updates-publish'),
                'errors'    => !empty($errors['publish']) ? array($errors['publish']) : array(),
                'value'     => (int) $post->publish
            )

        )

    ));
    ?>

</form>