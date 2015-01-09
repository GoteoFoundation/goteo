<?php

use Goteo\Library\Text,
    Goteo\Core\View,
    Goteo\Model;

$user = $this['user'];

$locations = array();
foreach ($this['locations'] as $loc) {
    $locations[] = $loc->name;
}

// si geolocalizado
$geoloc = $user->geoloc;
$geolocation = !empty($geoloc) ? Model\Location::get($geoloc) : null;


// contenido
$content = explode('<hr />', $this['page']->content);

?>
<form action="/dashboard/profile/location" method="post" enctype="multipart/form-data">
    <div class="widget">

    <?php
    if ($user->unlocable) :
        echo $content[0];

    elseif ($geolocation instanceof Model\Location) :
        echo str_replace('<!-- MAPA -->',
                View::get('widget/map.html.php', array('lat'=>$geolocation->lat,'lon'=>$geolocation->lon, 'name'=>$geolocation->name)),
                $content[1]);

    else :
        echo $content[2];

    endif;
    ?>

    </div>
</form>
<?php if (empty($geoloc)) : ?>
<script type="text/javascript">
$(function () {

    var locations = ["<?php echo implode('", "', $locations); ?>"];

    /* Autocomplete para localidad */
    $( "#user_location" ).autocomplete({
      source: locations,
      minLength: 2
    });

});
</script>
<?php endif; ?>
