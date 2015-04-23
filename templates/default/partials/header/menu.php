<?php
use Goteo\Core\ACL,
    Goteo\Core\NodeSys;

$nodes = Nodesys::activeNodes();

?>
    <div id="menu">

        <h2><?= $this->text('regular-menu') ?></h2>

        <ul>
            <li class="home"><a class="node-jump" href="<?php echo SITE_URL ?>"><?= $this->text('regular-home') ?></a></li>
            <li class="explore"><a class="button red" href="/discover"><?= $this->text('regular-discover') ?></a></li>
            <li class="create"><a class="button aqua" href="/project/create"><?= $this->text('regular-create') ?></a></li>
            <li class="search">
                <form method="get" action="/discover/results">
                    <fieldset>
                        <legend><?= $this->text('regular-search') ?></legend>
                        <input type="text" name="query"  />
                        <input type="submit" value="Buscar" />
                    </fieldset>
                </form>
            </li>
            <li class="community"><a href="/community"><span><?= $this->text('community-menu-main') ?></span></a>
                <div>
                    <ul>
                        <?php // si estamos en easy mode no pintamos estos enlaces
                        if (!defined('GOTEO_EASY') || \GOTEO_EASY !== true) : ?>
                        <li><a href="/community"><span><?= $this->text('community-menu-activity') ?></span></a></li>
<?php /* quitamos Compartiendo por ahora
                        <li><a href="/community/sharemates"><span><?= $this->text('community-menu-sharemates') ?></span></a></li>
 */ ?>
                        <?php endif; ?>
                        <!-- nodos activos -->
                        <?php  foreach ($nodes as $node) : ?>
                        <li><a class="node-jump" href="<?php echo $node->url ?>"><?php echo $node->name ?></a></li>
                        <?php  endforeach; ?>
                    </ul>
                </div>
            </li>

            <?php if (!empty($_SESSION['user'])): ?>
            <li class="dashboard"><a href="/dashboard"><span><?= $this->text('dashboard-menu-main') ?></span><img src="<?php echo $_SESSION['user']->avatar->getLink(28, 28, true); ?>" /></a>
                <div>
                    <ul>
                        <li><a href="/dashboard/profile"><span><?= $this->text('dashboard-menu-profile') ?></span></a></li>
                        <li><a href="/dashboard/activity"><span><?= $this->text('dashboard-menu-activity') ?></span></a></li>
                        <li><a href="/dashboard/projects"><span><?= $this->text('dashboard-menu-projects') ?></span></a></li>
                        <li><a href="/dashboard/profile/preferences"><span><?= $this->text('dashboard-menu-profile-references'); ?></span></a></li>

                        <?php if ( isset($_SESSION['user']->roles['caller']) ) : ?>
                            <li><a href="/dashboard/calls"><span><?= $this->text('dashboard-menu-calls') ?></span></a></li>
                        <?php endif; ?>

                        <?php if ( isset($_SESSION['user']->roles['translator']) ||  isset($_SESSION['user']->roles['admin']) || isset($_SESSION['user']->roles['superadmin']) ) : ?>
                            <li><a href="/translate"><span><?= $this->text('regular-translate_board') ?></span></a></li>
                        <?php endif; ?>

                        <?php if ( isset($_SESSION['user']->roles['checker']) ) : ?>
                            <li><a href="/review"><span><?= $this->text('regular-review_board') ?></span></a></li>
                        <?php endif; ?>

                        <?php if ( isset($_SESSION['user']->roles['admin']) || isset($_SESSION['user']->roles['superadmin']) ) : ?>
                            <li><a href="/admin"><span><?= $this->text('regular-admin_board') ?></span></a></li>
                        <?php endif; ?>

                        <?php if ( isset($_SESSION['user']->roles['manager']) ) : ?>
                            <li><a href="/manage"><span><?= $this->text('regular-manage_board') ?></span></a></li>
                        <?php endif; ?>

                        <li class="logout"><a href="/user/logout"><span><?= $this->text('regular-logout') ?></span></a></li>
                    </ul>
                </div>
            </li>
            <?php else: ?>
            <li class="login">
                <a href="<?php echo SEC_URL; ?>/user/login"><?= $this->text('regular-login') ?></a>
            </li>

            <?php endif ?>
        </ul>
    </div>
