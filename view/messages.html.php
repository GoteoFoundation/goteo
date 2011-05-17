<?php $bodyClass = 'home'; include 'view/prologue.html.php' ?>

        <?php include 'view/header.html.php' ?>

        <div id="main">

            <h3>Mensajes en el proyecto <?php echo $this['project']->name; ?></h3>

            <p><?php echo $this['content']; ?></p>

            <form method="post" action="/message/<?php echo $this['project']->id; ?>">
                Escribe tu mensaje<br />
                <textarea name="message" cols="10" rows="5"></textarea>
                Hilo: <input type="text" name="thread" value="" /><br />
                <br />
                <input type="submit" value="Enviar" />
            </form>

        
        </div>        

        <?php include 'view/footer.html.php' ?>
    
<?php include 'view/epilogue.html.php' ?>