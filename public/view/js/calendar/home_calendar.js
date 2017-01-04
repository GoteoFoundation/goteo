/*
@licstart  The following is the entire license notice for the
JavaScript code in this page.

Copyright (C) 2010  Goteo Foundation

The JavaScript code in this page is free software: you can
redistribute it and/or modify it under the terms of the GNU
General Public License (GNU GPL) as published by the Free Software
Foundation, either version 3 of the License, or (at your option)
any later version.  The code is distributed WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS
FOR A PARTICULAR PURPOSE.  See the GNU GPL for more details.

As additional permission under GNU GPL version 3 section 7, you
may distribute non-source (e.g., minimized or compacted) forms of
that code without the copy of the GNU GPL normally required by
section 4, provided you include this license notice and a URL
through which recipients can access the Corresponding Source.


@licend  The above is the entire license notice
for the JavaScript code in this page.
*/

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
            item_end=item.end.dateTime || item.end.date;

            var current_day=moment(new Date()).format("YYYY-MM-DD");
            var event_date=moment(new Date(item_start)).format("YYYY-MM-DD");


              if(current_day<=event_date)
              {
                var event_month=moment(new Date(item_start)).format("MMMM");
                var event_day=moment(new Date(item_start)).format("D");
                var event_text_day=moment(new Date(item_start)).format("dddd");
                var event_start=moment(new Date(item_start)).format("HH:mm");
                var event_end=moment(new Date(item_end)).format("HH:mm");


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

                $("#main-calendar").addClass(event_category.substr(0,1).toLowerCase()+"-bg");
                $("#inside").addClass(event_category.substr(0,1).toLowerCase());
                $("#extra-calendar").addClass(event_category.substr(0,1).toLowerCase());
                break;
              }
            }
            });

        });
