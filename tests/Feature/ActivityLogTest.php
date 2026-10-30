<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_changes_are_recorded_in_the_activity_log(): void
    {
        $product = Product::factory()->create(['name' => 'Aurora Monitor']);

        $product->update(['name' => 'Aurora Monitor Pro']);

        $product->delete();

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'product',
            'event' => 'created',
            'description' => 'Product "Aurora Monitor" was created',
        ]);

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'product',
            'event' => 'updated',
            'description' => 'Product "Aurora Monitor Pro" was updated',
        ]);

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'product',
            'event' => 'deleted',
            'description' => 'Product "Aurora Monitor Pro" was deleted',
        ]);
    }

    public function test_category_changes_are_recorded_in_the_activity_log(): void
    {
        $category = Category::factory()->create(['name' => 'Electronics']);

        $category->update(['name' => 'Electronics & Gadgets']);
        $category->delete();

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'category',
            'event' => 'created',
            'description' => 'Category "Electronics" was created',
        ]);

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'category',
            'event' => 'deleted',
            'description' => 'Category "Electronics & Gadgets" was deleted',
        ]);
    }

    public function test_the_activity_log_page_lists_recorded_activities(): void
    {
        Product::factory()->create(['name' => 'Aurora Monitor']);

        $this->get(route('activities.index'))
            ->assertOk()
            ->assertSee('Activity Log')
            ->assertSee('Product "Aurora Monitor" was created');
    }

    public function test_activities_can_be_filtered_by_event_and_type(): void
    {
        Product::factory()->create();
        Category::factory()->create();

        $this->get(route('activities.index', ['event' => 'created', 'type' => 'product']))
            ->assertOk()
            ->assertViewHas('activities', fn ($activities) => $activities->total() === 1
                && $activities->first()->log_name === 'product');
    }

    public function test_activities_can_be_searched_by_description(): void
    {
        Product::factory()->create(['name' => 'Aurora Monitor']);
        Product::factory()->create(['name' => 'Nimbus Keyboard']);

        $this->get(route('activities.index', ['search' => 'Aurora']))
            ->assertOk()
            ->assertViewHas('activities', fn ($activities) => $activities->total() === 1);
    }
}
