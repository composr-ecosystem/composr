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
class Hook_startup_cms_homesite__for_outdated_version
{
    public function run()
    {
        if (!addon_installed('cms_homesite')) {
            return;
        }

        if (!addon_installed('downloads')) {
            return;
        }

        // Viewing outdated docs?
        $zone_name = get_zone_name();
        if (get_param_integer('keep_old_docs_test', 0) == 1 && $GLOBALS['DEV_MODE']) {
            $zone_name = 'docs10'; // LEGACY
        }
        $matches = [];
        if (preg_match('#^docs(\d+)$#', $zone_name, $matches) != 0) {
            $docs_viewed_for_number = intval($matches[1]);

            require_code('cms_homesite');

            $latest_number = intval(get_latest_version_basis_number());
            if (($latest_number !== null) && ($latest_number > $docs_viewed_for_number) && (file_exists(get_custom_file_base() . '/docs' . strval($latest_number)))) {
                $message = 'The latest version of Composr CMS is ' . strval($latest_number) . ', but you are viewing archived documentation <em>text</em> for version ' . strval($docs_viewed_for_number) . '.';
                $message .= '<br />Be aware that old documentation is not fully maintained. Screenshots may be wrong/missing, links may become broken, etc.';
                $message .= '<br />We recommend that if you are actively working on your site, that you upgrade. Holding off upgrading too long is a security risk.';
                attach_message(protect_from_escaping($message), 'notice');
            }
        }

        // Viewing outdated addons?
        if (get_page_name() == 'downloads') {
            $type = get_param_string('type', 'browse');
            $id = get_param_integer('id', db_get_first_id());
            $cat_id = null;
            if ($type == 'browse') {
                $cat_id = $id;
            }
            if ($type == 'entry') {
                $cat_id = $GLOBALS['SITE_DB']->query_select_value_if_there('download_downloads', 'category_id', ['id' => $id]);
            }
            if ($cat_id !== null) {
                require_code('cms_homesite');

                $in_bad_cat = false;
                $latest_number = get_latest_version_basis_number();
                $addons_viewed_for_number = null;

                while (($cat_id !== null) && ($latest_number !== null)) {
                    $cat_details = $GLOBALS['SITE_DB']->query_select('download_categories', ['category', 'parent_id'], ['id' => $cat_id], '', 1);
                    if (array_key_exists(0, $cat_details)) {
                        $cat_detail = $cat_details[0];
                        $cat = get_translated_text($cat_detail['category']);

                        if (get_param_integer('keep_old_addons_test', 0) == 1 && $GLOBALS['DEV_MODE']) {
                            $cat = 'Version 8';
                        }

                        if ($cat == 'Addons' || $cat == 'Composr Releases') { // We know these are under root, no need to recurse further
                            break;
                        }

                        if ((preg_match('#^Version ([\d\.]+)$#', $cat, $matches) != 0) && (get_translated_text($GLOBALS['SITE_DB']->query_select_value('download_categories', 'category', ['id' => $cat_detail['parent_id']])) == 'Addons')) {
                            $addons_viewed_for_dotted = get_version_dotted__from_anything($matches[1]);
                            list(, , , , $_addons_viewed_for_number) = get_version_components__from_dotted($addons_viewed_for_dotted);
                            $addons_viewed_for_number = intval($_addons_viewed_for_number);
                            if ($latest_number > $addons_viewed_for_number) {
                                $in_bad_cat = true;
                                break;
                            }
                        }

                        $cat_id = $cat_detail['parent_id'];
                    } else {
                        break;
                    }
                }

                if ($in_bad_cat) {
                    $message = 'The latest version of Composr CMS is ' . strval($latest_number) . ', but you are viewing archived addons for version ' . strval($addons_viewed_for_number) . '.';
                    $message .= '<br />Be aware that old non-bundled addons are not fully maintained. Screenshots may be wrong/missing, etc.';
                    $message .= '<br />We recommend that if you are actively working on your site, that you upgrade. Holding off upgrading too long is a security risk.';
                    attach_message(protect_from_escaping($message), 'notice');
                }
            }
        }
    }
}
