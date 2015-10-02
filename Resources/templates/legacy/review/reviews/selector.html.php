<div class="widget reviews">
    <div id="review-selector">
        <form id="selector-form" name="selector_form" action="<?php echo '/review/'.$vars['section'].'/'.$vars['option'].'/select'; ?>" method="post">
        <label for="selector">Revisión:</label>
        <select id="selector" name="review" onchange="document.getElementById('selector-form').submit();">
        <?php foreach ($vars['reviews'] as $review) : ?>
            <option value="<?php echo $review->id; ?>"<?php if ($review->id == $_SESSION['review']->id) echo ' selected="selected"'; ?>><?php echo $review->name; ?></option>
        <?php endforeach; ?>
        </select>
        <!-- un boton para seleccionar si no tiene javascript -->
        </form>
    </div>
</div>
