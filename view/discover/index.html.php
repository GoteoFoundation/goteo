<?php $bodyClass = 'home'; include 'view/prologue.html.php' ?>

        <?php include 'view/header.html.php' ?>

        <div id="main">
            
        <form method="get" action="/discover/results">
            <fieldset>
                <legend>Buscar</legend>
                <input type="text" name="query"  />
                <input type="submit" value="Buscar" >
            </fieldset>
        </form>

		<?php
		foreach ($this['types'] as $type=>$list) {
            if (empty($list))
                continue;

            echo '<h3>' . $this['title'][$type] . '</h3>
                <a href="/discover/view/' . $type . '">Ver todos</a>';
            foreach ($list as $project) {
                // la instancia del proyecto es $project
                // se pintan con el mismo widget que en result
                echo '<p>' . $project->name. '<br />';
                if ($project->status == 3 && $project->owner != $_SESSION['user']->id)
                    echo '<a href="/invest/' . $project->id . '">[Apóyalo]</a>';
                
                echo '<a href="/project/' . $project->id . '">[Ver proyecto]</a><br />
                    Obtenido: ' . $project->invested . ' &euro;<br />
                    Mínimo: ' . $project->mincost . ' &euro;<br />
                    Óptimo: ' . $project->maxcost . ' &euro;<br />
                    Quedan: ' . $project->days . ' días<br />
                    Cofinanciadores: ' . count($project->investors) . '<br />
               </p>';
            }
		}
		?>
        
        </div>        

        <?php include 'view/footer.html.php' ?>
    
<?php include 'view/epilogue.html.php' ?>