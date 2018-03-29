<!-- sticky menu -->

<div class="sticky-menu" data-offset-top="600" data-spy="affix">
	<div class="container-fluid">
  	<ul class="filters list-inline center-block text-center">
        <?php foreach ($this->blog_sections as $key => $section) : ?>
            <a href="<?= '/blog/section/' . $key ?>" >
                <li class="<?php if ($section == $key||$key=='mission') echo 'active' ?>">
                    <?= $this->text($section) ?>
                </li>
            </a>
        <?php endforeach; ?>
    </ul>	
	</div>
</div>