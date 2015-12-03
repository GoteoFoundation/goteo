<?php

use Goteo\Library\Text,
    Goteo\Library\SuperForm,
    Goteo\Core\View;

$project = $vars['project'];
$errors = $project->errors[$vars['step']] ?: array();
$okeys  = $project->okeys[$vars['step']] ?: array();

$costs = array();

if (!empty($project->costs)) {

    foreach ($project->costs as $cost) {

        $req_class = $cost->required ? 'required_cost-yes' : 'required_cost-no';

        $ch = array();

        if (!empty($vars["cost-{$cost->id}-edit"])) {

            $costTypes = array();

            foreach ($vars['types'] as $id => $type) {
                $costTypes["cost-{$cost->id}-type-{$id}"] = array(
                    'name'  => "cost-{$cost->id}-type",
                    'value' => $id,
                    'type'  => 'radio',
                    'class' => "cost-type $id",
                    'label' => $type,
                    'hint'  => Text::get('tooltip-project-cost-type-'.$id),
                    'checked' => $id == $cost->type  ? true : false
                );
            }

            $costs["cost-{$cost->id}"] = array(
                'type'      => 'group',
                'class'     => 'cost editcost '.$req_class,
                'children'  => array(
                    "cost-{$cost->id}-edit" => array(
                        'type'  => 'hidden',
                        'value' => '1'
                    ),
                    "cost-{$cost->id}-cost" => array(
                        'title'     => Text::get('costs-field-cost'),
                        'required'  => true,
                        'type'      => 'textbox',
                        'size'      => 100,
                        'class'     => 'inline',
                        'value'     => $cost->cost,
                        'errors'    => !empty($errors["cost-{$cost->id}-cost"]) ? array($errors["cost-{$cost->id}-cost"]) : array(),
                        'ok'        => !empty($okeys["cost-{$cost->id}-cost"]) ? array($okeys["cost-{$cost->id}-cost"]) : array(),
                        'hint'      => Text::get('tooltip-project-cost-cost'),
                    ),
                    "cost-{$cost->id}-type" => array(
                        'title'     => Text::get('costs-field-type'),
                        'required'  => true,
                        'class'     => 'inline',
                        'type'      => 'group',
                        'children'  => $costTypes,
                        'value'     => $cost->type,
                        'errors'    => !empty($errors["cost-{$cost->id}-type"]) ? array($errors["cost-{$cost->id}-type"]) : array(),
                        'ok'        => !empty($okeys["cost-{$cost->id}-type"]) ? array($okeys["cost-{$cost->id}-type"]) : array(),
                        'hint'      => Text::get('tooltip-project-cost-type'),
                    ),
                    "cost-{$cost->id}-description" => array(
                        'type'      => 'textarea',
                        'required'  => true,
                        'title'     => Text::get('costs-field-description'),
                        'cols'      => 100,
                        'rows'      => 4,
                        'class'     => 'inline cost-description',
                        'hint'      => Text::get('tooltip-project-cost-description'),
                        'errors'    => !empty($errors["cost-{$cost->id}-description"]) ? array($errors["cost-{$cost->id}-description"]) : array(),
                        'ok'        => !empty($okeys["cost-{$cost->id}-description"]) ? array($okeys["cost-{$cost->id}-description"]) : array(),
                        'value'     => $cost->description
                    ),
                    "cost-{$cost->id}-amount" => array(
                        'type'      => 'textbox',
                        'required'  => true,
                        'title'     => Text::get('costs-field-amount'),
                        'size'      => 8,
                        'class'     => 'inline cost-amount',
                        'hint'      => Text::get('tooltip-project-cost-amount'),
                        'errors'    => !empty($errors["cost-{$cost->id}-amount"]) ? array($errors["cost-{$cost->id}-amount"]) : array(),
                        'ok'        => !empty($okeys["cost-{$cost->id}-amount"]) ? array($okeys["cost-{$cost->id}-amount"]) : array(),
                        'value'     => $cost->amount_original,
                        'symbol'     => $cost->currency_html
                    ),
                    "cost-{$cost->id}-required"  => array(
                        'required'  => true,
/*                        'title'     => Text::get('costs-field-required_cost'),  */
                        'class'     => 'inline cost-required cols_2',
                        'type'      => 'radios',
                        'options'   => array (
                            array(
                                    'value'     => '1',
                                    'class'     => 'required_cost-yes',
                                    'label'     => Text::get('costs-field-required_cost-yes')
                                ),
                            array(
                                    'value'     => '0',
                                    'class'     => 'required_cost-no',
                                    'label'     => Text::get('costs-field-required_cost-no')
                                )
                        ),
                        'value'     => $cost->required,
                        'errors'    => !empty($errors["cost-{$cost->id}-required"]) ? array($errors["cost-{$cost->id}-required"]) : array(),
                        'ok'        => !empty($okeys["cost-{$cost->id}-required"]) ? array($okeys["cost-{$cost->id}-required"]) : array(),
                        'hint'      => Text::get('tooltip-project-cost-required'),
                    ),
                    /*"cost-{$cost->id}-dates" => array(
                        'type'      => 'group',
                        'required'  => $cost->type == 'task' ? true : false,
                        'title'     => Text::get('costs-field-dates'),
                        'class'     => 'inline cost-dates',
                        'errors'    => !empty($errors["cost-{$cost->id}-dates"]) ? array($errors["cost-{$cost->id}-dates"]) : array(),
                        'ok'        => !empty($okeys["cost-{$cost->id}-dates"]) ? array($okeys["cost-{$cost->id}-dates"]) : array(),
                        'hint'      => Text::get('tooltip-project-cost-dates'),
                        'children'  => array(
                            "cost-{$cost->id}-from"  => array(
                                'class'     => 'inline cost-from',
                                'type'      => 'datebox',
                                'size'      => 8,
                                'title'     => Text::get('costs-field-date_from'),
                                'value'     => $cost->from
                            ),
                            "cost-{$cost->id}-until"  => array(
                                'class'     => 'inline cost-until',
                                'type'      => 'datebox',
                                'size'      => 8,
                                'title'     => Text::get('costs-field-date_until'),
                                'value'     => $cost->until
                            )
                        )
                    ),*/
                    "cost-{$cost->id}-buttons" => array(
                        'type' => 'group',
                        'class' => 'buttons',
                        'children' => array(
                            "cost-{$cost->id}-ok" => array(
                                'type'  => 'submit',
                                'label' => Text::get('form-accept-button'),
                                'class' => 'inline ok'
                            ),
                            "cost-{$cost->id}-remove" => array(
                                'type'  => 'submit',
                                'label' => Text::get('form-remove-button'),
                                'class' => 'inline remove weak'
                            )
                        )
                    )
                )
            );

        } else {
            $costs["cost-{$cost->id}"] = array(
                'class'     => 'cost ' . $req_class,
                'view'      => 'project/edit/costs/cost.html.php',
                'data'      => array('cost' => $cost),
            );

        }


    }
}

