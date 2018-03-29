 <div class="section list-posts container">
 	<ul class="filters list-inline center-block text-center">
	    <?php foreach ($this->blog_sections as $key => $section) : ?>
	        <a href="<?= '/blog/section/' . $key ?>" >
	            <li class="<?php if ($section == $key||$key=='mission') echo 'active' ?>">
	                <?= $this->text($section) ?>
	            </li>
	        </a>
	    <?php endforeach; ?>
	</ul>
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
