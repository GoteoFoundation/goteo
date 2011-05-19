<?php $bodyClass = 'home'; include 'view/prologue.html.php' ?>

        <?php include 'view/header.html.php' ?>

        <div id="main">
            <h2>Proyectos encontrados</h2>

            <p><?php echo $this['message']; ?></p>

            <?php if (isset($this['results'])) : ?>
                <?php foreach ($this['results'] as $result) : ?>
                    <?php echo '<p>
                        <a href="/project/' . $result->id . '" target="_blank">' . $result->name . '</a>';
                        if ($result->owner != $_SESSION['user']->id) {
                            echo '<a href="/project/' . $result->id . '/invest" target="_blank">[Apóyalo]</a>';
                        }
                        echo '<a href="/project/' . $result->id . '/messages" target="_blank">[Mensajes]</a>
                        <a href="/project/' . $result->id . '/supporters" target="_blank">[Cofinanciadores]</a><br />
                        Obtenido: ' . $result->invested . ' &euro;<br />
                        Mínimo: ' . $result->mincost . ' &euro;<br />
                        Óptimo: ' . $result->maxcost . ' &euro;<br />
                        Quedan: ' . $result->days . ' días<br />
                        Cofinanciadores: ' . count($result->project->investors) . '<br />
                   </p>'; ?>
                <?php endforeach; ?>
            <?php else : ?>
                <p>No se encontraron resultados para <?php echo $_GET['query']; ?></p>
            <?php endif; ?>
        
        </div>        

        <?php include 'view/footer.html.php' ?>
    
<?php include 'view/epilogue.html.php' ?>