$sfid = 'sf-project-costs';

// en función de si es pre-form o form

if ($project->draft) {
    $help_cost=array(
            'type'      => 'checkbox',
            'class'     => 'cols_1',
            'required'  => false,
            'name'      => 'help_cost',
            'label'     => Text::get('project-help-cost'),
            'hint'      => Text::get('tooltip-project-help-cost'),
            'errors'    => array(),
            'ok'        => array(),
            'checked'   => (bool) $project->help_cost,
            'value'     => 1
        );
}
else
    $help_cost= array(
        'type'  => 'hidden',
        'class' => 'inline',
        'value'     => $project->help_cost
    );

echo SuperForm::get(array(

    'id'            => $sfid,

    'action'        => '',
    'level'         => $vars['level'],
    'method'        => 'post',
    'title'         => Text::get('costs-main-header'),
    'hint'          => Text::get('guide-project-costs'),
    'class'         => 'aqua',

    'elements'      => array(
        'process_costs' => array (
            'type' => 'hidden',
            'value' => 'costs'
        ),

        'anchor-costs' => array(
            'type' => 'html',
            'html' => '<a name="costs"></a>'
        ),

        'costs' => array(
            'type'      => 'group',
            'required'  => true,
            'title'     => Text::get('costs-fields-main-title'),
            'hint'      => Text::get('tooltip-project-costs'),
            'errors'    => !empty($errors["costs"]) ? array($errors["costs"]) : array(),
            'ok'        => !empty($okeys["costs"]) ? array($okeys["costs"]) : array(),
            'children'  => $costs  + array(
                'cost-add' => array(
                    'type'  => 'submit',
                    'label' => Text::get('form-add-button'),
                    'class' => 'add red',
                )
            )
        ),

        'help_cost' => $help_cost,

        'cost-meter' => array(
            'title'     => Text::get('costs-fields-metter-title'),
            'required'  => true,
            'class'     => 'cost-meter',
            'errors'    => !empty($errors["total-costs"]) ? array($errors["total-costs"]) : array(),
            'ok'        => !empty($okeys["total-costs"]) ? array($okeys["total-costs"]) : array(),
            'view'      => new View('project/edit/costs/meter.html.php', array(
                'project'   => $project
            )),
            'hint'      => Text::get('tooltip-project-totals')
        ),

         "one_round"  => array(
                        'required'  => true,
                        'title'     => Text::get('costs-field-select-rounds'),
                        'class'     => 'inline cost-required cols_2',
                        'type'      => 'radios',
                        'options'   => array (
                            array(
                                    'value'     => '1',
                                    'class'     => 'required_cost-yes',
                                    'label'     => Text::get('project-one-round')
                                ),
                            array(
                                    'value'     => '0',
                                    'class'     => 'required_cost-no',
                                    'label'     => 'Dos rondas',
                                    'label'     => Text::get('project-two-rounds')
                                )
                        ),
                        'value'     => empty($project->one_round) ? '0' : $project->one_round,
                        'errors'    => array(),
                        'ok'        => array(),
                        'hint'      => Text::get('tooltip-project-rounds')
        ),


        //  aligerando
        // 'resource' => array(
        //     'type'      => 'textarea',
        //     'cols'      => 40,
        //     'rows'      => 4,
        //     'title'     => Text::get('costs-field-resoure'),
        //     'hint'      => Text::get('tooltip-project-resource'),
        //     'errors'    => !empty($errors["resource"]) ? array($errors["resource"]) : array(),
        //     'ok'        => !empty($okeys["resource"]) ? array($okeys["resource"]) : array(),
        //     'value'     => $project->resource
        // ),


        /*'schedule' => array(
            'type'      => 'html',
            'class'     => 'schedule',
            'hint'      => Text::get('tooltip-project-schedule'),
            'html'      => View::get('project/widget/schedule.html.php', array('project' => $project))
        ),*/

        'footer' => array(
            'type'      => 'group',
            'children'  => array(
                'errors' => array(
                    'title' => Text::get('form-footer-errors_title'),
                    'view'  => new View('project/edit/errors.html.php', array(
                        'project'   => $project,
                        'step'      => $vars['step']
                    ))
                ),
                'buttons'  => array(
                    'type'  => 'group',
                    'children' => array(
                        'next' => array(
                            'type'  => 'submit',
                            'name'  => 'view-step-'.$vars['next'],
                            'label' => Text::get('form-next-button'),
                            'class' => 'next'
                        )
                    )
                )
            )

        )

    )

));


