<?php

$this->layout('blog/layout', [
	'bodyClass' => 'blog',
    'title' => 'Donar :: Goteo.org',
    'meta_description' => 'Ayuda a la Fundación Goteo con tu donativo'
    ]);

$this->section('blog-content');

?>

<?= $this->insert('donate/donate/partials/donate_info') ?>

<!-- team -->
<?= $this->insert('foundation/donor') ?>

<?= $this->insert('donate/donate/partials/donate_methods') ?>

<?= $this->insert('donate/donate/partials/foundation_accounts') ?>


<?php $this->replace() ?>
