<?php
$sdgs = $this->project->getSdgs();
if (!empty($sdgs)):
    $sdgs = array_slice($sdgs, 0, 3)
    ?>
    <div class="col-md-4 col-sm-4 col-xs-12">
        <h4>Objetivos de desarrollo</h4>
        <article class="card-sdgs">
            <h3>El proyecto ayuda al cumplimiento de los siguientes ODS</h3>
            <div class="slider-sdgs">
                <?php foreach ($sdgs as $sdg): ?>
                    <div class="inner-sdgs">
                        <img src="<?= $this->asset("img/sdg/sdg{$sdg->id}.svg") ?>" alt="<?= $sdg->name ?>" width="90px">
                    </div>
                <?php endforeach; ?>
            </div>
        </article>
    </div>
<?php endif; ?>
