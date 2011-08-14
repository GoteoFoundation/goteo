<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div>
                <h2>Panel principal de administración</h2>
            </div>

            <div class="sub-menu">
                <div class="admin-menu">
                    <ul>
                        <li class="home"><a href="/admin">Mainboard</a></li>
                        <li class="checking"><a href="/admin/overview">Listado de proyectos</a></li>
                        <li class="accounting"><a href="/admin/accounting">Listado de aportes</a></li>
<!--                    <li class="comunication"><a href="/admin/comunication">Comunicación</a></li> -->
                    </ul>
                </div>
            </div>

        </div>

        <div id="main">

            <div class="center">

                <div class="widget board collapse">
                    <h3 class="title">Contenidos</h3>
                    <ul>
                        <li><a href="/admin/posts">Entradas de blog en portada o footer</a></li>
                        <li><a href="/admin/promote">Proyectos destacados</a></li>
                        <li><a href="/admin/texts">Gestión de textos</a></li>
                        <li><a href="/admin/faq">Preguntas frecuentes</a></li>
                        <li><a href="/admin/pages">Páginas institucionales</a></li>
                    </ul>
                </div>

                <div class="widget board collapse">
                    <h3 class="title">Auxiliares</h3>
                    <ul>
                        <li><a href="/admin/icons">Tipos de Retorno/Recompensa</a></li>
                        <li><a href="/admin/licenses">Licencias</a></li>
                        <li><a href="/admin/categories">Categorias</a></li>
                        <li><a href="/admin/tags">Tags del blog</a></li>
                        <li><a href="/admin/templates">Plantillas emails</a></li>
                    </ul>
                </div>

                <div class="widget board collapse">
                    <h3 class="title">Plataforma Goteo</h3>
                    <ul>
                        <li><a href="/admin/blog">Blog</a></li>
                        <li><a href="/admin/accounting">Aportes</a></li>
                        <li><a href="/admin/managing">Usuarios</a></li>
                        <li><a href="/admin/rewards">Retornos colectivos</a></li>
                        <li><a href="/admin/news">Noticias</a></li>
                        <li><a href="/admin/sponsors">Patrocinadores</a></li>
                        <li><a href="/admin/campaigns">Campañas</a></li>
<!--                    <li><a href="/admin/moderate">Moderar mensajes</a></li>  -->
                    </ul>
                </div>

                <div class="widget board collapse">
                    <h3 class="title">Evaluación de proyectos</h3>
                    <ul>
                        <li><a href="/admin/overview">Listado</a></li>
                        <li><a href="/admin/criteria">Criterios</a></li>
                        <li><a href="/admin/checking">Revisiones</a></li>
                    </ul>
                </div>

            </div>

        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';
