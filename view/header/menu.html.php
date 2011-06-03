    <div id="menu">
        
        <h2>Menú</h2>
        
        <ul>
            <li class="home"><a href="/">Inicio</a></li>                        
            <li class="explore"><a class="button red" href="/discover">Descubre proyectos</a></li>
            <li class="create"><a class="button aqua" href="/project/create">Crea un proyecto</a></li>
            <li class="search">
                <form method="get" action="/discover/results">
                    <fieldset>
                        <legend>Buscar</legend>
                        <input type="text" name="query"  />
                        <input type="submit" value="Buscar" >
                    </fieldset>
                </form>
            </li>
            <li class="community"><a href=""><span>Comunidad</span></a>
                <div>
                    <ul>                        
                    </ul>
                </div>
            </li>
            <?php if (!empty($_SESSION['user'])): ?>            
            <li class="dashboard"><a href="/dashboard"><span>Mi Dashboard <em>(Jaume)</em></span></a>
                <div>
                    <ul>
                        <li><a href="/dashboard/activity"><span>Mi actividad</span></a></li>
                        <li><a href="/dashboard/profile"><span>Mi perfil</span></a></li>
                        <li><a href="/dashboard/projects"><span>Mis proyectos</span></a></li>
                        <li class="logout"><a href="/user/logout"><span>Cerrar sesión</span></a></li>
                    </ul>
                </div>
            </li>            
            <?php else: ?>            
            <li class="login">
                
                <a href="/user/login">Accede</a>
                
                <form method="post" action="/user/login">
                    
                    <fieldset>                    
                        <div>
                            <label>Nombre de usuario: <br />
                            <input type="text" name="username" />
                        </div>

                        <div>
                            <label>Contraseña: <br />
                            <input type="password" name="password" />
                        </div>

                        <input type="submit" value="Acceder" />
                    
                    </fieldset>
                </form>                                                
                </div>
            </li>
            
            <?php endif ?>
        </ul>
    </div>