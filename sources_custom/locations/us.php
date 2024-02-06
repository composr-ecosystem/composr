<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    world_regions
 */

/**
 * Get a map betweeen regions and possible states.
 *
 * @return array Map: region=>list of states
 */
function get_region_structure_US() : array
{
    return [
        'Northeast' => [
            'CT' => 'Connecticut',
            'ME' => 'Maine',
            'MA' => 'Massachusetts',
            'NH' => 'New Hampshire',
            'NJ' => 'New Jersey',
            'NY' => 'New York',
            'PA' => 'Pennsylvania',
            'RI' => 'Rhode Island',
            'VT' => 'Vermont',
        ],

        'Midwest' => [
            'IL' => 'Illinois',
            'IN' => 'Indiana',
            'IA' => 'Iowa',
            'KS' => 'Kansas',
            'MI' => 'Michigan',
            'MN' => 'Minnesota',
            'MO' => 'Missouri',
            'NE' => 'Nebraska',
            'ND' => 'North Dakota',
            'OH' => 'Ohio',
            'SD' => 'South Dakota',
            'WI' => 'Wisconsin',
        ],

        'South' => [
            'AL' => 'Alabama',
            'AR' => 'Arkansas',
            'DC' => 'D.C.',
            'DE' => 'Delaware',
            'FL' => 'Florida',
            'GA' => 'Georgia',
            'KY' => 'Kentucky',
            'LA' => 'Louisiana',
            'MD' => 'Maryland',
            'MS' => 'Mississippi',
            'NC' => 'North Carolina',
            'OK' => 'Oklahoma',
            'SC' => 'South Carolina',
            'TN' => 'Tennessee',
            'TX' => 'Texas',
            'VA' => 'Virginia',
            'WV' => 'West Virginia',
        ],

        'West' => [
            'AK' => 'Alaska ',
            'AZ' => 'Arizona',
            'CA' => 'California',
            'CO' => 'Colorado',
            'HI' => 'Hawaii',
            'ID' => 'Idaho',
            'MT' => 'Montana',
            'NV' => 'Nevada',
            'NM' => 'New Mexico',
            'OR' => 'Oregon',
            'UT' => 'Utah',
            'WA' => 'Washington',
            'WY' => 'Wyoming',
        ],
    ];
}

function find_state_name_from_code_US($code)
{
    $structure = get_region_structure_US();

    foreach ($structure as $region) {
        if (isset($region[$code])) {
            return $region[$code];
        }
    }

    return null;
}

function find_state_code_from_name_US($name)
{
    $structure = get_region_structure_US();

    foreach ($structure as $region) {
        $code = array_search($name, $region);
        if ($code !== false) {
            return $code;
        }
    }

    return null;
}

function get_states_US()
{
    $structure = get_region_structure_US();

    $states = [];

    foreach ($structure as $region) {
        $states += $region;
    }

    cms_mb_asort($states, SORT_NATURAL | SORT_FLAG_CASE);

    return $states;
}
