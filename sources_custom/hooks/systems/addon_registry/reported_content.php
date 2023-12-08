<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    reported_content
 */

/**
 * Hook class.
 */
class Hook_addon_registry_reported_content
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
        return 'Development';
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
        return 'A new module for reporting content, that can be linked into templates via setting up a URL into that module.

Eventually this addon will probably become a core part of the system, but for now it only exists in non-bundled land without any default integration. It works like the reports posts system in the forum, i.e. actually saves the reports onto the forum in the same way.

I\'ll give an example for news...

In the [tt]NEWS_ENTRY_SCREEN[/tt] template you\'ll find the action links are driven by...

[codebox]
{$,Load up the staff actions template to display staff actions uniformly (we relay our parameters to it)...}
{+START,INCLUDE,STAFF_ACTIONS}
1_URL={SUBMIT_URL*}
1_TITLE={$?,{BLOG},{!ADD_NEWS_BLOG},{!ADD_NEWS}}
1_REL=add
1_NOREDIRECT=1
1_ICON=menu/_generic_admin/add_one
2_URL={EDIT_URL*}
2_ACCESSKEY=q
2_TITLE={!EDIT_LINK}
2_REL=edit
2_ICON=menu/_generic_admin/edit_this
3_URL={NEWSLETTER_URL*}
3_TITLE={+START,IF_NON_EMPTY,{NEWSLETTER_URL}}{!newsletter:NEWSLETTER_SEND}{+END}
3_ICON=menu/site_meta/newsletters
{+END}
[/codebox]

change to:

[codebox]
{$,Load up the staff actions template to display staff actions uniformly (we relay our parameters to it)...}
{+START,INCLUDE,STAFF_ACTIONS}
1_URL={SUBMIT_URL*}
1_TITLE={$?,{BLOG},{!ADD_NEWS_BLOG},{!ADD_NEWS}}
1_REL=add
1_NOREDIRECT=1
1_ICON=menu/_generic_admin/add_one
2_URL={EDIT_URL*}
2_ACCESSKEY=q
2_TITLE={!EDIT_LINK}
2_REL=edit
2_ICON=menu/_generic_admin/edit_this
3_URL={NEWSLETTER_URL*}
3_TITLE={+START,IF_NON_EMPTY,{NEWSLETTER_URL}}{!newsletter:NEWSLETTER_SEND}{+END}
3_ICON=menu/site_meta/newsletters
4_URL={$PAGE_LINK,_SEARCH:report_content:content_type=news:content_id={ID}:url={$SELF_URL&}}
4_TITLE=Report this
4_ICON=buttons/report
{+END}
[/codebox]

So, you are probably thinking: what does [tt]_SEARCH:report_content:content_type=download:content_id={ID}:url={$SELF_URL&}[/tt] mean?
 - [tt]_SEARCH[/tt] -- look for the report_content module, wherever it is; you could put [tt]site[/tt] instead, it doesn\'t matter much
 - [tt]report_content[/tt] -- our reporting module that does the work
 - [tt]news[/tt] -- the content type; the name of a file in [tt]sources/hooks/systems/content_meta_aware[/tt]
 - [tt]{ID}[/tt] -- content ID; usually it\'s passed into a template as a parameter named \'ID\' like it is here
 - [tt]{$SELF_URL&}[/tt] -- the current URL needs to be passed through';
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
            'sources_custom/hooks/systems/addon_registry/reported_content.php',
            'lang_custom/EN/report_content.ini',
            'site/pages/modules_custom/report_content.php',
            'themes/default/templates_custom/REPORTED_CONTENT_FCOMCODE.tpl',
            'sources_custom/hooks/systems/config/reported_times.php',
        );
    }
}
