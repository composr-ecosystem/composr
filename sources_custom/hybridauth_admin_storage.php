<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    hybridauth
 */

use Hybridauth\Exception\RuntimeException;

/**
 * Hybridauth storage manager
 */
class ComposrHybridauthValuesStorage implements Hybridauth\Storage\StorageInterface
{
    protected $data;
    protected $made_change = false;
    protected $prefix = '';

    public function __construct($prefix = '')
    {
        $this->prefix = $prefix;

        $data = get_value('hybridauth_admin_storage', null, true);
        if ($data === null) {
            $this->data = [];
        } else {
            $this->data = json_decode($data, true);
            if (!is_array($this->data)) {
                $this->data = [];
            }
        }
    }

    public function get($key)
    {
        if (!in_array($key, ['provider', 'alternate_config'])) {
            $key = $this->prefix . $key;
        }

        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    public function set($key, $value)
    {
        if (!in_array($key, ['provider', 'alternate_config'])) {
            $key = $this->prefix . $key;
        }

        $this->data[$key] = $value;
        $this->prepare_for_save();
    }

    protected function prepare_for_save()
    {
        if (!$this->made_change) {
            cms_register_shutdown_function_safe([$this, 'save']);
            $this->made_change = true;
        }
    }

    public function save()
    {
        set_value('hybridauth_admin_storage', json_encode($this->data), true);
    }

    public function clear()
    {
        $this->data = [];
        $this->prepare_for_save();
    }

    public function delete($key)
    {
        if (!in_array($key, ['provider', 'alternate_config'])) {
            $key = $this->prefix . $key;
        }

        unset($this->data[$key]);
        $this->prepare_for_save();
    }

    public function deleteMatch($key)
    {
        if (!in_array($key, ['provider', 'alternate_config'])) {
            $key = $this->prefix . $key;
        }

        $made_change = false;
        foreach ($this->data as $k => $val) {
            if (strstr($k, $key)) {
                unset($this->data[$k]);
                $made_change = true;
            }
        }
        if ($made_change) {
            $this->prepare_for_save();
        }
    }
}
