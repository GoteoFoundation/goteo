function ucfirst(string)
{
    return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
}

String.prototype.convertirURL = function() {
    return this.replace(/[A-Za-z]+:\/\/[A-Za-z0-9-_]+\.[A-Za-z0-9-_:%&~\?\/.=]+/g, function(url) {
        return url.link(url);
    });
};


function filter_calendar()
{
    var input_time_min=$("#date-filter-from").val();
    var input_time_max=$("#date-filter-until").val();
    var input_category=$("#category-filter").val();
    var input_location=$("#location-filter").val();

    if(input_time_min||input_time_max||input_category||input_location)
    {
        if(location.port)
            var port=':'+location.port;
        else
            var port='';

    var share_url=location.hostname+port+'/export-calendar?date-filter-from='+input_time_min+'&date-filter-until='+input_time_max+'&category-filter='+input_category+'&location-filter='+input_location;

    $("#share-url").val(share_url);

    $("div.share-url").show();

    }

    else
        $("div.share-url").hide();

    var time_min=moment(new Date(input_time_min)).format("YYYY-MM-DDT00:00:00");
    var time_max=moment(new Date(input_time_max)).format("YYYY-MM-DDT23:59:59");

    var url =  "https://www.googleapis.com/calendar/v3/calendars/l44ukbe8tsjlr50djnk2kl2cik%40group.calendar.google.com/events?singleEvents=true&key=AIzaSyBtKe8e-5DfwDeKFUTcrRmOU7BzXMndg1Y&maxResults=25000&orderBy=startTime&timeMin="+time_min+"Z&timeMax="+time_max+"Z";

    var events_list="";

    var event_img="";
    var img_url="";
    var img_type="";

        $.getJSON(url, function(data) {
            for(i in data['items']) {
                item = data['items'][i];

                //tenemos en cuenta eventos de todo un dia
                item_start=item.start.dateTime || item.start.date;
                item_end=item.end.dateTime || item.end.date;

                var event_date=moment(new Date(item_start)).format("DD | MM | YYYY");

                if(!item.description)
                    item.description="";

                // Search category

                if ((item.summary.search("#taller")>=0)||(item.description.search("#taller")>=0)) {
                    event_category="Taller";
                    item.summary=item.summary.replace('#taller','');
                }
                if ((item.summary.search("#evento")>=0)||(item.description.search("#evento")>=0)) {
                    event_category="Evento";
                    item.summary=item.summary.replace('#evento','');
                }
                if ((item.summary.search("#proyecto")>=0)||(item.description.search("#proyecto")>=0)) {
                    event_category="Proyecto";
                    item.summary=item.summary.replace('#proyecto','');
                }
                if ((item.summary.search("#convocatoria")>=0)||(item.description.search("#convocatoria")>=0)) {
                    event_category="Convocatoria";
                    item.summary=item.summary.replace('#convocatoria','');
                }
                if ((item.summary.search("#red")>=0)||(item.description.search("#red")>=0)) {
                    event_category="Red y Políticas";
                    item.summary=item.summary.replace('#red','');
                }

                if(!item.location)
                    item.location="";

                if(!item.summary)
                    item.summary="";

                //Search location

                //Barcelona
                if ((item.location.search("Barcelona")>=0)||(item.description.search("#Barcelona")>=0))
                    event_location=0;

                //Nacional
                else if ((item.location.search("España")>=0)||(item.location.search("Spain")>=0)||(item.description.search("#España")>=0))
                    event_location=1;

                //Internacional
                else event_location=2;

                if(input_location!="")
                    if(input_location==event_location)
                        var check_location=1;
                    else
                        var check_location=0;

                else
                    var check_location=1;



                if(item.attachments){
                    img_type=item.attachments[0].mimeType;
                    if(img_type=="image/jpeg"||img_type=="image/png"||img_type=="image/gif")
                    {
                        img_id=item.attachments[0].fileId;
                        event_img='<div class="item"><img class="export-event-img" src="https://drive.google.com/uc?export=view&id='+img_id+'"></div>';
                    }
                    else
                        event_img="";
                }
                else
                    event_img="";

                if((input_category==""||input_category==event_category)&&check_location)
                {

                    var description=item.description.convertirURL().replace(new RegExp("\n","g"), "<br>");

                    events_list+=   '<div class="event">'+
                                        '<div class="item">'+event_date+'</div>'+
                                        '<div class="item">'+item.summary+'</div>'+
                                        '<div class="item">'+item.location+'</div>'+
                                        event_img+
                                        '<div class="item description">'+description+'</div>'+
                                    '</div>';

                }

            }

            $("#exported-events").html(events_list);

        });
}

$(document).ready(function() {

    filter_calendar();

    $(function(){
        $('input.datepicker').Zebra_DatePicker({
            days: ['Domingo', 'Lunes', 'Martes', 'Mi\u00E9rcoles', 'Jueves', 'Viernes', 'S\u00E1bado'],
            days_abbr: ['D', 'L', 'M', 'X', 'J', 'V', 'S'],
            months: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            months_abbr: ['Ene', 'Feb', 'Mar', 'Abr', 'Mayo', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            show_select_today: 'Hoy'
         });
    });

    $("#filtrar").click(function(){
         filter_calendar();
    });

});
