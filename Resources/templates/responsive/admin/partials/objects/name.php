<?php

// add avatar if exists in entry
if(isset($this->entry['avatar'])) {
 echo $this->insert('admin/partials/objects/avatar', ['value' => $this->entry['avatar']]);
}

echo $this->insert('admin/partials/objects/text');
