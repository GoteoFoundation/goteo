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

$(document).ready(function() {
  
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

              return false;
            },
      
            loading: function(bool) {
              $('#loading').toggle(bool);
            }
      
            });
    
            });
