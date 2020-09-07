goteo.typeahead_engines = goteo.typeahead_engines || {};

goteo.typeahead_engines.post = function (settings) {
    var defaults = settings && settings.defaults || false;

    var engine = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('id', 'title'),
        identify: function (o) { return o.id; },
        dupDetector: function (a, b) { return a.id === b.id; },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        prefetch: {
            url: '/api/blog/posts',
            filter: function (response) {
                // console.log('prefetch hit', response);
                return response.list;
            }
        },
        remote: {
            url: '/api/blog/posts?&q=%QUERY',
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
        name: 'post',
        displayKey: 'title',
        source: engineWithDefaults,
        templates: {
            header: '<h3>' + (goteo.texts && goteo.texts['admin-posts'] || 'posts') + '</h3>',
            suggestion: function (datum) {
                // console.log('post suggestion', datum);
                var t = '<div>';
                t += datum.title + '</div>';
                return t;

            }
        }
    }
};
