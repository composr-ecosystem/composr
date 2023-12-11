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
class Hook_pointstore_disastr
{
    /**
     * Standard pointstore item initialisation function.
     */
    public function init()
    {
        require_lang('disastr');
    }

    /**
     * Standard pointstore item initialisation function.
     *
     * @return array The "shop fronts"
     */
    public function info()
    {
        if (!$GLOBALS['SITE_DB']->table_exists('members_diseases')) {
            return array();
        }

        $class = str_replace('hook_pointstore_', '', strtolower(get_class($this)));

        $next_url = build_url(array('page' => '_SELF', 'type' => 'action', 'id' => $class), '_SELF');
        return array(do_template('POINTSTORE_' . strtoupper($class), array('NEXT_URL' => $next_url)));
    }

    /**
     * Standard interface stage of pointstore item purchase.
     *
     * @return Tempcode The UI
     */
    public function action()
    {
        require_code('form_templates');

        $class = str_replace('hook_pointstore_', '', strtolower(get_class($this)));

        $title = get_screen_title('DISEASES_CURES_IMMUNIZATIONS_TITLE');

        $fields = '<div class="wide_table_wrap"><table class="columned_table wide_table results_table"><thead><tr><th colspan="2">Disease</th><th width="33%">Cure</th><th width="33%">Immunisation</th></tr></thead><tbody>';

        $cnt = 0;

        $member_id = get_member();
        $rows = $GLOBALS['SITE_DB']->query_select('diseases', array('*'), null, 'ORDER BY name');
        foreach ($rows as $disease) {
            $_cure_url = build_url(array('page' => 'pointstore', 'type' => 'action_done', 'id' => 'disastr', 'disease' => $disease['id'], 'cure' => 1), '_SEARCH');
            $cure_url = $_cure_url->evaluate();

            $_immunization_url = build_url(array('page' => 'pointstore', 'type' => 'action_done', 'id' => 'disastr', 'disease' => $disease['id'], 'immunization' => 1), '_SEARCH');
            $immunization_url = $_immunization_url->evaluate();

            $member_rows = $GLOBALS['SITE_DB']->query_select('members_diseases', array('*'), array('member_id' => $member_id, 'disease_id' => $disease['id']), '', 1);

            $get_cure = true;
            $get_immunization = true;

            if (isset($member_rows[0]['sick']) && $member_rows[0]['sick'] == 1 && $member_rows[0]['cure'] == 0) {
                //member already infected
                $get_cure = true;
                $get_immunization = false;
            } elseif (isset($member_rows[0]['sick']) && $member_rows[0]['sick'] == 0 && $member_rows[0]['immunisation'] == 0) {
                //member listed but not infected and not immunised
                $get_cure = false;
                $get_immunization = true;
            } elseif (!isset($member_rows[0])) {
                //member not listed (so not infected and not immunised)
                $get_cure = false;
                $get_immunization = true;
            } elseif (isset($member_rows[0]['sick']) && ($member_rows[0]['cure'] == 1 || $member_rows[0]['immunisation'] == 1)) {
                //skip this disease - because user has been immunised or has been cured
                $get_cure = false;
                $get_immunization = false;
            }

            if ($get_immunization || $get_cure) {
                $fields .= '
                <tr style="border: 1px solid #ccc; background-color: #D4E0F1;">
                    <td width="45"><img width="45" src="' . escape_html(get_custom_base_url() . '/' . $disease['image']) . '" /></td>
                    <td>' . escape_html($disease['name']) . '</td>
                    <td width="33%">' . ($get_cure ? ('<a href="' . escape_html($cure_url) . '">' . escape_html($disease['cure']) . '</a> costs ' . escape_html(integer_format($disease['cure_price'])) . ' points') : (do_lang('NA_EM') . ' (you\'re not infected)')) . '</td>
                    <td width="33%">' . (!$get_cure ? ('<a href="' . escape_html($immunization_url) . '">' . escape_html($disease['immunisation']) . '</a> costs ' . escape_html(integer_format($disease['immunisation_price'])) . ' points') : (do_lang('NA_EM') . ' (you\'re already infected)')) . '</td>
                </tr>';

                $cnt++;
            }
        }

        if ($cnt == 0) {
            $fields .= '<tr><td colspan="4">' . do_lang('NO_DISASTR_ENTRIES_TO_DISPLAY') . '</td></tr>';
        }
        $fields .= '</tbody></table></div>';

        return do_template('POINTSTORE_DISASTR_DISEASES', array('_GUID' => 'fbbe019ec60abf82b585618ec7f1453c', 'TITLE' => $title, 'FIELDS' => $fields));
    }

    /**
     * Standard actualisation stage of pointstore item purchase.
     *
     * @return Tempcode The UI
     */
    public function action_done()
    {
        $class = str_replace('hook_pointstore_', '', strtolower(get_class($this)));

        $disease_id = get_param_string('disease', 0);
        $member_id = get_member();

        $get_cure = get_param_integer('cure', 0);
        $get_immunization = get_param_integer('immunization', 0);

        $member_rows = $GLOBALS['SITE_DB']->query_select('members_diseases', array('*'), array('member_id' => $member_id, 'disease_id' => $disease_id), '', 1);

        $rows = $GLOBALS['SITE_DB']->query_select('diseases', array('*'), array('id' => $disease_id), '', 1);
        if (!array_key_exists(0, $rows)) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
        }

        $cure_price = $rows[0]['cure_price'];
        $immunization_price = $rows[0]['immunisation_price'];
        $amount = ($get_immunization == 1) ? $immunization_price : $cure_price;

        $title = get_screen_title('DISEASES_CURES_IMMUNIZATIONS_TITLE');

        // Check points
        $points_left = available_points(get_member());
        if (!has_privilege(get_member(), 'give_points_self')) {
            if ($points_left < $amount) {
                return warn_screen($title, do_lang_tempcode('_CANT_AFFORD_THIS'));
            }
        }

        // Actuate
        require_code('points2');
        if ($get_immunization == 1) {
            charge_member(get_member(), $amount, do_lang('IMMUNIZATION_PURCHASED'));
        } else {
            charge_member(get_member(), $amount, do_lang('CURE_PURCHASED'));
        }

        if (!array_key_exists(0, $member_rows)) {
            $GLOBALS['SITE_DB']->query_insert('members_diseases', array('member_id' => $member_id, 'disease_id' => $disease_id, 'sick' => 0, 'cure' => $get_cure, 'immunisation' => $get_immunization));
        } else {
            $GLOBALS['SITE_DB']->query_update('members_diseases', array('member_id' => $member_id, 'disease_id' => $disease_id, 'sick' => 0, 'cure' => $get_cure, 'immunisation' => $get_immunization), array('member_id' => $member_id, 'disease_id' => $disease_id), '', 1);
        }

        if ($get_immunization == 1) {
            // Show message
            $result = do_lang_tempcode('IMMUNIZATION_CONGRATULATIONS');
        } else {
            // Show message
            $result = do_lang_tempcode('CURE_CONGRATULATIONS');
        }

        $url = build_url(array('page' => '_SELF', 'type' => 'browse'), '_SELF');
        return redirect_screen($title, $url, $result);
    }
}
