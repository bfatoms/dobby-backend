<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

trait Sortable
{
    public function scopeArrange($q, $key, $sort_order = 'desc', $fk = null)
    {
        if (Str::contains($key, ".")) {
            $keys = explode('.', $key);

            $relation = Str::camel($keys[0]);

            $relation_model = $this->{$relation}()->getRelated();

            $relation_model_table = $relation_model->getTable();

            $table = $this->getTable();

            $key = Str::snake($keys[0]) . "_$keys[1]";

            $fk = $this->{$relation}()->getForeignKeyName();

            if (method_exists($this->{$relation}(), 'getOwnerKeyName')) {
                $pk = $this->{$relation}()->getOwnerKeyName();

                $q->select(
                    "{$table}.*",
                    DB::raw(
                        "(select {$keys[1]}
                        from {$relation_model_table}
                        WHERE {$table}.$fk = {$relation_model_table}.{$pk} limit 1)
                        AS {$key}"
                    )
                );
            } else {
                $pk = $this->{$relation}()->getLocalKeyName();

                $q->select(
                    "{$table}.*",
                    DB::raw(
                        "(select {$keys[1]}
                        from {$relation_model_table}
                        WHERE {$relation_model_table}.$fk = {$table}.{$pk} limit 1)
                        AS {$key}"
                    )
                );
            }
        }

        return $q->orderBy($key, $sort_order);
    }
}
