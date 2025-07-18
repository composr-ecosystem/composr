<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.


 NOTE TO PROGRAMMERS:
   Do not edit this file. If you need to make changes, save your changed file to the appropriate *_custom folder
   **** If you ignore this advice, then your website upgrades (e.g. for bug fixes) will likely kill your changes ****

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    core_cns
 */

/**
 * Hook class.
 */
class Hook_admin_stats_cns_demographics
{
    /**
     * Define stats screens implemented in this hook.
     *
     * @return ?array List of screens, each being a map written as do-next icon specification style (null: hook is disabled).
     */
    public function info()
    {
        if (get_forum_type() != 'cns') {
            return null;
        }

        require_lang('stats');

        return array(
            array('demographics' => array('DEMOGRAPHICS', 'menu/adminzone/audit/statistics/demographics'),),
            array('menu/adminzone/audit/statistics/demographics', array('_SELF', array('type' => 'demographics'), '_SELF'), do_lang('DEMOGRAPHICS'), 'DESCRIPTION_DEMOGRAPHICS'),
        );
    }

    /**
     * The UI to show Conversr demographics.
     *
     * @param  object $ob The stats module object
     * @param  string $type The screen type
     * @return Tempcode The UI
     */
    public function demographics($ob, $type)
    {
        require_lang('cns');

        //This will show a plain bar chart with all the downloads listed
        $title = get_screen_title('DEMOGRAPHICS');

        // Handle time range
        if (get_param_integer('dated', 0) == 0) {
            $title = get_screen_title('DEMOGRAPHICS');

            return $ob->get_between($title, false, null, do_lang_tempcode('DEMOGRAPHICS_STATS_RANGE'));
        }
        $time_start = post_param_date('time_start', true);
        $time_end = post_param_date('time_end', true);
        if (!is_null($time_end)) {
            $time_end += 60 * 60 * 24 - 1; // So it is end of day not start
        }

        if ((is_null($time_start)) && (is_null($time_end))) {
            $rows = $GLOBALS['FORUM_DB']->query_select('f_members', array('m_dob_year', 'COUNT(*) AS cnt', null, 'GROUP BY m_dob_year'));
        } else {
            if (is_null($time_start)) {
                $time_start = 0;
            }
            if (is_null($time_end)) {
                $time_end = time();
            }

            $title = get_screen_title('SECTION_DEMOGRAPHICS_RANGE', true, array(escape_html(get_timezoned_date($time_start, false)), escape_html(get_timezoned_date($time_end, false))));

            $rows = $GLOBALS['FORUM_DB']->query('SELECT m_dob_year,COUNT(*) AS cnt FROM ' . $GLOBALS['FORUM_DB']->get_table_prefix() . 'f_members WHERE m_join_time>' . strval($time_start) . ' AND m_join_time<' . strval($time_end) . ' GROUP BY m_dob_year');
        }

        if (count($rows) < 1) {
            return warn_screen($title, do_lang_tempcode('NO_DATA'));
        }

        // Gather data
        $demographics = array();
        $demographics[do_lang('UNKNOWN')] = 0;
        for ($i = 0; $i < 30; $i++) {
            $demographics[strval($i)] = 0;
        }
        for ($i = 30; $i < 100; $i += 5) {
            $demographics[strval($i) . '-' . strval($i + 4)] = 0;
        }
        $demographics['100+'] = 0;
        list($current_day, $current_month, $current_year) = explode(' ', date('j m Y', utctime_to_usertime(time())));
        foreach ($rows as $i => $row) {
            $day = 1;
            $month = 1;
            $year = $row['m_dob_year'];
            if (!is_null($year)) {
                $age = intval($current_year) - $year;
                if ($age < 0) {
                    $age = 0;
                }

                if ($age >= 100) {
                    $age_string = '100+';
                } elseif ($age >= 30) {
                    $age_string = strval(intval($age / 5) * 5) . '-' . strval(intval($age / 5) * 5 + 4);
                } else {
                    $age_string = strval($age);
                }

                $demographics[$age_string] += array_key_exists('cnt', $row) ? $row['cnt'] : 1;
            } else {
                $demographics[do_lang('UNKNOWN')] += array_key_exists('cnt', $row) ? $row['cnt'] : 1;
            }
        }

        $start = 0;
        $max = 1000; // Little trick, as we want all to fit
        $sortables = array();

        require_code('templates_results_table');
        $fields_title = results_field_title(array(do_lang_tempcode('AGE'), do_lang_tempcode('COUNT_TOTAL')), $sortables);
        $fields = new Tempcode();
        $i = 0;
        foreach ($demographics as $_age => $value) {
            if (is_integer($_age)) {
                $_age = strval($_age);
            }
            $percent = round(100.0 * floatval($value) / floatval(count($rows)), 2);
            $fields->attach(results_entry(array($_age, integer_format($value) . ' (' . float_format($percent) . '%)'), true));
            $i++;
        }
        $list = results_table(do_lang_tempcode('DEMOGRAPHICS'), $start, 'start', $max, 'max', count($demographics), $fields_title, $fields, $sortables, '', '', 'sort', new Tempcode());

        $output = create_bar_chart($demographics, do_lang('AGE'), do_lang('COUNT_TOTAL'), '', '');
        $ob->save_graph('Global-Demographics', $output);

        $graph = do_template('STATS_GRAPH', array('_GUID' => 'ae6e151806c83a6ac6df84b645de3f69', 'GRAPH' => $ob->get_stats_url('Global-Demographics'), 'TITLE' => do_lang_tempcode('DEMOGRAPHICS'), 'TEXT' => do_lang_tempcode('DESCRIPTION_DEMOGRAPHICS')));

        $tpl = do_template('STATS_SCREEN', array('_GUID' => 'f60cb424d07969f1e74eb10bb6a5eeda', 'TITLE' => $title, 'NO_CSV' => '1', 'GRAPH' => $graph, 'STATS' => $list));

        require_code('templates_internalise_screen');
        return internalise_own_screen($tpl);
    }
}
