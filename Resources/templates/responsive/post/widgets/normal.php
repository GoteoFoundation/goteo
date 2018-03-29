<?php 

$image= $this->post->header_image ? $this->post->header_image : $this->post->image; ?>
<?php $author=$this->post->getAuthor(); ?>

<div class="post-widget">
    <a class="img-link" href="<?= '/blog/'.$this->post->id ?>">
    	<?php if($image): ?>
        	<img class="img-link" src="<?= $image->getLink(350, 200, true); ?>" alt="<?= $this->post->title ?>"/>
        <?php else: ?>
            <img class="img-link" src="/assets/img/blog/widget_post_default.png" alt="<?= $this->post->title ?>"/>
    	<?php endif; ?>
    </a>
    <div class="content">
        <div class="title">
            <a class="a-unstyled" href="<?= '/blog/'.$this->post->id ?>">
            	<?= $this->text_truncate($this->post->title, 100) ?>
            </a>
        </div>
        <div class="related-author">
            <img src="<?= $author->avatar->getLink(35, 35, true); ?>" ?>
            <?= $author->name ?>
        </div>
    </div>
</div>