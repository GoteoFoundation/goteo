<?php
    $post = $this->post;
    $user = $this->user;
?>

<article class="post">
    <header class="card-header">
        <div class="post-image">
            <?php if ($post->image): ?>
                <img class="post-image" src="<?= $post->image->getLink(80,80,false) ?>" alt="<?= $post->title ?>">
            <?php else: ?>
                <img class="post-image" src="/assets/img/blog/header_default.png" width="80">
            <?php endif; ?>
        </div>
    </header>

    <div class="card-body">
        <h2>
            <?= $post->title ?>
        </h2>
        <p><?= $post->subtitle ?></p>
    </div>

    <footer>
        <div class="author-info">
            <div class="author-image">
                <img src="<?= $user->avatar->getLink(30,30,false) ?>>" alt="<?= $post->author->name ?>>">
            </div>
            <span><?= $user->name ?>></span>
        </div>
        <span>
            <?= date_formater($this->post->date) ?>
        </span>
    </footer>
</article>
