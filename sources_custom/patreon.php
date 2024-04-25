<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    patreon
 */

function get_patreon_hybridauth_adapters()
{
    require_code('hybridauth_admin');

    $before_type_strictness = ini_get('ocproducts.type_strictness');
    cms_ini_set('ocproducts.type_strictness', '0');
    $before_xss_detect = ini_get('ocproducts.xss_detect');
    cms_ini_set('ocproducts.xss_detect', '0');

    list($hybridauth, , $providers) = initiate_hybridauth_admin(0, 'admin', 'Patreon');

    if (!isset($providers['Patreon'])) {
        return [];
    }

    $adapters = [];

    if ($providers['Patreon']['enabled']) {
        $adapter = $hybridauth->getAdapter('Patreon');
        if ($adapter->isConnected()) {
            $adapters[] = $adapter;
        }
    }

    foreach ($providers['Patreon']['alternate_configs'] as $alternate_config) {
        list($_hybridauth, , $_providers) = initiate_hybridauth_admin(0, $alternate_config, 'Patreon');
        if ($_providers['Patreon']['enabled']) {
            $adapter = $_hybridauth->getAdapter('Patreon');
            if ($adapter->isConnected()) {
                $adapters[] = $adapter;
            }
        }
    }

    cms_ini_set('ocproducts.type_strictness', $before_type_strictness);
    cms_ini_set('ocproducts.xss_detect', $before_xss_detect);

    return $adapters;
}

function get_patreon_patrons_on_minimum_level($monthly)
{
    $patreon_patrons = [];

    $rows = $GLOBALS['SITE_DB']->query('SELECT * FROM ' . get_table_prefix() . 'patreon_patrons WHERE p_monthly>=' . strval($monthly));
    foreach ($rows as $row) {
        $username = $GLOBALS['FORUM_DRIVER']->get_username($row['p_member_id']);

        $patreon_patrons[] = [
            'name' => $row['p_name'],
            'username' => $username,
            'monthly' => $row['p_monthly'],
            'tier' => $row['p_tier'],
        ];
    }

    sort_maps_by($patreon_patrons, 'name', false, true);

    return $patreon_patrons;
}

function patreon_sync()
{
    $adapters = get_patreon_hybridauth_adapters();
    if (empty($adapters)) {
        return;
    }

    $before_type_strictness = ini_get('ocproducts.type_strictness');
    cms_ini_set('ocproducts.type_strictness', '0');
    $before_xss_detect = ini_get('ocproducts.xss_detect');
    cms_ini_set('ocproducts.xss_detect', '0');

    $existing = list_to_map(null, $GLOBALS['SITE_DB']->query_select('patreon_patrons', ['p_member_id', 'p_tier']));
    $remaining = $existing;

    foreach ($adapters as $adapter) {
        // Hybridauth code...

        try {
            $campaigns = [];
            $campaignsUrl = 'oauth2/v2/campaigns';
            do {
                $response = $adapter->apiRequest($campaignsUrl);
                $data = new \Hybridauth\Data\Collection($response);

                if (!$data->exists('data')) {
                    throw new UnexpectedApiResponseException('Provider API returned an unexpected response.');
                }

                foreach ($data->filter('data')->toArray() as $item) {
                    $campaign = new \Hybridauth\Data\Collection($item);
                    $campaigns[] = $campaign->get('id');
                }

                if ($data->filter('links')->exists('next')) {
                    $campaignsUrl = $data->filter('links')->get('next');

                    $pagedList = true;
                } else {
                    $pagedList = false;
                }
            } while ($pagedList);

            $all_members = [];

            foreach ($campaigns as $campaignId) {
                $params = ['include' => 'currently_entitled_tiers', 'fields[member]' => 'full_name,patron_status,email', 'fields[tier]' => 'title,amount_cents'];
                $membersUrl = 'oauth2/v2/campaigns/' . $campaignId . '/members?' . http_build_query($params);

                do {
                    $response = $adapter->apiRequest($membersUrl);

                    $data = new \Hybridauth\Data\Collection($response);

                    if (!$data->exists('data')) {
                        throw new UnexpectedApiResponseException('Provider API returned an unexpected response.');
                    }

                    $tierDetails = [];

                    foreach ($data->filter('included')->toArray() as $item) {
                        $includedItem = new \Hybridauth\Data\Collection($item);
                        if ($includedItem->get('type') == 'tier') {
                            $tierDetails[$includedItem->get('id')] = [
                                'title' => $includedItem->filter('attributes')->get('title'),
                                'monthly' => intval(floor((float)$includedItem->filter('attributes')->get('amount_cents') / 100.0)),
                            ];
                        }
                    }

                    foreach ($data->filter('data')->toArray() as $item) {
                        $member = new \Hybridauth\Data\Collection($item);

                        if ($member->filter('attributes')->get('patron_status') == 'active_patron') {
                            $tiers = [];
                            foreach ($member->filter('relationships')->filter('currently_entitled_tiers')->get('data') as $_item) {
                                $tier = new \Hybridauth\Data\Collection($_item);
                                $tierId = $tier->get('id');
                                $tiers[] = $tierDetails[$tierId];
                            }

                            $all_members[] = [
                                'id' => $member->get('id'),
                                'name' => $member->filter('attributes')->get('full_name'),
                                'email' => $member->filter('attributes')->get('email'),
                                'tiers' => $tiers,
                            ];
                        }
                    }

                    if ($data->filter('links')->exists('next')) {
                        $membersUrl = $data->filter('links')->get('next');

                        $pagedList = true;
                    } else {
                        $pagedList = false;
                    }
                } while ($pagedList);
            }
        } catch (Exception $e) {
            require_code('failure');
            cms_error_log('Hybridauth: ERROR Patreon -- ' . $e->getMessage(), 'error_occurred_api');

            return;
        } finally {
            cms_ini_set('ocproducts.type_strictness', $before_type_strictness);
            cms_ini_set('ocproducts.xss_detect', $before_xss_detect);
        }

        $member_ids = [];
        foreach ($all_members as $member_map) {
            $member_id = $GLOBALS['FORUM_DRIVER']->get_member_from_email_address($member_map['email']);
            if ($member_id !== null) {
                foreach ($member_map['tiers'] as $tier_map) {
                    $map = [
                        'p_member_id' => $member_id,
                        'p_tier' => $tier_map['title'],
                    ];
                    $sz = serialize($map);
                    if (isset($existing[$sz])) {
                        unset($remaining[$sz]);
                    } elseif (!isset($existing[$sz])) {
                        $extra = [
                            'p_id' => $member_map['id'],
                            'p_monthly' => $tier_map['monthly'],
                            'p_name' => $member_map['name'],
                        ];
                        $GLOBALS['SITE_DB']->query_insert('patreon_patrons', $map + $extra);
                        $existing[$sz] = $map;
                    }
                }
            }
        }
    }

    foreach ($remaining as $map) {
        $GLOBALS['SITE_DB']->query_delete('patreon_patrons', $map, '', 1);
    }
}

