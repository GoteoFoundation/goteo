<?php

$ob = $this->raw('ob');

// add avatar if exists in entry
if($avatar = $ob->getAvatar()) {
    echo $this->insert('admin/partials/objects/avatar', ['value' => $avatar]);
}

echo $this->insert('admin/partials/objects/text');
