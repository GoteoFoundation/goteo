<?php
use Goteo\Core\NodeSys;

$nodes = NodeSys::activeNodes();

$url_project_create = $this->url_project_create ? $this->url_project_create : '/project/create';
?>
    <div id="menu">

        <h2><?= $this->text('regular-menu') ?></h2>

        <ul>
            <li class="home"><a class="node-jump" href="<?= $this->get_config('url.main') ?>"><?= $this->text('regular-home') ?></a></li>
            <li class="explore"><a class="button red" href="/discover"><?= $this->text('regular-discover') ?></a></li>
            <li class="create"><a class="button aqua" href="<?= $url_project_create ?>"><?= $this->text('regular-create') ?></a></li>
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
                        <li><a href="/community"><span><?= $this->text('community-menu-activity') ?></span></a></li>
<?php /* quitamos Compartiendo por ahora
                        <li><a href="/community/sharemates"><span><?= $this->text('community-menu-sharemates') ?></span></a></li>
 */ ?>
                        <!-- nodos activos -->
                        <?php  foreach ($nodes as $node) : ?>
                        <li><a class="node-jump" href="<?php echo $node->url ?>"><?php echo $node->name ?></a></li>
                        <?php  endforeach; ?>
                    </ul>
                </div>
            </li>

            <?php if ($this->is_logged()): ?>
            <li class="dashboard"><a href="/dashboard"><span><?= $this->text('dashboard-menu-main') ?></span><img src="<?php echo $this->get_user()->avatar->getLink(28, 28, true); ?>" /></a>
                <div>
                    <ul>
                        <li><a href="/dashboard/profile"><span><?= $this->text('dashboard-menu-profile') ?></span></a></li>
                        <li><a href="/dashboard/activity"><span><?= $this->text('dashboard-menu-activity') ?></span></a></li>
                        <li><a href="/dashboard/wallet"><span><?= $this->text('dashboard-menu-pool') ?></span></a></li>
                        <li><a href="/dashboard/projects"><span><?= $this->text('dashboard-menu-projects') ?></span></a></li>
                        <li><a href="/dashboard/profile/preferences"><span><?= $this->text('dashboard-menu-profile-preferences'); ?></span></a></li>

                        <?php if ( isset($this->get_user()->roles['translator']) ||  isset($this->get_user()->roles['admin']) || isset($this->get_user()->roles['superadmin']) ) : ?>
                            <li><a href="/translate"><span><?= $this->text('regular-translate_board') ?></span></a></li>
                        <?php endif; ?>

                        <?php if ( isset($this->get_user()->roles['checker']) ) : ?>
                            <li><a href="/review"><span><?= $this->text('regular-review_board') ?></span></a></li>
                        <?php endif; ?>

                        <?php if ( $this->is_admin() ): ?>
                            <li><a href="/admin"><span><?= $this->text('regular-admin_board') ?></span></a></li>
                        <?php endif; ?>

                        <li class="logout"><a href="/user/logout"><span><?= $this->text('regular-logout') ?></span></a></li>
                    </ul>
                </div>
            </li>
            <?php else: ?>
            <li class="login">
                <a href="/user/login"><?= $this->text('regular-login') ?></a>
            </li>

            <?php endif ?>
        </ul>
    </div>
