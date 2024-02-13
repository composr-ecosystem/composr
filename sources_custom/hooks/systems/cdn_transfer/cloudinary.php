<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cloudinary
 */

/**
 * Hook class.
 */
class Hook_cdn_transfer_cloudinary
{
    /**
     * Find whether the hook is enabled.
     *
     * @return boolean Whether it is
     */
    public function is_enabled() : bool
    {
        if (!addon_installed('cloudinary')) {
            return false;
        }

        $cloud_name = get_option('cloudinary_cloud_name');
        $api_key = get_option('cloudinary_api_key');
        $api_secret = get_option('cloudinary_api_secret');
        if ($cloud_name == '') {
            return false;
        }
        if ($api_key == '') {
            return false;
        }
        if ($api_secret == '') {
            return false;
        }

        if (($GLOBALS['FORUM_DRIVER']->is_staff(get_member())) && (get_param_integer('keep_cloudinary', null) === 0)) {
            return false;
        }

        if ((get_option('cloudinary_test_mode') == '1') && (get_param_integer('keep_cloudinary', 0) != 1)) {
            return false;
        }

        return true;
    }

    /**
     * Converts an uploaded file into a URL, by moving it to an appropriate place on the CDN.
     *
     * @param  PATH $path The disk path of the upload. Should be a temporary path that is deleted by the calling code
     * @param  ID_TEXT $upload_folder The folder name in uploads/ where we would normally put this upload, if we weren't transferring it to the CDN
     * @param  string $filename Filename to upload with. May not be respected, depending on service implementation
     * @param  integer $obfuscate Whether to obfuscate file names so the URLs can not be guessed/derived (a OBFUSCATE_* constant)
     * @param  boolean $accept_errors Whether to accept upload errors
     * @param  string $id ID (returned by reference)
     * @return ?URLPATH URL on syndicated server (null: did not syndicate)
     */
    public function transfer_upload(string $path, string $upload_folder, string $filename, int $obfuscate = 0, bool $accept_errors = false, string &$id = null) : ?string
    {
        $dirs = explode("\n", get_option('cloudinary_transfer_directories'));
        if (!in_array($upload_folder, $dirs)) {
            return null;
        }

        // Proceed...

        destrictify();

        $cloud_name = get_option('cloudinary_cloud_name');
        $api_key = get_option('cloudinary_api_key');
        $api_secret = get_option('cloudinary_api_secret');

        require_code('Cloudinary/autoload');

        \Cloudinary::config([
            'cloud_name' => $cloud_name,
            'api_key' => $api_key,
            'api_secret' => $api_secret,
        ]);

        $tags = [
            $GLOBALS['FORUM_DRIVER']->get_username(get_member()),
            get_site_name(),
            get_zone_name(),
            get_page_name(),
        ];

        $options = [
            'resource_type' => 'auto',
            'tags' => $tags,
            'angle' => 'exif',
        ];

        if ($obfuscate != 0) {
            $options['public_id'] = $upload_folder . '/' . preg_replace('#\.[^\.]*$#', '', $filename);
        } else {
            $options['use_filename'] = true;
            $options['unique_filename'] = true;
        }

        try {
            $result = \Cloudinary\Uploader::upload(
                $path,
                $options
            );
        } catch (Exception $e) {
            $errormsg = 'Cloudinary: ' . $e->getMessage();

            require_code('failure');
            cms_error_log($errormsg, 'error_occurred_api');

            if ($accept_errors) {
                attach_message($errormsg, 'warn');
                return false;
            }
            warn_exit($errormsg);
        }

        if (strpos(get_base_url(), 'https://') === false) {
            $url = $result['url'];
        } else {
            $url = $result['secure_url'];
        }

        if (is_image($filename, IMAGE_CRITERIA_WEBSAFE)) {
            // 1024 version
            $url = preg_replace('#^(.*/image/upload/)(.*)$#', '$1c_limit,w_1024/$2', $url);
        }

        $id = $result['public_id'];

        restrictify();

        return $url;
    }

    /**
     * Delete an upload. Currently just used by the unit test to clean up.
     *
     * @param  string $id ID number
     */
    public function delete_image_upload(string $id)
    {
        try {
            $result = \Cloudinary\Uploader::destroy(
                $id,
                ['resource_type' => 'image']
            );
        } catch (Exception $e) {
            warn_exit($e->getMessage());
        }
    }

    // IDEA: #3829 Support deletion properly. This is hard though, as we would need to track upload ownership somewhere or uniqueness (else temporary URL "uploads" could be used as a vector to hijack other people's original uploads).
}
