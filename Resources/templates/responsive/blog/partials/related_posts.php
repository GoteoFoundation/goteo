<?php if($this->related_posts): ?>
	<div class="section related-posts">
		<div class="container">
            <h2 class="title"><?= $this->text('blog-related-posts') ?></h2>
			<div class="row">
				<?php foreach($this->related_posts as $related_post): ?>
					<div class="col-md-4 related-post">
                        <?= $this->insert('post/widgets/normal', [
                            'post' => $related_post
                        ]) ?>
                    </div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
<?php endif; ?>