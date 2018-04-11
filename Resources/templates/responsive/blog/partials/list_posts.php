<ul class="filters list-inline center-block text-center">
	<?php foreach ($this->blog_sections as $key => $section) : ?>
		<?php $icon= $key=='matchfunding' ? 'icon-call' : 'icon-'.$key ?>
		<?php $description= $section.'-description'; ?>
	    <a href="<?= '/blog/section/' . $key ?>" >
	        <li class="<?php if ($section == $key||$key=='mission') echo 'active' ?>">
	        	<span class="block icon icon-3x <?= $icon ?>"></span>
	        	<br>
	            <span><?= $this->text($section) ?></span>
	        </li>
	    </a>
	<?php endforeach; ?>
</ul>
 <div class="section list-posts container">
 	<div class="description">
 		<?= $this->text($description) ?>
 	</div>
 	<div class="row">
	 	<?php foreach($this->list_posts as $post): ?>
	 	<div class="col-md-4">
	 		<?= $this->insert('post/widgets/normal', [
	            'post' => $post
	        ]) ?>
	 	</div>
	 	<?php endforeach; ?>
 	</div>
 </div>
