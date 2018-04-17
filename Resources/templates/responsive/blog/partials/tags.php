<?php if($this->post->tags): ?>
	<div class="section">
		<div class="container">
            <ul class="tags list-inline center-block text-center">
	            <?php foreach ($this->post->tags as $key => $tag) : ?>
	                <a href="<?= '/blog-tag/' . $key ?>" >
	                    <li>
	                    <?= $tag ?>  
	                    </li>
	                </a>
	            <?php endforeach; ?>
        	</ul>
		</div>
	</div>
<?php endif; ?>