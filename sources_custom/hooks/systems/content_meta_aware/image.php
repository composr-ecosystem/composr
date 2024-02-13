<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    workflows
 */

if (!function_exists('init__hooks__systems__content_meta_aware__image')) {
    function init__hooks__systems__content_meta_aware__image($in)
    {
        if (!addon_installed('workflows')) {
            return $in;
        }

        $in = override_str_replace_exactly(
            "'table' => 'images',",
            "'table' => 'images', 'uses_workflow' => true,",
            $in
        );

        return $in;
    }
}
