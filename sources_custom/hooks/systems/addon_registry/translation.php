<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2018

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    translation
 */

/**
 * Hook class.
 */
class Hook_addon_registry_translation
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
        return 'Information Display';
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
        return array();
    }

    /**
     * Get the addon licence (one-line summary only)
     *
     * @return string The licence
     */
    public function get_licence()
    {
        return 'Licensed on the same terms as Composr';
    }

    /**
     * Get the description of the addon
     *
     * @return string Description of the addon
     */
    public function get_description()
    {
        return 'Provides a simple API and Tempcode symbol for translating text, via Google Translate. The symbol is designed for assistive translations of user-supplied content, not for wholesale/transparent content translation.

Google Translate will need configuring in the standard Composr config option for this to work:
Admin Zone > Setup > Configuration > Site options > Internationalisation > Google translate API key

To use the symbol just wrap it around text that needs translating in templates, e.g.:
[code="Tempcode"]
{$PROVIDE_WITH_TRANSLATION,{TEXT},{$TRANS_TEXT_CONTEXT_html_block},FR}
[/code]
This example translates French in [tt]{TEXT}[/tt] to the site\'s current language, within the context of a block of HTML. The original text is followed by the translated text, and as it is a block of HTML it also includes the "Powered by Google Translate" message.

Here are the available translation modes:
1) [tt]{$TRANS_TEXT_CONTEXT_plain}[/tt] -- translate plain text
2) [tt]{$TRANS_TEXT_CONTEXT_html_block}[/tt] -- translate a block of HTML (with a credit message to Google)
3) [tt]{$TRANS_TEXT_CONTEXT_html_inline}[/tt] -- translate inline HTML (without a credit message to Google)

If you omit the source language then it will be autodetected.
';
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
        return 'themes/default/images/icons/48x48/menu/_generic_admin/tool.png';
    }

    /**
     * Get a list of files that belong to this addon
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return array(
            'sources_custom/hooks/systems/addon_registry/translation.php',
            'sources_custom/translation.php',
            'sources_custom/hooks/systems/symbols/PROVIDE_WITH_TRANSLATION.php',
            'themes/default/images_custom/google_translate.svg',
            'sources_custom/hooks/systems/startup/translation.php',
        );
    }

    /**
     * Uninstall the addon.
     */
    public function uninstall()
    {
        $GLOBALS['SITE_DB']->drop_table_if_exists('translation_cache');
    }

    /**
     * Install the addon.
     *
     * @param  ?integer $upgrade_from What version we're upgrading from (null: new install)
     */
    public function install($upgrade_from = null)
    {
        if ($upgrade_from === null) {
            $GLOBALS['SITE_DB']->create_table('translation_cache', array(
                'id' => '*AUTO',
                't_lang_from' => 'LANGUAGE_NAME',
                't_lang_to' => 'LANGUAGE_NAME',
                't_text' => 'LONG_TEXT',
                't_context' => 'INTEGER',
                't_text_result' => 'LONG_TEXT',
            ));
            $GLOBALS['SITE_DB']->create_index('translation_cache', 'lookup', array('t_lang_from', 't_lang_to', 't_text(100)', 't_context'));
        }
    }
}
