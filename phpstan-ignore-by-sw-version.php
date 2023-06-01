<?php declare(strict_types = 1);

$config = [];
if (version_compare(str_replace('v','',getenv('SW_VERSION')), '6.5', '>=')) {
  $config['parameters']['excludePaths']['analyse'][] = getenv('PLUGIN_DIR').'/src/Compatibility';
}
return $config;
