<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    crosswordr
 */

/**
 * Hook class.
 */
class Hook_addon_registry_crosswordr
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
        return 'Fun and Games';
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
            'Laurynas Butkus',
        );
    }

    /**
     * Get the addon licence (one-line summary only)
     *
     * @return string The licence
     */
    public function get_licence()
    {
        return 'GPL';
    }

    /**
     * Get the description of the addon
     *
     * @return string Description of the addon
     */
    public function get_description()
    {
        return 'Block to generate random crosswords, based on meta keywords and top forum posters.

The block takes the following parameters: cols (default is 15), rows (default is 15), max_words (default is 15), param (this is a name for the crossword generated; it will cache against this name for the cols/rows/max_words, so that people get a consistent crossword).

When staff view the block, a message about the answers is posted.

We suggest sites use this block in a competition and award points to the first member to get it. i.e. by posting this within a forum topic where users can reply.

Note: the crossword does not have any interactivity and requires somewhere for users to reply such as a forum topic.

Usage example: [code="Comcode"][block]main_crossword[/block][/code]';
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
            'requires' => array(
                'Conversr',
            ),
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
            'sources_custom/hooks/systems/addon_registry/crosswordr.php',
            'sources_custom/php-crossword/index.html',
            'sources_custom/php-crossword/COPYING',
            'sources_custom/php-crossword/php_crossword.class.php',
            'sources_custom/php-crossword/php_crossword_cell.class.php',
            'sources_custom/php-crossword/php_crossword_client.class.php',
            'sources_custom/php-crossword/php_crossword_grid.class.php',
            'sources_custom/php-crossword/php_crossword_word.class.php',
            'sources_custom/miniblocks/main_crossword.php',
            'themes/default/css_custom/crossword.css',
            'sources_custom/php-crossword/.htaccess',
        );
    }
}