?>
<script type="text/javascript">
$(function () {

    var costs = $('div#<?php echo $sfid ?> li.element#li-costs');

    //abrir editor
    costs.delegate('li.element.cost input.edit', 'click', function (event) {
        event.preventDefault();
        var data = {};
        data[this.name] = '1';
        costs.superform({
            data : data
        });
    });

    //guardar datos
    costs.delegate('li.element.cost input.ok', 'click', function (event) {
        event.preventDefault();
        var data = {};
        data[this.name.substring(0, this.name.length-2) + 'edit'] = '0';
        costs.superform({
            data : data
        });
    });

    costs.delegate('li.element.cost input.remove', 'click', function (event) {
        event.preventDefault();
        var data = {};
        data[this.name] = '1';
        costs.superform({
            data : data
        });
    });

    costs.delegate('#li-cost-add input', 'click', function (event) {
        // hack para superar un error insondable del superform
        event.preventDefault();
        var data = {};
        data[this.name] = '1';
        //envia los datos y actualiza el contenido con la respuesta
        costs.superform({
            data : data
        });
    });

    costs.bind('superform.ajax.done', function (event, html, new_el) {
        //Como html es un string, solo actualiza contenido, no reenvia los datos
        $('li#li-cost-meter').superform(html);
        // $('li#schedule').superform(html)
    });

});
</script>
