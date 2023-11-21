<section class="posts">
    <h2><?= $this->t('regular-posts') ?></h2>

    <div class="post-grid">
        <?php foreach($this->posts as $post): ?>
            <?= $this->insert('creator/partials/post_item', ['post' => $post]); ?>
        <?php endforeach; ?>
    </div>
</section>
