<?php

use Goteo\Library\Text,
    Goteo\Model;

$user = $this['user'];

$locs = Model\Location::getAll();
foreach ($locs as $loc) {
    $locations[] = $loc->name;
}

?>
<form action="/dashboard/profile/location" method="post" enctype="multipart/form-data">

    <div class="widget">
        <input type="hidden" name="action" value="check" />

        <blockquote><?php echo Text::get('guide-dashboard-user-location') ?></blockquote>

        <div class="ui-widget">
            <label for="user_location"><?php echo Text::get('profile-field-location'); ?></label><br />
            <input type="text" id="user_location" name="location" value="<?php echo $user->location; ?>" style="width:300px;"/>
        </div>

        <input type="submit" name="test" value="Testear" />
    </div>
    
</form>
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
