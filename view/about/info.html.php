<?php 
use Goteo\Library\Text,
    Goteo\Core\View;

$posts = $this['posts'];

include 'view/prologue.html.php';
include 'view/header.html.php';

$bodyClass = 'about blog';

$go_up = Text::get('regular-go_up');
?>

	<div id="sub-header-secondary">
		<div class="clearfix">
			<h2><a href="/about">GOTEO<span class="red">INFO</span></a></h2>
            <?php echo new View('view/header/share.html.php') ?>
		</div>
	</div>

	<div id="main" class="threecols">
		<div id="about-content">
            <h3 class="title">Sobre Goteo</h3>
            <?php if (!empty($posts)) : ?>
                <div class="about-page">
                <?php foreach ($posts as $post) : ?>
                    <div class="post">
                        <?php if (count($post->gallery) > 1) : ?>
                        <script type="text/javascript" >
                            $(function(){
                                $('#post-gallery<?php echo $post->id ?>').slides({
                                    container: 'post-gallery-container',
                                    paginationClass: 'slderpag',
                                    generatePagination: false,
                                    play: 0
                                });
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
                        <?php if (!empty($post->legend)) : ?>
                            <div class="embed-legend">
                                <?php echo $post->legend; ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($post->gallery)) : ?>
                		<div id="post-gallery<?php echo $post->id ?>" class="post-gallery">
                            <div class="post-gallery-container">
                                <?php $i = 1; foreach ($post->gallery as $image) : ?>
                                <div class="gallery-image gallery-post<?php echo $post->id ?>" id="gallery-post<?php echo $post->id ?>-<?php echo $i ?>">
                                    <img src="/image/<?php echo $image->id; ?>/500/285" alt="<?php echo $post->title; ?>" />
                                </div>
                                <?php $i++; endforeach; ?>
                            </div>
                            <!-- carrusel de imagenes si hay mas de una -->
                                <?php if (count($post->gallery) > 1) : ?>
                                    <a class="prev">prev</a>
                                        <ul class="slderpag">
                                            <?php $i = 1; foreach ($post->gallery as $image) : ?>
                                            <li><a href="#" id="navi-gallery-post<?php echo $post->id ?>-<?php echo $i ?>" rel="gallery-post<?php echo $post->id ?>-<?php echo $i ?>" class="navi-gallery-post<?php echo $post->id ?>">
                                        <?php echo htmlspecialchars($image->name) ?></a>
                                            </li>
                                            <?php $i++; endforeach ?>
                                        </ul>
                                    <a class="next">next</a>
                                <?php endif; ?>
                            <!-- carrusel de imagenes -->
                        </div>
                        <?php endif; ?>
                    </div>
                    <a class="up" href="#"><?php echo $go_up; ?></a>
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
