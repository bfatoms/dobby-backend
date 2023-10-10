<?php

namespace App\Models\Concerns;

trait Listable
{
    public function scopeList($query, $limit = null, $only = '*', $sort=null)
    {
        $list_sort = $sort ?? request('list_sort') ?? 'created_at';

        $perPage = $limit ?? request('limit') ?? 25;
        
        $query =  (request('all') == "true")
            ? $query->when($limit, function ($query) use ($limit) {
                $query->limit($limit);
            })
            ->when(request('limit'), function ($query) {
                $query->limit(request('limit'));
            })->when(request('offset'), function ($query) {
                $query->offset(request('offset'));
            })->latest($list_sort)
            ->get($only)
            : $query->latest($list_sort)->paginate($perPage, $only);

        // dd($query->toSql());
    
        return $query;
    }
}
