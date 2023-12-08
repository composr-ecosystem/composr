<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    analysr
 */

/**
 * Hook class.
 */
class Hook_stats_external
{
    /**
     * Show a stats section.
     *
     * @return Tempcode The result of execution.
     */
    public function run()
    {
        $bits = new Tempcode();
        $map = array();
        $url = get_base_url();
        list($rank, $links, $speed) = getAlexaRank($url);
        if ($rank != '') {
            $map['Alexa rank'] = $rank;
        }
        if ($links != '') {
            $map['Back links'] = protect_from_escaping('<a title="Show back links" href="http://www.google.co.uk/search?as_lq=' . urlencode($url) . '">' . $links . '</a>');
        }
        if ($speed != '') {
            $map['Speed'] = $speed;
        }
        foreach ($map as $key => $val) {
            $bits->attach(do_template('BLOCK_SIDE_STATS_SUBLINE', array('_GUID' => 'fa391b1b773cd8a4b283cb6617af898b', 'KEY' => $key, 'VALUE' => is_null($val) ? '' : $val)));
        }
        $section = do_template('BLOCK_SIDE_STATS_SECTION', array('_GUID' => '0d26b94a7903aab57d76d72da53eca98', 'SECTION' => 'Meta stats', 'CONTENT' => $bits));

        return $section;
    }
}

function getAlexaRank($url)
{
    $test = get_value_newer_than('alexa__' . md5($url), time() - 60 * 60 * 24 * 5, true);
    if ($test !== null) {
        return unserialize($test);
    }

    require_code('files');
    $p = array();
    $_url = 'https://www.alexa.com/minisiteinfo/' . urlencode($url);
    $result = http_download_file($_url, null, false, false, 'Composr', null, null, null, null, null, null, null, null, 1.0);
    if (($result !== null) && (preg_match('#([\d,]+)\s*</a>\s*</div>\s*<div class="label">Alexa Traffic Rank#s', $result, $p) != 0)) {
        $rank = integer_format(intval($p[1]));
    } else {
        $rank = do_lang('NA');
    }
    if (($result !== null) && (preg_match('#([\d,]+)\s*</a>\s*</div>\s*<div class="label">Sites Linking In#s', $result, $p) != 0)) {
        $links = integer_format(intval($p[1]));
    } else {
        $links = '';
    }
    $speed = '';

    // we would like, but cannot get (without an API key)...
    /*
        time on site
        reach (as a percentage)
        page views
        audience (i.e. what country views the site most)
     */

    $ret = array($rank, $links, $speed);

    set_value('alexa__' . md5($url), serialize($ret), true);

    return $ret;
}
