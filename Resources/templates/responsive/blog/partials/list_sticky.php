<!-- sticky menu -->

<div class="sticky-menu sticky-list <?= $this->total <8 ? 'visible-xs' : '' ?> " data-offset-top="650" data-spy="affix">
      	<ul class="filters list-inline center-block text-center">
            <?php foreach ($this->blog_sections as $key => $section) : ?>
                <?php $icon= $key=='matchfunding' ? 'icon-call' : 'icon-'.$key ?>
                <a href="<?= '/blog-section/' . $key.'#filters' ?>" >
                    <li class="<?php if ($this->section == $key) echo 'active' ?>">
                        <span class="block icon icon-3x <?= $icon ?>"></span>
                        <br>
                        <span><?= $this->text($section) ?></span>
                    </li>
                </a>
            <?php endforeach; ?>
        </ul>
</div>