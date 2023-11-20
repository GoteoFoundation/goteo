<?php
?>

<section class="posts">
    <?php foreach($this->posts as $post): ?>
        <?= $this->insert('creator/partials/post_item', ['post' => $post]); ?>
    <?php endforeach; ?>
</section>
