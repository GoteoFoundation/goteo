/*!
 * FullCalendar v2.2.7 Google Calendar Plugin
 * Docs & License: http://arshaw.com/fullcalendar/
 * (c) 2013 Adam Shaw
 */
 
(function(factory) {
	if (typeof define === 'function' && define.amd) {
		define([ 'jquery' ], factory);
	}
	else {
		factory(jQuery);
	}
})(function($) {


var API_BASE = 'https://www.googleapis.com/calendar/v3/calendars';
var fc = $.fullCalendar;
var applyAll = fc.applyAll;


fc.sourceNormalizers.push(function(sourceOptions) {
	var googleCalendarId = sourceOptions.googleCalendarId;
	var url = sourceOptions.url;
	var match;

	// if the Google Calendar ID hasn't been explicitly defined
	if (!googleCalendarId && url) {

		// detect if the ID was specified as a single string.
		// will match calendars like "asdf1234@calendar.google.com" in addition to person email calendars.
		if ((match = /^[^\/]+@([^\/\.]+\.)*(google|googlemail|gmail)\.com$/.test(url))) {
			googleCalendarId = url;
		}
		// try to scrape it out of a V1 or V3 API feed URL
		else if (
			(match = /^https:\/\/www.googleapis.com\/calendar\/v3\/calendars\/([^\/]*)/.exec(url)) ||
			(match = /^https?:\/\/www.google.com\/calendar\/feeds\/([^\/]*)/.exec(url))
		) {
			googleCalendarId = decodeURIComponent(match[1]);
		}

		if (googleCalendarId) {
			sourceOptions.googleCalendarId = googleCalendarId;
		}
	}


	if (googleCalendarId) { // is this a Google Calendar?

		// make each Google Calendar source uneditable by default
		if (sourceOptions.editable == null) {
			sourceOptions.editable = false;
		}

		// We want removeEventSource to work, but it won't know about the googleCalendarId primitive.
		// Shoehorn it into the url, which will function as the unique primitive. Won't cause side effects.
		// This hack is obsolete since 2.2.3, but keep it so this plugin file is compatible with old versions.
		sourceOptions.url = googleCalendarId;
	}
});


fc.sourceFetchers.push(function(sourceOptions, start, end, timezone) {
	if (sourceOptions.googleCalendarId) {
		return transformOptions(sourceOptions, start, end, timezone, this); // `this` is the calendar
	}
});


function transformOptions(sourceOptions, start, end, timezone, calendar) {
	var url = API_BASE + '/' + encodeURIComponent(sourceOptions.googleCalendarId) + '/events?callback=?'; // jsonp
	var apiKey = sourceOptions.googleCalendarApiKey || calendar.options.googleCalendarApiKey;
	var success = sourceOptions.success;
	var data;
	var timezoneArg; // populated when a specific timezone. escaped to Google's liking

	function reportError(message, apiErrorObjs) {
		var errorObjs = apiErrorObjs || [ { message: message } ]; // to be passed into error handlers
		var consoleObj = window.console;
		var consoleWarnFunc = consoleObj ? (consoleObj.warn || consoleObj.log) : null;

		// call error handlers
		(sourceOptions.googleCalendarError || $.noop).apply(calendar, errorObjs);
		(calendar.options.googleCalendarError || $.noop).apply(calendar, errorObjs);

		// print error to debug console
		if (consoleWarnFunc) {
			consoleWarnFunc.apply(consoleObj, [ message ].concat(apiErrorObjs || []));
		}
	}

	if (!apiKey) {
		reportError("Specify a googleCalendarApiKey. See http://fullcalendar.io/docs/google_calendar/");
		return {}; // an empty source to use instead. won't fetch anything.
	}

	// The API expects an ISO8601 datetime with a time and timezone part.
	// Since the calendar's timezone offset isn't always known, request the date in UTC and pad it by a day on each
	// side, guaranteeing we will receive all events in the desired range, albeit a superset.
	// .utc() will set a zone and give it a 00:00:00 time.
	if (!start.hasZone()) {
		start = start.clone().utc().add(-1, 'day');
	}
	if (!end.hasZone()) {
		end = end.clone().utc().add(1, 'day');
	}

	// when sending timezone names to Google, only accepts underscores, not spaces
	if (timezone && timezone != 'local') {
		timezoneArg = timezone.replace(' ', '_');
	}

	data = $.extend({}, sourceOptions.data || {}, {
		key: apiKey,
		timeMin: start.format(),
		timeMax: end.format(),
		timeZone: timezoneArg,
		singleEvents: true,
		maxResults: 9999
	});

	return $.extend({}, sourceOptions, {
		googleCalendarId: null, // prevents source-normalizing from happening again
		url: url,
		data: data,
		startParam: true, // `false` omits this parameter. we already included it above
		endParam: true, // same
		timezoneParam: false, // same
		success: function(data) {
			var events = [];
			var successArgs;
			var successRes;
			var event_category="";
			var open=0;

			if (data.error) {
				reportError('Google Calendar API: ' + data.error.message, data.error.errors);
			}
			else if (data.items) {
				$.each(data.items, function(i, entry) {
					var url = entry.htmlLink;

					// make the URLs for each event show times in the correct timezone
					if (timezoneArg) {
						url = injectQsComponent(url, 'ctz=' + timezoneArg);
					}

					if(!entry.description)
						entry.description="";

					if ((entry.summary.search("#taller")>=0)||(entry.description.search("#taller")>=0)) {
   					event_category="Taller";
   					entry.description=entry.description.replace('#taller','');
   					entry.summary=entry.summary.replace('#taller','');
					}
					if ((entry.summary.search("#evento")>=0)||(entry.description.search("#evento")>=0)) {
   					event_category="Evento";
   					entry.description=entry.description.replace('#evento','');
   					entry.summary=entry.summary.replace('#evento','');
					}
					if ((entry.summary.search("#proyecto")>=0)||(entry.description.search("#proyecto")>=0)) {
   					event_category="Proyecto";
   					entry.description=entry.description.replace('#proyecto','');
   					entry.summary=entry.summary.replace('#proyecto','');
					}
					if ((entry.summary.search("#convocatoria")>=0)||(entry.description.search("#convocatoria")>=0)) {
   					event_category="Convocatoria";
   					entry.description=entry.description.replace('#convocatoria','');
   					entry.summary=entry.summary.replace('#convocatoria','');
					}
					if ((entry.summary.search("#red")>=0)||(entry.description.search("#red")>=0)) {
   					event_category="Red y Políticas";
   					entry.description=entry.description.replace('#red','');
   					entry.summary=entry.summary.replace('#red','');
					}

					url_hash=window.location.hash.substr(1);

					if (entry.id==url_hash)
					{
						open=1;
					}

					events.push({
						id: entry.id,
						title: entry.summary,
						category: event_category,
						start: entry.start.dateTime || entry.start.date || "Sin determinar", // try timed. will fall back to all-day
						end: entry.end.dateTime || entry.end.date || "?", // same
						url: url,
						location: entry.location || "Sin determinar",
						description: entry.description,
						open: open
					});

					event_category="";
					open=0;
				});

				// call the success handler(s) and allow it to return a new events array
				successArgs = [ events ].concat(Array.prototype.slice.call(arguments, 1)); // forward other jq args
				successRes = applyAll(success, this, successArgs);
				if ($.isArray(successRes)) {
					return successRes;
				}
			}

			return events;
		}
	});
}


// Injects a string like "arg=value" into the querystring of a URL
function injectQsComponent(url, component) {
	// inject it after the querystring but before the fragment
	return url.replace(/(\?.*?)?(#|$)/, function(whole, qs, hash) {
		return (qs ? qs + '&' : '?') + component + hash;
	});
}


});
