<?php

$this->layout('default::layout');


// some customs view replacements
$lang = '/'.$this->current_lang();
?>

<?php $this->section('footer-social') ?>

    <div class="block social" style="border-right:#ebe9ea 2px solid;">
        <h6 class="title"><?=$this->text('footer-header-social') ?></h6>
        <ul>
            <li class="twitter"><a href="<?=$this->text('social-account-twitter') ?>" target="_blank"><?=$this->text('regular-twitter') ?></a></li>
            <li class="facebook"><a href="<?=$this->text('social-account-facebook') ?>" target="_blank"><?=$this->text('regular-facebook') ?></a></li>
            <li class="calendar"><a href="/calendar" target="_blank"><?=$this->text('regular-calendar') ?></a></li>
            <li class="gplus"><a href="<?=$this->text('social-account-google') ?>" target="_blank"><?=$this->text('regular-google') ?></a></li>
            <li class="rss"><a rel="alternate" type="application/rss+xml" title="RSS" href="/rss<?= $lang ?>" target="_blank"><?=$this->text('regular-share-rss')?></a></li>

        </ul>
    </div>

<?php $this->replace() ?>
