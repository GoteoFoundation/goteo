<?php

use Goteo\Core\View;

$bodyClass = 'home';

include 'view/prologue.html.php';

include 'view/header.html.php' ?>

        <div id="sub-header">
            <div>
                <h2 class="title">Resultado de búsqueda</h2>
            </div>

        </div>

        <div id="main">
            <?php echo new View('view/discover/searcher.html.php',
                                array('params'     => $this['params'])); ?>

            <p><?php echo $this['message']; ?></p>

            <div class="widget projects promos">
                <?php if (isset($this['results'])) : ?>
                    <?php foreach ($this['results'] as $result) : ?>
                    <div>
                    <?php
                        // la instancia del proyecto es $result
                        // se pintan con el mismo widget que en portada
                        echo new View('view/project/widget/project.html.php', array(
                            'project' => $result
                        )); 
                    ?>
                    </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p>No hemos encontrado ningún proyecto que cunmpla los criterios de búsqueda</p>
                <?php endif; ?>
            </div>
        
        </div>        

        <?php include 'view/footer.html.php' ?>
    
<?php include 'view/epilogue.html.php' ?>