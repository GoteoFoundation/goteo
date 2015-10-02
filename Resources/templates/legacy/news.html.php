<?php
use Goteo\Library\Text,
    Goteo\Model\Blog\Post,
    Goteo\Core\View,
    Goteo\Model\Image,
    Goteo\Util\Pagination\Paginated,
    Goteo\Util\Pagination\DoubleBarLayout;

$bodyClass = 'news';

$read_more = Text::get('regular-read_more');

// noticias
$news = $vars['news'];

// últimas entradas del blog goteo
$list = array();

$title = Text::get('blog-side-last_posts');
$items = Post::getAll(1, 7);
// enlace a la entrada
foreach ($items as $item) {
    $list[] = '<a href="/blog/'.$item->id.'"> '.Text::recorta($item->title, 100).'</a>';
}

// paginacion
$pagedResults = new Paginated($news, 7, isset($_GET['page']) ? $_GET['page'] : 1);

include __DIR__ . '/prologue.html.php';
include __DIR__ . '/header.html.php';
?>
<div id="sub-header-secondary">
    <div class="clearfix">
        <h2>GOTEO<span class="red">NEWS</span></h2>
        <?php echo View::get('header/share.html.php') ?>
    </div>
</div>
<div id="main" class="threecols">
    <div id="news-content">
        <?php while ($content = $pagedResults->fetchPagedRow()) : ?>
            <div class="widget news-content-module">
                <a name="news<?php echo $content->id ?>" />
                <h3><?php echo $content->title; ?></h3>
                <?php if($content->image instanceof Image) { ?>
                    <img src="<?php echo $content->image->getLink(150, 85); ?>" alt="" class="logo_new" />
                <?php } ?>
                <blockquote><?php echo $content->description; ?></blockquote>
                <a href="<?php echo $content->url; ?>"><?php echo $read_more; ?></a>
            </div>
        <?php endwhile; ?>
        <ul id="pagination">
            <?php   $pagedResults->setLayout(new DoubleBarLayout());
                    echo $pagedResults->fetchPagedNavigation(); ?>
        </ul>
    </div>
    <div id="news-sidebar">
        <div class="widget news-sidebar-module">
            <h3 class="supertitle"><?php echo $title; ?></h3>
            <ul id="news-side-posts">
                <?php foreach ($list as $item) : ?>
                <li><?php echo $item; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
<?php
include __DIR__ . '/footer.html.php';
include __DIR__ . '/epilogue.html.php';
?>
