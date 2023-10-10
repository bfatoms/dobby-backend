<?php

namespace App\Models\Concerns;

use App\Models\Setting;
use Illuminate\Support\Arr;

trait OrderPrefixNumberSetter
{
    public static function bootOrderPrefixNumberSetter()
    {
        static::creating(function ($data) {
            $allowed = config('company.ORDER_TYPES')[$data['order_type']]['ALLOWED_STATUS_CREATE'];

            if (!in_array($data['status'], $allowed)) {
                abort(422, "NOT_ALLOWED_TO_CREATE_STATUS_{$data['status']}");
            }

            $order_type = strtoupper($data['order_type']);

            $prefix = Setting::getPrefix($order_type);

            $order_number = Setting::getNextNumber($order_type);

            $data['order_number_prefix'] = $prefix;

            $data['order_number'] = $order_number;
        });

        static::created(function ($data) {
            $set_next = config('company.ORDER_TYPES')[$data['order_type']]['SETTING_NUMBER'];
            
            if (!empty($set_next)) {
                Setting::setNextNumber($data['order_number'], $data['order_type']);
            }
        });

        static::saving(function ($data) {
            if (optional($data)['id']) {
                $old = $data->getOriginal();

                $allowed = config('company.ORDER_TYPES')[$data['order_type']]['ALLOWED_STATUS_UPDATE'][$old['status']];

                $allowed[] = $old['status'];

                if (!in_array($data['status'], $allowed)) {
                    abort(422, "NOT_ALLOWED_TO_UPDATE_STATUS_{$data['status']}");
                }

                $disallow = config('company.ORDER_TYPES')[$data['order_type']]['DISALLOWED_UPDATE'];

                if (in_array($old['status'], $disallow)) {
                    $current_status = $data['status'];

                    $data->fill(Arr::except($old, ['created_at', 'updated_at']));

                    $data->status = $current_status;
                }
            }
        });
    }
}
