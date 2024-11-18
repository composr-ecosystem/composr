<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cms_homesite
 */

/**
 * Hook class.
 */
class Hook_endpoint_cms_homesite_personal_upgrader
{
    /**
     * Return information about this endpoint.
     *
     * @param  ?string $type Standard type parameter, usually either of add/edit/delete/view (null: not-set)
     * @param  ?string $id Standard ID parameter (null: not-set)
     * @return ?array Info about the hook (null: endpoint is disabled)
     */
    public function info(?string $type, ?string $id) : ?array
    {
        if (!addon_installed('cms_homesite')) {
            return null;
        }
        if (!addon_installed('downloads')) {
            return null;
        }
        if (!addon_installed('news')) {
            return null;
        }

        return [
            'authorization' => false,
            'log_stats_event' => 'cms_homesite/personal_upgrader',
        ];
    }

    /**
     * Run an API endpoint.
     *
     * @param  ?string $type Standard type parameter, usually either of add/edit/delete/view (null: not-set)
     * @param  ?string $id Standard ID parameter (null: not-set)
     * @return array Data structure that will be converted to correct response type
     */
    public function run(?string $type, ?string $id) : array
    {
        require_code('version2');
        require_code('cms_homesite');
        require_code('cms_homesite_make_upgrader');

        $from_version_dotted = get_param_string('from', $id); // From can be specified as ID

        $to_version_dotted = get_param_string('to', null);
        if ($to_version_dotted === null) {
            $to_version_dotted = get_latest_version_dotted();
        }

        // LEGACY: Cannot upgrade <11.alpha4 to 11.beta or higher; must first upgrade to 11.alpha4
        if (($from_version_dotted !== null) && (strpos($from_version_dotted, '11.alpha') !== false) && ($from_version_dotted != '11.alpha4') && (($to_version_dotted === null) || strpos($to_version_dotted, '11.alpha') === false)) {
            if ($id === '_LEGACY_') { // LEGACY
                warn_exit(protect_from_escaping('You need to upgrade to 11 alpha4 first before upgrading to a later release. This is because changes made in the upgrader will corrupt your site if you immediately skip 11 alpha4. Please go to https://composr.app/news/view/releases/composr-11-alpha4.htm?blog=0 (Make a Composr upgrader box) to upgrade to 11 alpha4. After upgrading fully to 11 alpha4, run the upgrader again normally, and you should be able to then upgrade to the latest release.'));
                exit();
            }
            return ['success' => false, 'error_details' => 'You need to upgrade to 11 alpha4 first before upgrading to a later release. This is because changes made in the upgrader will corrupt your site if you immediately skip 11 alpha4. Please go to https://composr.app/news/view/releases/composr-11-alpha4.htm?blog=0 (Make a Composr upgrader box) to upgrade to 11 alpha4. After upgrading fully to 11 alpha4, run the upgrader again normally, and you should be able to then upgrade to the latest release.'];
        }

        // Uh oh! We probably do not have a stable version up.
        if ($to_version_dotted === null) {
            if ($id === '_LEGACY_') { // LEGACY
                warn_exit(protect_from_escaping('Internal Error: We do not know what version you want to upgrade to, and there are no stable versions in the system (which we use as the default). Please generate an upgrade file from ' . get_brand_base_url() . ', in the news article for the newest release. You can then use this in the upgrader when transferring across new / updated files. You may have to re-load the upgrader manually by going to baseurl/upgrader.php so you do not get this error again.'));
                exit();
            }
            return ['success' => false, 'error_details' => 'Internal Error: We do not know what version you want to upgrade to, and there are no stable versions in the system (which we use as the default). Please generate an upgrade file from ' . get_brand_base_url() . ', in the news article for the newest release. You can then use this in the upgrader when transferring across new / updated files. You may have to re-load the upgrader manually by going to baseurl/upgrader.php so you do not get this error again.'];
        }

        // LEGACY
        $addon_name_remap = [
            // 9 => 10
            'cedi' => 'wiki',
            'occle' => 'commandr',
            'ocf_avatars' => 'cns_avatars',
            'ocf_cartoon_avatars' => 'cns_cartoon_avatars',
            'ocf_clubs' => 'cns_clubs',
            'ocf_contactmember' => 'cns_contact_member',
            'ocf_cpfs' => 'cns_cpfs',
            'ocf_forum' => 'cns_forum',
            'ocf_member_avatars' => 'cns_member_avatars',
            'ocf_member_photos' => 'cns_member_photos',
            'ocf_member_titles' => 'cns_member_titles',
            'ocf_multi_moderations' => 'cns_multi_moderations',
            'ocf_post_templates' => 'cns_post_templates',
            'ocf_reported_posts' => 'cns_reported_posts',
            'ocf_signatures' => 'cns_signatures',
            'ocf_thematic_avatars' => 'cns_thematic_avatars',
            'ocf_warnings' => 'cns_warnings',

            // 10 => 11
            'unvalidated' => 'validation',
            'composr_homesite' => 'cms_homesite',
            'composr_homesite_support_credits' => 'cms_homesite_support_credits',
        ];

        $addons = [];
        foreach (array_keys($_GET) as $key) {
            if (substr($key, 0, 6) == 'addon_') {
                $addon_name = substr($key, 6);

                if (isset($addon_name_remap[$addon_name])) {
                    $addon_name = $addon_name_remap[$addon_name];
                }

                $addons[$addon_name] = true;
            }
        }
        ksort($addons);

        list($tar_path, $err) = make_upgrade_get_path($from_version_dotted, $to_version_dotted, $addons);
        if ($tar_path === null) {
            warn_exit(protect_from_escaping($err));
        }

        // Note by default wget ignores these Content-Disposition filenames. You can set a custom one with '-O', or use '--content-disposition' to make it respect the one here
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: inline; filename="' . escape_header(basename($tar_path), true) . '"');

        cms_ob_end_clean();
        readfile($tar_path);

        // Custom output; we should exit to prevent traditional REST interface from continuing.
        exit();
    }
}
