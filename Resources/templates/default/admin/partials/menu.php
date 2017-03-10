<div class="admin-menu">
<?php

$zones = array(
    'admin-contents' => array('node', 'blog', 'texts', 'faq', 'pages', 'categories', 'social_commitment', 'licenses', 'icons', 'tags', 'criteria', 'templates', 'glossary', 'info', 'wordcount', 'milestones'),
    'admin-projects' => array('projects', 'accounts', 'reviews', 'translates', 'rewards', 'commons'),
    'admin-users' => array('users', 'worth', 'mailing', 'sent'),
    'admin-home' => array('home', 'promote', 'news', 'banners', 'footer', 'recent', 'open_tags', 'stories'),
    'admin-sponsors' => array('newsletter', 'sponsors', 'nodes', 'transnodes'),
    );

foreach($zones as $id => $parts) {
    $zones[$id] = array_intersect($parts, array_keys($this->admin_menu));
    if(empty($zones[$id])) unset($zones[$id]);
}
$super = array('projects', 'users', 'accounts', 'nodes', 'newsletter');

$super = array_intersect($super, array_keys($this->admin_menu));
?>

<?php $this->section('admin-menu-left') // This allows to be extended ?>

    <?php foreach ($zones as $id => $parts) : ?>

        <fieldset id="menu-<?= $id ?>">
            <legend><?= $this->text($id) ?></legend>
            <ul class="ul-admin">

            <?php $this->section('menu-fieldset-' . $id) // This allows to be extended ?>

                <?php foreach ($parts as $action) : ?>
                    <li<?= ($action === $this->option ? ' class="selected"' : '') ?>><a href="/admin/<?= $action ?>"><?= $this->admin_menu[$action] ?></a></li>
                <?php endforeach ?>

            <?php $this->stop() ?>

            </ul>
        </fieldset>

    <?php endforeach ?>

<?php $this->stop() ?>


</div>

<?php if($super): ?>

    <div class="widget board">
        <ul class="ul-admin">

            <?php $this->section('admin-menu-top') // This allows to be extended ?>

                <?php foreach ($super as $id) : ?>

                        <li<?= ($id === $this->option ? ' class="selected"' : '') ?>><a href="/admin/<?= $id ?>"><?=  $this->admin_menu[$id] ?></a></li>

                <?php endforeach ?>

            <?php $this->stop() ?>

        </ul>
    </div>

<?php endif; ?>
