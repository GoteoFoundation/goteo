<?php

use Goteo\Library\Text,
    Goteo\Model\Blog\Post,
    Goteo\Library\Feed;

$blog = $vars['blog'];

$list = array();

switch ($vars['type']) {
    case 'posts':
        $title = Text::get('blog-side-last_posts');
        $items = Post::getAll(null, 7);
        // enlace a la entrada
        foreach ($items as $item) {
            $list[] = '<a href="/blog/'.$item->id.'"> '.Text::recorta($item->title, 100).'</a>';
        }
        break;
    case 'tags':
        $title = Text::get('blog-side-tags');
        $items = Post\Tag::getList();
        // enlace a la lista de entradas con filtro tag
        foreach ($items as $item) {
            if ($item->used > 0) {
                $list[] = '<a href="/blog?tag='.$item->id.'">'.$item->name.'</a>';
            }
        }
        break;
    case 'comments':
        $title = Text::get('blog-side-last_comments');
        $items = Post\Comment::getList($blog->id);
        // enlace a la entrada en la que ha comentado
        foreach ($items as $item) {
            $text = Text::recorta($item->text, 200);
            $list[] = "
				<div>
					<!--span class='avatar'><img src='" . $item->user->avatar->getLink(50, 50, true) . "' alt='' /></span-->
					<span class='date'>{$item->date}</span>
					<div class='high-comment'>
						<strong><a href=\"/blog/{$item->post}\">{$item->user->name}</a></strong>
						<p>{$text}</p>
					</div>
				</div>";
            }
        break;
    case 'feed':  // actividad reciente de proyectos
        $title = Text::get('feed-head-projects');
        $list = Feed::getProjUpdates(10);
        break;
}

if (!empty($list)) : ?>
<?php if ($vars['type'] == 'feed') : ?>
<div class="widget blog-sidebar-module feed">
    <h3 class="supertitle" style="margin-bottom: 0px;"><?php echo $title; ?></h3>
    <ul id="blog-side-comments" style="width: 260px;">
        <?php foreach ($list as $item) : ?>
        <li><?php echo Feed::subItem($item); ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php else : ?>
<div class="widget blog-sidebar-module">
    <h3 class="supertitle"><?php echo $title; ?></h3>
    <ul id="blog-side-<?php echo $vars['type']; ?>">
        <?php foreach ($list as $item) : ?>
        <li><?php echo $item; ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>
<?php endif; ?>
