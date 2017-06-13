<?php

$this->layout('admin/layout') ?>

<?php $this->section('admin-content') ?>

<div class="widget">

    <div class="ui-widget">
        <label for="change_user_input">Buscador usuario:</label><br />
        <?= $this->insert('admin/partials/typeahead_form', [
                                                            'id' => 'change_user_input',
                                                            // 'hidden' => true
                                                            ]) ?>
    </div>

    <div class="ui-widget">
        <label for="change_user_input">Buscador proyectos:</label><br />
        <?= $this->insert('admin/partials/typeahead_form', [
                                                            'id' => 'change_project_input',
                                                            // 'hidden' => true
                                                            ]) ?>
    </div>

    <form id="filter-form" action="/admin/accounts/add" method="post">
        <p>
            <label for="invest-amount">Importe:</label><br />
            <input type="text" id="invest-amount" name="amount" value="<?= $this->get_query('amount') ?>" />
        </p>

        <p>
            <label for="invest-anonymous">Aporte anónimo:</label><br />
            <input id="invest-anonymous" type="checkbox" name="anonymous" value="1">
        </p>


        <input type="hidden" id="user" name="user" value="" /> <br />
        <input type="hidden" id="project" name="project" value="" />
        <input type="submit" name="add" value="Generar aporte" />

    </form>
</div>

<?php $this->replace() ?>

<?php $this->section('footer') ?>
<script type="text/javascript">
// @license magnet:?xt=urn:btih:0b31508aeb0634b347b8270c7bee4d411b5d4109&dn=agpl-3.0.txt
$(function(){
    $.typeahead({
        input: "#change_user_input",
        // order: "asc",
        dynamic:true,
        hint:true,
        searchOnFocus:true,
        accent:true,
        emptyTemplate: 'No result for "{{query}}"',
        display: ["id", "name", "email"],
        template: '<span>' +
            '<span class="avatar">' +
                '<img src="/img/tinyc/{{avatar}}">' +
            '</span> ' +
            '<span class="name">{{name}}</span> ' +
            '<span class="info">({{id}}, {{email}})</span>' +
        '</span>',
        source: {
            list: {
                url: [{
                        url: "/api/users",
                        data: {
                            q: "{{query}}",
                        }
                    }, 'list']
            }
        },
        callback: {
            onClickAfter: function (node, a, item, event) {
                event.preventDefault();
                // node.val(item.id);
                $("#user").val(item.id);
            }
        },
        debug: true
    });


    $.typeahead({
        input: "#change_project_input",
        // order: "asc",
        dynamic:true,
        hint:true,
        searchOnFocus:true,
        accent:true,
        emptyTemplate: 'No result for "{{query}}"',
        display: ["id", "name", "owner"],
        template: '<span>' +
            '<span class="avatar">' +
                '<img src="/img/tinyc/{{image}}">' +
            '</span> ' +
            '<span class="name">{{name}}</span> ' +
            '<span class="info">({{id}}, {{owner}})</span>' +
        '</span>',
        source: {
            list: {
                url: [{
                        url: "/api/projects",
                        data: {
                            q: "{{query}}",
                        }
                    }, 'list']
            }
        },
        callback: {
            onClickAfter: function (node, a, item, event) {
                event.preventDefault();
                $("#project").val(item.id);
            }
        },
        debug: true
    });
});
// @license-end
</script>

<?php $this->append() ?>
