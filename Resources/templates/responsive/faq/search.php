<?php $this->layout('faq/layout') ?>

<?php $this->section('faq-content') ?>

    <?= $this->insert('faq/partials/header', ['view' => 'search', 'search' => $this->search, 'header' => 'search']) ?>

    <section class="search container">
        <?php if (!$this->totalFaqs): ?>
            <h2 class="no-results"><?= $this->t('faq-search-empty') ?></h2>
        <?php else: ?>
            <?php foreach($this->faqSections as $section): ?>
                <?php if (!empty($this->faqs[$section->id])): ?>
                    <div class="row">
                        <article>
                            <h2><a href="/faq/<?= $section->slug ?>"><?= $section->name ?></a></h2>
                            <?php foreach($this->faqs[$section->id] as $faq): ?>
                                <article class="col-sm-8 col-sm-offset-1">
                                    <h3><a href="/faq/<?= $section->slug ?>/<?= $faq->slug ?>"><?= $faq->title ?></a></h3>
                                    <?= $this->markdown($faq->description) ?>
                                </article>
                            <?php endforeach; ?>
                        </article>
                        <hr>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>

<?php $this->replace() ?>
