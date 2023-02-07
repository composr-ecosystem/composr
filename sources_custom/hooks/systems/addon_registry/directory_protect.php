<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

 See docs/LICENSE.md for full licensing information.

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
    public function get_chmod_array(bool $runtime = false) : array
    {
        return [];
    }

    /**
     * Get the version of Composr this addon is for.
     *
     * @return float Version number
     */
    public function get_version() : float
    {
        return cms_version_number();
    }

    /**
     * Get the addon category.
     *
     * @return string The category
     */
    public function get_category() : string
    {
        return 'Architecture';
    }

    /**
     * Get the addon author.
     *
     * @return string The author
     */
    public function get_author() : string
    {
        return 'ocProducts';
    }

    /**
     * Find other authors.
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution() : array
    {
        return [];
    }

    /**
     * Get the addon licence (one-line summary only).
     *
     * @return string The licence
     */
    public function get_licence() : string
    {
        return 'Licensed on the same terms as Composr';
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description() : string
    {
        return 'Protect the files in a directory by routing requests through a Composr script. Composr will search for the page containing the linked content and only grant access if the current user has access to that page. Developers could customise the code to apply different permission rules.

You will need to add a new rule into your .htaccess file to control the routing. Assuming all the files were under a \'videos\' directory:
[code]
RewriteRule ^/?(videos/.*)$ data_custom/directory_protect.php\\?file=$1 [L,QSA]
[/code]

Put the rule above this part of the Composr default:
[code]
# Anything that would point to a real file should actually be allowed to do so. If you have a "RewriteBase /subdir" command, you may need to change to "%{DOCUMENT_ROOT}/subdir/$1".
RewriteCond $1 ^\d+.shtml [OR]
RewriteCond $1 \.(1st|3g2|3gp|3gp2|3gpp|3p|7z|aac|ai|aif|aifc|aiff|asf|atom|avi|bin|bmp|br|bz2|css|csv|cur|diff|dmg|doc|docx|dot|dotx|eml|exe|f4v|gif|gz|html|ico|ics|ini|iso|jpe|jpeg|jpg|js|json|keynote|log|m2v|m4v|mdb|mid|mov|mp2|mp3|mp4|mpa|mpe|mpeg|mpg|mpv2|numbers|odb|odc|odg|odi|odp|ods|odt|ogg|ogv|otf|pages|patch|pdf|php|png|ppt|pptx|ps|psd|pub|qt|ra|ram|rar|rm|rss|rtf|sql|svg|swf|tar|tga|tgz|tif|tiff|torrent|tpl|ttf|txt|yaml|yml|vsd|vtt|wav|weba|webm|webp|wma|wmv|woff|woff2|xls|xlsx|xml|xsd|xsl|zip)($|\?) [OR]
RewriteCond %{DOCUMENT_ROOT}/$1 -f [OR]
RewriteCond %{DOCUMENT_ROOT}/$1 -l [OR]
RewriteCond %{DOCUMENT_ROOT}/$1 -d [OR]
RewriteCond $1 -f [OR]
RewriteCond $1 -l [OR]
RewriteCond $1 -d
RewriteRule ^/?(.*) - [L]
[/code]';
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
            'sources_custom/hooks/systems/addon_registry/directory_protect.php',
            'data_custom/directory_protect.php',
        ];
    }
}
