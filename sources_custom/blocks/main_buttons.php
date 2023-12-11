<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    charity_banners
 */

/**
 * Block class.
 */
class Block_main_buttons
{
    /**
     * Find details of the block.
     *
     * @return ?array Map of block info (null: block is disabled).
     */
    public function info()
    {
        $info = array();
        $info['author'] = 'Chris Graham';
        $info['organisation'] = 'ocProducts';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 2;
        $info['locked'] = false;
        $info['parameters'] = array('param', 'extra', 'max');
        return $info;
    }

    /**
     * Find caching details for the block.
     *
     * @return ?array Map of cache details (cache_on and ttl) (null: block is disabled).
     */
    public function caching_environment()
    {
        $info = array();
        $info['cache_on'] = 'array(array_key_exists(\'param\',$map)?$map[\'param\']:\'\',array_key_exists(\'extra\',$map)?$map[\'extra\']:\'\',array_key_exists(\'max\',$map)?intval($map[\'max\']):100)';
        $info['ttl'] = (get_value('no_block_timeout') === '1') ? 60 * 60 * 24 * 365 * 5/*5 year timeout*/ : 60 * 24 * 7;
        return $info;
    }

    /**
     * Install the block.
     *
     * @param  ?integer $upgrade_from What version we're upgrading from (null: new install)
     * @param  ?integer $upgrade_from_hack What hack version we're upgrading from (null: new-install/not-upgrading-from-a-hacked-version)
     */
    public function install($upgrade_from = null, $upgrade_from_hack = null)
    {
        // first ensure there is 'buttons' banners category, and if it doesn't exist create it
        $id = 'buttons';
        $is_textual = 0;
        $image_width = 120;
        $image_height = 60;
        $max_file_size = 70;
        $comcode_inline = 0;

        $test = $GLOBALS['SITE_DB']->query_select_value_if_there('banner_types', 'id', array('id' => $id));
        if (is_null($test)) {
            $GLOBALS['SITE_DB']->query_insert('banner_types', array(
                'id' => $id,
                't_is_textual' => $is_textual,
                't_image_width' => $image_width,
                't_image_height' => $image_height,
                't_max_file_size' => $max_file_size,
                't_comcode_inline' => $comcode_inline
            ));

            log_it('ADD_BANNER_TYPE', $id);
        }

        $submitter = $GLOBALS['FORUM_DRIVER']->get_guest_id();

        require_code('banners3');

        // Create default banners, if they don't exist
        add_banner_quiet('composr', 'data_custom/images/causes/composr.gif', brand_name(), brand_name(), 0, get_brand_base_url() . '/', 3, '', 0, null, $submitter, 1, 'buttons', null, 0, 0, 0, 0, null);
        add_banner_quiet('firefox', 'data_custom/images/causes/firefox.gif', 'Firefox', 'Firefox', 0, 'http://www.mozilla.com/firefox/', 3, '', 0, null, $submitter, 1, 'buttons', null, 0, 0, 0, 0, null);
        add_banner_quiet('w3cxhtml', 'data_custom/images/causes/w3c-xhtml.gif', 'W3C XHTML', 'W3C XHTML', 0, 'http://www.w3.org/MarkUp/', 3, '', 0, null, $submitter, 1, 'buttons', null, 0, 0, 0, 0, null);
        add_banner_quiet('w3ccss', 'data_custom/images/causes/w3c-css.gif', 'W3C CSS', 'W3C CSS', 0, 'http://www.w3.org/Style/CSS/', 3, '', 0, null, $submitter, 1, 'buttons', null, 0, 0, 0, 0, null);
        //add_banner_quiet('w3cwcag', 'data_custom/images/causes/w3c-wcag.gif', 'W3C WCAG', 'W3C WCAG', 0, 'http://www.w3.org/TR/WCAG10/', 3, '', 0, null, $submitter, 1, 'buttons', null, 0, 0, 0, 0, null);   //no banner image
        add_banner_quiet('cancerresearch', 'data_custom/images/causes/cancerresearch.gif', 'Cancer Research', 'Cancer Research', 0, 'http://www.cancerresearchuk.org/', 3, '', 0, null, $submitter, 1, 'buttons', null, 0, 0, 0, 0, null);
        add_banner_quiet('rspca', 'data_custom/images/causes/rspca.gif', 'RSPCA', 'RSPCA', 0, 'http://www.rspca.org.uk/home', 3, '', 0, null, $submitter, 1, 'buttons', null, 0, 0, 0, 0, null);
        add_banner_quiet('peta', 'data_custom/images/causes/peta.gif', 'PETA', 'PETA', 0, 'http://www.peta.org', 3, '', 0, null, $submitter, 1, 'buttons', null, 0, 0, 0, 0, null);
        add_banner_quiet('Unicef', 'data_custom/images/causes/unicef.gif', 'Unicef', 'Unicef', 0, 'http://www.unicef.org', 3, '', 0, null, $submitter, 1, 'buttons', null, 0, 0, 0, 0, null);
        add_banner_quiet('wwf', 'data_custom/images/causes/wwf.gif', 'WWF', 'WWF', 0, 'http://www.wwf.org/', 3, '', 0, null, $submitter, 1, 'buttons', null, 0, 0, 0, 0, null);
        add_banner_quiet('greenpeace', 'data_custom/images/causes/greenpeace.gif', 'Greenpeace', 'Greenpeace', 0, 'http://www.greenpeace.com', 3, '', 0, null, $submitter, 1, 'buttons', null, 0, 0, 0, 0, null);
        add_banner_quiet('helptheaged', 'data_custom/images/causes/helptheaged.gif', 'HelpTheAged', 'HelpTheAged', 0, 'http://www.helptheaged.org.uk/en-gb', 3, '', 0, null, $submitter, 1, 'buttons', null, 0, 0, 0, 0, null);
        add_banner_quiet('nspcc', 'data_custom/images/causes/nspcc.gif', 'NSPCC', 'NSPCC', 0, 'http://www.nspcc.org.uk/', 3, '', 0, null, $submitter, 1, 'buttons', null, 0, 0, 0, 0, null);
        add_banner_quiet('oxfam', 'data_custom/images/causes/oxfam.gif', 'Oxfam', 'Oxfam', 0, 'http://www.oxfam.org', 3, '', 0, null, $submitter, 1, 'buttons', null, 0, 0, 0, 0, null);
        add_banner_quiet('cnd', 'data_custom/images/causes/cnd.gif', 'CND', 'CND', 0, 'http://www.cnduk.org/', 3, '', 0, null, $submitter, 1, 'buttons', null, 0, 0, 0, 0, null);
        add_banner_quiet('amnestyinternational', 'data_custom/images/causes/amnestyinternational.gif', 'Amnesty International', 'Amnesty International', 0, 'http://www.amnesty.org/', 3, '', 0, null, $submitter, 1, 'buttons', null, 0, 0, 0, 0, null);
        add_banner_quiet('bhf', 'data_custom/images/causes/bhf.gif', 'British Heart Foundation', 'British Heart Foundation', 0, 'http://www.bhf.org.uk/', 3, '', 0, null, $submitter, 1, 'buttons', null, 0, 0, 0, 0, null);
        add_banner_quiet('gnu', 'data_custom/images/causes/gnu.gif', 'GNU', 'GNU', 0, 'http://www.gnu.org/', 3, '', 0, null, $submitter, 1, 'buttons', null, 0, 0, 0, 0, null);
    }

