<?php if(!$this->tag->name): ?>
	
	<?= $this->insert('blog/partials/filters') ?>

<?php if(!$this->section) $description=$this->text('blog-main-description') ?>

<?php else: ?>
	<h2 class="tag-title"><?= ucfirst($this->tag->name) ?></h2>
<?php endif; ?>
 <div class="section list-posts container">
 	<?php if($description): ?>
	 	<div class="description">
	 		<?= $this->text($description) ?>
	 	</div>
 	<?php endif; ?>
 	<div class="row">
	 	<?php foreach($this->list_posts as $post): ?>
	 	<div class="col-md-4">
	 		<?= $this->insert('post/widgets/normal', [
	            'post' => $post
	        ]) ?>
	 	</div>
	 	<?php endforeach; ?>
 	</div>

 	<?= $this->insert('partials/utils/paginator', ['total' => $this->total, 'limit' => $this->limit ? $this->limit : 10]) ?>

 </div>
