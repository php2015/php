<?php

namespace app\common\repositories;

use app\common\models\UniAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Illuminate\Database\QueryException;

class OptionRepository extends Repository
{
    /**
     * Create a new option repository.
     *
     * @return void
     */
    public function __construct()
    {
        try {
            if(\YunShop::app()->uniacid == ''){
                $options = DB::table('yz_options')
                    ->where('enabled',1)
                    ->get();
            }else{
                $options = DB::table('yz_options')
                    ->where('uniacid', \YunShop::app()->uniacid)
                    ->orWhere('uniacid', 0)
                    ->get();
            }
//            $options = DB::table('yz_options')->get();
        } catch (QueryException $e) {
            $options = [];
        }

        foreach ($options as $option) {
            $this->items[$option['option_name']] = $option;
        }

    }

    /**
     * Get the specified option value.
     *
     * @param  string $key
     * @param  mixed $default
     * @param  raw $raw return raw value without convertion
     * @return mixed
     */
    public function get($key, $default = null, $raw = false)
    {
        if (!$this->has($key) && Arr::has(config('options'), $key)) {
            $this->set($key, config("options.$key"));
        }

        $value = Arr::get($this->items, $key, $default);

        if ($raw) return $value;

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;

            case 'false':
            case '(false)':
                return false;

            case 'null':
            case '(null)':
                return;

            default:
                return $value;
                break;
        }
    }

    /**
     * Set a given option value.
     *
     * @param  array|string $key
     * @param  mixed $value
     * @return void
     */
    public function set($key, $value = null)
    {
        if (is_array($key)) {
            // If given key is an array
            foreach ($key as $innerKey => $innerValue) {
                Arr::set($this->items, $innerKey, $innerValue);
                $this->doSetOption($innerKey, $innerValue);
            }
        } else {
            Arr::set($this->items, $key, $value);
            $this->doSetOption($key, $value);
        }
    }

    /**
     * Do really save modified options to database.
     *
     * @return void
     */
    protected function doSetOption($key, $value)
    {
        try {
            if (!DB::table('yz_options')->where('option_name', $key)->first()) {
                $uniAccount = UniAccount::get();
                $pluginData = [];
                foreach ($uniAccount as $u) {
                    $pluginData[] = [
                        'uniacid' => $u->uniacid,
                        'option_name' => $key,
                        'option_value' => $value
                    ];
                }

                DB::table('yz_options')
                    ->insert($pluginData);
            } else {
                DB::table('yz_options')
                    ->where('option_name', $key)
                    ->update(['option_value' => $value]);
            }
        } catch (QueryException $e) {
            return;
        }
    }

    /**
     * Do really save modified options to database.
     *
     * @deprecated
     * @return void
     */
    public function save()
    {
        $this->itemsModified = array_unique($this->itemsModified);

        try {
            foreach ($this->itemsModified as $key) {
                if (!DB::table('yz_options')->where('option_name', $key)->first()) {
                    DB::table('yz_options')
                        ->insert(['option_name' => $key, 'option_value' => $this[$key]]);
                } else {
                    DB::table('yz_options')
                        ->where('option_name', $key)
                        ->update(['option_value' => $this[$key]]);
                }
            }

            // clear the list
            $this->itemsModified = [];
        } catch (QueryException $e) {
            return;
        }
    }

    /**
     * Prepend a value onto an array option value.
     *
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function prepend($key, $value)
    {
        $array = $this->get($key);

        array_unshift($array, $value);

        $this->set($key, $array);
    }

    /**
     * Return the options with key in the given array.
     *
     * @param  array $array
     * @return array
     */
    public function only(Array $array)
    {
        $result = [];

        foreach ($this->items as $key => $value) {
            if (in_array($key, $array)) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Save all modified options into database
     */
    public function __destruct()
    {
        $this->save();
    }

    public function editEnabledById($id, $enabled)
    {
        return DB::table('yz_options')->where('id', $id)->update(['enabled' => $enabled]);
    }

    public function editTopShowById($id, $enabled)
    {
        return DB::table('yz_options')->where('uniacid', \YunShop::app()->uniacid)->where('id', $id)->update(['top_show' => $enabled]);
    }

    public function insertPlugin($pluginData)
    {
        return DB::table('yz_options')->insert($pluginData);
    }


}
