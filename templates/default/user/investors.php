<?php

use Goteo\Core\View,
    Goteo\Util\Pagination\Paginated,
    Goteo\Util\Pagination\DoubleBarLayout;

$user = $this->user;
$worthcracy = $this->worthcracy;

//TODO: remove this paginated thing
// en la página de cofinanciadores, paginación de 20 en 20
$pagedResults = new Paginated($this->investors, 20, isset($_GET['page']) ? $_GET['page'] : 1);

$this->layout('layout', [
    'bodyClass' => 'user-profile',
    ]);

$this->section('content');

?>

<?= View::get('user/widget/header.html.php', array('user' => $user)) ?>

<div id="main">

    <div class="center">
        <div class="widget project-supporters">
            <h3 class="title"><?= $this->text('profile-my_investors-header'); ?></h3>
            <dl class="summary">
                <dt class="supporters"><?= $this->text('project-menu-supporters'); ?></dt>
                <dd class="supporters"><?= count($this->investors) ?></dd>
            </dl>

            <div class="supporters">
                <ul>
                <?php while ($investor = $pagedResults->fetchPagedRow()) : ?>
                    <li class="activable"><?= View::get('user/widget/supporter.html.php', array('user' => $investor, 'worthcracy' => $worthcracy)) ?></li>
                <?php endwhile ?>
                </ul>
            </div>

            <ul id="pagination">
                <?php   $pagedResults->setLayout(new DoubleBarLayout());
                        echo $pagedResults->fetchPagedNavigation(); ?>
            </ul>

        </div>
    </div>
    <div class="side">
        <?= View::get('user/widget/sharemates.html.php', $this->vars) ?>
        <?= View::get('user/widget/user.html.php', $this->vars) ?>
    </div>

</div>

<?php $this->replace() ?>

