<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_tutorials
 */

/**
 * Hook class.
 */
class Hook_snippet_tutorial_box
{
    /**
     * Run function for snippet hooks. Generates XHTML to insert into a page using AJAX.
     *
     * @return Tempcode The snippet
     */
    public function run()
    {
        require_code('tutorials');

        $tutorial_name = get_param_string('tutorial_name');

        $metadata = get_tutorial_metadata($tutorial_name);
        $_tutorial = templatify_tutorial($metadata, false);

        return do_template('TUTORIAL_BOX', $_tutorial);
    }
}
