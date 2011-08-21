<?php
use Goteo\Library\Text,
    Goteo\Model\Blog\Post;

$post = Post::get($this['post']);
$level = (int) $this['level'] ?: 3;
//@TODO: Si el usuario es el dueño del blog o tiene permiso para moderar, boton de borrar comentario
?>
<h<?php echo $level ?> class="title"><?php echo Text::get('blog-coments-header'); ?></h<?php echo $level ?>>
<div class="widget post-comments">

<?php if (!empty($post->comments)): ?>

    <div id="post-comments">

    <?php foreach ($post->comments as $comment) : ?>

        <div class="message<?php if ($comment->user->id == $this['owner']) echo ' owner'; ?>">
           <div class="arrow-up"></div>
           <span class="avatar">
           <a href="/user/profile/<?php echo htmlspecialchars($comment->user->id)?>" target="_blank">
           <img src="/image/<?php echo $comment->user->avatar->id; ?>/50/50/1" alt="<?php echo $comment->user->name; ?>" /></span></span>
           <h<?php echo $level ?> class="user">
		   <a href="/user/profile/<?php echo htmlspecialchars($comment->user->id)?>" target="_blank"><?php echo htmlspecialchars($comment->user->name) ?>
           </a></h<?php echo $level ?>>
           <div class="date"><span><?php echo $comment->date ?></span></div>
           <blockquote><?php echo $comment->text; ?></blockquote>
       </div>

    <?php endforeach; ?>

    </div>

<?php else : ?>

    <p><?php echo Text::get('blog-comments_no_comments'); ?></p>

<?php endif; ?>

</div>
