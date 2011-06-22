<?php

use Goteo\Core\View;

$bodyClass = 'home';

include 'view/prologue.html.php';

include 'view/header.html.php' ?>

        <div id="sub-header">
            <div>
                <h2>Por categoria, lugar o retorno,</h2>
                <span type="color:red;">encuentra el proyecto</span> con el que más te identificas
            </div>

        </div>

        <div id="main">
            <?php echo new View('view/discover/searcher.html.php',
                                array(
                                    'categories' => $categories,
                                    'locations'  => $locations,
                                    'rewards'    => $rewards
                                )
                ); ?>

		<?php foreach ($this['types'] as $type=>$list) :
            if (empty($list))
                continue;
            ?>
            <div class="widget projects promos">
                <h2 class="title"><?php echo $this['title'][$type]; ?></h2>
                <?php foreach ($list as $project) : ?>
                    <?php
                    // la instancia del proyecto es $project
                    // se pintan con el mismo widget que en la portada, sin balloon
                    echo new View('view/project/widget/project.html.php', array(
                        'project' => $project
                    )); ?>
                <?php endforeach; ?>
                
                <div class="more"><a href="/discover/view/<?php echo $type; ?>">Ver más</a></div>
                
            </div>

        <?php endforeach; ?>
        
        </div>        

        <?php include 'view/footer.html.php' ?>
    
<?php include 'view/epilogue.html.php' ?>