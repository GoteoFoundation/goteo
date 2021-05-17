<?php

$image= $this->post->header_image ? $this->post->header_image : $this->post->image; ?>
<?php $author=$this->post->getAuthor(); ?>

<div class="post-widget">
    <a class="img-link" href="<?= '/blog/'.$this->post->getSlug() ?>">
    	<?php if($image): ?>
        	<img loading="lazy" class="img-link" src="<?= $image->getLink(230, 150, true); ?>" alt="<?= $this->post->title ?>"/>
        <?php else: ?>
            <img loading="lazy" class="img-link" src="/assets/img/blog/widget_post_default.png" alt="<?= $this->post->title ?>"/>
    	<?php endif; ?>
    </a>
    <div class="content">
        <div class="date">
          <?=  date_formater($this->post->date) ?>
        </div>
        <div class="title">
          <a href="<?= '/blog/' . $this->post->getSlug() . $this->lang_url_query($this->lang_current()) ?>">
            <?= $this->text_truncate($this->post->title, 20) ?>
          </a>
        </div>
        <div class="subtitle">
          <?= $this->text_truncate($this->post->subtitle, 80) ?>
        </div>
        <div class="author">
          <?= $author->name ?>
        </div>
        <a class="arrow" href="<?= '/blog/' . $this->post->getSlug() . $this->lang_url_query($this->lang_current()) ?>">
          <span class="icon icon-arrow icon-2x"></span>
        </a>
    </div>
</div>
