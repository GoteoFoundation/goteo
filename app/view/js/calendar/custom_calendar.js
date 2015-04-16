/*url2link = function(text)
{
    var expr = /(https?://S+)/gi;
    var anchr= '<a href="$1" >$1</a>';
     
    return text.replace(expr,anchr);
}*/

String.prototype.convertirURL = function() {
    return this.replace(/[A-Za-z]+:\/\/[A-Za-z0-9-_]+\.[A-Za-z0-9-_:%&~\?\/.=]+/g, function(url) {
        return url.link(url);
    });
};

/*Funcion que devuelve eventos del mismo dia*/

function other_events(date){
   var event_date=moment(new Date(date)).format("MM-DD-YYYY");
   var other_events="";
   var num=0;

   $("#extra-events").css( "display", "none" );

   var url =  "https://www.googleapis.com/calendar/v3/calendars/l44ukbe8tsjlr50djnk2kl2cik%40group.calendar.google.com/events?singleEvents=true&key=AIzaSyBtKe8e-5DfwDeKFUTcrRmOU7BzXMndg1Y&orderBy=startTime";
        $.getJSON(url, function(data) {
          for(i in data['items']) {
            item = data['items'][i];

            //tenemos en cuenta eventos de todo un dia
            item_start=item.start.dateTime || item.start.date;
            item_end=item.end.dateTime || item.end.date;

            var item_date=moment(new Date(item_start)).format("MM-DD-YYYY");

              if(event_date==item_date)
              {
                num++;
                var event_start=moment(new Date(item_start)).format("HH:mm");
                var event_end=moment(new Date(item_end)).format("HH:mm");
                var reg = /#taller|#evento|#proyecto|#convocatoria|#red/i;

                if(num>1)
                {
                  other_events=other_events+'<a class="other-event" target="_blank" href="/calendar#'+item.id+'" ><span class="event-hour hour-other-event">'+event_start+' - '+event_end+'</span>'+item.summary.replace(reg,'')+'</a>';
                  $("#extra-events").css( "display", "block" );
                }
               
              }
            }

            $("#other-events").html(other_events);


            });
}


$(document).ready(function() {

            $(".other-event").on('click',function() { 
                 location.reload('true'); //Or window.location.href = window.location.href
                 console.log ("Clicked!"); 
            });
  
            $('#calendar').fullCalendar({

            googleCalendarApiKey: 'AIzaSyBtKe8e-5DfwDeKFUTcrRmOU7BzXMndg1Y',
    
            // Goteo calendario publico
            events: 'l44ukbe8tsjlr50djnk2kl2cik@group.calendar.google.com',
      
            eventClick: function(event) {
              // opens events in a popup window
             
              $("#read-more").css( "display", "block" );

              $("#event-description-text").html(event.description.convertirURL());

              $("#event-title").html(event.title.toUpperCase());
              
              var event_date=moment(new Date(event.start)).format("DD | MM | YYYY");
              var event_start=moment(new Date(event.start)).format("HH:mm");
              var event_end=moment(new Date(event.end)).format("HH:mm");
    
              $("#event-date").html(event_date);
              $("#event-location").html(event.location.substr(0,65));
              $("#event-hour").html(event_start+" - "+event_end);
              $("#event-category").html(event.category);

              $("#event-category").removeClass();
              $("#category-info").removeClass();
              $("#event-description-img").removeClass();

              if(!$('#event-category').is(':empty'))
              {
                $("#event-category").addClass("category-background");
                $("#category-info").addClass("category-info");
                $("#event-description-img").addClass("event-description-img "+event.category.substr(0,1).toLowerCase());
              }
              else
                $("#category-info").addClass("nodisplay");

              $("#category-letter").html(event.category.substr(0,1));
              $("#category-letter").removeClass();
              $("#category-letter").addClass("category-legend "+ event.category.substr(0,1).toLowerCase());

              $("#event-facebook").attr("href", "http://facebook.com/sharer.php?u="+document.URL);
              $("#event-twitter").attr("href", "http://twitter.com/home?status="+document.URL);
              $("#event-calendar-add").attr("href", event.url);

              $('html, body').animate({
              scrollTop: ($('#read-more').offset().top)
              },500);
              document.location.hash = event.id;

              other_events(event.start);

              return false;
            },
      
            loading: function(bool) {
              $('#loading').toggle(bool);
            }
      
            });
    
            });
