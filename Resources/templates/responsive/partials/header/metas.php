<?php


if(empty($this->og_description)) $this->og_description = $this->meta_description;
if(empty($this->og_title)) $this->og_title = $this->title;
if(empty($this->og_url)) $this->og_url = $this->url;

?>
    <meta name="description" content="<?=$this->meta_description?>">
    <meta name="keywords" content="<?=$this->meta_keywords?>">
    <meta name="author" content="<?=$this->meta_author?>">
    <meta name="copyright" content="<?=$this->meta_copyright?>">

    <meta property="og:title" content="<?= $this->og_title?>">
    <meta property="og:description" content="<?= $this->og_description?>">
    <meta property="og:type" content="activity">
    <meta property="og:site_name" content="Goteo.org">
    <meta property="og:url" content="<?= $this->og_url?>">

    <meta name="twitter:card" content="summary" />

<meta name="twitter:site" content="@goteofunding" />

<meta name="twitter:title" content="<?= $this->title ?>" />

<meta name="twitter:description" content="<?=$this->meta_description?>" />

<meta name="twitter:image" content="<?= $this->tw_image ?>">


<?php

if (is_array($this->og_image) && $this->og_image) :
    foreach ($this->og_image as $ogimg) :
?>
    <meta property="og:image" content="<?=$ogimg?>">
<?php
   endforeach;
else :
?>
    <meta property="og:image" content="<?=$this->og_image?>">

<?php endif ?>
