<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    unslider
 */

/**
 * Block class.
 */
class Block_main_unslider
{
    /**
     * Find details of the block.
     *
     * @return ?array Map of block info (null: block is disabled)
     */
    public function info() : ?array
    {
        $info = [];
        $info['author'] = 'Chris Graham';
        $info['organisation'] = 'ocProducts';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 1;
        $info['locked'] = false;
        $info['min_cms_version'] = 11.0;
        $info['addon'] = 'unslider';
        $info['parameters'] = ['pages', 'width', 'height', 'buttons', 'delay', 'speed', 'keypresses', 'slider_id', 'bgcolor'];
        return $info;
    }

    /**
     * Find caching details for the block.
     *
     * @return ?array Map of cache details (cache_on and ttl) (null: block is disabled)
     */
    public function caching_environment() : ?array
    {
        $info = [];
        $info['cache_on'] = <<<'PHP'
        [
            explode(',', isset($map['pages']) ? $map['pages'] : 'slide1,slide2,slide3,slide4,slide5,slide6'),
            isset($map['width']) ? $map['width'] : '100%',
            isset($map['height']) ? $map['height'] : '',
            ((isset($map['buttons']) ? $map['buttons'] : '1') == '1'),
            strval(intval(isset($map['delay']) ? $map['delay'] : '3000')),
            strval(intval(isset($map['speed']) ? $map['speed'] : '500')),
            ((isset($map['keypresses']) ? $map['keypresses'] : '0') == '1'),
            isset($map['slider_id']) ? $map['slider_id'] : 'unslider',
            isset($map['bgcolor']) ? str_replace('#', '', $map['bgcolor']) : '',
        ]
PHP;
        $info['ttl'] = 1000; /* Page include is going to happen within Tempcode, so caching won't affect that */
        return $info;
    }

    /**
     * Execute the block.
     *
     * @param  array $map A map of parameters
     * @return Tempcode The result of execution
     */
    public function run(array $map) : object
    {
        i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

        $error_msg = new Tempcode();
        if (!addon_installed__messaged('unslider', $error_msg)) {
            return $error_msg;
        }

        require_lang('unslider');
        require_css('unslider');

        $block_id = get_block_id($map);

        $pages = explode(',', isset($map['pages']) ? $map['pages'] : 'slide1,slide2,slide3,slide4,slide5,slide6');
        $width = isset($map['width']) ? $map['width'] : '';
        if (($width == '100%') || ($width == 'auto')) {
            $width = '';
        }
        $height = isset($map['height']) ? $map['height'] : '';
        if ($height == 'auto') {
            $height = '';
        }
        if (is_numeric($width)) {
            $width .= 'px';
        }
        if (is_numeric($height)) {
            $height .= 'px';
        }
        $buttons = ((isset($map['buttons']) ? $map['buttons'] : '1') == '1');
        $delay = strval(intval(isset($map['delay']) ? $map['delay'] : '3000'));
        if ($delay == '0') {
            $delay = '';
        }
        $speed = strval(intval(isset($map['speed']) ? $map['speed'] : '500'));
        $keypresses = ((isset($map['keypresses']) ? $map['keypresses'] : '0') == '1');
        $slider_id = isset($map['slider_id']) ? $map['slider_id'] : 'unslider';

        $bgcolor = isset($map['bgcolor']) ? str_replace('#', '', $map['bgcolor']) : '';
        $bgcolors = [];
        if (strpos($bgcolor, ',') === false) {
            for ($i = 0; $i < count($pages); $i++) {
                $bgcolors[$pages[$i]] = $bgcolor;
            }
        } else {
            $_bgcolors = explode(',', $bgcolor);
            for ($i = 0; $i < count($pages); $i++) {
                $bgcolors[$pages[$i]] = isset($_bgcolors[$i]) ? $_bgcolors[$i] : '';
            }
        }

        return do_template('BLOCK_MAIN_UNSLIDER', [
            '_GUID' => 'ae60f714ef84227c0cb958b65f7a253c',
            'BLOCK_ID' => $block_id,
            'PAGES' => $pages,
            'WIDTH' => $width,
            'HEIGHT' => $height,
            'FLUID' => (substr($width, -1) == '%') || ($width == ''),
            'BUTTONS' => $buttons,
            'DELAY' => $delay,
            'SPEED' => $speed,
            'KEYPRESSES' => $keypresses,
            'SLIDER_ID' => $slider_id,
            'BGCOLORS' => $bgcolors,
        ]);
    }
}
