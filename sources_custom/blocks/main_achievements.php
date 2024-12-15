<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    achievements
 */

/**
 * Block class.
 */
class Block_main_achievements
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
        $info['parameters'] = ['param', 'size'];
        $info['addon'] = 'achievements';
        $info['min_cms_version'] = 11.0;
        return $info;
    }

    /**
     * Find caching details for the block.
     *
     * @return ?array Map of cache details (cache_on and ttl) (null: block is disabled)
     */
    public function caching_environment() : ?array
    {
        $info = [];
        $info['cache_on'] = <<<'PHP'
        [
            array_key_exists('param', $map) ? $map['param'] : '',
            array_key_exists('size', $map) ? $map['size'] : '32',
        ]
PHP;
        $info['ttl'] = 60;
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
        if (!addon_installed__messaged('achievements', $error_msg)) {
            return $error_msg;
        }

        // Options...

        if (@cms_empty_safe($map['param'])) {
            $member_id = get_member();
        } else {
            $member_id = intval($map['param']);
        }

        // No achievements for guests
        if (is_guest($member_id)) {
            return new Tempcode();
        }

        // Prepare the block...
        $block_id = get_block_id($map);
        $size = isset($map['size']) ? $map['size'] : '32'; // must be a single number only representing width

        // Load the achievements for this member (we don't use cache because the block already has its own cache)
        require_code('achievements');
        require_code('urls');
        require_code('images');
        require_code('tempcode');
        require_code('temporal');

        $ob = load_achievements(false);
        $show_hidden = ($member_id == get_member()); // Members can view their own earned hidden achievements
        $data = $ob->get_unlocked_achievements($member_id, $show_hidden);

        // Process the achievements
        $achievements = [];
        foreach ($data as $name => $details) {
            // Process image
            $image = $details['image'];
            if ($image !== null) {
                if (!looks_like_url($image)) {
                    $_image = get_custom_file_base() . '/' . $image;
                    if (!is_image($_image, IMAGE_CRITERIA_WEBSAFE)) {
                        $image = find_theme_image($image, true);
                    }
                } elseif (!is_image($image, IMAGE_CRITERIA_WEBSAFE)) {
                    $image = '';
                }
            } else {
                $image = '';
            }
            $image_thumb = new Tempcode();
            if ($image != '') {
                $image_thumb = symbol_tempcode('THUMBNAIL', [$image, $size]);
            }


            $date_and_time = get_timezoned_date_time($details['date_and_time'], false);

            $achievements[] = [
                'ACHIEVEMENT_NAME' => $name,
                'ACHIEVEMENT_TITLE' => $details['title'],
                'ACHIEVEMENT_IMAGE' => $image_thumb,
                'ACHIEVEMENT_DATE_AND_TIME' => $date_and_time,
            ];
        }

        return do_template('BLOCK_MAIN_ACHIEVEMENTS', [
            '_GUID' => '7edde46b9c1e5019be5b803f16b774ca',
            'BLOCK_ID' => $block_id,
            'BLOCK_PARAMS' => comma_list_arr_to_str(['block_id' => $block_id] + $map),

            'ACHIEVEMENTS' => $achievements,
            'IMAGE_SIZE' => $size, // In case we are using an SVG
        ]);
    }
}
