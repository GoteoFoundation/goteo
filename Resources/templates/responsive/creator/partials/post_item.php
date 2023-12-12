<?php
    $post = $this->post;
    $user = $this->user;
?>

<article class="post">
    <?php if ($post->image): ?>
        <div class="post-image">
                <img class="post-image" src="<?= $post->image->getLink(80,80,false) ?>" alt="<?= $post->title ?>">
        </div>
    <?php endif; ?>

    <div class="card-body">
        <div class="card-info">
            <h2><a href="<?= "/blog/$post->slug" ?>" target="_blank" ><?= $post->title ?></a></h2>
            <p><?= $post->subtitle ?></p>
        </div>
        <div class="card-author-info">
            <div class="author-image">
                <img src="<?= $user->avatar->getLink(30,30,false) ?>" alt="<?= $user->name ?>"> <?= $user->name ?>
            </div>
            <span><?= date_formater($this->post->date) ?></span>
        </div>
    </div>
</article>
