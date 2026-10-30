<?php

namespace App\Models;

use App\Enums\Status;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

#[Fillable(['name', 'status'])]
class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory, LogsActivity;

    /**
     * Configure the activity log for the category.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('category')
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->setDescriptionForEvent(fn (string $event): string => match ($event) {
                'created' => "Category \"{$this->name}\" was created",
                'updated' => "Category \"{$this->name}\" was updated",
                'deleted' => "Category \"{$this->name}\" was deleted",
                default => "Category \"{$this->name}\" was {$event}",
            });
    }

    /**
     * Get the products that belong to the category.
     *
     * @return HasMany<Product, $this>
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Scope the query to categories matching a name search term.
     *
     * @param  Builder<Category>  $query
     * @return Builder<Category>
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        return $query->when($term !== '', fn (Builder $query) => $query->where('name', 'like', "%{$term}%"));
    }

    /**
     * Scope the query to active categories.
     *
     * @param  Builder<Category>  $query
     * @return Builder<Category>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', Status::Active->value);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => Status::class,
        ];
    }
}
