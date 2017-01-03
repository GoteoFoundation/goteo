<?php
use Goteo\Core\View,
    Goteo\Library\Text,
    Goteo\Library\SuperForm;

$project = $vars['project'];
$errors = $vars['errors'];

$costs = array();

if (!empty($project->costs)) {

    foreach ($project->costs as $cost) {

        $req_class = $cost->required ? 'required_cost-yes' : 'required_cost-no';

        $ch = array();

        if (!empty($vars["cost-{$cost->id}-edit"])) {

            $original = \Goteo\Model\Project\Cost::get($cost->id);

            $costs["cost-{$cost->id}"] = array(
                'type'      => 'group',
                'class'     => 'cost editcost '.$req_class,
                'children'  => array(
                    "cost-{$cost->id}-edit" => array(
                        'type'      => 'hidden',
                        'value'      => '1'
                    ),
                    "cost-{$cost->id}-cost-orig" => array(
                        'title'     => Text::get('costs-field-cost'),
                        'type'      => 'html',
                        'html'      => $original->cost
                    ),
                    "cost-{$cost->id}-cost" => array(
                        'title'     => '',
                        'type'      => 'textbox',
                        'size'      => 100,
                        'class'     => 'inline',
                        'value'     => $cost->cost,
                        'errors'    => array(),
                        'ok'        => array()
                    ),
                    "cost-{$cost->id}-description-orig" => array(
                        'type'      => 'html',
                        'title'     => Text::get('costs-field-description'),
                        'html'      => nl2br($original->description)
                    ),
                    "cost-{$cost->id}-description" => array(
                        'type'      => 'textarea',
                        'title'     => '',
                        'cols'      => 100,
                        'rows'      => 4,
                        'class'     => 'inline cost-description',
                        'hint'      => Text::get('tooltip-project-cost-description'),
                        'errors'    => array(),
                        'ok'        => array(),
                        'value'     => $cost->description
                    ),
                    "cost-{$cost->id}-buttons" => array(
                        'type' => 'group',
                        'class' => 'buttons',
                        'children' => array(
                            "cost-{$cost->id}-ok" => array(
                                'type'  => 'submit',
                                'label' => Text::get('form-accept-button'),
                                'class' => 'inline ok'
                            )
                        )
                    )
                )
            );

        } else {
            $costs["cost-{$cost->id}"] = array(
                'class'     => 'cost ' . $req_class,
                'view'      => 'dashboard/translates/costs/cost.html.php',
                'data'      => array('cost' => $cost),
            );

        }


    }
}

$sfid = 'sf-project-costs';
?>

<form method="post" action="/dashboard/translates/costs/save" class="project" enctype="multipart/form-data">

<?php echo SuperForm::get(array(

    'id'            => $sfid,

    'action'        => '',
    'level'         => 3,
    'method'        => 'post',
    'title'         => '',
    'hint'          => Text::get('guide-project-supports'),
    'class'         => 'aqua',
    /*
    'footer'        => array(
        'view-step-preview' => array(
            'type'  => 'submit',
            'name'  => 'save-costs',
            'label' => Text::get('regular-save'),
            'class' => 'next'
        )
    ),
    */
    'elements'      => array(
        'process_costs' => array (
            'type' => 'Hidden',
            'value' => 'costs'
        ),

        'costs' => array(
            'type'      => 'Group',
            'title'     => Text::get('costs-fields-main-title'),
            'hint'      => Text::get('tooltip-project-costs'),
            'errors'    => array(),
            'ok'        => array(),
            'children'  => $costs
        )
    )

));
?>
</form>
<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
$(function () {

    var costs = $('div#<?php echo $sfid ?> li.element#li-costs');

    costs.delegate('li.element.cost input.edit', 'click', function (event) {
        event.preventDefault();
        var data = {};
        data[this.name] = '1';
        costs.superform({data:data});
    });

    costs.delegate('li.element.editcost input.ok', 'click', function (event) {
        event.preventDefault();
        var data = {};
        data[this.name.substring(0, this.name.length-2) + 'edit'] = '0';
        costs.superform({data:data});
    });

});
// @license-end
</script>
