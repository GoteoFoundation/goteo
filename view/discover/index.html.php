<?php

use Goteo\Core\View,
    Goteo\Model\Project\Category,
    Goteo\Model\Project\Reward,
    Goteo\Library\Location;

$bodyClass = 'home';

$categories = Category::getAll();
$locations = Location::getList();
$rewards = Reward::icons('individual');

include 'view/prologue.html.php';

include 'view/header.html.php' ?>

        <div id="sub-header">
            <div>
                <h2>Por categoria, lugar o retorno,</h2>
                <span type="color:red;">encuentra el proyecto</span> con el que más te identificas
            </div>

        </div>

        <div id="main">
            
        <form method="get" action="/discover/results">
            <fieldset>
                <legend>Buscar</legend>
                <label>Por texto: <input type="text" name="query"  /></label>
                <br />
                <label>Por categoria:
                    <select name="category">
                        <option value="">Todas las categorias</option>
                    <?php foreach ($categories as $id=>$name) : ?>
                        <option value="<?php echo $id; ?>"><?php echo $name; ?></option>
                    <?php endforeach; ?>
                    </select>
                </label>
                <br />
                <label>Por lugar:
                    <select name="location">
                        <option value="">Todos los lugares</option>
                    <?php foreach ($locations as $id=>$name) : ?>
                        <option value="<?php echo $id; ?>"><?php echo $name; ?></option>
                    <?php endforeach; ?>
                    </select>
                </label>
                <br />
                <label>Por retorno:
                    <select name="reward">
                        <option value="">Todos los tipos</option>
                    <?php foreach ($rewards as $id=>$reward) : ?>
                        <option value="<?php echo $id; ?>"><?php echo $reward->name; ?></option>
                    <?php endforeach; ?>
                    </select>
                </label>
                <br />
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