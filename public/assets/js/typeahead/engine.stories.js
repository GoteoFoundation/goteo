goteo.typeahead_engines = goteo.typeahead_engines || {};

goteo.typeahead_engines.story = function (settings) {
    var defaults = settings && settings.defaults || false;

    var engine = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('id', 'title', 'subtitle'),
        identify: function (o) { return o.id; },
        dupDetector: function (a, b) { return a.id === b.id; },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        prefetch: {
            url: '/api/stories',
            filter: function (response) {
                // console.log('prefetch hit', response);
                return response.list;
            }
        },
        remote: {
            url: '/api/stories?&q=%QUERY',
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
        name: 'story',
        displayKey: 'title',
        source: engineWithDefaults,
        templates: {
            header: '<h3>' + (goteo.texts && goteo.texts['admin-stories'] || 'stories') + '</h3>',
            suggestion: function (datum) {
                // console.log('story suggestion', datum);
                var t = '<div>';
                if(datum.logo) t += '<img src="' + datum.image + '" class="img-circle"> ';
                t += datum.title + '</div>';
                return t;

            }
        }
    }
};
