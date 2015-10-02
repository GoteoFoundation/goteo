/**
 *
 * Explicacion del codigo que contiene
 * 
 */
    function navi (item, cuantos) {

        if (item == '') item = 'image';

        $(".navi-"+item+"").removeClass('active');
        $("."+item+"").hide();

        $("#"+item+"-1").show();
        $("#navi-"+item+"-1").addClass('active');

        $(".navi-arrow-"+item).click(function (event) {
            event.preventDefault();

            /* Quitar todos los active, ocultar todos los elementos */
            $(".navi-"+item+"").removeClass('active');
            $("."+item+"").hide();
            /* Poner acctive a este, mostrar este */
            $("#navi-"+item+"-"+this.rel).addClass('active');
            $("#"+item+"-"+this.rel).show();

            var prev;
            var next;

            if (this.id == item+'-navi-next') {
                prev = parseFloat($("#"+item+"-navi-prev").attr('rel')) - 1;
                next = parseFloat($("#"+item+"-navi-next").attr('rel')) + 1;
            } else {
                prev = parseFloat(this.rel) - 1;
                next = parseFloat(this.rel);
            }

            if (prev < 1) {
                prev = cuantos;
            }

            if (next > cuantos) {
                next = 1;
            }

            if (next < 1) {
                next = cuantos;
            }

            if (prev > cuantos) {
                prev = 1;
            }

            $("#"+item+"-navi-prev").attr('rel', prev);
            $("#"+item+"-navi-next").attr('rel', next);
        });

        $(".navi-"+item).click(function (event) {
            event.preventDefault();

            /* Quitar todos los active, ocultar todos los elementos */
            $(".navi-"+item).removeClass('active');
            $("."+item).hide();
            /* Poner acctive a este, mostrar este*/
            $(this).addClass('active');
            $("#"+this.rel).show();
        });
    }
