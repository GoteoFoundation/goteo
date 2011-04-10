<?php $bodyClass = 'home'; include 'view/prologue.html.php' ?>

        <?php include 'view/header.html.php' ?>

        <div id="main">
        <p><?php echo $this['message']; ?></p>

		<?php
		foreach ($this['projects'] as $project) {
			echo '<p><a href="/project/' . $project->id . '">' . $project->name . '</a> <a href="/invest/' . $project->id . '">[Apóyalo]</a><br />
                Obtenido: ' . $project->invested . ' &euro;<br />
                Mínimo: ' . $project->mincost . ' &euro;<br />
                Óptimo: ' . $project->maxcost . ' &euro;<br />
                Quedan: ' . $project->days . ' días<br />
                Cofinanciadores: ' . count($project->investors) . '</p>';
		}
		?>
        
        </div>        

        <?php include 'view/footer.html.php' ?>
    
<?php include 'view/epilogue.html.php' ?>