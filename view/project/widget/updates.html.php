<?php

use Goteo\Library\Text;

$project = $this['project'];

// segun lo que tengamos que mostrar :  lista o entrada
// uso la libreria blog para sacar los datos adecuados para esta vista

$level = (int) $this['level'] ?: 3;

?>
<div class="widget project-summary">
    
    <h<?php echo $level ?>><?php echo htmlspecialchars($project->name) ?></h<?php echo $level ?>>

    <!-- una entrada -->
    <?php if (!empty($post)): ?>
    <div class="post">
        <h<?php echo $level + 1?>><?php echo $post->title; ?></h<?php echo $level + 1?>>
        <?php echo $post->content; //quizas con una vista sea mejor ?>

        <p>Comentarios</p>

        <p>Escribe tu comentario</p>
    </div>
    <?php endif ?>

    <!-- Lista de entradas -->
    <?php if (!empty($list)): ?>
    <div class="posts">
        <?php foreach ($list as $post) : ?>
            <p><?php \trace($post); ?>Resumen de entrada, para ver comentarios, nuevo comentario, ir a la pagina de la entrada</p>
        <?php endforeach; ?>
    </div>
    <?php endif ?>
    
</div>