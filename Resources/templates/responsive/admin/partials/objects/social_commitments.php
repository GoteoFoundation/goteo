<?php
foreach($this->raw('value') as $sc) {
    echo $this->insert('admin/partials/objects/text', ['value' => '<img class="" src="' . $sc->getIcon()->getLink(32,32) . '" alt="' . $sc->name . '" title="' . $sc->name . '">', 'class' => 'avatar']);
}
?>
