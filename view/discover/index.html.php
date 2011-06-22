<?php

use Goteo\Core\View,
    Goteo\Library\Text;

$bodyClass = 'discover';

include 'view/prologue.html.php';

include 'view/header.html.php' ?>

        <div id="sub-header">
            <div>
                <h2><?php echo Text::get('discover-header-supertitle'); ?></h2>
                <?php echo Text::get('discover-header-title'); ?>
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
                
                <div class="more"><a href="/discover/view/<?php echo $type; ?>"><?php echo Text::get('regular-see_more'); ?></a></div>
                
            </div>

        <?php endforeach; ?>
        
        </div>        

        <?php include 'view/footer.html.php' ?>
    
<?php include 'view/epilogue.html.php' ?>