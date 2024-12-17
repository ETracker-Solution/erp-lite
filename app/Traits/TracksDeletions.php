<?php

namespace App\Traits;

use App\Models\TrashTrack;

trait TracksDeletions
{
    public static function bootTracksDeletions(): void
    {
        static::deleting(function ($model) {
            TrashTrack::create([
                'table_name' => $model->getTable(),
                'data' => json_encode($model->toArray()),
                'ip_address' => request()->ip(),
                'user_agent' => request()->header('User-Agent'),
                'user_name' => auth()->user()->name ?? 'Guest',
                'user_id' => auth()->id() ?? null,
            ]);
        });
    }
}
