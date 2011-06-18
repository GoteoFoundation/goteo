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
                        <li class="checking"><a href="/admin/checking">Revisión de proyectos</a></li>
                        <li class="accounting"><a href="/admin/accounting">Transacciones</a></li>
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
                        <li><a href="/admin/posts">Entradas en portada</a></li>
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
                        <!-- <li><a href="/admin/interests">Intereses de ususarios</a></li> -->
                        <li><a href="/admin/tags">Tags del blog</a></li>
                    </ul>
                </div>

                <div class="widget board collapse">
                    <h3 class="title">Plataforma Goteo</h3>
                    <ul>
                        <li><a href="/admin/blog">Blog</a></li>
                        <li><a href="/admin/managing">Usuarios</a></li>
                        <li><a href="/admin/rewards">Retornos colectivos</a></li>
<!--                    <li><a href="/admin/moderate">Moderar mensajes</a></li>  -->
                    </ul>
                </div>

            </div>

        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';
