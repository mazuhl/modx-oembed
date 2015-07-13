<?php
class oEmbed {
    private $_config = array();
    private $_provider = null;
    private $_embed_data = null;
    public $providers = array();

    public function __construct(modX &$modx, array $config = array(), $newProvider = array()) {
        $this->modx =& $modx;

        $this->_config = $config;
        $this->providers = array_merge(array(
            'youtube_com' => array(
                'url' => 'http://www.youtube.com/oembed',
                'pattern' => 'http://(www\.)?youtube\.com/watch[^`"\s\<\>]*'
            ),
            'blip_tv' => array(
                'url' => 'http://blip.tv/oembed/',
                'pattern' => 'http://(.*)?blip\.tv/file/[^`"\'\s\<\>]*'
            ),
            'vimeo_com'  => array(
                'url' => 'http://www.vimeo.com/api/oembed.{format}',
                'pattern' => 'http://(www\.)?vimeo\.com/[^`"\'\s\<\>]*'
            ),
            'dailymotion_com' => array(
                'url' => 'http://www.dailymotion.com/api/oembed',
                'pattern' => 'http://(www\.)?dailymotion\.com/[^`"\'\s\<\>]*'
            ),
            'flickr_com' => array(
                'url' => 'http://www.flickr.com/services/oembed/',
                'pattern' => 'http://(www\.)?flickr\.com/[^`"\'\s\<\>]*'
            ),
            'hulu_com' => array(
                'url' => 'http://www.hulu.com/api/oembed.{format}',
                'pattern' => 'http://(www\.)?hulu\.com/watch/[^`"\'\s\<\>]*'
            ),
            'viddler_com' => array(
                'url' => 'http://lab.viddler.com/services/oembed/',
                'pattern' => 'http://(www\.)?viddler\.com/[^`"\'\s\<\>]*'
            ),
            'qik_com' => array(
                'url' => 'http://qik.com/api/oembed.{format}',
                'pattern' => 'http://qik\.com/[^`"\'\s\<\>]*'
            ),
            'revision3_com' => array(
                'url' => 'http://revision3.com/api/oembed/',
                'pattern' => 'http://revision3\.com/[^`"\'\s\<\>]*'
            ),
            'photobucket_com' => array(
                'url' => 'http://photobucket.com/oembed',
                'pattern' => 'http://(s.+|gi.+)photobucket\.com/(albums|groups)/[^`"\'\s\<\>]*'
            ),
            'scribd_com' => array(
                'url' => 'http://www.scribd.com/services/oembed',
                'pattern' => 'http://(www\.)?scribd\.com/[^`"\'\s\<\>]*'
            ),
            'wordpress_tv' => array(
                'url' => 'http://wordpress.tv/oembed/',
                'pattern' => 'http://wordpress\.tv/[^`"\'\s\<\>]*'
            )
        ), $newProvider);
    }
    public function getProviderByUrl() {
        $name = null;
        foreach ($this->providers as $provider => $data) {
            $pattern = '#' . $data['pattern'] . '#i';
            if (preg_match($pattern, $this->_config['url'])) {
                $name = $provider;
                break 1;
            }
        }
        return $name;
    }

    public function getRequestUrl() {
        if (!$this->_provider) $this->_provider = $this->getProviderByUrl();
        if (isset($this->providers[$this->_provider])) {
            $provider_url = str_replace('{format}', $this->_config['format'], $this->providers[$this->_provider]['url']);
            return $provider_url .
                '?url=' . urlencode($this->_config['url']) .
                '&maxwidth=' . $this->_config['maxwidth'] .
                '&maxheight=' . $this->_config['maxheight'] .
                '&format=' . $this->_config['format'];
        } elseif ($this->_config['discover']) {
            return $this->discoverRequestUrl();
        }
        return null;
    }
    
    public function setProvider($provider_name) {
        if (array_key_exists($provider_name, $this->providers)) {
            $this->_provider = $provider_name;
            return true;
        } else {
            return false;
        }
    }

