goteo.typeahead_engines = goteo.typeahead_engines || {};

goteo.typeahead_engines.channel = function (settings) {
    var defaults = settings && settings.defaults || false;

    var engine = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('id', 'name', 'subtitle'),
        identify: function (o) { return o.id; },
        dupDetector: function (a, b) { return a.id === b.id; },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        prefetch: {
            url: '/api/channels',
            filter: function (response) {
                // console.log('prefetch hit', response);
                return response.list;
            }
        },
        remote: {
            url: '/api/channels?q=%QUERY',
            wildcard: '%QUERY',
            filter: function (response) {
                // console.log('remote hit', response);
                return response.list;
            }
        }
    });

    // initialize the bloodhound suggestion engine
    engine.initialize();

    var engineWithDefaults = function (q, sync, async) {
        if (q === '' && defaults) {
            // console.log('get defaults');
            sync(engine.index.all());
            async([]);
        }
        else {
            engine.search(q, sync, async);
        }
    };

    // Engine
    return {
        name: 'channel',
        displayKey: 'name',
        source: engineWithDefaults,
        templates: {
            header: '<h3>' + (goteo.texts && goteo.texts['admin-channels'] || 'channels') + '</h3>',
            suggestion: function (datum) {
                // console.log('channel suggestion', datum);
                var t = '<div>';
                if(datum.logo) t += '<img src="' + datum.logo + '" class="img-circle"> ';
                t += datum.name + '</div>';
                return t;
            }
        }
    }
};
