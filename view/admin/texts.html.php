<?php

use Goteo\Library\Text;

$bodyClass = 'admin';

include 'view/prologue.html.php';

    include 'view/header.html.php'; ?>

        <div id="main">
            <h2>Gestión de textos</h2>

            <p><a href="/admin">Volver al Menú de administración</a></p>

            <?php if (!empty($this['errors'])) :
                echo '<p>';
                foreach ($this['errors'] as $error) : ?>
                    <span style="color:red;"><?php echo $error; ?></span><br />
            <?php endforeach;
                echo '</p>';
                endif;?>


            <?php if(isset($this['text'])) : ?>
                <p><a href="/admin/texts">Volver a la lista de textos</a></p>
                <p>Traduciendo a <?php echo $this['using']->name; ?></p>
				<form action="/admin/translate/<?php echo $this['text']->id; ?>/<?php $this['using']->lang; ?><?php if (isset($_GET['filter'])) echo '?filter=' . $_GET['filter']; ?>" method="post">
					<dl>
						<dt><label for="newtext"><?php echo $this['text']->purpose; ?></label></dt>
						<dd><textarea id="newtext" name="newtext" cols="100" rows="6"><?php echo $this['text']->text; ?></textarea></dd>
					</dl>
					<input type="submit" name="translate" value="Aplicar" />
				</form>
            <?php endif; ?>

            <?php if(isset($this['texts'])) : ?>
                <p>Viendo textos en <?php echo $this['using']->name; ?></p>

                <form id="textfilter-form" action="/admin/texts/" method="get">
                    <label for="textid-filter">Filtrar los textos de:</label>
                    <select id="textid-filter" name="filter" onchange="document.getElementById('textfilter-form').submit();">
                        <option value="">Todos los textos</option>
                    <?php foreach ($this['filters'] as $filterId=>$filterName) : ?>
                        <option value="<?php echo $filterId; ?>"<?php if ($_GET['filter'] == $filterId) echo ' selected="selected"';?>><?php echo $filterName; ?></option>
                    <?php endforeach; ?>
                    </select>
                </form>
                
                <ul>
                <?php foreach ($this['texts'] as $text) : ?>
                    <li>
                        <label title="<?php echo $text->purpose; ?>"><?php echo $text->id; ?>:</label>
                        <a title="<?php echo $text->purpose; ?>" href='/admin/translate/<?php echo $text->id; ?>?filter=<?php echo $_GET['filter']; ?>'>[Cambiar]</a>
                        <p><?php echo $text->text; ?></p>
                    </li>
                <?php endforeach; ?>
                </ul>
            <?php endif; ?>

        </div>

<?php
    include 'view/footer.html.php';
include 'view/epilogue.html.php';