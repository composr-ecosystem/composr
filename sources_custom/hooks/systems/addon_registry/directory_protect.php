<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    directory_protect
 */

/**
 * Hook class.
 */
class Hook_addon_registry_directory_protect
{
    /**
     * Get a list of file permissions to set.
     *
     * @param  boolean $runtime Whether to include wildcards represented runtime-created chmoddable files
     * @return array File permissions to set
     */
    public function get_chmod_array($runtime = false)
    {
        return [];
    }

    /**
     * Get the version of Composr this addon is for.
     *
     * @return float Version number
     */
    public function get_version()
    {
        return cms_version_number();
    }

    /**
     * Get the addon category.
     *
     * @return string The category
     */
    public function get_category()
    {
        return 'Admin Utilities';
    }

    /**
     * Get the addon author.
     *
     * @return string The author
     */
    public function get_author()
    {
        return 'ocProducts';
    }

    /**
     * Find other authors.
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution()
    {
        return [];
    }

    /**
     * Get the addon licence (one-line summary only).
     *
     * @return string The licence
     */
    public function get_licence()
    {
        return 'Licensed on the same terms as Composr';
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description()
    {
        return 'Protect the files in a directory by routing requests through a Composr script. Composr will search for the page containing the linked content and only grant access if the current user has access to that page. Developers could customise the code to apply different permission rules.

You will need to add a new rule into your .htaccess file to control the routing. Assuming all the files were under a \'videos\' directory:
[code]
RewriteRule ^(videos/.*)$ data_custom/directory_protect.php\\?file=$1 [L,QSA]

Put the rule above this part of the Composr default:
# Anything that would point to a real file should actually be allowed to do so
RewriteCond %{DOCUMENT_ROOT}/git/$1 -f [OR]
RewriteCond %{DOCUMENT_ROOT}/git/$1 -l [OR]
RewriteCond %{DOCUMENT_ROOT}/git/$1 -d
RewriteRule (.*) - [L]
RewriteRule (.*) - [L]
[/code]';
    }

    /**
     * Get a list of tutorials that apply to this addon.
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials()
    {
        return [];
    }

    /**
     * Get a mapping of dependency types.
     *
     * @return array File permissions to set
     */
    public function get_dependencies()
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
    public function get_default_icon()
    {
        return 'themes/default/images/icons/admin/component.svg';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return [
            'sources_custom/hooks/systems/addon_registry/directory_protect.php',
            'data_custom/directory_protect.php',
        ];
    }
}
