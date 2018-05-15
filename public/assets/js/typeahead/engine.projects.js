goteo.typeahead_engines = goteo.typeahead_engines || {};

goteo.typeahead_engines.project = function (settings) {
    var prefetch_statuses = settings && settings.prefetch_statuses || '3',
        remote_statuses = settings && settings.remote_statuses || '3,4,5,6',
        defaults = settings && settings.defaults || false;

    var engine = new Bloodhound({
        // datumTokenizer: function (list) {
        //     console.log('token', list);
        //     return Bloodhound.tokenizers.whitespace(list.name);
        // },
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('id', 'name', 'subtitle'),
        identify: function (o) { return o.id; },
        dupDetector: function (a, b) { return a.id === b.id; },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        prefetch: {
            url: '/api/projects?status=' + prefetch_statuses,
            filter: function (response) {
                // console.log('prefetch hit', response);
                return response.list;
            }
        },
        remote: {
            url: '/api/projects?status=' + remote_statuses + '&q=%QUERY',
            wildcard: '%QUERY',
            filter: function (response) {
                // console.log('remote hit', response);
                return response.list;
            }
        }
    });

    // initialize the bloodhound suggestion engine
    // engine.clearPrefetchCache();
    engine.initialize();

    var engineWithDefaults = function (q, sync, async) {
        if (q === '' && defaults) {
            // console.log('get defaults');
            // sync(engine.get([]));
            sync(engine.index.all());
            async([]);
        }
        else {
            engine.search(q, sync, async);
        }
    };

    // Engine
    return {
        name: 'project',
        displayKey: 'name',
        source: engineWithDefaults,
        templates: {
            header: '<h3>' + (goteo.texts && goteo.texts['admin-projects'] || 'Projects') + '</h3>',
            suggestion: function (datum) {
                var label = 'default';
                if (datum.status === 2) label = 'info';
                if (datum.status === 3) label = 'orange';
                if (datum.status === 4) label = 'success';
                if (datum.status === 5) label = 'success';
                if (datum.status === 6) label = 'danger';
                var t = '<div>';
                if(datum.image) t += '<img src="' + datum.image + '" class="img-circle"> ';
                t += '<span class="label label-' + label + '">' + datum.status_desc + '</span> ';
                t += datum.name + '</div>';
                return t;
            }
        }
    }
};
