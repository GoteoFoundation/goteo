<?php

use Goteo\Library\Text,
    Goteo\Core\View,
    Goteo\Model;

$geolocation = $this['geolocation'];

?>
<form action="/dashboard/profile/location" method="post" enctype="multipart/form-data">
    <div class="widget">

<?php

    if ($geolocation instanceof Model\User\UserLocation) :
        //show button do not locate me
        echo '<h3>' . Text::get('dashboard-user-location-located-title') . '</h3>';
        echo '<p><a href="/dashboard/profile">' . Text::get('dashboard-user-location-change') . '</p></a>';

        //show map located
        echo View::get( 'widget/map.html.php',
                   array(
                    'latitude' => $geolocation->latitude,
                    'longitude' => $geolocation->longitude,
                    'name' => $geolocation->name
                  )
        );
?>
        <p><?php echo Text::get('dashboard-user-location-located-text') ?></p>
        <p><input name="unlocate" value="<?php echo htmlspecialchars(Text::get('dashboard-user-location-unlocate')); ?>" type="submit"></p>
<?php

    else :
        //show button locate me
?>
        <h3 style="color:red"><?php echo Text::get('dashboard-user-location-unlocated-title') ?></h3>
        <p><?php echo Text::get('dashboard-user-location-unlocated-text') ?></p>
        <p><input name="locable" value="<?php echo htmlspecialchars(Text::get('dashboard-user-location-locate')); ?>" type="submit"></p>

<?php
    endif;

?>

    </div>
</form>

