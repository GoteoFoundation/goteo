<?php
$posts = $this->posts;
if (empty($posts))
    return;
?>

<section class="posts">
    <h2><?= $this->t('regular-posts') ?></h2>

    <div class="post-grid">
        <?php foreach($posts as $post): ?>
            <?= $this->insert('creator/partials/post_item', ['post' => $post]); ?>
        <?php endforeach; ?>
    </div>
</section>
