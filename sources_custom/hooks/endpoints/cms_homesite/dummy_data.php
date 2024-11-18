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
class Hook_endpoint_cms_homesite_dummy_data
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

        return [
            'authorization' => ['maintenance_password'],
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
        // Set the size of the dummy data in bytes (32 MB)
        $size = 32 * 1024 * 1024;

        // Generate the dummy data
        $dummy_data = str_repeat('A', $size);

        // Send the appropriate headers
        header('Content-Type: application/octet-stream');
        header('Content-Length: ' . strval($size));
        header('Content-Disposition: attachment; filename="dummy_data.txt"');

        // Output the dummy data
        echo $dummy_data;

        // Non-standard output; we must terminate
        exit();
    }
}
