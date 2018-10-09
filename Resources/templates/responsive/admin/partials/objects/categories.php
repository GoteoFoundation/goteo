<?php
foreach($this->raw('value') as $c) {
    echo $this->insert('admin/partials/objects/text', ['value' => $c->name, 'class' => 'avatar'])."<br>\n";
}
