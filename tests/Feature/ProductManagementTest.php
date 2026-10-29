<?php

namespace Tests\Feature;

use App\Enums\Status;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_root_url_redirects_to_the_product_listing(): void
    {
        $this->get('/')->assertRedirect(route('products.index'));
    }

    public function test_products_are_paginated_five_per_page(): void
    {
        $category = Category::factory()->create();
        Product::factory()->count(8)->create(['category_id' => $category->id]);

        $this->get(route('products.index'))
            ->assertOk()
            ->assertViewHas('products', fn ($products) => $products->count() === 5 && $products->total() === 8);
    }

    public function test_products_can_be_searched_by_name_or_sku(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id, 'name' => 'Aurora Monitor', 'sku' => 'ELC-1001']);
        Product::factory()->create(['category_id' => $category->id, 'name' => 'Nimbus Keyboard', 'sku' => 'ELC-1002']);

        $this->get(route('products.index', ['search' => 'Aurora']))
            ->assertSee('Aurora Monitor')
            ->assertDontSee('Nimbus Keyboard');

        $this->get(route('products.index', ['search' => 'ELC-1002']))
            ->assertSee('Nimbus Keyboard')
            ->assertDontSee('Aurora Monitor');
    }

    public function test_products_can_be_filtered_by_category_and_status(): void
    {
        $electronics = Category::factory()->create(['name' => 'Electronics']);
        $apparel = Category::factory()->create(['name' => 'Apparel']);

        Product::factory()->create(['category_id' => $electronics->id, 'name' => 'Active Gadget', 'status' => Status::Active]);
        Product::factory()->inactive()->create(['category_id' => $electronics->id, 'name' => 'Retired Gadget']);
        Product::factory()->create(['category_id' => $apparel->id, 'name' => 'Linen Shirt']);

        $this->get(route('products.index', ['category' => $electronics->id, 'status' => Status::Inactive->value]))
            ->assertSee('Retired Gadget')
            ->assertDontSee('Active Gadget')
            ->assertDontSee('Linen Shirt');
    }

    public function test_products_can_be_sorted_by_price(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id, 'name' => 'Cheap Item', 'price' => 10]);
        Product::factory()->create(['category_id' => $category->id, 'name' => 'Pricey Item', 'price' => 900]);

        $this->get(route('products.index', ['sort' => 'price', 'direction' => 'asc']))
            ->assertSeeInOrder(['Cheap Item', 'Pricey Item']);

        $this->get(route('products.index', ['sort' => 'price', 'direction' => 'desc']))
            ->assertSeeInOrder(['Pricey Item', 'Cheap Item']);
    }

    public function test_prices_are_displayed_in_rupees(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id, 'price' => 1499.50]);

        $this->get(route('products.index'))
            ->assertOk()
            ->assertSee('Price (₹)')
            ->assertSee('₹1,499.50');
    }

    public function test_the_create_and_edit_forms_render(): void
    {
        $product = Product::factory()->create();

        $this->get(route('products.create'))->assertOk()->assertSee('Add a product');
        $this->get(route('products.edit', $product))->assertOk()->assertSee('Edit product');
    }

    public function test_category_dropdowns_only_list_active_categories(): void
    {
        Category::factory()->create(['name' => 'Live Category']);
        Category::factory()->inactive()->create(['name' => 'Retired Category']);

        $this->get(route('products.create'))
            ->assertOk()
            ->assertSee('Live Category')
            ->assertDontSee('Retired Category');

        $this->get(route('products.index'))
            ->assertOk()
            ->assertSee('Live Category')
            ->assertDontSee('Retired Category');
    }

    public function test_the_edit_form_keeps_a_products_inactive_category(): void
    {
        $category = Category::factory()->inactive()->create(['name' => 'Retired Category']);
        $product = Product::factory()->create(['category_id' => $category->id]);

        $this->get(route('products.edit', $product))
            ->assertOk()
            ->assertSee('Retired Category (inactive)');
    }

    public function test_a_product_can_be_created(): void
    {
        $category = Category::factory()->create();

        $this->post(route('products.store'), [
            'name' => 'New Widget',
            'sku' => 'WGT-9000',
            'category_id' => $category->id,
            'price' => 49.99,
            'status' => Status::Active->value,
        ])->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('products', ['sku' => 'WGT-9000', 'name' => 'New Widget']);
    }

    public function test_store_validation_rejects_invalid_input(): void
    {
        Category::factory()->create();
        Product::factory()->create(['sku' => 'DUP-1']);

        $this->post(route('products.store'), [
            'name' => '',
            'sku' => 'DUP-1',
            'category_id' => 999,
            'price' => 0,
            'status' => 'Archived',
        ])->assertSessionHasErrors(['name', 'sku', 'category_id', 'price', 'status']);

        $this->assertDatabaseCount('products', 1);
    }

    public function test_a_product_can_be_updated(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id, 'sku' => 'KEEP-1']);

        $this->put(route('products.update', $product), [
            'name' => 'Updated name',
            'sku' => 'KEEP-1',
            'category_id' => $category->id,
            'price' => 123.45,
            'status' => Status::Inactive->value,
        ])->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated name',
            'status' => Status::Inactive->value,
        ]);
    }

    public function test_sku_must_be_unique_across_other_products(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id, 'sku' => 'TAKEN-1']);
        $product = Product::factory()->create(['category_id' => $category->id, 'sku' => 'MINE-1']);

        $this->put(route('products.update', $product), [
            'name' => $product->name,
            'sku' => 'TAKEN-1',
            'category_id' => $category->id,
            'price' => 10,
            'status' => Status::Active->value,
        ])->assertSessionHasErrors('sku');
    }

    public function test_a_product_can_be_deleted(): void
    {
        $product = Product::factory()->create();

        $this->delete(route('products.destroy', $product))
            ->assertRedirect(route('products.index'));

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
