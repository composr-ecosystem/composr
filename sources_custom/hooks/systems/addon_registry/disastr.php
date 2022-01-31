<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    disastr
 */

/**
 * Hook class.
 */
class Hook_addon_registry_disastr
{
    /**
     * Get a list of file permissions to set
     *
     * @param  boolean $runtime Whether to include wildcards represented runtime-created chmoddable files
     * @return array File permissions to set
     */
    public function get_chmod_array($runtime = false)
    {
        return array(
            'uploads/disastr_addon',
        );
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
        return 'Kamen Blaginov';
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
        return 'Encourage your website users to interact more and increase their activity. You can release a number of diseases all at once or one at a time. Disastr comes configured with a number of precreated viruses and you can add more. There are also Cures and Immunizations for the diseases which can be bought through the point store. Each disease will cause a member\'s points total to become sick and start going down unless they buy the cure. The cure is usually twice as costly as the immunisation. If the user cannot afford the cure they will have to interact more with the site to rebuild up their points total to be able to afford to buy it. All the preconfigured diseases come unreleased and you have the opportunity to choose when they are released and how virulent they are. Users which have been infected will be sent a notification with a link to the cure. Once cured, members can still be re-infected if they have not bought an Immunization. The diseases are spread via the friend lists in Composr.

To configure the diseases go to Admin Zone > Setup > Manage Diseases.';
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
                'Cron',
                'Conversr',
                'points',
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
        return 'themes/default/images_custom/icons/48x48/menu/disastr_log.png';
    }

    /**
     * Get a list of files that belong to this addon
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return array(
            'themes/default/images_custom/icons/24x24/menu/disastr_log.png',
            'themes/default/images_custom/icons/48x48/menu/disastr_log.png',
            'sources_custom/hooks/systems/addon_registry/disastr.php',
            'sources_custom/hooks/systems/notifications/got_disease.php',
            'adminzone/pages/modules_custom/admin_disastr.php',
            'lang_custom/EN/disastr.ini',
            'sources_custom/hooks/modules/pointstore/disastr.php',
            'sources_custom/hooks/systems/cron/disastr.php',
            'sources_custom/hooks/systems/page_groupings/disastr.php',
            'themes/default/templates_custom/POINTSTORE_DISASTR.tpl',
            'themes/default/templates_custom/POINTSTORE_DISASTR_DISEASES.tpl',
            'uploads/disastr_addon/index.html',
            'data_custom/images/disastr/hazard.jpg', // LEGACY
            'data_custom/images/disastr/hazard.png',
            'data_custom/images/disastr/index.html',
        );
    }
}
