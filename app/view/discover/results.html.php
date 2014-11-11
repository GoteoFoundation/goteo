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

            <div class="widget projects">
                <?php if (!empty($this['results'])) :
                    foreach ($this['results'] as $result) :
                        echo new View('view/project/widget/project.html.php', array(
                            'project' => $result
                        )); 
                    endforeach;
                else :
                    echo Text::get('discover-results-empty');
                endif; ?>
            </div>
        
        </div>        

        <?php include 'view/footer.html.php' ?>
    
<?php include 'view/epilogue.html.php' ?>