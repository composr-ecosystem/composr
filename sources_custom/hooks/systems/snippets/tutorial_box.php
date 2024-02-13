<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    composr_tutorials
 */

/**
 * Hook class.
 */
class Hook_snippet_tutorial_box
{
    /**
     * Run function for snippet hooks. Generates HTML to insert into a page using AJAX.
     *
     * @return Tempcode The snippet
     */
    public function run() : object
    {
        if (!addon_installed('composr_tutorials')) {
            return new Tempcode();
        }

        require_code('tutorials');

        $tutorial_name = get_param_string('tutorial_name');

        $metadata = get_tutorial_metadata($tutorial_name);
        $_tutorial = templatify_tutorial($metadata, false);

        return do_template('TUTORIAL_BOX', $_tutorial);
    }
}
