<?php $channel=$this->channel; ?>

<?php if($channel->getPosts()): ?>
	<div class="section posts">
		<div class="container">
            <h2 class="title"><?= $this->text('home-posts-header') ?></h2>
			<div class="row">
				<?php foreach($channel->getPosts() as $related_post): ?>
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
