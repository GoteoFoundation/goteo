<?php
use Goteo\Library\Text,
    Goteo\Core\View,
    Goteo\Model\Image;
	
echo new View("view/bazar/prologue.html.php");
echo new View("view/bazar/header.html.php", $this);

echo new View("view/bazar/name.html.php", $this);
echo new View("view/bazar/form.html.php", $this;

// echo new View("view/bazar/proj.html.php");
// echo new View("view/bazar/slide.html.php");

echo new View("view/bazar/footer.html.php");
echo new View("view/bazar/epilogue.html.php");
?>