<?php

use Goteo\Library\Text,
    Goteo\Core\View;

$blog = $this['blog'];
$posts = $blog->posts;

$bodyClass = 'blog';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div>
                <h2>GOTEO BLOG</h2>
            </div>
        </div>

        <div id="main" class="threecols">

            <div class="center">
            <?php if ($this['show'] == 'list') : ?>
                <?php if (!empty($posts)) : ?>
                    <?php foreach ($posts as $post) : ?>
                        <div class="widget">
                            <?php echo new View('view/blog/post.html.php', array('post'=>$post->id)); ?>
                            <?php if ($this['show'] == 'list') : ?>
                                <div class="more"><a href="/blog/<?php echo $post->id; ?>">Leer más</a></div>
                            <?php endif; ?>

                            <?php if ($this['show'] == 'list') : ?>
                                <p><a href="/blog/<?php echo $post->id; ?>"><?php echo $post->num_comments > 0 ? $post->num_comments : 'Sin'; ?> comentarios.</a></p>
                            <?php else : ?>
                                <p><?php echo $post->num_comments > 0 ? $post->num_comments : 'Sin'; ?> comentarios.</p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p>No hay entradas</p>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ($this['show'] == 'post') : ?>
            <div class="post">
                <?php
                    echo new View('view/blog/post.html.php', $this);
                    echo new View('view/blog/comments.html.php', $this);
                    echo new View('view/blog/sendComment.html.php', $this);
                ?>
            </div>
            <?php endif; ?>
            </div>

            <div class="side">
                <?php echo new View('view/blog/side.html.php', array()) ; ?>
            </div>

        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';
