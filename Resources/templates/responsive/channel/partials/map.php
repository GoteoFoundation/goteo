<?php
$section = current($this->channel->getSections('map'));

if($section):
    $config = $this->channel->getConfig();
    $map_config = $config["map"];
    $params = [
        'channel' => $this->channel->id,
        'lang' => $this->lang_url_query($this->lang_current()),
    ];

    $url = '/map';

    if ($map_config['geojson']) {
        $params['geojson'] = urlencode($map_config['geojson']);
    }

    if ($map_config['zoom']) {
        $url .= '/' . $map_config['zoom'];
    }

    if ($map_config['center']) {
        $url .= '/' . implode(',', $map_config['center']);
    }
?>
<div class="section map">
    <div class="container">
        <div class="row">
            <h2 class="title text-center">
                <?= $section->main_title ?: $this->t('channel-call-map-section-title') ?>
            </h2>

            <iframe src="<?= $this->get_url() ?><?= $url ?>?<?= http_build_query($params) ?>"
                    loading="auto"
                    width="100%"
                    height="500"
                    style="border:none;"
                    class="spacer-20"
                    allowfullscreen></iframe>
        </div>
    </div>
</div>
<?php endif; ?>
