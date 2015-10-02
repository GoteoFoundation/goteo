<input name="<?php
/*
 * This file is part of the Goteo Package.
 *
 * (c) Platoniq y Fundación Goteo <fundacion@goteo.org>
 *
 * For the full copyright and license information, please view the README.md
 * and LICENSE files that was distributed with this source code.
 */ echo htmlspecialchars($this['name']) ?>" type="text"<?php

if (isset($this['class'])) echo ' class="datepicker ' . htmlspecialchars($this['class']) . '"';

if (isset($this['size'])) echo ' size="' . ((int) $this['size']) . '"';

if($this['data'] && is_array($this['data'])) {
    foreach($this['data'] as $key => $val) {
        echo ' data-' . $key . '="' . htmlspecialchars($val) . '"';
    }
}

?> value="<?php if (isset($this['value'])) echo htmlspecialchars($this['value']) ?>"<?php

?> />
<?php
//TODO: borrar esto y hacerlo mejor via javascript externo!
// hace falta cargar el javascript si se pide fuera de un superform
if ($this['js'] == true) : ?>
<script type="text/javascript" src="<?php echo SRC_URL ?>/view/js/datepicker.min.js"></script>
<script type="text/javascript">

        (function () {

            var dp = $('#<?php echo $this['id'] ?> input');

        dp.DatePicker({
            format: 'Y-m-d',
            date: '<?php echo $this['value'] ?>',
            current: '<?php echo $this['value'] ?>',
            starts: 1,
            position: 'bottom',
            eventName: 'click',
            onBeforeShow: function(){
                dp.DatePickerSetDate(dp.val(), true);
            },
            onChange: function(formatted, dates){
                dp.val(formatted);
                dp.DatePickerHide();
                dp.focus();
            },
            locale: {
                days: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
                daysShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'],
                daysMin: ['D', 'L', 'M', 'X', 'J', 'V', 'S'],
                months: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                monthsShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                week: []
            }
        });

    })();
</script>
<?php endif; ?>
