<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    weather
 */

/**
 * Block class.
 */
class Block_side_weather
{
    /**
     * Find details of the block.
     *
     * @return ?array Map of block info (null: block is disabled)
     */
    public function info() : ?array
    {
        $info = [];
        $info['author'] = 'Manuprathap';
        $info['organisation'] = 'ocProducts';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 7;
        $info['update_require_upgrade'] = true;
        $info['locked'] = false;
        $info['min_cms_version'] = 11.0;
        $info['addon'] = 'weather';
        $info['parameters'] = ['param', 'units', 'max_days', 'api'];
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
            (array_key_exists('units', $map) && ($map['units'] != '')) ? $map['units'] : 'metric',
            isset($map['max_days']) ? intval($map['max_days']) : 5,
            array_key_exists('param', $map) ? $map['param'] : '',
            (array_key_exists('api', $map) && ($map['api'] != '')) ? $map['api'] : null,
        ]
PHP;
        $info['special_cache_flags'] = CACHE_AGAINST_DEFAULT | CACHE_AGAINST_STAFF_STATUS;
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
        if (!addon_installed__messaged('weather', $error_msg)) {
            return $error_msg;
        }

        require_code('weather');

        $block_id = get_block_id($map);

        $location_search = (array_key_exists('param', $map) && ($map['param'] != '')) ? $map['param'] : '51.500833,-0.141944';
        $units = (array_key_exists('units', $map) && ($map['units'] != '')) ? $map['units'] : 'metric';
        $max_days = isset($map['max_days']) ? intval($map['max_days']) : 5;
        $api = (array_key_exists('api', $map) && ($map['api'] != '')) ? $map['api'] : null;

        $matches = [];
        if (preg_match('#^(\-?\d+(\.\d+)?),(\-?\d+(\.\d+)?)$#', $location_search, $matches) != 0) {
            $latitude = floatval($matches[1]);
            $longitude = floatval($matches[3]);
            $location_search = null;
        } else {
            $latitude = null;
            $longitude = null;
        }

        switch ($units) {
            case 'imperial':
                $temperature_units = '&#186;F';
                $precipitation_units = '"';
                $visibility_units = "'";
                $speed_units = 'mph';
                break;

            case 'metric':
            default:
                $temperature_units = '&#186;C';
                $precipitation_units = 'mm';
                $speed_units = 'kph';
                $visibility_units = 'm';
                $units = 'metric';
                break;
        }

        try {
            $result = weather_lookup($location_search, $latitude, $longitude, $units, $max_days, $api);
        } catch (Exception $e) {
            $errormsg = $e->getMessage();

            $GLOBALS['DO_NOT_CACHE_THIS'] = true;
            require_code('failure');
            cms_error_log($errormsg, 'error_occurred_api');

            if (cron_installed(true)) {
                if (!$GLOBALS['FORUM_DRIVER']->is_staff(get_member())) {
                    return new Tempcode();
                }
            }
            return do_template('INLINE_WIP_MESSAGE', ['_GUID' => '046c437a5c3799838155b5c5fbe3be26', 'MESSAGE' => htmlentities($errormsg)]);
        }

        list($current_conditions, $forecast) = $result;

