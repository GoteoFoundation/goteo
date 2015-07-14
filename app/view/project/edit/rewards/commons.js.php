<?php
/* Esta vista es para el ajax de gestión de retornos colectivos
 * Desde /admin/commons y desde /dashboard/projects/commons
 * Necesita haber usado la vista view/project/edit/rewards/view_commons.html.php
 */
?>
<script type="text/javascript">

    // Versión mejorada de:
    // https://stackoverflow.com/questions/5717093/check-if-a-javascript-string-is-an-url
    // Test with  http://subdomain.example.com:8080/asdf-1234?par=var&par2=var2#asdf342
    // Literal hyphens in character class must be escaped
    function ValidURL(str) {
        var pattern = new RegExp('^(https?:\/\/)?'+ // protocol
            '((\\w([\\w-]*[\\w])*)\.)+[a-z]{2,}'+ // domain name
            '(:\\d+)?(\/[\\w\\-%_.~+]*)*'+ // port and path
            '(\\?[;&\\w%_.~+=\\-]*)?'+ // query string
            '(#[\\w\\-_]*)?$','i'); // fragment locater

        return pattern.test(str);
    }

    function fulsocial (proj, rew, val) {
        success_text = $.ajax({async: false, type: "POST", data: ({project: proj, reward: rew, value: val}), url: '/c7feb7803386d713e60894036feeee9e/ce8c56139d45ec05e0aa2261c0a48af9'}).responseText;

        if (String(success_text).trim() == 'OK') {
            if (val == 1) {
                $("#rew"+rew).html('<span style="color: green; font-weight: bold;">Cumplido</span>&nbsp;<a href="#" onclick="return fulsocial(\''+proj+'\', \''+rew+'\', 0)">[X]</a>');
            } else {
                $("#rew"+rew).html('<span style="color: red; font-weight: bold;">Pendiente</span>&nbsp;<a href="#" onclick="return fulsocial(\''+proj+'\', \''+rew+'\', 1)">[ok]</a>');
            }
        } else {
            alert('No se ha modificado, error en webservice: *' + success_text + '*');
        }

        return false;
    }

    jQuery(document).ready(function ($) {

        // al filtrar por estado de proyecto
        $("#projStatus-filter").change(function(){
            $("#filter-form").submit();
        });

        // al clickar, oculta el div padre y muestra el div que se llama igual que el div apdre seguido de 'input'
        $(".doshow").click(function(event){
            var rew = $(this).attr('rel');
            $("#divrew"+rew+"url").hide();
            $("#divrew"+rew+"urlinput").show();
            $("#rew"+rew+"url").focus();

            event.preventDefault();
        });

        $(".dohide").click(function(event){
            var rew = $(this).attr('rel');
            $("#divrew"+rew+"urlinput").hide();
            $("#divrew"+rew+"url").show();

            event.preventDefault();
        });

        // al clickar
        $(".doreq").click(function(event){
            var proj = $(this).attr('proj');
            var rew = $(this).attr('rew');
            var val = $('#rew'+rew+'url').val();

            if (val.length === 0 || !val.trim()) {
                $('#rew'+rew+'url').focus();
                return;
            }

            if (!ValidURL(val)) {
                alert('La URL introducida no es válida. Por favor compruébela de nuevo.');
                $('#rew'+rew+'url').focus();
                return;
            }

            if (val.indexOf('http') != 0) {
                val = 'http://' + val;
                $('#rew'+rew+'url').val(val);
            }

            success_text = $.ajax({async: false, type: "POST", data: ({project: proj, reward: rew, value: val}), url: '/c7feb7803386d713e60894036feeee9e/d82318a7bec39ac2b78be96b8ec2b76e/'}).responseText;

            if (String(success_text).trim() == 'OK') {
                $("#divrew"+rew+"url a.rewurl").attr('href', val);
                $("#divrew"+rew+"url a.rewurl").html(val);
                $("#divrew"+rew+"urlinput").hide();
                $("#divrew"+rew+"url").show();
            } else {
                alert('No se ha modificado, error en webservice: *' + success_text + '*');
            }

            event.preventDefault();
        });
    });

</script>
