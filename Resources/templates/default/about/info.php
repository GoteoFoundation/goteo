<?php
use Goteo\Library\Text,
    Goteo\Core\View;

$posts = $this->posts;

$go_up = $this->text('regular-go_up');

$this->layout("layout", [
    'bodyClass' => 'about',
    'title' => $this->text('meta-title-info'),
    'meta_description' => $this->text('meta-description-info'),
    'og_description' => $this->text('meta-share-about')
    ]);

$this->section('content');
?>

	<div id="sub-header-secondary">
		<div class="clearfix">
			<h2><a href="/about">GOTEO<span class="red">INFO</span></a></h2>
            <?php echo View::get('header/share.html.php') ?>
		</div>
	</div>

	<div id="main">
		<div id="about-content">
            <h3 class="title"><?= $this->text('regular-header-about') ?></h3>
            <ul class="about-sections">
                <?php foreach ($posts as $post) : ?>
                    <?php $count++; ?>
                    <li><a class="element-<?= $count ?>" href="#info<?php echo $post->id; ?>"><?= preg_replace('/\s/', '<br />', $post->title, 1);  ?></a></li>
                <?php endforeach; ?>
            </ul>
            <br clear="all"></br>
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
                        <?php if(!empty($post->share_twitter)||!empty($post->share_facebook)):
                            if (LANG != 'es')
                                $share_lang= '?lang=' . LANG;
                    
                            $share_url = \SITE_URL . '/about'.$share_lang.'#info'.$post->id;

                            $facebook_url = 'http://facebook.com/sharer.php?u=' . urlencode($share_url) . '&t=' . urlencode($post->share_facebook);
                            $twitter_url = 'http://twitter.com/home?status=' . urlencode($post->share_twitter . ': ' . $share_url);
                            
                            ?>
                            <p><?= $this->text('regular-spread') ?></p>
                            <ul class="share">
                                <li class="twitter">
                                    <a href="<?php echo htmlentities($twitter_url) ?>" target="_blank"><?= $this->text('regular-twitter') ?></a>
                                </li>
                                <li class="facebook">
                                    <a href="<?php echo htmlentities($facebook_url) ?>" target="_blank"><?= $this->text('regular-facebook') ?></a>
                                </li>
                            </ul>
                        <?php endif; ?>
                    </div>
                   
                    <a class="up" href="#"><?php echo $go_up; ?></a>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>
		</div>
		

	</div>

    <?php $this->replace() ?>

