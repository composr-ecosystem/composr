<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class _oembed_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        disable_php_memory_limit();
        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLUGGISH);
    }
    public function testOEmbedManualPatternsConfigOption()
    {
        $map = [
            'https://www.youtube.com/watch?v=LDfzAA8fNKU' => 'https://www.youtube.com/oembed',
            'https://vimeo.com/channels/staffpicks/247068452' => 'http://vimeo.com/api/oembed.{format}',
            'https://www.dailymotion.com/video/x8eqst9' => 'http://www.dailymotion.com/services/oembed',
            'http://www.slideshare.net/scroisier/future-of-open-source-cms-4176880' => 'http://www.slideshare.net/api/oembed/2',
            'https://www.flickr.com/photos/rustumlongpig/7168441953/in/photolist-bVs93M-76hEZ5-9k5TDH-7Mho7j-auaThL-21Kwz5k-e2dQPt-95ZsSS-7CaVss-adyb9W-cDUY87-4DmcLP-8t3qxh-nGpsmz-cyCMtL-brsL2j-61mdVx-acjjsR-aoBGSC-opyRb8-acUVnL-tXUqaM-gzXeG-5qU7mj-wbCGS-6hGPs3-8w3yy5-9ata57-qibN8N-c72zAW-7ada8L-3LxAzh-DJfuwT-4DVwX9-bneCT-4DVxsY-aoTLwD-6gxHP-obXG69-8rugxw-doaVy1-3LxCmd-4Kz14i-8DkL9d-6NDjdS-StTQEG-3LxzV5-qAyAB-caBL63-64c6ut' => 'http://www.flickr.com/services/oembed?format={format}',
            'https://soundcloud.com/1lonr/time?in=1lonr/sets/land-of-nothing-real-1' => 'http://soundcloud.com/oembed?format={format}',
            /*
            Paid only, so cannot test anymore
            'https://twitter.com/socpub/status/971009263702167553' => 'https://api.twitter.com/1/statuses/oembed.{format}',
            'https://heartsnmagic.tumblr.com/post/663998783597772800' => 'http://api.embed.ly/1/oembed?key=123456',
            'http://edition.cnn.com/2009/HEALTH/04/06/hm.caffeine.withdrawal/index.html' => 'http://api.embed.ly/1/oembed?key=123456',
            'https://www.google.co.uk/maps/@51.6921416,0.4606626,7z?hl=en' => 'http://api.embed.ly/1/oembed?key=123456',
            'https://www.google.com/maps/@51.6921416,0.4606626,7z?hl=en' => 'http://api.embed.ly/1/oembed?key=123456',
            'http://www.imdb.com/title/tt1825683/' => 'http://api.embed.ly/1/oembed?key=123456',
            'https://www.scribd.com/document/372625296/PHP-docx' => 'http://api.embed.ly/1/oembed?key=123456',
            'https://en.wikipedia.org/wiki/Windows_8' => 'http://api.embed.ly/1/oembed?key=123456',
            'https://xkcd.com/1843/' => 'http://api.embed.ly/1/oembed?key=123456',
            */
        ];

        foreach ($map as $url => $oembed_endpoint) {
            if (($this->only !== null) && ($this->only != $url)) {
                continue;
            }

            $_url = str_replace('{format}', 'json', $oembed_endpoint) . ((strpos($oembed_endpoint, '?') === false) ? '?' : '&') . 'url=' . urlencode($url);
            $c = http_get_contents($_url, ['timeout' => 10.0, 'trigger_errors' => false]);
            $this->assertTrue(is_array(json_decode($c, true)), 'Failed on ' . $_url);
            if (php_function_allowed('usleep')) {
                usleep(2000000);
            }
        }
    }
}
