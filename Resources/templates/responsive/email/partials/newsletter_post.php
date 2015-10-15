<?php
$post = $this->post;
?>
<div>
    <span><?= $post->title ?></span>
    <div><?= $this->text_truncate($post->text) ?></div>
</div>
