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