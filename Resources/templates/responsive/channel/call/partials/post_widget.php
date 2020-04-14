<?php

$image= $this->post->header_image ? $this->post->header_image : $this->post->image; ?>
<?php $author=$this->post->getAuthor(); ?>

<div class="post-widget">
    <a class="img-link" href="<?= '/blog/'.$this->post->getSlug() ?>">
    	<?php if($image): ?>
        	<img class="img-link" src="<?= $image->getLink(230, 150, true); ?>" alt="<?= $this->post->title ?>"/>
        <?php else: ?>
            <img class="img-link" src="/assets/img/blog/widget_post_default.png" alt="<?= $this->post->title ?>"/>
    	<?php endif; ?>
    </a>
    <div class="content">
        <div class="date">
          <?=  $this->post->date ?>
        </div>
        <div class="title">
          <?= $this->text_truncate($this->post->title, 20) ?>
        </div>
        <div class="subtitle">
          <?= $this->text_truncate($this->post->subtitle, 80) ?>
        </div>
        <div class="author">
          <?= $author->name ?>
        </div>
        <a class="arrow" href="<?= '/blog/' . $this->post->getSlug() ?>">
          <span class="icon icon-arrow icon-2x"></span>
        </a>
    </div>
</div>
