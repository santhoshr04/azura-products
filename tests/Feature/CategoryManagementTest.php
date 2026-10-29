<?php

namespace Tests\Feature;

use App\Enums\Status;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_category_listing_shows_categories_with_product_counts(): void
    {
        $category = Category::factory()->create(['name' => 'Electronics']);
        Product::factory()->count(2)->create(['category_id' => $category->id]);

        $this->get(route('categories.index'))
            ->assertOk()
            ->assertSee('Electronics')
            ->assertViewHas('categories', fn ($categories) => $categories->first()->products_count === 2);
    }

    public function test_categories_can_be_searched_by_name(): void
    {
        Category::factory()->create(['name' => 'Electronics']);
        Category::factory()->create(['name' => 'Apparel']);

        $this->get(route('categories.index', ['search' => 'Appar']))
            ->assertSee('Apparel')
            ->assertDontSee('Electronics');
    }

    public function test_the_category_forms_render(): void
    {
        $category = Category::factory()->create();

        $this->get(route('categories.create'))->assertOk()->assertSee('Add a category');
        $this->get(route('categories.edit', $category))->assertOk()->assertSee('Edit category');
    }

    public function test_a_category_can_be_created(): void
    {
        $this->post(route('categories.store'), [
            'name' => 'Outdoor',
            'status' => Status::Active->value,
        ])->assertRedirect(route('categories.index'));

        $this->assertDatabaseHas('categories', [
            'name' => 'Outdoor',
            'status' => Status::Active->value,
        ]);
    }

    public function test_category_validation_rejects_invalid_input(): void
    {
        Category::factory()->create(['name' => 'Electronics']);

        $this->post(route('categories.store'), [
            'name' => 'Electronics',
            'status' => 'Archived',
        ])->assertSessionHasErrors(['name', 'status']);

        $this->assertDatabaseCount('categories', 1);
    }

    public function test_a_category_can_be_updated(): void
    {
        $category = Category::factory()->create(['name' => 'Home']);

        $this->put(route('categories.update', $category), [
            'name' => 'Home & Living',
            'status' => Status::Inactive->value,
        ])->assertRedirect(route('categories.index'));

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Home & Living',
            'status' => Status::Inactive->value,
        ]);
    }

    public function test_an_empty_category_can_be_deleted(): void
    {
        $category = Category::factory()->create();

        $this->delete(route('categories.destroy', $category))
            ->assertRedirect(route('categories.index'));

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_a_category_with_products_cannot_be_deleted(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id]);

        $this->delete(route('categories.destroy', $category))
            ->assertRedirect(route('categories.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }
}
