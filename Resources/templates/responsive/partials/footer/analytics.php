<?php

echo $this->insert('partials/utils/gtag', [
    'google' => $this->get_config('analytics.google')
]);

echo $this->insert('partials/utils/analytics_google', [
  'google' => $this->get_config('analytics.google')
]);

echo $this->insert('partials/utils/analytics_matomo', [
  'matomo' => $this->get_config('analytics.matomo')
]);
