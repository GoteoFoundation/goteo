<?php
$location = $this->location;
$radius = ($this->with_radius || $this->radius) ? intval($this->radius) : null;
$vars = ['content' => ($location->city ? $location->city : $location->region) ."<br>{$location->country} {$location->country_code}"];
if($location) {
    $vars['longitude'] = $location->longitude;
    $vars['latitude'] = $location->latitude;
} elseif($location->city) {
    $vars['address'] = $location->city;
}
if(!is_null($radius)) {
    $vars['radius']= $radius;
}

?>
    <dt>
        <label for="location">Geolocalización:</label><br />
    </dt>
    <dd>
        <input type="text" id="location" class="geo-autocomplete" data-geocoder-populate-city="#save-city" data-geocoder-populate-region="#save-region" data-geocoder-populate-country_code="#save-country" data-geocoder-populate-latitude="#save-latitude" data-geocoder-populate-longitude="#save-longitude" name="location" value="<?= $location->city ? $location->city : $location->region ?>" style="width: 99%;"/>
    </dd>

<?php if(!is_null($radius)): ?>
    <dt>Radio influencia (Km):</dt>
    <dd><input type="text" id="radius" class="geo-autocomplete-radius" data-geocoder-populate-radius="#save-radius" value="<?= $radius ?>"></dd>
<?php endif ?>

    <dt>Mapa</dt>
    <dd><?= $this->insert('partials/utils/map_canvas', $vars) ?></dd>

    <input type="hidden" name="latitude" id="save-latitude" value="<?= $location->latitude ?>" />
    <input type="hidden" name="longitude" id="save-longitude" value="<?= $location->longitude ?>" />
    <input type="hidden" name="city" id="save-city" value="<?= $location->city ?>" />
    <input type="hidden" name="region" id="save-region" value="<?= $location->region ?>" />
    <input type="hidden" name="country" id="save-country" value="<?= $location->country ?>" />
    <input type="hidden" name="radius" id="save-radius" value="<?= $location->radius ?>" />

