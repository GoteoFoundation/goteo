<?php
use Goteo\Core\View,
    Goteo\Model\Criteria;

echo View::get('review/reviews/selector.html.php', $vars);

$review   = $vars['review'];
$evaluation = $vars['evaluation'];

$sections = Criteria::sections();
$criteria = array();
foreach ($sections as $sectionId=>$sectionName) {
    $criteria[$sectionId] = Criteria::getAll($sectionId);
}

// si ya la ha dado por terminada no podrá tocar nada
$disabled = $review->ready == 1 ? 'disabled' : '';
?>
<?php foreach ($sections as $sectionId=>$sectionName) : ?>
<div class="widget">
    <h2 class="title"><?php echo $sectionName; ?></h2>
    <ul>
        <?php foreach ($criteria[$sectionId] as $crit) : ?>
        <li>
            <input class="evaluate-criteria" <?php echo $disabled; ?>
                   type="checkbox"
                   id="<?php echo $sectionId.'-criteria-'.$crit->id; ?>"
                   name="criteria-<?php echo $crit->id; ?>"
                   value="1"<?php if ($evaluation['criteria'][$crit->id] > 0) echo ' checked="checked"'; ?>/>

            <label for="<?php echo $sectionId.'-criteria-'.$crit->id; ?>"
                   title="<?php echo $crit->description; ?>"><?php echo $crit->title; ?></label>
        </li>
        <?php endforeach; ?>
    </ul>
    <p>
        <label for="<?php echo 'evaluate-' . $sectionId . '-evaluation'; ?>">Evaluación <?php echo strtolower($sectionName); ?>:</label><br />
        <textarea class="evaluate-comment" <?php echo $disabled; ?>
                  name="<?php echo $sectionId; ?>-evaluation"
                  id="<?php echo 'evaluate-' . $sectionId . '-evaluation'; ?>"
                  cols="100" rows="5"><?php echo $evaluation[$sectionId]['evaluation']; ?></textarea><br />
        <span id="<?php echo $sectionId; ?>-evaluation-result"></span>
    </p>
    <p>
        <label for="<?php echo 'evaluate-' . $sectionId . '-recommendation'; ?>">Que mejorarías <?php echo strtolower($sectionName); ?>:</label><br />
        <textarea class="evaluate-comment" <?php echo $disabled; ?>
                  name="<?php echo $sectionId; ?>-recommendation"
                  id="<?php echo 'evaluate-' . $sectionId . '-recommendation'; ?>"
                  cols="100" rows="5"><?php echo $evaluation[$sectionId]['recommendation']; ?></textarea><br />
        <span id="<?php echo $sectionId; ?>-recommendation-result"></span>
    </p>
</div>
<?php endforeach; ?>

<div class="widget">
    Puntuación final de tu revisión: <span id="total-score"><?php echo $evaluation['score'] . '/' . $evaluation['max']; ?></span>
</div>

<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt

jQuery(document).ready(function ($) {

    $('.evaluate-criteria').change(function () {
        success_text = $.ajax({async: false, type: "POST", data: ({campo: this.name, valor:this.checked}), url: '/ws/set_review_criteria/<?php echo $_SESSION['user']->id; ?>/<?php echo $review->id; ?>'}).responseText;
        $("#total-score").html(success_text);
    });

    $('.evaluate-comment').change(function () {
        success_text = $.ajax({async: false, type: "POST", data: ({campo: this.name, valor:this.value}), url: '/ws/set_review_comment/<?php echo $_SESSION['user']->id; ?>/<?php echo $review->id; ?>'}).responseText;
        $("#"+this.name+"-result").html(success_text);
    });

});
// @license-end
</script>
