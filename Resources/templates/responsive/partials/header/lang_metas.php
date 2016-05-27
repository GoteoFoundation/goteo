<?php foreach($this->lang_list() as $l => $lang):
        if($l == $this->lang_current()) continue;
 ?>
    <link rel="alternate" href="<?= $this->lang_url($l) ?>" hreflang="<?= $l ?>" />
<?php endforeach ?>
