<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    photo_verification
 */

/**
 * Hook class.
 */
class Hook_addon_registry_photo_verification
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
     * Get the minimum required version of the website software needed to use this addon.
     *
     * @return float Minimum required website software version
     */
    public function get_min_cms_version() : float
    {
        return 11.0;
    }

    /**
     * Get the maximum compatible version of the website software to use this addon.
     *
     * @return ?float Maximum compatible website software version (null: no maximum version currently)
     */
    public function get_max_cms_version() : ?float
    {
        return null;
    }

    /**
     * Get the addon category.
     *
     * @return string The category
     */
    public function get_category() : string
    {
        return 'Development';
    }

    /**
     * Get the addon author.
     *
     * @return string The author
     */
    public function get_author() : string
    {
        return 'Patrick Schmalstig';
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
        return 'GNU General Public License v3.0';
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description() : string
    {
        return 'This addon provides a simple tool for collecting photo ID verification in cases where a site may be providing adult content. The addon provides a simple form where members can upload a photo which is then sent to staff in a special Support Ticket. The process involves the following:
        [list="1"]
        [*]The member goes to the module page containing the form and instructions.
        [*]The form generates and presents a random string of characters along with a photo upload field.
        [*]The member is instructed to take a picture of themselves holding up a valid ID and a handwritten paper containing their username and the provided random string.
        [*]The submission is sent to staff as a Support Ticket; the software also logs the random string in the support ticket and the action log as proof of a genuine submission.
        [*]Staff can then manually verify the member\'s ID / age and grant them access to restricted content as necessary.
        [/list]

        After installing this addon, the following pages will be available:
        [list]
        [*]The verification form ([tt]site:photo_verification[/tt])
        [*]Instructions for verifying the authenticity of a verification request ([tt]adminzone:photo_verification[/tt])
        [/list]

        Additionally, a new special Support Ticket type will be created for verification requests: "Verification request".

        Note that this addon does not do any automatic restrictions; this is just a tool to allow for the uploading and checking of verification photos. Normally, restricted content would not be granted to members by default. And staff would create a special group to be manually assigned to members after being verified they are of legal age. And that group would grant them access to the restricted content.';
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
            'requires' => ['tickets'],
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
            'adminzone/pages/comcode_custom/EN/photo_verification.txt',
            'lang_custom/EN/photo_verification.ini',
            'site/pages/comcode_custom/EN/_photo_verification.txt',
            'site/pages/modules_custom/photo_verification.php',
            'sources_custom/hooks/systems/actionlog/photo_verification.php',
            'sources_custom/hooks/systems/addon_registry/photo_verification.php',
            'uploads/verification/index.html',
        ];
    }
}
