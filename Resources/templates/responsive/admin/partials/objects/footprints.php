<?php
foreach($this->raw('value') as $sdg) {
    // echo $this->insert('admin/partials/objects/text', ['value' => '<img class="" src="' . $sdg->getIcon()->getLink(32,32) . '" alt="' . $sdg->name . '" title="' . $sdg->name . '">', 'class' => 'avatar']);
    echo $this->insert('admin/partials/objects/text', ['value' => $sdg->name, 'class' => 'avatar'])."<br>\n";
}
?>
