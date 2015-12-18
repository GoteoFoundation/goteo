<?php $this->layout('admin/projects/edit_layout') ?>

<?php $this->section('admin-project-content');
$project = $this->project;
$location = $this->location;
 ?>

    <p>Localización del proyecto</p>

    <form method="post" action="/admin/projects/location/<?= $project->id ?>" >

    <p>
        <label for="location">Ubicación para el proyecto <strong><?= $project->name ?></strong>:</label><br />
        <input type="text" id="location" class="geo-autocomplete" data-geocoder-populate-city="#save-location" data-geocoder-populate-region="#save-region" data-geocoder-populate-country_code="#save-country" data-geocoder-populate-latitude="#save-latitude" data-geocoder-populate-longitude="#save-longitude" name="location" id="location" value="<?= $project->location; ?>" style="width: 475px;"/>
        <input type="submit" name="save-location" value="Guardar" />
    </p>
    <?php
            $vars = ['content' => $project->name."<br>{$project->location}"];
            if($location) {
                $vars['longitude'] = $location->longitude;
                $vars['latitude'] = $location->latitude;
            } elseif($project->location) {
                $vars['address'] = $project->location;
            }

            echo $this->insert('partials/utils/map_canvas', $vars);

    ?>
        <input type="hidden" name="latitude" id="save-latitude" value="" />
        <input type="hidden" name="longitude" id="save-longitude" value="" />
        <input type="hidden" name="city" id="save-city" value="" />
        <input type="hidden" name="region" id="save-region" value="" />
        <input type="hidden" name="country" id="save-country" value="" />

    </form>


<?php $this->replace() ?>
