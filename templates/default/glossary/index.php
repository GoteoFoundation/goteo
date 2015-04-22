<?php
use 
    
    Goteo\Util\Pagination\Paginated,
    Goteo\Util\Pagination\DoubleBarLayout;

$posts = $this->posts;
$index = $this->index;

$letters = array();

$bodyClass = 'glossary';

$go_up = $this->text('regular-go_up');

// paginacion
$pagedResults = new Paginated($posts, $this->tpp, isset($_GET['page']) ? $_GET['page'] : 1);

$this->layout("layout", [
    'bodyClass' => 'glossary',
    'title' => $this->text('meta-title-glossary'),
    'meta_description' => $this->text('meta-description-glossary')
    ]);

$this->section('content');
?>
	<div id="sub-header-secondary">
		<div class="clearfix">
            <h2><a href="/glossary">GOTEO<span class="red"><?=$this->text('footer-resources-glossary') ?></span></a></h2>
            <?=$this->insert('partials/header/share')?>
		</div>
	</div>

	<div id="main" class="threecols">
		<div id="glossary-content">
            <h3 class="title"><?=$this->text('regular-header-glossary') ?></h3>
            <?php if (!empty($posts)) : ?>
                <div class="glossary-page">
                <?php while ($post = $pagedResults->fetchPagedRow()) : ?>
                    <?php
                        $leter = $post->title[0];

                        if (!in_array($leter, $letters)) :
                            $letters[] = $leter;
                        ?>
                        <h4 class="supertitle"><?=$post->title[0] ?></h4>
                    <?php endif; ?>
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
                        <a name="term<?php echo $post->id  ?>"></a>
                        <h5 class="aqua"><?php echo $post->title; ?></h5>
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
                <?php endwhile; ?>
                </div>
                <ul id="pagination">
                    <?php   $pagedResults->setLayout(new DoubleBarLayout());
                            echo $pagedResults->fetchPagedNavigation(); ?>
                </ul>
            <?php endif; ?>
		</div>
		<div id="glossary-sidebar">
            <div class="widget glossary-sidebar-module">
                <?php foreach ($index as $leter=>$list) : ?>
                <h3 class="supertitle activable glossary-letter"><?php echo $leter; ?></h3>
                <ul class="glossary-letter-list">
                    <?php foreach ($list as $item) : ?>
                    <li><a href="<?php echo $item->url; ?>"><?php echo $item->title; ?></a></li>
                    <?php endforeach; ?>
                </ul>
                <?php endforeach; ?>
            </div>
		</div>

	</div>

<?php $this->replace() ?>
