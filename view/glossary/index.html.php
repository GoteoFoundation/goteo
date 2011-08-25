<?php
use Goteo\Library\Text,
    Goteo\Core\View,
    Goteo\Model\Glossary;

$posts = $this['posts'];
$index = $this['index'];

$letters = array();

$bodyClass = 'glossary';

$go_up = Text::get('regular-go_up');

// paginacion
require_once 'library/pagination/pagination.php';

$pagedResults = new \Paginated($posts, $this['tpp'], isset($_GET['page']) ? $_GET['page'] : 1);

include 'view/prologue.html.php';
include 'view/header.html.php'; 
?>
	<div id="sub-header-secondary">
		<div class="clearfix">
			<h2><a href="/glossary">GOTEO<span class="red">GLOSARIO</span></a></h2>
            <?php echo new View('view/header/share.html.php') ?>
		</div>
	</div>

    <script type="text/javascript" src="/view/js/inc/navi.js"></script>

	<div id="main" class="threecols">
		<div id="glossary-content">
            <h3 class="title">Glosario de términos utilizados en Goteo</h3>
            <?php if (!empty($posts)) : ?>
                <div class="glossary-page">
                <?php while ($post = $pagedResults->fetchPagedRow()) : ?>
                    <?php
                        $leter = $post->title[0];

                        if (!in_array($leter, $letters)) :
                            $letters[] = $leter;
                        ?>
                        <h4 class="supertitle"><?php echo $post->title[0]; ?></h4>
                    <?php endif; ?>
                    <div class="post">
                        <?php if (count($post->gallery) > 1) : ?>
                        <script type="text/javascript" >
                            jQuery(document).ready(function ($) {
                                    navi('gallery-post<?php echo $post->id; ?>', '<?php echo count($post->gallery) ?>');
                            });
                        </script>
                        <?php endif; ?>
                        <a name="term<?php echo $post->id  ?>"></a>
                        <h5 class="aqua"><?php echo $post->title; ?></h5>
                        <p><?php echo $post->text; ?></p>
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
                        <?php if (!empty($post->media->url)) : ?>
                            <div class="embed">
                                <?php echo $post->media->getEmbedCode(); ?>
                            </div>
                        <?php endif; ?>
                        <a class="up" href="#"><?php echo $go_up; ?></a>
                    </div>
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
<?php
    include 'view/footer.html.php';
	include 'view/epilogue.html.php';
