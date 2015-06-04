<?php
use Goteo\Library\Text,
    Goteo\Core\View;

$posts = $vars['posts'];

include __DIR__ . '/../prologue.html.php';
include __DIR__ . '/../header.html.php';

$bodyClass = 'about';

$go_up = Text::get('regular-go_up');
?>

	<div id="sub-header-secondary">
		<div class="clearfix">
			<h2><a href="/about">GOTEO<span class="red">INFO</span></a></h2>
            <?php echo View::get('header/share.html.php') ?>
		</div>
	</div>

<?php if($_SESSION['messages']) { include __DIR__ . '/../header/message.html.php'; } ?>

	<div id="main" class="threecols">
		<div id="about-content">
            <h3 class="title"><?php echo Text::get('regular-header-about'); ?></h3>
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
                        <?php if (!empty($post->media->url)) :
                                $embed = $post->media->getEmbedCode();
                                if (!empty($embed))  : ?>
                            <div class="embed"><?php echo $embed; ?></div>
                        <?php endif; endif; ?>
                        <?php if (!empty($post->legend)) : ?>
                            <div class="embed-legend">
                                <?php echo $post->legend; ?>
                            </div>
                        <?php endif; ?>
                        <?php if (count($post->gallery) > 1) : ?>
                		<div id="post-gallery<?php echo $post->id ?>" class="post-gallery">
                            <div class="post-gallery-container">
                                <?php $i = 1; foreach ($post->gallery as $image) : ?>
                                <div class="gallery-image gallery-post<?php echo $post->id ?>" id="gallery-post<?php echo $post->id ?>-<?php echo $i ?>">
                                    <img src="<?php echo $image->getLink(500, 285); ?>" alt="<?php echo htmlspecialchars($post->title) ?>" />
                                </div>
                                <?php $i++; endforeach; ?>
                            </div>
                            <!-- carrusel de imagenes si hay mas de una -->
                                <a class="prev">prev</a>
                                    <ul class="slderpag">
                                        <?php $i = 1; foreach ($post->gallery as $image) : ?>
                                        <li><a href="#" id="navi-gallery-post<?php echo $post->id ?>-<?php echo $i ?>" rel="gallery-post<?php echo $post->id ?>-<?php echo $i ?>" class="navi-gallery-post<?php echo $post->id ?>">
                                    <?php echo htmlspecialchars($image->name) ?></a>
                                        </li>
                                        <?php $i++; endforeach ?>
                                    </ul>
                                <a class="next">next</a>
                            <!-- carrusel de imagenes -->
                        </div>
                        <?php elseif ( $post->image instanceof \Goteo\Model\Image ) : ?>
                            <div class="gallery-image gallery-post<?php echo $post->id ?>" id="gallery-post<?php echo $post->id ?>-<?php echo $i ?>">
                                <img src="<?php echo $post->image->getLink(500, 285); ?>" alt="<?php echo htmlspecialchars($post->title) ?>" />
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
                <h3 class="supertitle"><?php echo Text::get('header-about-side'); ?></h3>
                <ul>
                    <?php foreach ($posts as $post) : ?>
                    <li><a href="#info<?php echo $post->id; ?>"><?php echo $post->title; ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
		</div>

	</div>

<?php
include __DIR__ . '/../footer.html.php';
include __DIR__ . '/../epilogue.html.php';
