<?php

if(empty($og_description)) $og_description = $meta_description;
if(empty($og_title)) $og_title = $title;
if(empty($og_url)) $og_url = $url;
if(empty($og_image)) $og_image = $image;

?>
    <meta name="description" content="<?=$this->e($meta_description)?>" />
    <meta name="keywords" content="<?=$this->e($meta_keywords)?>" />
    <meta name="author" content="<?=$this->e($meta_author)?>" />
    <meta name="copyright" content="<?=$this->e($meta_copyright)?>" />
    <meta name="robots" content="all" />
    <meta http-equiv="X-UA-Compatible" content="IE=Edge" />

    <meta property="og:title" content="<?=$this->e($og_title)?>" />
    <meta property="og:description" content="<?=$this->e($og_description)?>" />
    <meta property="og:type" content="activity" />
    <meta property="og:site_name" content="Goteo.org" />
    <meta property="og:url" content="<?=$this->e($og_url)?>" />
<?php
if (is_array($og_image)) :
    foreach ($og_image as $ogimg) :
?>
    <meta property="og:image" content="<?=$ogimg?>" />
<?php
   endforeach;
else :
?>
    <meta property="og:image" content="<?=$og_image?>" />
<?php
endif;
?>
