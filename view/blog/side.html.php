<?php

use Goteo\Library\Text,
    Goteo\Model\Blog\Post;

$blog = $this['blog'];

$list = array();

switch ($this['type']) {
    case 'posts':
        $title = Text::get('blog-side-last_posts');
        $items = Post::getAll($blog->id, 7);
        // enlace a la entrada
        foreach ($items as $item) {
            $list[] = '<a href="/blog/'.$item->id.'">'.Text::recorta($item->title, 100).'</a>';
        }
        break;
    case 'tags':
        $title = Text::get('blog-side-tags');
        $items = Post\Tag::getList($blog->id);
        // enlace a la lista de entradas con filtro tag
        foreach ($items as $item) {
            if ($item->used > 0) {
                $list[] = '<a href="/blog/?tag='.$item->id.'">'.$item->name.'</a>';
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
    <span>{$item->date}</span><br />
    <strong>{$item->user->name}</strong>
    <p>{$text}</p>
</div>";
            }
        break;
}

if (!empty($list)) : ?>
<div class="widget">
    <h3 class="title"><?php echo $title; ?></h3>
    <ul id="blog-side-<?php echo $this['type']; ?>">
        <?php foreach ($list as $item) : ?>
        <li><?php echo $item; ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>