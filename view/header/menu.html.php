<?php
use Goteo\Core\ACL,
    Goteo\Library\Text;
?>
    <div id="menu">
        
        <h2><?php echo Text::get('regular-menu'); ?></h2>
        
        <ul>
            <li class="home"><a href="/"><?php echo Text::get('regular-home'); ?></a></li>
            <li class="explore"><a class="button red" href="/discover"><?php echo Text::get('regular-discover'); ?></a></li>
            <li class="create"><a class="button aqua" href="/project/create"><?php echo Text::get('regular-create'); ?></a></li>
            <li class="search">
                <form method="get" action="/discover/results">
                    <fieldset>
                        <legend><?php echo Text::get('regular-search'); ?></legend>
                        <input type="text" name="query"  />
                        <input type="submit" value="Buscar" >
                    </fieldset>
                </form>
            </li>
            <li class="community"><a href="/community"><span><?php echo Text::get('regular-community'); ?></span></a>
                <div>
                    <ul>                        
                    </ul>
                </div>
            </li>
            <?php if (!empty($_SESSION['user'])): ?>            
            <li class="dashboard"><a href="/dashboard"><span>Mi Dashboard <em><?php echo $_SESSION['user']->name; ?></em></span></a>
                <div>
                    <ul>
                        <li><a href="/dashboard/activity"><span>Mi actividad</span></a></li>
                        <li><a href="/dashboard/profile"><span>Mi perfil</span></a></li>
                        <li><a href="/dashboard/projects"><span>Mis proyectos</span></a></li>
                        <?php if (ACL::check('/translate')) : ?>
                        <li><a href="/translate"><span><?php echo Text::get('regular-translate_board'); ?></span></a></li>
                        <?php endif; ?>
                        <?php if (ACL::check('/review')) : ?>
                        <li><a href="/review"><span><?php echo Text::get('regular-review_board'); ?></span></a></li>
                        <?php endif; ?>
                        <?php if (ACL::check('/admin')) : ?>
                        <li><a href="/admin"><span><?php echo Text::get('regular-admin_board'); ?></span></a></li>
                        <?php endif; ?>
                        <li class="logout"><a href="/user/logout"><span><?php echo Text::get('regular-logout'); ?></span></a></li>
                    </ul>
                </div>
            </li>            
            <?php else: ?>            
            <li class="login">
                <a href="/user/login"><?php echo Text::get('regular-login'); ?></a>
            </li>
            
            <?php endif ?>
        </ul>
    </div>