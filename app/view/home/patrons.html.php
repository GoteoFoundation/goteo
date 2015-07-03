<?php
use Goteo\Core\View,
    Goteo\Library\Text;

$patrons = $vars['patrons'];

if (count($patrons) > 4) :  ?>
<script type="text/javascript">
    $(function(){
        $('#slides_patrons').slides({
            container: 'slder_patrons',
            generatePagination: false,
            play: 0
        });
    });
</script>
<?php endif; ?>
<div class="widget patrons">

    <h2 class="title"><?php echo Text::get('home-patrons-header'); ?></h2>

    <div id="slides_patrons" class="patronrow">
        <?php if (count($patrons) > 4) : ?>
            <div class="arrow-left">
                <a class="prev">prev</a>
            </div>
        <?php endif ?>
        <div class="slder_patrons">
            <div class="row">
            <?php $c=1; foreach ($patrons as $patron) {
                echo View::get('user/widget/patron.html.php', array('user' => $patron));
                if ( ($c % 4) == 0 && $c != count($patrons)) { echo '</div><div class="row">'; }
            $c++; } ?>
            </div>
        </div>
        <?php if (count($patrons) > 4) : ?>
        <div class="arrow-right">
            <a class="next">next</a>
        </div>
        <?php endif ?>
    </div>

</div>
