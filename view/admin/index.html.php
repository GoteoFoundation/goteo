<?php

use Goteo\Library\Text;

$bodyClass = 'project-show';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div>
                <h2>Panel principal de administración</h2>
            </div>

            <div class="sub-menu">
                <div class="project-menu">
                    <ul>
                        <li class="home"><a href="/admin">Mainboard</a></li>
                        <li class="needs"><a href="/admin/checking">Revisión de proyectos</a></li>
                    </ul>
                </div>
            </div>

        </div>

        <div id="main" class="threecols">
            <div class="center">
                <div class="widget collapse">
                    <h3 class="title">Contenidos</h3>
                    <ul>
                        <li><a href="/admin/posts">Entradas</a></li>
                        <li><a href="/admin/promote">Proyectos destacados</a></li>
                        <li><a href="/admin/texts">Gestión de textos</a></li>
                        <li><a href="/admin/faq">Preguntas frecuentes</a></li>
                        <li><a href="/admin/pages">Páginas institucionales</a></li>
                    </ul>

                    <h3 class="title">Auxiliares</h3>
                    <ul>
                        <li><a href="/admin/icons">Tipos de Retorno/Recompensa</a></li>
                        <li><a href="/admin/licenses">Licencias</a></li>
                    </ul>

                    <h3 class="title">Plataforma Goteo</h3>
                    <p>Estos están pendientes de planificación o en desarrollo</p>
                    <ul>
                        <li><a href="/admin/managing">Usuarios y nodos</a></li>
                        <li><a href="/admin/rewards">Retornos y recompensas</a></li>
                        <li class="supporters"><a href="/admin/accounting">Transacciones</a></li>
                        <li class="messages"><a href="/admin/messages">Mensajes</a></li>
                        <li><a href="/admin/categories">Categorias</a></li>
                        <li><a href="/admin/interests">Intereses</a></li>
                    </ul>

                </div>
            </div>
        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';
