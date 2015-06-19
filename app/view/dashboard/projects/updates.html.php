<?php
use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\SuperForm;

$blog  = $vars['blog'];
$posts = $vars['posts'];
$project = $vars['project'];

$errors = $vars['errors'];

$level = $vars['level'] = 3;

$url = '/dashboard/projects/updates';

if ($vars['action'] == 'none') return;

?>
<?php if ($vars['action'] == 'list') : ?>
<div class="widget">
    <?php if (!empty($blog->id) && $blog->active) : ?>
        <a class="button" href="<?php echo $url; ?>/add">Publicar nueva entrada</a>
    <?php endif; ?>

    <!-- lista -->
    <?php if (!empty($posts)) : ?>
    <?php foreach ($posts as $post) : ?>
        <div class="post">
            <a class="button" href="<?php echo $url; ?>/edit/<?php echo $post->id; ?>"><?php echo Text::get('regular-edit') ?></a>&nbsp;&nbsp;&nbsp;
            <a class="remove button weak" href="<?php echo $url; ?>/delete/<?php echo $post->id; ?>" onclick="return confirm('¿Seguro que deseas eliminar esta actualización?');">Eliminar</a>
            <span><?php echo $post->publish ? Text::get('regular-published_yes') : Text::get('regular-published_no'); ?></span>
            <a href="/project/<?php echo $project->id; ?>/updates/<?php echo $post->id; ?>?preview=<?php echo $_SESSION['user']->id ?>" target="_blank" style="text-decoration: none;"><strong><?php echo $post->title; ?></strong></a>
            <span><?php echo $post->date; ?></span>
            <a class="button" href="/project/<?php echo $project->id; ?>/updates/<?php echo $post->id; ?>?preview=<?php echo $_SESSION['user']->id ?>" target="_blank"><?php echo $post->publish ? 'Ver' : 'Previsualizar'; ?></a>
        </div>
    <?php endforeach; ?>
    <?php else : ?>
        <p><?php echo Text::get('blog-no_posts') ?></p>
    <?php endif; ?>

</div>

<?php  else : // sueprform!

        $post  = $vars['post']; // si edit
        if (empty($post->author)) $post->author = $_SESSION['user']->id;

        $allow = array(
            array(
                'value'     => 1,
                'label'     => Text::get('regular-yes')
                ),
            array(
                'value'     => 0,
                'label'     => Text::get('regular-no')
                )
        );

        $images = array();
        foreach ($post->gallery as $image) {
            $images[] = array(
                'type'  => 'html',
                'class' => 'inline gallery-image',
                'html'  => is_object($image) ?
                           $image . '<img src="' . SITE_URL . '/image/' . $image->id . '/128/128" alt="Imagen" /><button class="image-remove weak" type="submit" name="gallery-'.$image->hash.'-remove" title="Quitar imagen" value="remove"></button>' :
                           ''
            );

        }

        if (!empty($post->media->url)) {
            $media = array(
                    'type'  => 'media',
                    'title' => Text::get('overview-field-media_preview'),
                    'class' => 'inline media',
                    'type'  => 'html',
                    'html'  => !empty($post->media) ? $post->media->getEmbedCode() : ''
            );
        } else {
            $media = array(
                'type'  => 'hidden',
                'class' => 'inline'
            );


        }
    ?>

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
        width: '630px'
    });
});
</script>

    <form method="post" action="/dashboard/projects/updates/<?php echo $vars['action']; ?>/<?php echo $post->id; ?>" class="project" enctype="multipart/form-data">

    <?php echo SuperForm::get(array(
        //si no se quiere que se auto-actualize el formulario descomentar la siguiente linea:
        'autoupdate'    => false,

        'action'        => '',
        'level'         => $vars['level'],
        'method'        => 'post',
        'title'         => '',
        'hint'          => Text::get('guide-project-updates'),
        'class'         => 'aqua',
        'footer'        => array(
            'view-step-preview' => array(
                'type'  => 'submit',
                'name'  => 'save-post',
                'label' => Text::get('regular-save'),
                'class' => 'next'
            )
        ),
        'elements'      => array(
            'id' => array (
                'type' => 'hidden',
                'value' => $post->id
            ),
            'author' => array (
                'type' => 'hidden',
                'value' => $post->author
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
                        'label' => Text::get('form-image_upload-button'),
                        'class' => 'inline image_upload',
                        'title' => Text::get('profile-field-avatar_upload'),
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
                'value'     => (string) $post->media
            ),

            'media-upload' => array(
                'name' => "upload",
                'type'  => 'submit',
                'label' => Text::get('form-upload-button'),
                'class' => 'inline media-upload'
            ),

            'media-preview' => $media,

            'legend' => array(
                'type'      => 'textarea',
                'title'     => Text::get('regular-media_legend'),
                'value'     => $post->legend,
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
            ),
            'publish' => array(
                'title'     => 'Publicado',
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

<?php endif; ?>
