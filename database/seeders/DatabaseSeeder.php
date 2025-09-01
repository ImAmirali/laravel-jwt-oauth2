<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductDimension;
use App\Models\ProductImage;
use App\Models\ProductReview;
use App\Models\ProductTag;
use App\Models\Tag;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /**
         * seed the category table
         */
        $categories=['Smartphone', 'Laptop', 'Toy', 'Headphone',  'Furniture', 'Gaming'];
        Category::factory()
            ->sequence(...array_map(fn ($category) => [
                'name' => $category,
                'slug' => str($category)->slug(),
                'url' => 'http://localhost/api/products/' . str($category)->slug(),
                ], $categories))
            ->count(count($categories))
            ->create();

        /**
         * seed the brands table
         */
        $brands=[
            'سامسونگ', 'اپل', 'لنوو','ایسوس','سونی',
        ];
        Brand::factory()
            ->sequence(...array_map(fn ($brand) => ['name' => $brand], $brands))
            ->count(count($brands))
            ->create();

        /**
         * seed the tags table
         */
        $tags=[
            'موبایل', 'لپ تاپ', 'کامپیوتر', 'تلویزیون', 'هدفون',
            'کیبورد', 'ماوس', 'مانیتور', 'کنسول بازی', 'هارد دیسک',
        ];
        Tag::factory()
            ->sequence(...array_map(fn ($tag) => ['name' => $tag], $tags))
            ->count(count($tags))
            ->create();

        /**
         * seed the users and product tables with related tables
         */
        User::factory()->count(10)
            ->has(Product::factory()->count(20)
                ->has(ProductDimension::factory(), 'dimensions')
                ->has(ProductImage::factory()->count(3)
                    ->sequence(fn (Sequence $sequence) => ['position' => $sequence->index % 3 + 1]), 'images'))
            ->create();

        /**
         * seed the product_tags table
         */
        ProductTag::factory()
            ->count(10)
            ->create();

        /**
         * seed the product_reviews table
         */
        ProductReview::factory()
            ->count(20)
            ->create();
    }
}
