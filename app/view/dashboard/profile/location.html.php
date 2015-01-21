<?php

use Goteo\Library\Text,
    Goteo\Core\View,
    Goteo\Model;

$user = $this['user'];

// si geolocalizado
$geolocation = $user->geoloc;

// contenido
$content = explode('<hr />', $this['page']->content);

?>
<form action="/dashboard/profile/location" method="post" enctype="multipart/form-data">
    <div class="widget">

<?php

    if ($user->unlocable) {
        echo $content[0];
    }
    elseif ($geolocation instanceof Model\User\UserLocation) {
        echo str_replace('<!-- MAPA -->',
                View::get('widget/map.html.php',
                         array(
                            'latitude' => $geolocation->locations[0]->latitude,
                            'longitude' => $geolocation->locations[0]->longitude,
                            'name' => $geolocation->locations[0]->name)
                        ),
                $content[1]);
    }
    else {
        echo $content[2];
    }

?>

    </div>
</form>
<?php if ($geolocation) : ?>
<script type="text/javascript">
$(function () {

    /* Autocomplete para localidad */
    $( "#user_location" ).autocomplete({

    });

});
</script>
<?php endif; ?>
