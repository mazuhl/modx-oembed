<?php
if (empty($url)) {
    return false;
} else {
    $config['url'] = $url;
}
$config['maxwidth'] = !empty($maxwidth) ? (integer) $maxwidth : 600;
$config['maxheight'] = !empty($maxheight) ? (integer) $maxheight : 600;
$config['format'] = !empty($format) ? $format : 'json';
$config['outputMode'] = !empty($outputMode) ? $outputMode : 'full';
$config['discover'] = $discover;
$config['tpl'] = $tpl;
$output = '';

include_once $modx->getOption('core_path').'components/oembed/oembed.class.php';
$oembed = new oEmbed($modx, $config);
$oembed->fetch();
$output = $oembed->getOutput();

return $output;