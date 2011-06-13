<?php

use Goteo\Library\Text;

$bodyClass = 'blog';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="sub-header">
            <div>
                <h2>GOTEO BLOG</h2>
            </div>


        </div>

        <div id="main" class="threecols">

            <div class="side">
                <p>Ultimas entradas</p>
                <p>Tags</p>
                <p>Ultimos ocmentarios</p>
            </div>

            <div class="center">
                <p>Lista de posts</p>
                <p>Un post y sus comentarios</p>
                <p>Enviar un comentario</p>
            </div>

        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';
