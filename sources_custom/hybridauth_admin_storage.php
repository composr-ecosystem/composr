<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

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

    public function __construct()
    {
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
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    public function set($key, $value)
    {
        $this->data[$key] = $value;
        $this->prepare_for_save();
    }

    protected function prepare_for_save()
    {
        if (!$this->made_change) {
            register_shutdown_function([$this, 'save']);
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
        unset($this->data[$key]);
        $this->prepare_for_save();
    }

    public function deleteMatch($key)
    {
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