    /**
     * Execute the block.
     *
     * @param  array $map A map of parameters.
     * @return Tempcode The result of execution.
     */
    public function run($map)
    {
        i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

        require_css('banners');

        if (!array_key_exists('param', $map)) {
            $map['param'] = '';
        }
        if (!array_key_exists('extra', $map)) {
            $map['extra'] = '';
        }
        if (!array_key_exists('title', $map)) {
            $map['title'] = 'I support'; // default value
        }
        $max = array_key_exists('max', $map) ? intval($map['max']) : 100;
        $height = (!empty($map['height'])) ? $map['height'] : '100%';//default: 100%

        $set_height = '';
        if ($height != '100%') {
            $set_height = ' style="overflow: auto; width: 100%!important; height: ' . $height . '!important;" ';
        }

        require_code('banners');

        $b_type = $map['param'];
        $myquery = banner_select_sql($b_type) . ' ORDER BY name';
        $banners = $GLOBALS['SITE_DB']->query($myquery, 200/*Just in case of ridiculous numbers*/);
        $assemble = new Tempcode();

        if (count($banners) > $max) {
            shuffle($banners);
            $banners = array_slice($banners, 0, $max);
        }

        foreach ($banners as $i => $banner) {
            $bd = show_banner($banner['name'], $banner['b_title_text'], get_translated_tempcode('banners', $banner, 'caption'), $banner['b_direct_code'], $banner['img_url'], '', $banner['site_url'], $banner['b_type'], $banner['submitter']);
            $more_coming = ($i < count($banners) - 1);
            $assemble->attach(do_template('BLOCK_MAIN_BANNER_WAVE_BWRAP_CUSTOM', array('_GUID' => 'b7d22f954147f0d012cb6eaeaf721e8f', 'EXTRA' => $map['extra'], 'TYPE' => $map['param'], 'BANNER' => $bd, 'MORE_COMING' => $more_coming)));
        }

        return do_template('BLOCK_MAIN_BUTTONS', array('_GUID' => 'b78228b68ce7f275c6cbb6055e37081e', 'EXTRA' => $map['extra'], 'TYPE' => $map['param'], 'ASSEMBLE' => $assemble, 'TITLE' => $map['title'], 'SET_HEIGHT' => $set_height));
    }
}
