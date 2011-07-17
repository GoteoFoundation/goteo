<?php 

use Goteo\Core\View,
    Goteo\Library\Text;

$currentPost = $this['posts'][$this['post']];

$bodyClass = 'home';

include 'view/prologue.html.php';
include 'view/header.html.php' ?>

    <script type="text/javascript">

    jQuery(document).ready(function ($) {

        $("#home-post-<?php echo $this['post']; ?>").show();

        $(".navi-home-post").click(function (event) {
            event.preventDefault();

            /* Quitar todos los active, ocultar todos los elementos */
            $(".navi-home-post").removeClass('active');
            $(".post").hide();
            /* Poner acctive a este, mostrar este*/
            $(this).addClass('active');
            $("#"+this.rel).show();
            /*
             * Y si lo quisieramos hacer sin  cargar todo el html, usariamos esto
            content = $.ajax({async: false, url: '<?php echo SITE_URL; ?>/ws/get_home_post/'+this.rel}).responseText;
            $('#home-post').html(content);
            */
        });

    });
    </script>

        <div id="sub-header">
            <div>
                <h2><?php echo Text::html('login-banner-header'); ?></h2>
            </div>

        </div>

        <div id="main">
            
            <?php if (!empty($this['posts'])): ?>
            
            <div class="widget learn">
                
            <h2 class="title"><?php echo Text::get('home-posts-header'); ?></h2>
            
                <ul>
                    <?php foreach ($this['posts'] as $post) : ?>
                    <li><a href="?post=<?php echo $post->id ?>" rel="home-post-<?php echo $post->id ?>" class="tipsy navi-home-post<?php if ($post->id == $this['post']) echo ' active'; ?>" title="<?php echo htmlspecialchars($post->title) ?>">
                        <?php echo htmlspecialchars($post->title) ?></a>
                    </li>
                    <?php endforeach ?>
                </ul>
                                                                             
                <?php foreach ($this['posts'] as $post) : ?>
                <div class="post" id="home-post-<?php echo $post->id; ?>">
                    <h3><?php echo $post->title; ?></h3>
                    <?php if (!empty($post->media->url)) : ?>
                        <div class="embed">
                            <?php echo $post->media->getEmbedCode(); ?>
                        </div>
                    <?php elseif (!empty($post->image)) : ?>
                        <div class="image">
                            <img src="/image/<?php echo $post->image->id; ?>/500/285" alt="Imagen"/>
                        </div>
                    <?php endif; ?>
                    
                    <div class="description">
                        <?php echo Text::recorta($post->text, 500) ?>
                    </div>

                    <div class="read_more"><a href="/blog/<?php echo $post->id; ?>"><?php echo Text::get('regular-read_more') ?></a></div>
                </div>                
                <?php endforeach; ?>
               
            </div>
            
            <?php endif ?>
            
            <div class="widget projects">
                
                <h2 class="title"><?php echo Text::get('home-promotes-header'); ?></h2>
            
                <?php foreach ($this['promotes'] as $promo) : ?>
                
                        <?php echo new View('view/project/widget/project.html.php', array(
                            'project' => $promo->projectData,
                            'balloon' => '<h4>' . htmlspecialchars($promo->title) . '</h4>' .
                                         '<blockquote>' . $promo->description . '</blockquote>'
                        )) ?>
                    
                <?php endforeach ?>

            </div>

        </div>

        <?php include 'view/footer.html.php' ?>
    
<?php include 'view/epilogue.html.php' ?>