    public function fetch () {
        $request_url = $this->getRequestUrl();
        if ($request_url != null) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $request_url);
            //echo ($request_url);
            //die();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            $response = curl_exec($ch);
            //echo print_r($response);
            //die();
            curl_close($ch);
        } else {
            $this->_embed_data = null;
            return null;
        }
        if ($response) {
            $response = trim($response);
        } else {
            return null;
        }
        if (strtolower($this->_config['format']) == 'json') {
            $embed_data = json_decode($response);
            if (is_object($embed_data))
                $this->_embed_data = $embed_data;
        } elseif ((strtolower($this->_config['format']) == 'xml') && function_exists('simplexml_load_string')) {
             $embed_data = simplexml_load_string($response);
             if (is_object($embed_data))
                 $this->_embed_data = $embed_data;
        }
        return $this->_embed_data;
    }

    public function getOutput() {
        if ($this->_embed_data === null) {
            return $this->_config['url'];
        } elseif ($this->_config['outputMode'] == 'full') {
            if ($this->_embed_data->width) {
                if (preg_match('/\d+%/', $this->_embed_data->width)) {
                    $this->_embed_data->wrapperWidth = (int) ($this->_config['maxwidth'] / 100 * (int) $this->_embed_data->width);
                } else {
                    $this->_embed_data->wrapperWidth = (int) $this->_embed_data->width;
                }
            } else {
                $this->_embed_data->wrapperWidth = $this->_config['maxwidth'];
            }
            switch ($this->_embed_data->type) {
                case 'photo':
                    $this->_embed_data->embed = $this->_embed_data->url;
                    $defTpl = '
                        <div class="oembed oembed-photo" style="width: [[+wrapperWidth]]px">
                            <img src="[[+embed]]" width="[[+width]]" height="[[+height]]" alt="[[+title]]" />
                            <p>[[+title]] by <a href="[[+author_url]]" target="_blank">[[+author_name]]</a></p>
                        </div>
                    ';
                    break;
                case 'video':
                    $this->_embed_data->embed = $this->_embed_data->html;
                    $defTpl = '
                        <div class="oembed oembed-video" style="width: [[+wrapperWidth]]px">
                            [[+embed]]
                            <p>[[+title]] by <a href="[[+author_url]]" target="_blank">[[+author_name]]</a></p>
                        </div>
                    ';
                    break;
                case 'rich':
                    $this->_embed_data->embed = $this->_embed_data->html;
                    $defTpl = '
                        <div class="oembed oembed-rich" style="width: [[+wrapperWidth]]px">
                            [[+embed]]
                        </div>
                    ';
                    break;
                case 'link':
                    $this->_embed_data->embed = $this->_config['url'];
                    $defTpl = '
                        <span class="oembed oembed-link"><a href="[[+embed]]">[[+title]]</a>
                    ';
                    break;
                default:
                    return $this->_config['url'];
            }
            if(!empty($this->_config['tpl'])) {
                $chunk = $this->modx->getObject('modChunk', array('name' => $this->_config['tpl']));
                if ($chunk) {
                    $chunkContent = $chunk->getContent();
                } else {
                    $chunkContent = $defTpl;
                }
            } else {
                $chunkContent = $defTpl;
            }
            $embed_data = (array) $this->_embed_data;
            $embedChunk = $this->modx->newObject('modChunk');
            $embedChunk->setCacheable(false);
            $embedChunk->setContent($chunkContent);
            return $embedChunk->process($embed_data);
        } else {
            switch ($this->_embed_data->type) {
                case 'photo':
                    $minimal_output = $this->_embed_data->url;
                    break;
                case 'video':
                    $minimal_output = $this->_embed_data->html;
                    break;
                case 'rich':
                    $minimal_output = $this->_embed_data->html;
                    break;
                case 'link':
                    $minimal_output = $this->_config['url'];
                    break;
                default:
                    $minimal_output = $this->_config['url'];
            }
            return $minimal_output;
        }
    }

    private function discoverRequestUrl() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->_config['url']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $response = curl_exec($ch);
        curl_close($ch);
        if (strtolower($this->_config['format']) == 'json') {
            if (preg_match('#<link\s.*application/json\+oembed.*>#i', $response, $matches)) {
                preg_match('#(?<=(href=")|(href=\')).+?(?="|\')#i', $matches[0], $match_url);
                return $match_url[0] .
                    '&maxwidth=' . $this->_config['maxwidth'] .
                    '&maxheight=' . $this->_config['maxheight'] .
                    '&format=json';
            }
        } elseif (strtolower($this->_config['format']) == 'xml') {
            if (preg_match('#<link\s.*text/xml\+oembed.*>#i', $response, $matches) || preg_match('#<link\s.*application/xml\+oembed.*>#i', $response, $matches)) {
                preg_match('#(?<=(href=")|(href=\')).+?(?="|\')#i', $matches[0], $match_url);
                return $match_url[0] .
                    '&maxwidth=' . $this->_config['maxwidth'] .
                    '&maxheight=' . $this->_config['maxheight'] .
                    '&format=xml';
            }
        }
    }

    public function autoEmbed() {
        $content = &$this->modx->resource->_output;
        foreach ($this->providers as $name => $data) {
            $this->_provider = $name;
            if ($this->_config['strictMatch'] == false) {
                $pattern = '#((?<=\s)|(?<=^))' . $data['pattern'] . '((?=\s)|(?=$))#mi';
            } else {
                $pattern = '#^' . $data['pattern'] . '$#mi';
            }
            $content = preg_replace_callback (
                $pattern,
                array( &$this, 'getAutoEmbedOutput'),
                $content
            );
        }
    }

    private function getAutoEmbedOutput($matches) {
        $this->_config['url'] = $matches[0];
        $this->fetch();
        return $this->getOutput();
    }
}
?>
