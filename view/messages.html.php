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

            <ul id="messages">
		<?php
		foreach ($this['project']->messages as $message) {
                echo '<li class="thread">
                    <div class="message">
                   <img src="' . $message->user->avatar . '" class="avatar" />
                   <span class="user">' . $message->user->name . '</span>   <span class="when">' . $message->date . '</span><br />
                   <p>' . $message->message . '</p>
                   <quote>Para contestar a este poner ' . $message->id . ' en la casilla \'Hilo \'</quote>
                   </div>';

                if (!empty($message->responses)) {
                    echo '<ul>';
                    foreach ($message->responses as $child) {
                    echo '<li class="answer">
                       <div class="child">
                       <img src="' . $child->user->avatar . '" class="avatar" />
                       <span class="user">' . $child->user->name . '</span>   <span class="when">' . $child->date . '</span><br />
                       <p>' . $child->message . '</p>
                       </div>
                   </li>';
                    }
                    echo '</ul>';
                }

                echo '</li>';
		}
		?>
                </ul>
        
        </div>        

        <?php include 'view/footer.html.php' ?>
    
<?php include 'view/epilogue.html.php' ?>