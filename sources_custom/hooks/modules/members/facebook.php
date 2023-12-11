<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    facebook_support
 */

/**
 * Hook class.
 */
class Hook_members_facebook
{
    /**
     * Get sections to inject to about tab of the member profile.
     *
     * @param  MEMBER $member_id The ID of the member we are getting sections for
     * @return array List of sections. Each tuple is Tempcode.
     */
    public function get_sections($member_id)
    {
        return array(do_template('MEMBER_FACEBOOK', array('_GUID' => '233c4cf6852e67fd2687dadb2ddff4c1', 'MEMBER_ID' => strval($member_id))));
    }
}
