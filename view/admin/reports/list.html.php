<?php

use Goteo\Library\Text;

$reports = $this['reports'];
$filters = $this['filters'];
$data    = $this['data'];
?>
<a href="/admin/reports/top" class="button">Top backers</a>
&nbsp;&nbsp;&nbsp;
<a href="/admin/reports/projects" class="button">Impulsores</a>
&nbsp;&nbsp;&nbsp;
<a href="/admin/reports/calls" class="button">Convocadores</a>
&nbsp;&nbsp;&nbsp;
<a href="/admin/reports/paypal" class="button">Estado actual PayPal</a>
&nbsp;&nbsp;&nbsp;
<a href="/admin/reports/geoloc" class="button">Geolocalizados</a>

<div class="widget board">
    <form id="filter-form" action="/admin/reports" method="get">

        <div style="float:left;margin:5px;">
            <label for="report-filter">Informe</label><br />
            <select id ="report-filter" name="report">
                <option value="">Seleccionar informe</option>
                <?php foreach ($reports as $repId=>$repName) :
                    $selected = ($repId == $filters['report']) ? ' selected="selected"' : '';
                    ?>
                <option value="<?php echo $repId; ?>"<?php echo $selected; ?>><?php echo $repName; ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="float:left;margin:5px;display: none;" id="date-filter-from">
            <label for="date-filter-from">Fecha desde</label><br />
            <input type="text" name="date_from" value ="<?php echo $filters['date_from']?>" />

            <script type="text/javascript" src="http://resources.devgoteo.org/view/js/datepicker.min.js"></script>
            <script type="text/javascript">

                (function () {

                    var dp = $('#date-filter-from input');

                    dp.DatePicker({
                        format: 'Y-m-d',
                        date: '',
                        current: '',
                        starts: 0,
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
                            days: ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábad', 'Domingo'],
                            daysShort: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
                            daysMin: ['L', 'M', 'X', 'J', 'V', 'S', 'D'],
                            months: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                            monthsShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                            week: []
                        }
                    });

                })();
            </script>
        </div>

        <div style="float:left;margin:5px;display: none;" id="date-filter-until">
            <label for="date-filter-until">Fecha hasta</label><br />
            <input type="text" name="date_until" value ="<?php echo $filters['date_until']?>" />

            <script type="text/javascript">

                (function () {

                    var dp = $('#date-filter-until input');

                    dp.DatePicker({
                        format: 'Y-m-d',
                        date: '',
                        current: '',
                        starts: 0,
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
                            days: ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábad', 'Domingo'],
                            daysShort: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
                            daysMin: ['L', 'M', 'X', 'J', 'V', 'S', 'D'],
                            months: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                            monthsShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                            week: []
                        }
                    });

                })();
            </script>
        </div>

        <br clear="both" />

        <div style="float:left;margin:5px;">
            <input type="submit" value="Ver" />
        </div>
    </form>
</div>

<script type="text/javascript">
    /* para mostrar los campos de fechas solo en los informes que es posible usarlas */
    $(function () {
        $("#report-filter").change( function() {
            if (($(this).val() == 'money') || ($(this).val() == 'projects')) {
                $("#date-filter-from").show();
                $("#date-filter-until").show();
            } else {
                $("#date-filter-from").hide();
                $("#date-filter-until").hide();
            }
        });

        if (($("#report-filter").val() == 'money') || ($("#report-filter").val() == 'projects')) {
            $("#date-filter-from").show();
            $("#date-filter-until").show();
        }
    });

</script>

<div class="widget board">
<?php if (!empty($data)) : ?>
    
    <?php if (in_array($filters['report'], array('money', 'projects'))) {
            if (!empty($filters['date_from'])) {
                $inicio = $filters['date_from'];
            } else {
                $inicio = "el inicio de Goteo";
            }

            if (!empty($filters['date_until'])) {
                $fin = $filters['date_until'];
            } else {
                $fin = "hasta hoy";
            }

            if ((empty($filters['date_from'])) && empty($filters['date_until'])) {
                $period = "todos los datos, desde el inicio de Goteo hasta hoy";
            } else {
                $period = "período comprendido entre " . $inicio . " y " . $fin;
            }
    ?>
    <p>El siguiente informe se ha calculado para: <?php echo $period; ?>.</p>
    <?php } ?>
    <table>
        <thead>
            <tr>
                <td></td>
                <?php foreach ($data['columns'] as $column) : ?>
                <th><?php echo $column; ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data['rows'] as $i=>$label) : ?>
            <tr>
                <th style="text-align: left;"><?php echo $label; ?></th>
                <?php foreach ($data['data'][$i] as $value) : ?>
                <td style="text-align: right;"><?php echo $value; ?></td>
                <?php endforeach; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php else : ?>
<p>No se han encontrado registros</p>
<?php endif; ?>
