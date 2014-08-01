<?php 
/* Esta vista es para el ajax de gestión de retornos colectivos
 * Desde /admin/commons  y desde /dashboard/projects/commons
 * Necesita haber usado la vista view/project/edit/rewards/view_commons.html.php
 */ 
?>
<script type="text/javascript">
    function fulsocial (proj, rew, val) {
        success_text = $.ajax({async: false, type: "POST", data: ({project: proj, reward: rew, value: val}), url: '<?php echo SITE_URL; ?>/c7feb7803386d713e60894036feeee9e/ce8c56139d45ec05e0aa2261c0a48af9'}).responseText;

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
            success_text = $.ajax({async: false, type: "POST", data: ({project: proj, reward: rew, value: val}), url: '<?php echo SITE_URL; ?>/c7feb7803386d713e60894036feeee9e/d82318a7bec39ac2b78be96b8ec2b76e/'}).responseText;

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