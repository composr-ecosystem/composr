<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    columns
 */

/**
 * Hook class.
 */
class Hook_addon_registry_columns
{
    /**
     * Get a list of file permissions to set
     *
     * @param  boolean $runtime Whether to include wildcards represented runtime-created chmoddable files
     * @return array File permissions to set
     */
    public function get_chmod_array($runtime = false)
    {
        return array();
    }

    /**
     * Get the version of Composr this addon is for
     *
     * @return float Version number
     */
    public function get_version()
    {
        return cms_version_number();
    }

    /**
     * Get the addon category
     *
     * @return string The category
     */
    public function get_category()
    {
        return 'Graphical';
    }

    /**
     * Get the addon author
     *
     * @return string The author
     */
    public function get_author()
    {
        return 'Chris Graham';
    }

    /**
     * Find other authors
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution()
    {
        return array(
            'Based on the code of Adam Wulf ("Columnizer")',
        );
    }

    /**
     * Get the addon licence (one-line summary only)
     *
     * @return string The licence
     */
    public function get_licence()
    {
        return 'Creative Commons Attribution 3.0';
    }

    /**
     * Get the description of the addon
     *
     * @return string Description of the addon
     */
    public function get_description()
    {
        return 'Automatically columnise Comcode. Any HTML [tt]div[/tt] tag with a [tt].column_wrapper[/tt] CSS class will be automatically put into columns.

Here is an example in Comcode...
[code]
[surround="column_wrapper"]
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed ornare mi ut convallis molestie. Morbi finibus sagittis sem vel vehicula. Duis aliquet pretium sapien, quis congue risus lacinia eu. Nunc gravida venenatis posuere. Vestibulum ac arcu magna. Integer libero leo, iaculis in lacus eget, fringilla accumsan dui. Quisque augue sem, commodo a suscipit et, suscipit sit amet sapien. Proin iaculis massa mi. Cras auctor, augue id lobortis fringilla, lectus ligula tempus sapien, quis ornare dolor mi a mi. Ut felis elit, vestibulum egestas dignissim a, consectetur at nisl. Sed semper ex sed felis pharetra, at pellentesque turpis porttitor. Nunc laoreet efficitur dui sed iaculis. Nunc nisi enim, dictum imperdiet semper non, molestie sit amet justo. In hac habitasse platea dictumst.

Mauris sit amet metus sit amet velit fermentum convallis. Maecenas dapibus at justo nec maximus. Vestibulum metus odio, vehicula nec ultricies eget, sagittis sit amet ligula. Morbi nec risus metus. In egestas malesuada magna ac interdum. In hac habitasse platea dictumst. Suspendisse pretium, felis laoreet eleifend condimentum, leo risus aliquam magna, in dapibus dolor mauris ac mauris. In at mollis purus. Vestibulum varius vehicula nunc. Sed blandit lobortis turpis ut finibus. Vivamus quis ante auctor massa volutpat rhoncus. Praesent sit amet sapien nunc.

Mauris sed dolor nec ante sollicitudin tristique at sed nulla. Etiam feugiat diam ac lorem mollis, quis congue ante iaculis. Etiam ut hendrerit enim. Morbi molestie dolor ac tellus iaculis tincidunt et sed dolor. Vivamus et ligula justo. Integer consequat lectus in metus feugiat tempor. Pellentesque eleifend iaculis porta.
[/surround]
[/code]

You may want to edit the column CSS, via editing the [tt]columns[/tt] CSS file. For example, to set the widths and column margins.';
    }

    /**
     * Get a list of tutorials that apply to this addon
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials()
    {
        return array();
    }

    /**
     * Get a mapping of dependency types
     *
     * @return array File permissions to set
     */
    public function get_dependencies()
    {
        return array(
            'requires' => array(),
            'recommends' => array(),
            'conflicts_with' => array()
        );
    }

    /**
     * Explicitly say which icon should be used
     *
     * @return URLPATH Icon
     */
    public function get_default_icon()
    {
        return 'themes/default/images/icons/48x48/menu/_generic_admin/component.png';
    }

    /**
     * Get a list of files that belong to this addon
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return array(
            'sources_custom/hooks/systems/addon_registry/columns.php',
            'themes/default/css_custom/columns.css',
            'themes/default/javascript_custom/columns.js',
            'sources_custom/hooks/systems/startup/columns.php',
        );
    }
}
