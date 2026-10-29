<?php

namespace App\Models;

use App\Enums\Status;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['category_id', 'name', 'sku', 'price', 'status'])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    /**
     * Get the category the product belongs to.
     *
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Scope the query to products matching a name or SKU search term.
     *
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        return $query->when($term !== '', function (Builder $query) use ($term) {
            $query->where(function (Builder $query) use ($term) {
                $query->where('name', 'like', "%{$term}%")
                    ->orWhere('sku', 'like', "%{$term}%");
            });
        });
    }

    /**
     * Scope the query to a single category.
     *
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeOfCategory(Builder $query, ?string $categoryId): Builder
    {
        return $query->when(
            $categoryId !== null && $categoryId !== '' && ctype_digit($categoryId),
            fn (Builder $query) => $query->where('category_id', (int) $categoryId),
        );
    }

    /**
     * Scope the query to a single status.
     *
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeOfStatus(Builder $query, ?string $status): Builder
    {
        $status = Status::tryFrom((string) $status);

        return $query->when(
            $status !== null,
            fn (Builder $query) => $query->where('status', $status->value),
        );
    }

    /**
     * Scope the query to an allowed sort column and direction.
     *
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeSort(Builder $query, ?string $column, ?string $direction): Builder
    {
        $columns = ['name', 'price', 'created_at'];
        $column = in_array($column, $columns, true) ? $column : 'created_at';
        $direction = $direction === 'asc' ? 'asc' : 'desc';

        return $query->orderBy($column, $direction)->orderBy('id', $direction);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'status' => Status::class,
        ];
    }
}
