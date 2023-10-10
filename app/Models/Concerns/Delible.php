<?php

namespace App\Models\Concerns;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait Delible
{
    public function tryDelete()
    {
        $deleted = false;
        try {
            DB::beginTransaction();
            $deleted = boolval($this->delete());
            DB::commit();
        } catch (QueryException $ex) {
            DB::rollback();
            $match = [];
            preg_match('/`(.*)`\.`(.*)`,/', $ex->errorInfo[2], $match);
            $table = strtoupper($match[2]);
            $class = strtoupper(Str::snake(str_replace("App\Models\\", "", self::class)));
            return "{$class}_IS_REQUIRED_FOR_{$table}";
        }
        return $deleted;
    }
}
