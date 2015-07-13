<?php
$eventName = $modx->event->name;
if ($eventName == 'OnWebPagePrerender') {
    include_once $modx->getOption('core_path').'components/oembed/oembed.class.php';
    $config = array();
    $config['strictMatch'] = $scriptProperties['strictMatch'];
    $config['maxwidth'] = $scriptProperties['maxwidth'];
    $config['maxheight'] = $scriptProperties['maxheight'];
    $config['format'] = $scriptProperties['format'];
    $config['outputMode'] = $scriptProperties['outputMode'];
    $config['discover'] = false;

    $oembed = new oEmbed($modx, $config);
    $oembed->autoEmbed();
}
