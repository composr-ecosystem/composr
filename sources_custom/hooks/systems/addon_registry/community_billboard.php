<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    community_billboard
 */

/**
 * Hook class.
 */
class Hook_addon_registry_community_billboard
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
        return 'Fun and Games';
    }

    /**
     * Get the addon author.
     *
     * @return string The author
     */
    public function get_author()
    {
        return 'Chris Graham';
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
        return 'Community billboard messages, designed to work with eCommerce to allow people buy community billboard advertising on the website.';
    }

    /**
     * Get a list of tutorials that apply to this addon.
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials()
    {
        return [
            'tut_points',
        ];
    }

    /**
     * Get a mapping of dependency types.
     *
     * @return array File permissions to set
     */
    public function get_dependencies()
    {
        return [
            'requires' => [
                'ecommerce',
            ],
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
        return 'themes/default/images_custom/icons/menu/adminzone/audit/community_billboard.svg';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return [
            'sources_custom/hooks/systems/privacy/community_billboard.php',
            'themes/default/images_custom/icons/menu/adminzone/audit/community_billboard.svg',
            'themes/default/images_custom/icons/menu/adminzone/audit/index.html',
            'themes/default/images_custom/icons/menu/adminzone/index.html',
            'themes/default/images_custom/icons/menu/index.html',
            'themes/default/images_custom/icons_monochrome/menu/adminzone/audit/community_billboard.svg',
            'themes/default/images_custom/icons_monochrome/menu/adminzone/audit/index.html',
            'themes/default/images_custom/icons_monochrome/menu/adminzone/index.html',
            'themes/default/images_custom/icons_monochrome/menu/index.html',
            'themes/default/templates_custom/COMMUNITY_BILLBOARD_DETAILS.tpl',
            'themes/default/templates_custom/COMMUNITY_BILLBOARD_STORE_LIST_LINE.tpl',
            'themes/default/templates_custom/ECOM_PRODUCT_COMMUNITY_BILLBOARD.tpl',
            'adminzone/pages/modules_custom/admin_community_billboard.php',
            'lang_custom/EN/community_billboard.ini',
            'sources_custom/hooks/systems/config/system_community_billboard.php',
            'sources_custom/hooks/systems/addon_registry/community_billboard.php',
            'sources_custom/hooks/modules/admin_import_types/community_billboard.php',
            'sources_custom/hooks/systems/symbols/COMMUNITY_BILLBOARD.php',
            'sources_custom/community_billboard.php',
            'sources_custom/hooks/blocks/main_staff_checklist/community_billboard.php',
            'sources_custom/hooks/systems/ecommerce/community_billboard.php',
            'sources_custom/hooks/systems/config/community_billboard_price.php',
            'sources_custom/hooks/systems/config/community_billboard_tax_code.php',
            'sources_custom/hooks/systems/config/community_billboard_price_points.php',
            'sources_custom/hooks/systems/config/is_on_community_billboard_buy.php',
            'sources_custom/hooks/systems/notifications/ecom_product_request_community_billboard.php',
            'sources_custom/hooks/systems/page_groupings/community_billboard.php',
            'themes/default/css_custom/community_billboard.css',
            'sources_custom/hooks/systems/actionlog/community_billboard.php',
        ];
    }

    /**
     * Get mapping between template names and the method of this class that can render a preview of them.
     *
     * @return array The mapping
     */
    public function tpl_previews()
    {
        return [
            'templates/COMMUNITY_BILLBOARD_DETAILS.tpl' => 'administrative__community_billboard_manage_screen',
            'templates/COMMUNITY_BILLBOARD_STORE_LIST_LINE.tpl' => 'administrative__community_billboard_manage_screen',
            'templates/ECOM_PRODUCT_COMMUNITY_BILLBOARD.tpl' => 'community_billboard_screen',
        ];
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declarative.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__community_billboard_screen()
    {
        return [
            lorem_globalise(do_lorem_template('ECOM_PRODUCT_COMMUNITY_BILLBOARD', [
                'QUEUE' => placeholder_number(),
                'DAYS' => placeholder_number(),
            ]), null, '', true),
        ];
    }

    /**
     * Get a preview(s) of a (group of) template(s), as a full standalone piece of HTML in Tempcode format.
     * Uses sources/lorem.php functions to place appropriate stock-text. Should not hard-code things, as the code is intended to be declarative.
     * Assumptions: You can assume all Lang/CSS/JavaScript files in this addon have been pre-required.
     *
     * @return array Array of previews, each is Tempcode. Normally we have just one preview, but occasionally it is good to test templates are flexible (e.g. if they use IF_EMPTY, we can test with and without blank data).
     */
    public function tpl_preview__administrative__community_billboard_manage_screen()
    {
        require_css('forms');

        $about_current = do_lorem_template('COMMUNITY_BILLBOARD_DETAILS', [
            'USERNAME' => lorem_word(),
            'DAYS_ORDERED' => lorem_phrase(),
            'DATE_RAW' => placeholder_date_raw(),
            'DATE' => placeholder_date(),
        ]);

        $out = new Tempcode();
        foreach (placeholder_array() as $key => $value) {
            $text = do_lorem_template('COMMUNITY_BILLBOARD_STORE_LIST_LINE', [
                'MESSAGE' => $value,
                'STATUS' => do_lang('NEW'),
            ]);
            $out->attach(do_lorem_template('FORM_SCREEN_INPUT_LIST_ENTRY', [
                'SELECTED' => false,
                'DISABLED' => false,
                'CLASS' => '',
                'NAME' => strval($key),
                'TEXT' => $text->evaluate(),
            ]));
        }
        $name = placeholder_id();
        $input = do_lorem_template('FORM_SCREEN_INPUT_LIST', [
            'TABINDEX' => '5',
            'REQUIRED' => '-required',
            'NAME' => $name,
            'CONTENT' => $out,
            'INLINE_LIST' => true,
            'SIZE' => '9',
        ]);
        $fields = do_lorem_template('FORM_SCREEN_FIELD', [
            'REQUIRED' => true,
            'SKIP_LABEL' => false,
            'PRETTY_NAME' => lorem_word(),
            'NAME' => $name,
            'DESCRIPTION' => lorem_sentence_html(),
            'DESCRIPTION_SIDE' => '',
            'INPUT' => $input,
            'COMCODE' => '',
        ]);

        return [
            lorem_globalise(do_lorem_template('FORM_SCREEN', [
                'TITLE' => lorem_title(),
                'TEXT' => $about_current,
                'HIDDEN' => '',
                'URL' => placeholder_url(),
                'GET' => true,
                'FIELDS' => $fields,
                'SUBMIT_ICON' => 'buttons/proceed',
                'SUBMIT_NAME' => lorem_word(),
                'SKIP_WEBSTANDARDS' => false,
                'SUPPORT_AUTOSAVE' => false,
                'JS_FUNCTION_CALLS' => [],
                'MODSECURITY_WORKAROUND' => false,
                'POST' => false,
                'ANALYTIC_EVENT_CATEGORY' => null,
            ]), null, '', true)
        ];
    }
}
