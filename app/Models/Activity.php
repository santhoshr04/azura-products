<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Models\Activity as SpatieActivity;

class Activity extends SpatieActivity
{
    /**
     * The events that can be logged and filtered on.
     *
     * @var array<int, string>
     */
    public const EVENTS = ['created', 'updated', 'deleted'];

    /**
     * The log names that can be filtered on.
     *
     * @var array<int, string>
     */
    public const TYPES = ['product', 'category'];

    /**
     * Scope the query to activities matching a description search term.
     *
     * @param  Builder<Activity>  $query
     * @return Builder<Activity>
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        return $query->when($term !== '', fn (Builder $query) => $query->where('description', 'like', "%{$term}%"));
    }

    /**
     * Scope the query to a single event.
     *
     * @param  Builder<Activity>  $query
     * @return Builder<Activity>
     */
    public function scopeOfEvent(Builder $query, ?string $event): Builder
    {
        return $query->when(
            in_array($event, self::EVENTS, true),
            fn (Builder $query) => $query->where('event', $event),
        );
    }

    /**
     * Scope the query to a single log name.
     *
     * @param  Builder<Activity>  $query
     * @return Builder<Activity>
     */
    public function scopeOfType(Builder $query, ?string $type): Builder
    {
        return $query->when(
            in_array($type, self::TYPES, true),
            fn (Builder $query) => $query->where('log_name', $type),
        );
    }

    /**
     * Scope the query to an allowed sort column and direction.
     *
     * @param  Builder<Activity>  $query
     * @return Builder<Activity>
     */
    public function scopeSort(Builder $query, ?string $column, ?string $direction): Builder
    {
        $columns = ['created_at', 'event', 'log_name'];
        $column = in_array($column, $columns, true) ? $column : 'created_at';
        $direction = $direction === 'asc' ? 'asc' : 'desc';

        return $query->orderBy($column, $direction)->orderBy('id', $direction);
    }
}
