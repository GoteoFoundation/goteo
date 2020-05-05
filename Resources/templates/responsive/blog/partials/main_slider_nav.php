<nav>
    <ul class="list-inline navbar-right hidden-xs">
        <li>
            <a href="<?= $this->get_config('url.main') ?>" class="scroller">
                <?= $this->text('blog-nav-platform') ?>
            </a>
        </li>
        <li>
            <a href="/matchfunding" class="scroller">
                <?= $this->text('home-menu-matchfunding') ?>
            </a>
        </li>
        <li>
            <a href="/project/create" class="btn btn-fashion">
                <?= $this->text('regular-create') ?>
            </a>
        </li>
        <!--
        <li>
            <a href="#search" class="search">
                <img src="/assets/img/home/icono_lupa_white.png" >
            </a>
        </li>
        -->
    </ul>
</nav>