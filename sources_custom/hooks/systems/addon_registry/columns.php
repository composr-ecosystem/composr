<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    columns
 */

/**
 * Hook class.
 */
class Hook_addon_registry_columns
{
    /**
     * Get a list of file permissions to set.
     *
     * @param  boolean $runtime Whether to include wildcards represented runtime-created chmoddable files
     * @return array File permissions to set
     */
    public function get_chmod_array(bool $runtime = false) : array
    {
        return [];
    }

    /**
     * Get the current version of this addon (usually software major, software minor, addon build).
     * Put the comment "// addon_version_auto_update" to the right of the return if you want release tools to automatically update this according to software version and find_addon_effective_mtime.
     *
     * @return SHORT_TEXT Version number
     */
    public function get_version() : string
    {
        return '11.0.0'; // addon_version_auto_update
    }

    /**
     * Get the minimum required version of the website software needed to use this addon.
     *
     * @return float Minimum required website software version
     */
    public function get_min_cms_version() : float
    {
        return 11.0;
    }

    /**
     * Get the maximum compatible version of the website software to use this addon.
     *
     * @return ?float Maximum compatible website software version (null: no maximum version currently)
     */
    public function get_max_cms_version() : ?float
    {
        return 11.9;
    }

    /**
     * Get the addon category.
     *
     * @return string The category
     */
    public function get_category() : string
    {
        return 'Graphical';
    }

    /**
     * Get the addon author.
     *
     * @return string The author
     */
    public function get_author() : string
    {
        return 'Chris Graham';
    }

    /**
     * Find other authors.
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution() : array
    {
        return [
            'Based on the code of Adam Wulf ("Columnizer")',
        ];
    }

    /**
     * Get the addon licence (one-line summary only).
     *
     * @return string The licence
     */
    public function get_licence() : string
    {
        return 'Creative Commons Attribution 3.0';
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description() : string
    {
        return 'Automatically columnise Comcode. Any HTML [tt]div[/tt] tag with a [tt].column-wrapper[/tt] CSS class will be automatically put into columns.

Here is an example in Comcode...
[code]
[surround="column-wrapper"]
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed ornare mi ut convallis molestie. Morbi finibus sagittis sem vel vehicula. Duis aliquet pretium sapien, quis congue risus lacinia eu. Nunc gravida venenatis posuere. Vestibulum ac arcu magna. Integer libero leo, iaculis in lacus eget, fringilla accumsan dui. Quisque augue sem, commodo a suscipit et, suscipit sit amet sapien. Proin iaculis massa mi. Cras auctor, augue id lobortis fringilla, lectus ligula tempus sapien, quis ornare dolor mi a mi. Ut felis elit, vestibulum egestas dignissim a, consectetur at nisl. Sed semper ex sed felis pharetra, at pellentesque turpis porttitor. Nunc laoreet efficitur dui sed iaculis. Nunc nisi enim, dictum imperdiet semper non, molestie sit amet justo. In hac habitasse platea dictumst.

Mauris sit amet metus sit amet velit fermentum convallis. Maecenas dapibus at justo nec maximus. Vestibulum metus odio, vehicula nec ultricies eget, sagittis sit amet ligula. Morbi nec risus metus. In egestas malesuada magna ac interdum. In hac habitasse platea dictumst. Suspendisse pretium, felis laoreet eleifend condimentum, leo risus aliquam magna, in dapibus dolor mauris ac mauris. In at mollis purus. Vestibulum varius vehicula nunc. Sed blandit lobortis turpis ut finibus. Vivamus quis ante auctor massa volutpat rhoncus. Praesent sit amet sapien nunc.

Mauris sed dolor nec ante sollicitudin tristique at sed nulla. Etiam feugiat diam ac lorem mollis, quis congue ante iaculis. Etiam ut hendrerit enim. Morbi molestie dolor ac tellus iaculis tincidunt et sed dolor. Vivamus et ligula justo. Integer consequat lectus in metus feugiat tempor. Pellentesque eleifend iaculis porta.
[/surround]
[/code]

You may want to edit the column CSS, via editing the [tt]columns[/tt] CSS file. For example, to set the widths and column margins.';
    }

    /**
     * Get a list of tutorials that apply to this addon.
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials() : array
    {
        return [];
    }

    /**
     * Get a mapping of dependency types.
     *
     * @return array A structure specifying dependency information
     */
    public function get_dependencies() : array
    {
        return [
            'requires' => [],
            'recommends' => [],
            'conflicts_with' => [],
        ];
    }

    /**
     * Explicitly say which icon should be used.
     *
     * @return URLPATH Icon
     */
    public function get_default_icon() : string
    {
        return 'themes/default/images/icons/admin/component.svg';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list() : array
    {
        return [
            'sources_custom/hooks/systems/addon_registry/columns.php',
            'sources_custom/hooks/systems/contentious_overrides/columns.php',
            'sources_custom/hooks/systems/startup/columns.php',
            'themes/default/css_custom/columns.css',
            'themes/default/javascript_custom/columns.js',
        ];
    }
}
