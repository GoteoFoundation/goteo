<?php

use Goteo\Core\View;

$bodyClass = 'home';

include 'view/prologue.html.php';

include 'view/header.html.php' ?>

        <div id="main">
            
        <form method="get" action="/discover/results">
            <fieldset>
                <legend>Buscar</legend>
                <input type="text" name="query"  />
                <input type="submit" value="Buscar" >
            </fieldset>
        </form>

		<?php foreach ($this['types'] as $type=>$list) :
            if (empty($list))
                continue;
            ?>
            <div class="widget projects promos">
                <h2 class="title"><?php echo $this['title'][$type]; ?></h2>
                <?php foreach ($list as $project) : ?>
                    <div>
                        <?php
                        // la instancia del proyecto es $project
                        // se pintan con el mismo widget que en la portada, sin balloon
                        echo new View('view/project/widget/project.html.php', array(
                            'project' => $project
                        )); ?>
                    </div>
                <?php endforeach; ?>
                <p>
                    <a href="/discover/view/<?php echo $type; ?>">Ver todos</a>
                </p>
            </div>

        <?php endforeach; ?>
        
        </div>        

        <?php include 'view/footer.html.php' ?>
    
<?php include 'view/epilogue.html.php' ?>