<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class config_lang_strings_test_set extends cms_test_case
{
    protected $id;

    public function testStrings()
    {
        $hooks = find_all_hooks('systems', 'config');
        $options = [];
        foreach (array_keys($hooks) as $hook) {
            $path = get_file_base() . '/sources/hooks/systems/config/' . filter_naughty_harsh($hook) . '.php';
            if (!file_exists($path)) {
                $path = get_file_base() . '/sources_custom/hooks/systems/config/' . filter_naughty_harsh($hook) . '.php';
            }
            $code = cms_file_get_contents_safe($path);

            require_code('hooks/systems/config/' . filter_naughty_harsh($hook));
            $ob = object_factory('Hook_config_' . filter_naughty_harsh($hook));
            $details = $ob->get_details();
            $options[$hook] = $details;

            $this->assertTrue(strpos($code, "@package    " . $details['addon']) !== false, 'Addon definition mismatch in ' . $hook);
        }
        require_all_lang();
        foreach ($options as $hook => $option) {
            $test = do_lang($option['human_name'], null, null, null, null, false);
            $this->assertFalse(($test === null), 'Could not load string: ' . $option['human_name']);

            if ($option['explanation'] === null) {
                $test = do_lang('CONFIG_GROUP_DEFAULT_DESCRIP_' . $option['group'], null, null, null, null, false);
            } else {
                if (!in_array($hook, /*Exceptions*/[
                    'forum_in_portal',
                    'newsletter_allow_ext_images',
                    'newsletter_enveloper_override',
                    'newsletter_smtp_from_address',
                    'newsletter_smtp_sockets_host',
                    'newsletter_smtp_sockets_password',
                    'newsletter_smtp_sockets_port',
                    'newsletter_smtp_sockets_use',
                    'newsletter_smtp_sockets_username',
                    'newsletter_website_email',
                ])) {
                    $this->assertTrue($option['explanation'] == 'CONFIG_OPTION_' . $hook, 'Mismatch of explanation lang string in ' . $hook . '; not necessarily a bug but likely a copy and paste error, can exception to code if intended');
                }

                $test = do_lang($option['explanation'], null, null, null, null, false);
            }
            $this->assertTrue($test !== null, 'Could not load explanation string: ' . $option['explanation']);

            $test = do_lang('CONFIG_CATEGORY_' . $option['category'], null, null, null, null, false);
            $this->assertFalse(($test === null), 'Could not load category string: CONFIG_CATEGORY_' . $option['category']);

            $test = do_lang($option['group'], null, null, null, null, false);
            $this->assertFalse(($test === null), 'Could not load group string: ' . $option['group']);
        }
    }
}
