<?php

use Goteo\Library\Text,
    Goteo\Model\Blog\Post;

$post = Post::get($this['post']);

$level = (int) $this['level'] ?: 3;

?>
<?php if (!empty($post->comments)): ?>
    <h<?php echo $level + 2?>>Comentarios</h<?php echo $level + 2?>>
    <?php foreach ($post->comments as $comment) : ?>
    <div class="message">
       <span class="avatar"><img src="/image/<?php echo $comment->user->avatar->id; ?>/50/50" alt="" /></span>
       <h<?php echo $level+3 ?> class="user"><?php echo htmlspecialchars($comment->user->name) ?></h<?php echo $level+3 ?>>
       <div class="date"><span><?php echo $comment->date ?></span></div>
       <blockquote><?php echo $comment->text; ?></blockquote>
   </div>
    <?php endforeach; ?>
<?php else : ?>
    <p>No hay comentarios</p>
<?php endif; ?>
