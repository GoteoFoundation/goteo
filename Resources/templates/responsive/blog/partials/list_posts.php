<?php if(!$this->tag->name): ?>
	<ul id="filters" class="filters list-inline center-block text-center">
		<?php foreach ($this->blog_sections as $key => $section_value) : ?>
			<?php $icon= $key=='matchfunding' ? 'icon-call' : 'icon-'.$key ?>
			<?php if($this->section == $key): ?>
				<?php $description= $section_value.'-description'; ?>
			<?php endif; ?>
		    <a href="<?= '/blog-section/' . $key.'#filters' ?>" >
		        <li class="<?php if ($this->section == $key) echo 'active' ?>">
		        	<span class="block icon icon-3x <?= $icon ?>"></span>
		        	<br>
		            <span><?= $this->text($section_value) ?></span>
		        </li>
		    </a>
		<?php endforeach; ?>
	</ul>

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
