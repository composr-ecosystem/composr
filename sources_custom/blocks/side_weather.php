<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2019

 See text/EN/licence.txt for full licensing information.

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
    public function info()
    {
        $info = array();
        $info['author'] = 'Manuprathap';
        $info['organisation'] = 'ocProducts';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 7;
        $info['update_require_upgrade'] = true;
        $info['locked'] = false;
        $info['parameters'] = array('param', 'units', 'max_days', 'api');
        return $info;
    }

    /**
     * Find caching details for the block.
     *
     * @return ?array Map of cache details (cache_on and ttl) (null: block is disabled)
     */
    public function caching_environment()
    {
        $info = array();
        $info['cache_on'] = 'array((array_key_exists(\'units\',$map) && ($map[\'units\']!=\'\'))?$map[\'units\']:\'metric\',isset($map[\'max_days\'])?intval($map[\'max_days\']):5,array_key_exists(\'param\',$map)?$map[\'param\']:\'\',(array_key_exists(\'api\',$map)&&($map[\'api\']!=\'\'))?$map[\'api\']:null)';
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
    public function run($map)
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

        $matches = array();
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

            case 'celsius':
            default:
                $temperature_units = '&#186;C';
                $precipitation_units = 'mm';
                $speed_units = 'kph';
                $visibility_units = 'm';
                $units = 'celsius';
                break;
        }

        $errormsg = '';

        $result = weather_lookup($location_search, $latitude, $longitude, $units, $max_days, $errormsg, $api);

        if ($result === null) {
            $GLOBALS['DO_NOT_CACHE_THIS'] = true;
            require_code('failure');
            relay_error_notification($errormsg, false, 'error_occurred_weather');

            if (cron_installed(true)) {
                if (!$GLOBALS['FORUM_DRIVER']->is_staff(get_member())) {
                    return new Tempcode();
                }
            }
            return do_template('INLINE_WIP_MESSAGE', array('_GUID' => '046c437a5c3799838155b5c5fbe3be26', 'MESSAGE' => htmlentities($errormsg)));
        }

        list($forecast, $current_conditions) = $result;

        $weather_days = array();
        foreach ($forecast['list'] as $weather_day) {
            $conditions = array();
            if (isset($weather_day['weather'])) {
                foreach ($weather_day['weather'] as $condition) {
                    $conditions[] = array(
                        'CONDITION' => $condition['description'],
                        'ICON_URL' => $condition['icon_url'],
                    );
                }
            }

            $weather_days[] = array(
                'TIMESTAMP' => strval($weather_day['dt']), // Unix timestamp

                'TEMPERATURE_AVERAGE' => isset($weather_day['main']['temp']) ? strval(intval(round($weather_day['main']['temp']))) : null,
                'TEMPERATURE_HIGH' => isset($weather_day['main']['high']) ? strval(intval(round($weather_day['main']['high']))) : null,
                'TEMPERATURE_LOW' => isset($weather_day['main']['low']) ? strval(intval(round($weather_day['main']['low']))) : null,

                'PRECIPITATION' => isset($weather_day['precipitation']) ? strval(intval(round($weather_day['precipitation']))) : null,
                'RAIN' => isset($weather_day['rain']) ? strval(intval(round($weather_day['rain']))) : null,
                'SNOW' => isset($weather_day['snow']) ? strval(intval(round($weather_day['snow']))) : null,

                'HUMIDITY' => isset($weather_day['main']['humidity']) ? strval(intval(round($weather_day['main']['humidity']))) : null,
                'VISIBILITY' => isset($weather_day['clouds']['all']) ? strval(intval(round($weather_day['clouds']['all']))) : null,

                'WIND_SPEED' => isset($weather_day['wind']['speed']) ? strval(intval(round($weather_day['wind']['speed']))) : null,
                'WIND_DIRECTION' => isset($weather_day['wind']['direction']) ? $weather_day['wind']['direction'] : null,
                'WIND_CHILL' => isset($weather_day['wind']['windchill']) ? strval(intval(round($weather_day['wind']['windchill']))) : null,

                'CONDITIONS' => $conditions,
            );

            if (count($weather_days) >= $max_days) {
                break;
            }
        }

        $conditions = array();
        if (isset($current_conditions['weather'])) {
            foreach ($current_conditions['weather'] as $condition) {
                $conditions[] = array(
                    'CONDITION' => $condition['description'],
                    'ICON_URL' => $condition['icon_url'],
                );
            }
        }

        $tpl_map = array(
            '_GUID' => '8b46b3437fbe05e587b11dd3347fa195',

            'BLOCK_ID' => $block_id,
            'BLOCK_PARAMS' => block_params_arr_to_str(array('block_id' => $block_id) + $map),

            'LOCATION_SEARCH' => $location_search,
            'UNITS' => $units,

            'TEMPERATURE_UNITS' => $temperature_units,
            'PRECIPITATION_UNITS' => $precipitation_units,
            'VISIBILITY_UNITS' => $visibility_units,
            'SPEED_UNITS' => $speed_units,

            'CITY_NAME' => isset($current_conditions['city']['name']) ? $current_conditions['city']['name'] : null,
            'COUNTRY_NAME' => isset($current_conditions['country']) ? find_country_name_from_iso($current_conditions['country']) : null,

            'CURRENT_TEMPERATURE' => isset($current_conditions['main']['temp']) ? strval(intval(round($current_conditions['main']['temp']))) : null,

            'CURRENT_HUMIDITY' => isset($current_conditions['main']['humidity']) ? strval(intval(round($current_conditions['main']['humidity']))) : null,
            'CURRENT_VISIBILITY' => isset($current_conditions['clouds']['all']) ? strval(intval(round($current_conditions['clouds']['all']))) : null,

            'CURRENT_WIND_SPEED' => isset($current_conditions['wind']['speed']) ? strval(intval(round($current_conditions['wind']['speed']))) : null,
            'CURRENT_WIND_DIRECTION' => isset($current_conditions['wind']['direction']) ? $current_conditions['wind']['direction'] : null,
            'CURRENT_WIND_CHILL' => isset($current_conditions['wind']['windchill']) ? strval(intval(round($current_conditions['wind']['windchill']))) : null,

            'CURRENT_CONDITIONS' => $conditions,

            'WEATHER_DAYS' => $weather_days,
        );

        return do_template('BLOCK_SIDE_WEATHER', $tpl_map);
    }
}
