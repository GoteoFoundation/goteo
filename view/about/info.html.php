<?php 
use Goteo\Library\Text,
    Goteo\Core\View;

$posts = $this['posts'];

include 'view/prologue.html.php';
include 'view/header.html.php';

$bodyClass = 'about';

$go_up = Text::get('regular-go_up');
?>

	<div id="sub-header-secondary">
		<div class="clearfix">
			<h2><a href="/about">GOTEO<span class="red">INFO</span></a></h2>
            <?php echo new View('view/header/share.html.php') ?>
		</div>
	</div>

    <script type="text/javascript" src="/view/js/inc/navi.js"></script>


	<div id="main" class="threecols">
		<div id="about-content">
            <h3 class="title">Sobre Goteo</h3>
            <?php if (!empty($posts)) : ?>
                <div class="about-page">
                <?php foreach ($posts as $post) : ?>
                    <div class="post">
                        <?php if (count($post->gallery) > 1) : ?>
                        <script type="text/javascript" >
                            jQuery(document).ready(function ($) {
                                    navi('gallery-post<?php echo $post->id; ?>', '<?php echo count($post->gallery) ?>');
                            });
                        </script>
                        <?php endif; ?>
                        <a name="info<?php echo $post->id  ?>"></a>
                        <h4><?php echo $post->title; ?></h4>
                        <p><?php echo $post->text; ?></p>
                        <?php if (!empty($post->media->url)) : ?>
                            <div class="embed">
                                <?php echo $post->media->getEmbedCode(); ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($post->gallery)) : ?>
                        <div class="gallery">
                            <?php $i = 1; foreach ($post->gallery as $image) : ?>
                            <div class="gallery-image gallery-post<?php echo $post->id ?>" id="gallery-post<?php echo $post->id ?>-<?php echo $i ?>">
                                <img src="/image/<?php echo $image->id; ?>/500/285" alt="<?php echo $post->title; ?>" />
                            </div>
                            <?php $i++; endforeach; ?>

                            <!-- carrusel de imagenes si hay mas de una -->
                            <?php if (count($post->gallery) > 1) : ?>
                            <ul class="navi">
                                <li class="prev"><a href="#" id="gallery-post<?php echo $post->id ?>-navi-prev" rel="<?php echo count($post->gallery) ?>" class="navi-arrow-gallery-post<?php echo $post->id ?>">Anterior</a></li>
                                <?php $i = 1; foreach ($post->gallery as $image) : ?>
                                <li><a href="#" id="navi-gallery-post<?php echo $post->id ?>-<?php echo $i ?>" rel="gallery-post<?php echo $post->id ?>-<?php echo $i ?>" class="navi-gallery-post<?php echo $post->id ?>">
                                    <?php echo htmlspecialchars($image->name) ?></a>
                                </li>
                                <?php $i++; endforeach ?>
                                <li class="next"><a href="#" id="gallery-post<?php echo $post->id ?>-navi-next" rel="2" class="navi-arrow-gallery-post<?php echo $post->id ?>">Siguiente</a></li>
                            </ul>
                            <?php endif; ?>
                            <!-- carrusel de imagenes -->
                        </div>
                        <?php endif; ?>
                        <a class="up" href="#"><?php echo $go_up; ?></a>
                    </div>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>
		</div>
		<div id="about-sidebar">
            <div class="widget about-sidebar-module">
                <h3 class="supertitle">Ideas fuerza</h3>
                <ul>
                    <?php foreach ($posts as $post) : ?>
                    <li><a href="#info<?php echo $post->id; ?>"><?php echo $post->title; ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
		</div>

	</div>
    
<?php
include 'view/footer.html.php';
include 'view/epilogue.html.php';
