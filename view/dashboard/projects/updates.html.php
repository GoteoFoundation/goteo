<?php
use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\SuperForm;

echo new View ('view/dashboard/projects/selector.html.php', $this);

$blog  = $this['blog'];
$posts = $blog->posts; // si lista

$erorrs = $this['errors'];

$this['level'] = 3;
?>

<div class="widget projects">
    <h2 class="title"><?php echo $this['message']; ?></h2>

<?php if ($this['action'] == 'list') : ?>

    <?php \trace($posts); ?>

<?php  else : // sueprform!

        $post  = $this['post']; // si edit
        $currently = array(
            array(
                'value'     => 1,
                'label'     => 'Sí'
                ),
            array(
                'value'     => 0,
                'label'     => 'No'
                )
        );
    ?>

    <form method="post" action="/dashboard/projects/updates/<?php echo $this['action']; ?>" class="project" enctype="multipart/form-data">

    <?php echo new SuperForm(array(

        'action'        => '',
        'level'         => $this['level'],
        'method'        => 'post',
        'title'         => '',
        'hint'          => Text::get('guide-project-updates'),
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
            'post' => array (
                'type' => 'hidden',
                'value' => $post->id
            ),
            'blog' => array (
                'type' => 'hidden',
                'value' => $blog->id
            ),
            'title' => array(
                'type'      => 'textbox',
                'required'  => true,
                'size'      => 20,
                'title'     => 'Título',
                'hint'      => Text::get('tooltip-updates-title'),
                'errors'    => !empty($errors['title']) ? array($errors['title']) : array(),
                'value'     => $post->title,
            ),
            'text' => array(
                'type'      => 'textarea',
                'required'  => true,
                'cols'      => 40,
                'rows'      => 4,
                'title'     => 'Cuéntanos algo sobre ti',
                'hint'      => Text::get('tooltip-updates-text'),
                'errors'    => !empty($errors['text']) ? array($errors['text']) : array(),
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
                    ),
                    'iamge-current' => array(
                        'type'  => 'group',
                        'title' => 'Imagen actual',
                        'class' => 'inline gallery',
                        'children'  => $post->image
                    )

                )
            ),
            'media' => array(
                'type'      => 'textarea',
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
                        'html'  => '<div>' . (!empty($post->media) ? $post->media->getEmbedCode() : '') .'</div>'
                    )
                )
            ),
            'allow' => array(
                'title'     => 'Permite comentarios',
                'type'      => 'slider',
                'options'   => $allow,
                'class'     => 'currently cols_' . count($allow),
                'hint'      => Text::get('tooltip-updates-allow_comments'),
                'errors'    => !empty($errors['allow']) ? array($errors['allow']) : array(),
                'value'     => $post->allow
            )

        )

    ));
    ?>

    </form>

<?php endif; ?>
    
</div>
