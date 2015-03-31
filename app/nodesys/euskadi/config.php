<?php
// Configuraciones específicas para nodos
// Metadata
define('NODE_META_TITLE', 'Goteo Euskadi - Prokomunaren kofinantzaketa');
define('NODE_META_DESCRIPTION', utf8_encode('Red social de financiación colectiva'));
define('NODE_META_KEYWORDS', utf8_encode('crowdfunding, procomún, commons, social, network, financiacion colectiva, cultural, creative commons, proyectos abiertos, open source, free software, licencias libres'));
define('NODE_META_AUTHOR', 'Fundación Goteo');
define('NODE_META_COPYRIGHT', 'Platoniq');
define('NODE_DEFAULT_LANG', 'es');
define('NODE_URL', 'http://euskadi.goteo.org');
define('NODE_NAME', 'GoteoEuskadi');
define('NODE_MAIL', 'aupa@euskadi.goteo.org');
define('NODE_ANALYTICS_TRACKER', "<script type=\"text/javascript\">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-17744816-5']);
  _gaq.push(['_trackPageview']);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
");