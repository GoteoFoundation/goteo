<?php $bodyClass = 'home'; include 'view/prologue.html.php' ?>

        <?php include 'view/header.html.php' ?>

        <div id="main">
            
            <h3><?php echo $this['title']; ?></h3>

            <?php
            foreach ($this['list'] as $project) {
                echo '<p>' . $project->name . '<br />';
                if ($project->status == 3)
                    echo '<a href="/invest/' . $project->id . '">[Apóyalo]</a>';

                echo '<a href="/project/' . $project->id . '">[Ver proyecto]</a><br />
                    Obtenido: ' . $project->invested . ' &euro;<br />
                    Mínimo: ' . $project->mincost . ' &euro;<br />
                    Óptimo: ' . $project->maxcost . ' &euro;<br />
                    Quedan: ' . $project->days . ' días<br />
                    Cofinanciadores: ' . count($project->investors) . '<br />
               </p>';
            }
            ?>
        
        </div>        

        <?php include 'view/footer.html.php' ?>
    
<?php include 'view/epilogue.html.php' ?>