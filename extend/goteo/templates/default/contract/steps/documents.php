<?php

$this->layout('contract/edit');

$contract = $this->contract;
$step = $this->step;
$errors = $this->errors;

$errors = $contract->errors[$step] ?: array();
$okeys  = $contract->okeys[$step] ?: array();

$docs = array();
foreach ($contract->docs as $doc) {

    // si es gestor o superadmin puede abrirlos
    $doc_html = $this->has_role(['manager', 'superadmin', 'root'])
    ? '<a href="/document/' . $doc->id . '/' . $doc->name . '" target="_blank">' . $doc->name . '</a>'
    : '<span style="margin-right: 10px;">' . $doc->name . '</span> <button type="submit" name="docs-'.$doc->id.'-remove" title="Quitar este documento" value="remove" class="image-remove" style="position:relative;"></button>';




    $docs[] = array(
        'type'  => 'html',
        'class' => 'inline',
        'html'  => $doc_html
    );

}

// campos de proyecto (descripción, objetivo, retornos)
// solo editables por gestor
// $descfields = (!isset($_SESSION['user']->roles['manager']) && !isset($_SESSION['user']->roles['superadmin']))
$descfields = !$this->has_role(['manager', 'superadmin', 'root'])
    ? array()
    : array(
            'type' => 'Group',
            'title'     => 'Proyecto',
            'children'  => array(
                'project_description' => array(
                    'type'      => 'TextArea',
                    'title'     => 'Descripción del proyecto',
                    'hint'      => $this->text('tooltip-contract-project_description'),
                    'required'  => true,
                    'errors'    => !empty($errors['project_description']) ? array($errors['project_description']) : array(),
                    'ok'        => !empty($okeys['project_description']) ? array($okeys['project_description']) : array(),
                    'value'     => $contract->project_description
                ),
                'project_invest' => array(
                    'type'      => 'TextArea',
                    'title'     => 'Objetivos de financiación',
                    'required'  => true,
                    'hint'      => $this->text('tooltip-contract-project_invest'),
                    'errors'    => !empty($errors['project_invest']) ? array($errors['project_invest']) : array(),
                    'ok'        => !empty($okeys['project_invest']) ? array($okeys['project_invest']) : array(),
                    'value'     => $contract->project_invest
                ),
                'project_return' => array(
                    'type'      => 'TextArea',
                    'title'     => 'Retornos comprometidos',
                    'hint'      => $this->text('tooltip-contract-project_return'),
                    'required'  => true,
                    'errors'    => !empty($errors['project_return']) ? array($errors['project_return']) : array(),
                    'ok'        => !empty($okeys['project_return']) ? array($okeys['project_return']) : array(),
                    'value'     => $contract->project_return
                )

            )
        );

$superform = array(
    'level'         => 3,
    'action'        => '',
    'method'        => 'post',
    'title'         => $this->text('contract-step-documents'),
    'hint'          => $this->text('guide-contract-documents'),
    'class'         => 'aqua',
    'elements'      => array(
        'process_documents' => array (
            'type' => 'Hidden',
            'value' => 'documents'
        ),

        'docs' => array(
            'title'     => 'Documentación',
            'type'      => 'Group',
            'required'  => true,
            'hint'      => $this->text('tooltip-contract-docs'),
            'errors'    => !empty($errors['docs']) ? array($errors['docs']) : array(),
            'ok'        => !empty($okeys['docs']) ? array($okeys['docs']) : array(),
            'class'     => 'doc',
            'children'  => array(
                'doc_upload'    => array(
                    'type'  => 'file',
                    'label' => 'Subir documento',
                    'class' => 'inline doc_upload',
                    'hint'  => $this->text('tooltip-contract-docs')
                )
            )
        ),
        'documents' => array(
            'type'  => 'Group',
            'title' => 'Documentos subidos',
            'class' => 'inline',
            'children'  => $docs
        ),

        'descfields' => $descfields,


        'footer' => array(
            'type'      => 'Group',
            'children'  => array(
                'errors' => array(
                    'title' => $this->text('form-footer-errors_title'),
                    'content'  => $this->insert('contract/partials/errors', array(
                        'contract'   => $contract,
                        'step'      => $step
                    ))
                ),
                'buttons'  => array(
                    'type'  => 'Group',
                    'children' => array(
                        'next' => array(
                            'type'  => 'Button',
                            'buttontype'  => 'submit',
                            'name'  => 'step',
                            'value'  => 'final',
                            'label' => $this->text('form-next-button'),
                            'class' => 'next'
                        )
                    )
                )
            )

        )

    )

);

foreach ($superform['elements'] as $id => &$element) {

    if (!empty($errors[$step][$id])) {
        $element['errors'] = array();
    }

}

$this->section('contract-edit-step');

echo \Goteo\Library\SuperForm::get($superform);

$this->replace();
