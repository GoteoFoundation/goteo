<?php
use Goteo\Core\ACL,
    Goteo\Core\NodeSys,
    Goteo\Library\Text;

$nodes = Nodesys::activeNodes();

?>
    <div id="menu">

        <h2><?php echo Text::get('regular-menu'); ?></h2>

        <ul>
            <li class="home"><a class="node-jump" href="<?php echo SITE_URL ?>"><?php echo Text::get('regular-home'); ?></a></li>
            <li class="explore"><a class="button red" href="/discover"><?php echo Text::get('regular-discover'); ?></a></li>
            <li class="create"><a class="button aqua" href="/project/create"><?php echo Text::get('regular-create'); ?></a></li>
            <li class="create"><a class="button violet" href="/discover/calls"><?php echo Text::get('regular-call-button'); ?></a></li>
            <li class="search">
                <form method="get" action="/discover/results">
                    <fieldset>
                        <legend><?php echo Text::get('regular-search'); ?></legend>
                        <input type="text" name="query"  />
                        <input type="submit" value="Buscar" />
                    </fieldset>
                </form>
            </li>
            <li class="community"><a href="/community"><span><?php echo Text::get('community-menu-main'); ?></span></a>
                <div>
                    <ul>
                        <li><a href="/community"><span><?php echo Text::get('community-menu-activity'); ?></span></a></li>
<?php /* quitamos Compartiendo por ahora
                        <li><a href="/community/sharemates"><span><?php echo Text::get('community-menu-sharemates'); ?></span></a></li>
 */ ?>
                        <!-- nodos activos -->
                        <?php  foreach ($nodes as $node) : ?>
                        <li><a class="node-jump" href="<?php echo $node->url ?>"><?php echo $node->name ?></a></li>
                        <?php  endforeach; ?>
                    </ul>
                </div>
            </li>

            <?php if (!empty($_SESSION['user'])): ?>
            <li class="dashboard"><a href="/dashboard"><span><?php echo Text::get('dashboard-menu-main'); ?></span><img src="<?php echo $_SESSION['user']->avatar->getLink(28, 28, true); ?>" /></a>
                <div>
                    <ul>
                        <li><a href="/dashboard/profile"><span><?php echo Text::get('dashboard-menu-profile'); ?></span></a></li>
                        <li><a href="/dashboard/activity"><span><?php echo Text::get('dashboard-menu-activity'); ?></span></a></li>
                        <li><a href="/dashboard/projects"><span><?php echo Text::get('dashboard-menu-projects'); ?></span></a></li>
                        <li><a href="/dashboard/profile/preferences"><span><?php echo Text::get('dashboard-menu-profile-preferences'); ?></span></a></li>

                        <?php if ( isset($_SESSION['user']->roles['caller']) ) : ?>
                            <li><a href="/dashboard/calls"><span><?php echo Text::get('dashboard-menu-calls'); ?></span></a></li>
                        <?php endif; ?>

                        <?php if ( isset($_SESSION['user']->roles['translator']) ||  isset($_SESSION['user']->roles['admin']) || isset($_SESSION['user']->roles['superadmin']) ) : ?>
                            <li><a href="/translate"><span><?php echo Text::get('regular-translate_board'); ?></span></a></li>
                        <?php endif; ?>

                        <?php if ( isset($_SESSION['user']->roles['checker']) ) : ?>
                            <li><a href="/review"><span><?php echo Text::get('regular-review_board'); ?></span></a></li>
                        <?php endif; ?>

                        <?php if ( isset($_SESSION['user']->roles['admin']) || isset($_SESSION['user']->roles['superadmin']) ) : ?>
                            <li><a href="/admin"><span><?php echo Text::get('regular-admin_board'); ?></span></a></li>
                        <?php endif; ?>

                        <?php if ( isset($_SESSION['user']->roles['manager']) ) : ?>
                            <li><a href="/manage"><span><?php echo Text::get('regular-manage_board'); ?></span></a></li>
                        <?php endif; ?>

                        <li class="logout"><a href="/user/logout"><span><?php echo Text::get('regular-logout'); ?></span></a></li>
                    </ul>
                </div>
            </li>
            <?php else: ?>
            <li class="login">
                <a href="<?php echo SEC_URL; ?>/user/login"><?php echo Text::get('regular-login'); ?></a>
            </li>

            <?php endif ?>
        </ul>
    </div>
