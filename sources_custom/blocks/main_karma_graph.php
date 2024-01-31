<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    karma
 */

/**
 * Block class.
 */
class Block_main_karma_graph
{
    /**
     * Find details of the block.
     *
     * @return ?array Map of block info (null: block is disabled)
     */
    public function info() : ?array
    {
        $info = [];
        $info['author'] = 'Patrick Schmalstig';
        $info['organisation'] = 'PDStig, LLC';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 1;
        $info['locked'] = false;
        $info['parameters'] = ['param'];
        $info['addon'] = 'karma';
        $info['min_cms_version'] = 11.0;
        return $info;
    }

    public function caching_environment()
     {
         $info = [];
         $info['cache_on'] = <<<'PHP'
         [
            get_member(),
            isset($map['param']) ? $map['param'] : null,
         ]
         PHP;
         $info['ttl'] = 5;
         return $info;
     }

    /**
     * Execute the block.
     *
     * @param  array $map A map of parameters
     * @return Tempcode The result of execution
     */
    public function run(array $map) : object
    {
        i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

        $error_msg = new Tempcode();
        if (!addon_installed__messaged('karma', $error_msg)) {
            return $error_msg;
        }

        // Options...

        if (@cms_empty_safe($map['param'])) {
            $member_id = get_member();
        } else {
            $member_id = intval($map['param']);
        }

        // No karma for guests
        if (is_guest($member_id)) {
            return new Tempcode();
        }

        // Privilege check...

        if (($member_id != get_member()) && !has_privilege(get_member(), 'view_others_karma')) {
            return new Tempcode();
        }
        $can_view_bad_karma = has_privilege(get_member(), 'view_bad_karma');

        // Prepare the block...

        require_lang('karma');
        require_code('karma');

        $block_id = get_block_id($map);

        $username = $GLOBALS['FORUM_DRIVER']->get_username($member_id, true);

        $threshold = intval(get_option('karma_threshold'));
        $karma = get_karma($member_id);
        $total_karma = $karma[0] - $karma[1];
        $karma_large_percent = ($threshold > 0) ? floatval(max($karma[0], $karma[1]) / $threshold) : 1.0;
        $large_is_bad = ($total_karma < 0);

        // If we cannot view bad karma, then treat as full bad karma
        if (!$can_view_bad_karma && $large_is_bad) {
            $karma_large_percent = 1.0;
        }

        if ($karma_large_percent > 1.0) {
            $karma_large_percent = 1.0;
        }

        if ($can_view_bad_karma) {
            if ($large_is_bad) {
                $karma_small_percent = $karma[1] > 0 ? ($karma[0] / $karma[1]) : 1.0;
                if ($karma[0] <= 0) {
                    $karma_small_percent = 0.0;
                }
            } else {
                $karma_small_percent = $karma[0] > 0 ? ($karma[1] / $karma[0]) : 1.0;
                if ($karma[1] <= 0) {
                    $karma_small_percent = 0.0;
                }
            }

            $karma_lang = do_lang_tempcode('_HAS_KARMA', $username, do_lang_tempcode('GOOD_BAD_KARMA', integer_format($karma[0]), integer_format($karma[1])));
        } else {
            $karma_large_percent = ($threshold > 0) ? floatval($total_karma / $threshold) : 1.0;

            // When we cannot view bad karma, if bad > good, then large is bad with a full red bar.
            if ($karma_large_percent < 0.0) {
                $karma_large_percent = 1.0;
                $large_is_bad = true;
            }
            $karma_small_percent = 0.0;

            $karma_lang = do_lang_tempcode('_HAS_KARMA', $username, do_lang_tempcode('TOTAL_KARMA', integer_format($total_karma)));
        }

        $karma_small_percent *= $karma_large_percent;

        return do_template('BLOCK_MAIN_KARMA_GRAPH', [
            'BLOCK_ID' => $block_id,
            'BLOCK_PARAMS' => comma_list_arr_to_str(['block_id' => $block_id] + $map),
            'KARMA_TITLE' => $karma_lang,
            'KARMA_LARGE' => float_to_raw_string($karma_large_percent * 100.0),
            'KARMA_SMALL' => float_to_raw_string($karma_small_percent * 100.0),
            'LARGE_IS_BAD' => $large_is_bad,
        ]);
    }
}
