<?php $this->layout('faq/layout') ?>

<?php $this->section('faq-content') ?>

    <?= $this->insert('faq/partials/header', ['view' => 'search', 'search' => $this->search]) ?>

    <section class="search container">
        <?php foreach($this->faqSections as $section): ?>
            <?php if (!empty($this->faqs[$section->id])): ?>
                <div class="row">
                    <h2><?= $section->name ?></h2>
                    <?php foreach($this->faqs[$section->id] as $faq): ?>
                        <article class="col-sm-8 col-sm-offset-1">
                            <h3><?= $faq->title ?></h3>
                            <h4><?= $faq->getSubsection()->name ?></h4>
                            <?= $this->markdown($faq->description) ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </section>

<?php $this->replace() ?>
