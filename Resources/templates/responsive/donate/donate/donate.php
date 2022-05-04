<?php

$this->layout('blog/layout', [
	'bodyClass' => 'blog',
    'title' => $this->t('donate-meta-title'),
    'meta_description' => $this->t('donate-meta-description')
    ]);

$this->section('blog-content');

?>

<?= $this->insert('donate/donate/partials/donate_info') ?>

<!-- team -->
<?= $this->insert('foundation/donor') ?>

<?= $this->insert('donate/donate/partials/donate_methods') ?>

<?= $this->insert('donate/donate/partials/foundation_accounts') ?>


<?php $this->replace() ?>
