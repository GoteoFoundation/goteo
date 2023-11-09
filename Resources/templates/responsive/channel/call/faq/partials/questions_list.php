<section class="section faq-list">
    <div class="container">
        <div class="channel-faqs">
            <?php foreach ($this->nodeFaqs as $faq): ?>
                <article class="faqs_module card">
                    <header>
                        <a href="<?= "/channel/{$this->channel->id}/faq/{$faq->slug}" ?>">
                            <h2 style="<?= $this->colors['primary'] ? "color:".$this->colors['primary'] : '' ?>"><?= $faq->name ?></h2>
                        </a>
                    </header>
                    <ul>
                        <?php foreach ($faq->getQuestions() as $faqQuestion): ?>
                            <li>
                                <a style="<?= $this->colors['secondary'] ? "color:".$this->colors['secondary'] : '' ?>" href="<?= "/channel/{$this->channel->id}/faq/{$faq->slug}#faq-{$faqQuestion->id}" ?>">
                                    <?= $faqQuestion->title ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
