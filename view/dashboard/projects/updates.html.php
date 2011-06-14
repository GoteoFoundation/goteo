<?php
use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\SuperForm;

echo new View ('view/dashboard/projects/selector.html.php', $this);

$blog  = $this['blog'];
$posts = $this['posts'];

$errors = $this['errors'];

$level = $this['level'] = 3;

$url = '/dashboard/projects/updates';

?>

    <h<?php echo $level ?> class="title"><?php echo $this['message']; ?></h<?php echo $level ?>>

<?php if ($this['action'] == 'list') : ?>

    <?php if (!empty($blog->id) && $blog->active) : ?>
    <a href="<?php echo $url; ?>/add">Publicar nueva entrada</a>
    <?php endif; ?>

    <!-- lista -->
    <?php if (!empty($posts)) : ?>
    <?php foreach ($posts as $post) : ?>
        <div class="widget">
            <h<?php echo $level+1 ?> class="title"><?php echo $post->title; ?></h<?php echo $level+1 ?>>
            <span style="display:block;"><?php echo $post->date; ?></span>
            <blockquote><?php echo Text::recorta($post->text, 500); ?></blockquote>
            <?php if (!empty($post->image)) : ?>
                <img src="/image/<?php echo $post->image->id; ?>/110/110" alt="Imagen"/>
            <?php endif; ?>
            <?php if (!empty($post->media)) : ?>
                <?php echo $post->media->getEmbedCode(); ?>
            <?php endif; ?>
            <div><a href="<?php echo $url; ?>/edit/<?php echo $post->id; ?>">[EDITAR]</a></div>
            <p><?php echo $post->num_commnets > 0 ? $post->num_comments : 'Sin'; ?> comentarios.   <a href="/project/<?php echo $blog->project; ?>/updates/<?php echo $post->id; ?>" target="_blank">Ir a ver/añadir comentarios</a></p>
        </div>
    <?php endforeach; ?>
    <?php else : ?>
    <p>No hay entradas</p>
    <?php endif; ?>

<?php  else : // sueprform!

        $post  = $this['post']; // si edit
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


        $image = array(
            'image' => array(
                'type'  => 'hidden',
                'value' => $post->image->id,
            ),
            'post-image' => array(
                'type'  => 'html',
                'class' => 'inline',
                'html'  => is_object($post->image) ?
                           $post->image . '<img src="' . htmlspecialchars($post->image->getLink(110, 110)) . '" alt="Imagen" />' :
                           ''
            )
        );

        if (!empty($post->image) && is_object($post->image))
            $image ["image-{$post->image->id}-remove"] = array(
                'type'  => 'submit',
                'label' => 'Quitar',
                'class' => 'inline remove image-remove'
            );


    ?>

    <form method="post" action="/dashboard/projects/updates/<?php echo $this['action']; ?>/<?php echo $post->id; ?>" class="project" enctype="multipart/form-data">

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
            'id' => array (
                'type' => 'hidden',
                'value' => $post->id
            ),
            'blog' => array (
                'type' => 'hidden',
                'value' => $post->blog
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
                'title'     => 'Texto de la entrada',
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
                        'children'  => $image
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
            "date" => array(
                'type'      => 'datebox',
                'required'  => true,
                'title'     => 'Fecha de publicación',
                'hint'      => Text::get('tooltip-updates-date'),
                'size'      => 8,
                'value'     => $post->date
            ),
            'allow' => array(
                'title'     => 'Permite comentarios',
                'type'      => 'slider',
                'options'   => $allow,
                'class'     => 'currently cols_' . count($allow),
                'hint'      => Text::get('tooltip-updates-allow_comments'),
                'errors'    => !empty($errors['allow']) ? array($errors['allow']) : array(),
                'value'     => (int) $post->allow
            )

        )

    ));
    ?>

    </form>

<?php endif; ?>