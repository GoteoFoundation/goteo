<?php

use Goteo\Core\View,
    Goteo\Library\Worth,
    Goteo\Library\Text,
    Goteo\Model\User\Interest,
    Goteo\Util\Pagination\Paginated,
    Goteo\Util\Pagination\DoubleBarLayout;

$bodyClass = 'user-profile';
include __DIR__ . '/../prologue.html.php';
include __DIR__ . '/../header.html.php';

$user = $vars['user'];
$worthcracy = Worth::getAll();

// en la página de cofinanciadores, paginación de 20 en 20
$pagedResults = new Paginated($vars['investors'], 20, isset($_GET['page']) ? $_GET['page'] : 1);
?>
<?php echo View::get('user/widget/header.html.php', array('user'=>$user)) ?>

<?php if($_SESSION['messages']) { include __DIR__ . '/../header/message.html.php'; } ?>

<div id="main">

    <div class="center">
        <div class="widget project-supporters">
            <h3 class="title"><?php echo Text::get('profile-my_investors-header'); ?></h3>
            <dl class="summary">
                <dt class="supporters"><?php echo Text::get('project-menu-supporters'); ?></dt>
                <dd class="supporters"><?php echo count($vars['investors']) ?></dd>
            </dl>

            <div class="supporters">
                <ul>
                <?php while ($investor = $pagedResults->fetchPagedRow()) : ?>
                    <li class="activable"><?php echo View::get('user/widget/supporter.html.php', array('user' => $investor, 'worthcracy' => $worthcracy)) ?></li>
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
        <?php echo View::get('user/widget/sharemates.html.php', $vars) ?>
        <?php echo View::get('user/widget/user.html.php', $vars) ?>
    </div>

</div>

<?php include __DIR__ . '/../footer.html.php' ?>

<?php include __DIR__ . '/../epilogue.html.php' ?>
