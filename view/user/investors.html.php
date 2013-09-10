<?php

use Goteo\Core\View,
    Goteo\Library\Worth,
    Goteo\Library\Text,
    Goteo\Model\User\Interest;

$bodyClass = 'user-profile';
include 'view/prologue.html.php';
include 'view/header.html.php';

$user = $this['user'];
$worthcracy = Worth::getAll();

// en la página de cofinanciadores, paginación de 20 en 20
require_once 'library/pagination/pagination.php';

$pagedResults = new \Paginated($this['investors'], 20, isset($_GET['page']) ? $_GET['page'] : 1);
?>
<?php echo new View('view/user/widget/header.html.php', array('user'=>$user)) ?>

<?php if(isset($_SESSION['messages'])) { include 'view/header/message.html.php'; } ?>

<div id="main">

    <div class="center">
        <div class="widget project-supporters">
            <h3 class="title"><?php echo Text::get('profile-my_investors-header'); ?></h3>
            <dl class="summary">
                <dt class="supporters"><?php echo Text::get('project-menu-supporters'); ?></dt>
                <dd class="supporters"><?php echo count($this['investors']) ?></dd>
            </dl>

            <div class="supporters">
                <ul>
                <?php while ($investor = $pagedResults->fetchPagedRow()) : ?>
                    <li class="activable"><?php echo new View('view/user/widget/supporter.html.php', array('user' => $investor, 'worthcracy' => $worthcracy)) ?></li>
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
        <?php echo new View('view/user/widget/sharemates.html.php', $this) ?>
        <?php echo new View('view/user/widget/user.html.php', $this) ?>
    </div>

</div>

<?php include 'view/footer.html.php' ?>

<?php include 'view/epilogue.html.php' ?>