function patreon_sync_individual_member($user_id, $alternate_config)
{
    require_code('hybridauth_admin');

    $before_type_strictness = ini_get('ocproducts.type_strictness');
    cms_ini_set('ocproducts.type_strictness', '0');
    $before_xss_detect = ini_get('ocproducts.xss_detect');
    cms_ini_set('ocproducts.xss_detect', '0');

    list($hybridauth, , $providers) = initiate_hybridauth_admin(0, $alternate_config, 'Patreon');

    if (!$providers['Patreon']['enabled']) {
        require_code('failure');
        cms_error_log('Hybridauth: ERROR Patreon provider not enabled, during webhook response', 'error_occurred_api');
        return;
    }

    $adapter = $hybridauth->getAdapter('Patreon');

    if (!$adapter->isConnected()) {
        require_code('failure');
        cms_error_log('Hybridauth: ERROR Patreon provider not connected, during webhook response', 'error_occurred_api');
        return;
    }

    try {
        // Hybridauth code...

        $params = ['include' => 'currently_entitled_tiers', 'fields[member]' => 'full_name,patron_status,email', 'fields[tier]' => 'title,amount_cents'];
        $membersUrl = 'oauth2/v2/members/' . $user_id . '?' . http_build_query($params);

        $response = $adapter->apiRequest($membersUrl);

        $data = new \Hybridauth\Data\Collection($response);

        if (!$data->exists('data')) {
            throw new UnexpectedApiResponseException('Provider API returned an unexpected response.');
        }

        $tierDetails = [];

        foreach ($data->filter('included')->toArray() as $item) {
            $includedItem = new \Hybridauth\Data\Collection($item);
            if ($includedItem->get('type') == 'tier') {
                $tierDetails[$includedItem->get('id')] = [
                    'title' => $includedItem->filter('attributes')->get('title'),
                    'monthly' => intval(floor((float)$includedItem->filter('attributes')->get('amount_cents') / 100.0)),
                ];
            }
        }

        $member = $data->filter('data');

        $tiers = [];
        if ($member->filter('attributes')->get('patron_status') == 'active_patron') {
            foreach ($member->filter('relationships')->filter('currently_entitled_tiers')->get('data') as $item) {
                $tier = new \Hybridauth\Data\Collection($item);
                $tierId = $tier->get('id');
                $tiers[] = $tierDetails[$tierId];
            }
        }

        $id = $member->get('id');
        $name = $member->filter('attributes')->get('full_name');
        $email = $member->filter('attributes')->get('email');
    } catch (Exception $e) {
        require_code('failure');
        cms_error_log('Hybridauth: ERROR ' . $e->getMessage(), 'error_occurred_api');

        return;
    } finally {
        cms_ini_set('ocproducts.type_strictness', $before_type_strictness);
        cms_ini_set('ocproducts.xss_detect', $before_xss_detect);
    }

    $member_id = $GLOBALS['FORUM_DRIVER']->get_member_from_email_address($email);

    if ($member_id === null) {
        return;
    }

    $GLOBALS['SITE_DB']->query_delete('patreon_patrons', [
        'p_member_id' => $member_id,
    ]);

    foreach ($tiers as $tier_map) {
        $GLOBALS['SITE_DB']->query_insert('patreon_patrons', [
            'p_member_id' => $member_id,
            'p_tier' => $tier_map['title'],
            'p_id' => $id,
            'p_monthly' => $tier_map['monthly'],
            'p_name' => $name,
        ]);
    }
}
