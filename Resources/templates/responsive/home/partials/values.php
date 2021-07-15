<div class="fluid-container data-container goteo-values">
    <div class="container">
        <div><h1 class="title text-center">Valores de Goteo</h1></div>
        <div class="text-center footprint-tabs">
            <ul>
                <?php foreach($this->footprints as $index => $footprint): ?>
                    <li>
                        <a href="" data-footprint="<?= $footprint->id ?>" class="<?= ($index == 0)? "active" : '' ?>" ></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php foreach($this->footprints as $index => $footprint): ?>
            <div class="row <?= ($index != 0)? "hidden" : '' ?>" id="goteo-values-<?= $footprint->id ?>">
                <div class="col footprint-briefing">
                    <img src="assets/img/footprint/<?= $footprint->id ?>.svg" heigh="70" width="70" alt="<?= $footprint->name ?>" class="footprint" />
                    <p><span class="footprint-label">Huella ecológica</span></p>
                    <h2>Cómo dejar una mejor huella en el planeta</h2>
                    <p>El impacto ambiental generado por la demanda humana hace de los recursos existentes en los ecosistemas del planeta se debiliten. Aquí apoyamos iniciativas de preservación de la capacidad ecológica de la Tierra de regenerar sus recursos</p>
                    <h3>Objetivos de desarrollo sostenible relacionados:</h3>
                    <p>Haz click para saber más:</p>
                    <ul>
                        <?php foreach($this->sdg_by_footprint[$footprint->id] as $sdg): ?>
                        <li><a href=""><img src="assets/img/ods/ods<?= $sdg->id ?>.svg" width="75" height="75" alt="<?= $sdg->name ?>"/></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="col footprint-info">
                    <div class="slider slider-footprint-data">

                        <div class="">
                            <img src="https://data.goteo.org/165x240c/cambio-climatico-1.jpg">
                            <h2>Datos huella ecológica</h2>
                            <h3><span>2,8</span> Planetas</h3>
                            <p>Serían necesarios si todo el mundo consumiera como un ciudadano medio de la UE.</p>
                        </div>
                        <div class="">
                            <img src="https://data.goteo.org/165x240c/captura-de-pantalla-2020-09-23-a-les-8.10.40.png">
                            <h2>Datos huella ecológica</h2>
                            <h3><span>2,8</span> Planetas</h3>
                            <p>Serían necesarios si todo el mundo consumiera como un ciudadano medio de la UE.</p>
                        </div>
                        <div class="">
                            <img src="https://data.goteo.org/165x240c/mrlr1gd-2.jpg">
                            <h2>Datos huella ecológica</h2>
                            <h3><span>2,8</span> Planetas</h3>
                            <p>Serían necesarios si todo el mundo consumiera como un ciudadano medio de la UE.</p>
                        </div>
                    </div>
                    <div class="slider slider-footprint-projects">
                        <?php foreach($this->projects_by_footprint[$footprint->id] as $index => $project): ?>
                            <div class="footprint-project">
                                <img src="<?= $project->image->getLink(600, 416, true); ?>" class="bg-project eco">
                                <div class="project-footprint">
                                    <img src="assets/img/footprint/<?= $footprint->id ?>.svg" height="70" width="70" alt="<?= $footprint->name ?>" class="footprint" />
                                </div>
                                <h2><a href="/project/<?= $project->id ?>"><?= $this->text_truncate($this->ee($project->name), 80); ?></a></h2>
                                <p><a href="/user/profile/<?= $this->project->user->id ?>"><?= $this->text('regular-by') . ' ' . $this->project->user->name ?></a></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="footprint-action">
                    <a href="">Ver proyectos de huella ecológica <span class="icon glyphicon glyphicon glyphicon-menu-right" aria-hidden="true"></span></a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>