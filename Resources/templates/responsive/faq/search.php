<?php $this->layout('faq/layout') ?>

<?php $this->section('faq-content') ?>

    <?= $this->insert('faq/partials/header', ['view' => 'search']) ?>

    <section class="search">
      	<div class="row">
            <?php foreach($this->faqs as $faq): ?>
                <article class="col-sm-8 col-sm-offset-1">
                    <h3><?= $faq->title ?></h3>
                    <h4><?= $faq->getSubsection()->name ?></h4>
                    <?= $this->markdown($faq->description) ?>
                </article>
            <?php endforeach; ?>
    	</div>
    </section>

<?php $this->replace() ?>
