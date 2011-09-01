<?php
use Goteo\Library\Text;

$bodyClass = 'community';

include 'view/prologue.html.php';
include 'view/header.html.php';
?>

    <div id="sub-header">
        <div>
            <h2><?php echo $this['name']; ?></h2>
        </div>
    </div>

    <div id="main">

        <div class="widget">
            <?php echo $this['content']; ?>
        </div>

        <div class="widget feed">
            <h3 class="title">Actividad reciente</h3>
            <p>
            Aqui la maquetación del feed:<br /><br />
                Marco de 3 columnas (supertitle, separador de elementos y scroll) + separador de columnas<br /><br />
                Maquetación de elementos<br /><br />
                Estilos generales para elementos<br /><br />
            </p>
        </div>

    </div>

<?php include 'view/footer.html.php' ?>
<?php include 'view/epilogue.html.php' ?>