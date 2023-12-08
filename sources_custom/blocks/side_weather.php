<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

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
     * @return ?array Map of block info (null: block is disabled).
     */
    public function info()
    {
        $info = array();
        $info['author'] = 'Manuprathap';
        $info['organisation'] = 'ocProducts';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 6;
        $info['update_require_upgrade'] = true;
        $info['locked'] = false;
        $info['parameters'] = array('param', 'unit', 'max_days');
        return $info;
    }

    /**
     * Uninstall the block.
     */
    public function uninstall()
    {
        $GLOBALS['SITE_DB']->drop_table_if_exists('cached_weather_codes');
    }

    /**
     * Install the block.
     *
     * @param  ?integer $upgrade_from What version we're upgrading from (null: new install)
     * @param  ?integer $upgrade_from_hack What hack version we're upgrading from (null: new-install/not-upgrading-from-a-hacked-version)
     */
    public function install($upgrade_from = null, $upgrade_from_hack = null)
    {
        $GLOBALS['SITE_DB']->create_table('cached_weather_codes', array(
            'id' => '*AUTO',
            'w_string' => 'SHORT_TEXT',
            'w_code' => 'INTEGER',
        ));
    }

    /**
     * Find caching details for the block.
     *
     * @return ?array Map of cache details (cache_on and ttl) (null: block is disabled).
     */
    public function caching_environment()
    {
        $info = array();
        $info['cache_on'] = 'array(isset($map[\'max_days\'])?intval($map[\'max_days\']):2,(array_key_exists(\'unit\',$map) && ($map[\'unit\']!=\'\'))?$map[\'unit\']:\'c\',array_key_exists(\'param\',$map)?$map[\'param\']:\'\')';
        $info['special_cache_flags'] = CACHE_AGAINST_DEFAULT | CACHE_AGAINST_STAFF_STATUS;
        $info['ttl'] = 60;
        return $info;
    }

    /**
     * Execute the block.
     *
     * @param  array $map A map of parameters.
     * @return Tempcode The result of execution.
     */
    public function run($map)
    {
        i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

        require_lang('weather');

        $block_id = get_block_id($map);

        $max_days = isset($map['max_days']) ? intval($map['max_days']) : 2;

        if (empty($map['param'])) {
            $loc_code = '2487889'; // if not found setting a default location for weather
        } else {
            $loc_code = $map['param']; // need to pass loc ID ex :INXX0087
        }

        if (!is_numeric($loc_code)) {
            $test = $GLOBALS['SITE_DB']->query_select_value_if_there('cached_weather_codes', 'w_code', array('w_string' => $loc_code));
            if (is_null($test)) {
                $matches = array();

                require_code('files');

                $url = 'http://query.yahooapis.com/v1/public/yql?q=' . urlencode('select * from geo.places where text="' . $loc_code . '"') . '&format=json&diagnostics=true&callback=cbfunc';
                $result = http_download_file($url);

                if (preg_match('#"woeid":\s*"(\d+)"#', $result, $matches) != 0) {
                    $loc_code = $matches[1];
                } else {
                    return new Tempcode();
                }

                if (is_numeric($loc_code)) {
                    $GLOBALS['SITE_DB']->query_insert('cached_weather_codes', array(
                        'w_string' => $map['param'],
                        'w_code' => intval($loc_code),
                    ));
                }
            } else {
                $loc_code = strval($test);
            }
        }

        $temperature_unit = (array_key_exists('unit', $map) && ($map['unit'] != '')) ? $map['unit'] : 'c';

        $json_url = 'http://query.yahooapis.com/v1/public/yql?q=select+%2A+from+weather.forecast+where+woeid%3D' . urlencode($loc_code) . '%20AND%20u%3D%22' . urlencode($temperature_unit) . '%22&format=json';
        $json = http_download_file($json_url, null, false);
        if (empty($json)) {
            if (empty($GLOBALS['HTTP_MESSAGE_B'])) {
                $GLOBALS['HTTP_MESSAGE_B'] = do_lang('HTTP_DOWNLOAD_STATUS_SERVER_ERROR', $json_url);
            }

            $GLOBALS['DO_NOT_CACHE_THIS'] = true;
            require_code('failure');
            relay_error_notification($GLOBALS['HTTP_MESSAGE_B'], false, 'error_occurred_weather');
            if (cron_installed(true)) {
                if (!$GLOBALS['FORUM_DRIVER']->is_staff(get_member())) {
                    return new Tempcode();
                }
            }
            return do_template('INLINE_WIP_MESSAGE', array('_GUID' => '046c437a5c3799838155b5c5fbe3be26', 'MESSAGE' => htmlentities($GLOBALS['HTTP_MESSAGE_B'])));
        }

        require_code('json');

        $data = @json_decode($json, true);

        if (!isset($data['query']['results']['channel']['location'])) {
            return new Tempcode(); // No weather for here
        }

        $feed = $data['query']['results']['channel'];

        $location_city = $feed['location']['city'];
        $location_region = $feed['location']['region'];
        $location_country = $feed['location']['country'];
        $wind_chill = $feed['wind']['chill'];
        $wind_direction = $feed['wind']['direction'];
        $wind_speed = $feed['wind']['speed'];
        $humidity = $feed['atmosphere']['humidity'];
        $visibility = $feed['atmosphere']['visibility'];
        $pressure = $feed['atmosphere']['pressure'];
        $pressure_rising = $feed['atmosphere']['rising'];
        $sunrise = $feed['astronomy']['sunrise'];
        $sunset = $feed['astronomy']['sunset'];

        $item = $feed['item'];

        $title = $item['title'];

        $matches = array();

        $image = '';
        if (preg_match('/<img src="(.*)"\/?' . '>/Usm', $item['description'], $matches) != 0) {
            $image = $matches[1];
        }

        $cur_conditions = '';
        if (preg_match('/Current Conditions:<\/b>\n<BR \/>(.*)<BR \/>/Uism', $item['description'], $matches) != 0) {
            $cur_conditions = $matches[1];
        }

        $forecast = '';
        if (preg_match('/Forecast:<\/b>\n<BR \/>(.*)<BR \/>/ism', $item['description'], $matches) != 0) {
            $forecast = $matches[1];
        }

        $lat = $item['lat'];
        $long = $item['long'];
        $full_link = $item['link'];
        $prepared_date = $item['pubDate'];
        $dates = array();
        foreach ($item['forecast'] as $i => $_forecast) {
            if ($i == $max_days) {
                break;
            }

            $dates[] = array(
                'DATE' => strtotime($_forecast['date']),
                'DAY' => $_forecast['day'],
                'LOW' => $_forecast['low'],
                'HIGH' => $_forecast['high'],
                'TEXT' => $_forecast['text'],
                'CODE' => $_forecast['code'],
            );
        }

        return do_template('BLOCK_SIDE_WEATHER', array(
            '_GUID' => '8b46b3437fbe05e587b11dd3347fa195',
            'TITLE' => $title,
            'LOC_CODE' => $loc_code,
            'IMAGE' => $image,
            'COND' => $cur_conditions,
            'FORECAST' => $forecast,
            'LOCATION_CITY' => $location_city,
            'LOCATION_REGION' => $location_region,
            'LOCATION_COUNTRY' => $location_country,
            'WIND_CHILL' => $wind_chill,
            'WIND_DIRECTION' => $wind_direction,
            'WIND_SPEED' => $wind_speed,
            'HUMIDITY' => $humidity,
            'VISIBILITY' => $visibility,
            'PRESSURE' => $pressure,
            'PRESSURE_RISING' => $pressure_rising,
            'SUNRISE' => $sunrise,
            'SUNSET' => $sunset,
            'LAT' => $lat,
            'LONG' => $long,
            'FULL_LINK' => $full_link,
            'PREPARED_DATE' => $prepared_date,
            'DATES' => $dates,
            'TEMPERATURE_UNIT' => $temperature_unit,
            'BLOCK_PARAMS' => block_params_arr_to_str(array('block_id' => $block_id) + $map),
        ));
    }
}
