<?php if($this->related_posts): ?>
	<div class="section related-posts">
		<div class="container">
            <h2 class="title"><?= $this->text('blog-related-posts') ?></h2>
			<div class="row">
				<?php foreach($this->related_posts as $related_post): ?>
					<?php $image= $related_post->header_image ? $related_post->header_image : $related_post->image; ?>
                    <?php $related_author=$related_post->getAuthor(); ?>

					<div class="col-md-4 related-post">
                        <a class="img-link" href="<?= '/blog/'.$related_post->id ?>">
                        	<?php if($image): ?>
                            	<img class="img-link" src="<?= $image->getLink(350, 200, true); ?>" alt="<?= $related_post->title ?>"/>
                        	<?php endif; ?>
                        </a>
                        <div class="content">
                            <div class="title">
                                <a class="a-unstyled" href="<?= '/blog/'.$related_post->id ?>">
                                	<?= $this->text_truncate($related_post->title, 100) ?>
                                </a>
                            </div>
                            <div class="related-author">
                                <img src="<?= $related_author->avatar->getLink(35, 35, true); ?>" ?>
                                <?= $related_author->name ?>
                            </div>
                        </div>
                    </div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
<?php endif; ?>