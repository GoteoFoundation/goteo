function ucfirst(string){ 
          return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase(); 
        }

$(document).ready(function() {
        var url =  "https://www.googleapis.com/calendar/v3/calendars/l44ukbe8tsjlr50djnk2kl2cik%40group.calendar.google.com/events?singleEvents=true&key=AIzaSyBtKe8e-5DfwDeKFUTcrRmOU7BzXMndg1Y&orderBy=startTime";
        $.getJSON(url, function(data) {
          for(i in data['items']) {
            item = data['items'][i];

            //tenemos en cuenta eventos de todo un dia
            item_start=item.start.dateTime || item.start.date;
            item_end=item.start.dateTime || item.start.date;

            var current_day=moment(new Date()).format("MM-DD-YYYY");
            var event_date=moment(new Date(item_start)).format("MM-DD-YYYY");

              if(current_day<=event_date)
              {
                var event_month=moment(new Date(item_start)).format("MMMM");
                var event_day=moment(new Date(item_start)).format("D");
                var event_text_day=moment(new Date(item_start)).format("dddd");
                var event_start=moment(new Date(item_start)).format("hh:mm");
                var event_end=moment(new Date(item_end)).format("hh:mm");


                $("#event-month").html(ucfirst(event_month));
                $("#event-day").html(ucfirst(event_day));
                $("#event-text-day").html(event_text_day);
                $("#event-start").html(event_start+" ");
                $("#event-end").html(" "+event_end);
                $("#event-location").html(item.location.substr(0,20));
                $("#mod-pojctopen").css( "display", "block" );

                if(!item.description)
                item.description="";

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

                $("#event-title").html(item.summary);
                $("#event-category").html(event_category);
                $("#event-link").attr("href", "/calendar#"+item.id);
                break;
              }
            }
            });
      
        });
