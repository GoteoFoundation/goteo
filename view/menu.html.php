    <?php
    
    $langs = array(
        'es'    => 'Español',
        'ca'    => 'Català',
        'en'    => 'English'
    );
    
    ?>
    <div id="menu">
        <ul>
            <li class="home"><a href="/"><span>Inicio</span></a></li>
            <?php if (!empty($langs)): ?>
            <li class="language"><a href="#"><span>Idioma</a>
                <ul>
                    <?php foreach ($langs as $code => $name): ?>
                    <li>
                        <?php if ($code === $_SESSION['lang']): ?>
                        <strong><?php echo htmlspecialchars($name) ?></strong>
                        <?php else: ?>
                        <a href="/?lang=<?php echo $code ?>"><?php echo htmlspecialchars($name) ?></a>
                        <?php endif ?>
                    </li>
                    <?php endforeach ?>
                </ul>                
            </li>
            <?php endif ?>
            <li class="community"><a href="/community"><span>Comunidad</span></a></li>
            <li class="explore"><a href="/project/explore"><span>Descubre <br />proyectos</span></a></li>
            <li class="create"><a href="/project/?create"><span>Crea un <br />proyecto</span></a></li>
            <li class="blog"><a href="/blog"><span>Blog</a></li>
            <li class="faq"><a href="/faq"><span>FAQ</a></li>            
            <li class="search">
                <form method="get" action="/search">
                    <input type="text" name="query"  />
                    <input type="submit" value="Buscar" >
                </form>
            </li>
            <?php if (!empty($_SESSION['user'])): ?>            
            <li class="dashboard"><a href="/dashboard"><span>Mi Dashboard <em>(Jaume)</em></span></a>
                <div>
                    <ul>
                        <li><a href="/dashboard/activity"><span>Mi actividad</span></a></li>
                        <li><a href="/dashboard/profile"><span>Mi perfil</span></a></li>
                        <li><a href="/dashboard/projects"><span>Mis proyectos</span></a></li>
                        <li class="logout""><a href="/user/logout"><span>Cerrar sesión</span></a></li>
                    </ul>
                </div>
            </li>            
            <?php else: ?>            
            <li class="login"><a href="/user/login"><span>Accede</span></a>
                <div>
                    <form method="post" action="/user/login">
                        <div>
                            <label>Nombre de usuario: 
                            <input type="text" name="username" />
                        </div>
                        <div>
                            <label>Contraseña: 
                            <input type="password" name="password" />
                        </div>

                        <input type="submit" value="Acceder" />                        
                    </form>
                </div>
            </li>
            <?php endif ?>
        </ul>
    </div>