        $weather_days = [];
        foreach ($forecast as $weather_day) {
            $conditions = [];
            if (isset($weather_day['conditions'])) {
                foreach ($weather_day['conditions'] as $condition) {
                    $conditions[] = [
                        'CONDITION' => $condition['description'],
                        'ICON_URL' => isset($condition['icon_url']) ? $condition['icon_url'] : null,
                    ];
                }
            }

            $weather_days[] = [
                'TIMESTAMP' => strval($weather_day['timestamp']), // Unix timestamp

                'TEMPERATURE_AVERAGE' => isset($weather_day['temperature_average']) ? strval(intval(round($weather_day['temperature_average']))) : null,
                'TEMPERATURE_HIGH' => isset($weather_day['temperature_high']) ? strval(intval(round($weather_day['temperature_high']))) : null,
                'TEMPERATURE_LOW' => isset($weather_day['temperature_low']) ? strval(intval(round($weather_day['temperature_low']))) : null,

                'PRECIPITATION' => isset($weather_day['precipitation']) ? strval(intval(round($weather_day['precipitation']))) : null,
                'RAIN' => isset($weather_day['rain']) ? strval(intval(round($weather_day['rain']))) : null,
                'SNOW' => isset($weather_day['snow']) ? strval(intval(round($weather_day['snow']))) : null,

                'HUMIDITY' => isset($weather_day['humidity']) ? strval(intval(round($weather_day['humidity']))) : null,
                'VISIBILITY' => isset($weather_day['visibility']) ? strval(intval(round($weather_day['visibility']))) : null,
                'CLOUDINESS' => isset($weather_day['cloudiness']) ? strval(intval(round($weather_day['cloudiness']))) : null,

                'WIND_SPEED' => isset($weather_day['wind_speed']) ? strval(intval(round($weather_day['wind_speed']))) : null,
                'WIND_DIRECTION' => isset($weather_day['wind_direction']) ? $weather_day['wind_direction'] : null,
                'WIND_CHILL' => isset($weather_day['wind_chill']) ? strval(intval(round($weather_day['wind_chill']))) : null,

                'CONDITIONS' => $conditions,
            ];

            if (count($weather_days) >= $max_days) {
                break;
            }
        }

        $conditions = [];
        if (isset($current_conditions['conditions'])) {
            foreach ($current_conditions['conditions'] as $condition) {
                $conditions[] = [
                    'CONDITION' => $condition['description'],
                    'ICON_URL' => isset($condition['icon_url']) ? $condition['icon_url'] : null,
                ];
            }
        }

        $tpl_map = [
            '_GUID' => '8b46b3437fbe05e587b11dd3347fa195',

            'BLOCK_ID' => $block_id,
            'BLOCK_PARAMS' => comma_list_arr_to_str(['block_id' => $block_id] + $map),

            'LOCATION_SEARCH' => $location_search,
            'UNITS' => $units,

            'TEMPERATURE_UNITS' => $temperature_units,
            'PRECIPITATION_UNITS' => $precipitation_units,
            'VISIBILITY_UNITS' => $visibility_units,
            'SPEED_UNITS' => $speed_units,

            'CITY_NAME' => isset($current_conditions['city_name']) ? $current_conditions['city_name'] : null,
            'COUNTRY_NAME' => isset($current_conditions['country_name']) ? $current_conditions['country_name'] : null,

            'CURRENT_TEMPERATURE' => isset($current_conditions['temperature']) ? strval(intval(round($current_conditions['temperature']))) : null,

            'CURRENT_HUMIDITY' => isset($current_conditions['humidity']) ? strval(intval(round($current_conditions['humidity']))) : null,
            'CURRENT_VISIBILITY' => isset($current_conditions['visibility']) ? strval(intval(round($current_conditions['visibility']))) : null,
            'CURRENT_CLOUDINESS' => isset($current_conditions['cloudiness']) ? strval(intval(round($current_conditions['cloudiness']))) : null,

            'CURRENT_WIND_SPEED' => isset($current_conditions['wind_speed']) ? strval(intval(round($current_conditions['wind_speed']))) : null,
            'CURRENT_WIND_DIRECTION' => isset($current_conditions['wind_direction']) ? $current_conditions['wind_direction'] : null,
            'CURRENT_WIND_CHILL' => isset($current_conditions['wind_chill']) ? strval(intval(round($current_conditions['wind_chill']))) : null,

            'CURRENT_CONDITIONS' => $conditions,

            'WEATHER_DAYS' => $weather_days,
        ];

        return do_template('BLOCK_SIDE_WEATHER', $tpl_map);
    }
}
