<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
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
    public function is_enabled()
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
     * @param  integer $obfuscate Whether to obfuscate file names so the URLs can not be guessed/derived (0=do not, 1=do, 2=make extension .bin as well)
     * @set 0 1 2
     * @param  boolean $accept_errors Whether to accept upload errors
     * @return ?URLPATH URL on syndicated server (null: did not syndicate)
     */
    public function transfer_upload($path, $upload_folder, $filename, $obfuscate = 0, $accept_errors = false)
    {
        $cloud_name = get_option('cloudinary_cloud_name');
        $api_key = get_option('cloudinary_api_key');
        $api_secret = get_option('cloudinary_api_secret');

        $dirs = explode("\n", get_option('cloudinary_transfer_directories'));
        if (!in_array($upload_folder, $dirs)) {
            return null;
        }

        // Proceed...

        cms_ini_set('ocproducts.type_strictness', '0');

        require_code('Cloudinary/Cloudinary');

        return cloudinary_transfer_upload($path, $upload_folder, $filename, $obfuscate, $accept_errors);
    }

    // IDEA: #3829 Support deletion. This is hard though, as we would need to track upload ownership somewhere or uniqueness (else temporary URL "uploads" could be used as a vector to hijack other people's original uploads).
}
