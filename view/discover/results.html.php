<?php

use Goteo\Core\View,
    Goteo\Library\Text;

$bodyClass = 'discover';

include 'view/prologue.html.php';

include 'view/header.html.php' ?>

        <div id="sub-header">
            <div>
                <h2 class="title"><?php echo Text::get('discover-results-header'); ?></h2>
            </div>

        </div>

        <div id="main">
            <?php echo new View('view/discover/searcher.html.php',
                                array('params'     => $this['params'])); ?>

            <div class="widget projects promos">
                <?php if (!empty($this['results'])) : ?>
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
                    <?php echo Text::get('discover-results-empty'); ?>
                <?php endif; ?>
            </div>
        
        </div>        

        <?php include 'view/footer.html.php' ?>
    
<?php include 'view/epilogue.html.php' ?>