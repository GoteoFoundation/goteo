<?php
foreach($this->raw('value') as $foot) {
    echo $this->insert('admin/partials/objects/text', ['value' => '<img class="" src="' . $foot->getIcon()->getLink(32,32) . '" alt="' . $foot->name . '" title="' . $foot->name . '">', 'class' => 'avatar']);
    // echo $this->insert('admin/partials/objects/text', ['value' => $foot->name, 'class' => 'avatar'])."<br>\n";
}
?>
