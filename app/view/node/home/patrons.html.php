<?php
use Goteo\Core\View,
    Goteo\Library\Text;

$patrons = $vars['patrons'];

if (count($patrons) > 3) :  ?>
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
<div id="node-patrons" class="content_widget rounded-corners">

    <h2><?php echo Text::get('home-patrons-header'); ?>
    <span class="line"></span>
    </h2>

    <div id="slides_patrons" class="patronrow">
        <?php if (count($patrons) > 3) : ?>
            <div class="arrow-left">
                <a class="prev">prev</a>
            </div>
        <?php endif ?>
        <div class="slder_patrons">
            <div class="row">
            <?php $c=1; foreach ($patrons as $patron) {
                echo View::Get('user/widget/patron.html.php', array('user' => $patron));
                if ( ($c % 3) == 0 && $c != count($patrons)) { echo '</div><div class="row">'; }
            $c++; } ?>
            </div>
        </div>
        <?php if (count($patrons) > 3) : ?>
        <div class="arrow-right">
            <a class="next">next</a>
        </div>
        <?php endif ?>
    </div>

</div>
