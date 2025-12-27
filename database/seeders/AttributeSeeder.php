<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeOption;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // === COMMON ATTRIBUTES (used across multiple categories) ===
            $commonAttributes = $this->createCommonAttributes();

            // === FASHION ATTRIBUTES ===
            $fashionAttributes = $this->createFashionAttributes();

            // === ELECTRONICS ATTRIBUTES ===
            $electronicsAttributes = $this->createElectronicsAttributes();

            // === BOOKS ATTRIBUTES ===
            $booksAttributes = $this->createBooksAttributes();

            // === HOME & APPLIANCES ATTRIBUTES ===
            $homeAttributes = $this->createHomeAppliancesAttributes();

            // Attach attributes to categories
            $this->attachAttributesToCategories(
                $commonAttributes,
                $fashionAttributes,
                $electronicsAttributes,
                $booksAttributes,
                $homeAttributes
            );
        });
    }

    /**
     * Create common attributes used across multiple categories.
     */
    private function createCommonAttributes(): array
    {
        $brand = Attribute::create([
            'name' => 'Thương hiệu',
            'slug' => 'thuong-hieu',
            'input_type' => 'select',
            'description' => 'Thương hiệu sản phẩm',
            'sort_order' => 1,
        ]);

        AttributeOption::insert([
            ['attribute_id' => $brand->id, 'value' => 'Apple', 'sort_order' => 1],
            ['attribute_id' => $brand->id, 'value' => 'Samsung', 'sort_order' => 2],
            ['attribute_id' => $brand->id, 'value' => 'Sony', 'sort_order' => 3],
            ['attribute_id' => $brand->id, 'value' => 'LG', 'sort_order' => 4],
            ['attribute_id' => $brand->id, 'value' => 'Nike', 'sort_order' => 5],
            ['attribute_id' => $brand->id, 'value' => 'Adidas', 'sort_order' => 6],
            ['attribute_id' => $brand->id, 'value' => 'Uniqlo', 'sort_order' => 7],
            ['attribute_id' => $brand->id, 'value' => 'Zara', 'sort_order' => 8],
            ['attribute_id' => $brand->id, 'value' => 'Xiaomi', 'sort_order' => 9],
            ['attribute_id' => $brand->id, 'value' => 'Khác', 'sort_order' => 10],
        ]);

        $color = Attribute::create([
            'name' => 'Màu sắc',
            'slug' => 'mau-sac',
            'input_type' => 'select',
            'description' => 'Màu sắc sản phẩm',
            'sort_order' => 2,
        ]);

        AttributeOption::insert([
            ['attribute_id' => $color->id, 'value' => 'Đen', 'color_code' => '#000000', 'sort_order' => 1],
            ['attribute_id' => $color->id, 'value' => 'Trắng', 'color_code' => '#FFFFFF', 'sort_order' => 2],
            ['attribute_id' => $color->id, 'value' => 'Đỏ', 'color_code' => '#FF0000', 'sort_order' => 3],
            ['attribute_id' => $color->id, 'value' => 'Xanh dương', 'color_code' => '#0000FF', 'sort_order' => 4],
            ['attribute_id' => $color->id, 'value' => 'Xanh lá', 'color_code' => '#00FF00', 'sort_order' => 5],
            ['attribute_id' => $color->id, 'value' => 'Vàng', 'color_code' => '#FFFF00', 'sort_order' => 6],
            ['attribute_id' => $color->id, 'value' => 'Hồng', 'color_code' => '#FFC0CB', 'sort_order' => 7],
            ['attribute_id' => $color->id, 'value' => 'Xám', 'color_code' => '#808080', 'sort_order' => 8],
            ['attribute_id' => $color->id, 'value' => 'Nâu', 'color_code' => '#A52A2A', 'sort_order' => 9],
        ]);

        $warranty = Attribute::create([
            'name' => 'Thời gian bảo hành',
            'slug' => 'bao-hanh',
            'input_type' => 'select',
            'description' => 'Thời gian bảo hành sản phẩm',
            'sort_order' => 99,
        ]);

        AttributeOption::insert([
            ['attribute_id' => $warranty->id, 'value' => 'Không bảo hành', 'sort_order' => 1],
            ['attribute_id' => $warranty->id, 'value' => '6 tháng', 'sort_order' => 2],
            ['attribute_id' => $warranty->id, 'value' => '12 tháng', 'sort_order' => 3],
            ['attribute_id' => $warranty->id, 'value' => '24 tháng', 'sort_order' => 4],
            ['attribute_id' => $warranty->id, 'value' => '36 tháng', 'sort_order' => 5],
        ]);

        return compact('brand', 'color', 'warranty');
    }

    /**
     * Create fashion-specific attributes.
     */
    private function createFashionAttributes(): array
    {
        $size = Attribute::create([
            'name' => 'Kích thước',
            'slug' => 'kich-thuoc',
            'input_type' => 'select',
            'description' => 'Kích thước quần áo, giày dép',
            'sort_order' => 3,
        ]);

        AttributeOption::insert([
            ['attribute_id' => $size->id, 'value' => 'XS', 'sort_order' => 1],
            ['attribute_id' => $size->id, 'value' => 'S', 'sort_order' => 2],
            ['attribute_id' => $size->id, 'value' => 'M', 'sort_order' => 3],
            ['attribute_id' => $size->id, 'value' => 'L', 'sort_order' => 4],
            ['attribute_id' => $size->id, 'value' => 'XL', 'sort_order' => 5],
            ['attribute_id' => $size->id, 'value' => 'XXL', 'sort_order' => 6],
            ['attribute_id' => $size->id, 'value' => '3XL', 'sort_order' => 7],
        ]);

        $material = Attribute::create([
            'name' => 'Chất liệu',
            'slug' => 'chat-lieu',
            'input_type' => 'select',
            'description' => 'Chất liệu vải, da',
            'sort_order' => 4,
        ]);

        AttributeOption::insert([
            ['attribute_id' => $material->id, 'value' => 'Cotton', 'sort_order' => 1],
            ['attribute_id' => $material->id, 'value' => 'Polyester', 'sort_order' => 2],
            ['attribute_id' => $material->id, 'value' => 'Da thật', 'sort_order' => 3],
            ['attribute_id' => $material->id, 'value' => 'Da PU', 'sort_order' => 4],
            ['attribute_id' => $material->id, 'value' => 'Vải thun', 'sort_order' => 5],
            ['attribute_id' => $material->id, 'value' => 'Lụa', 'sort_order' => 6],
        ]);

        $gender = Attribute::create([
            'name' => 'Giới tính',
            'slug' => 'gioi-tinh',
            'input_type' => 'select',
            'description' => 'Sản phẩm dành cho',
            'sort_order' => 5,
        ]);

        AttributeOption::insert([
            ['attribute_id' => $gender->id, 'value' => 'Nam', 'sort_order' => 1],
            ['attribute_id' => $gender->id, 'value' => 'Nữ', 'sort_order' => 2],
            ['attribute_id' => $gender->id, 'value' => 'Unisex', 'sort_order' => 3],
        ]);

        return compact('size', 'material', 'gender');
    }

    /**
     * Create electronics-specific attributes.
     */
    private function createElectronicsAttributes(): array
    {
        $ram = Attribute::create([
            'name' => 'RAM',
            'slug' => 'ram',
            'input_type' => 'select',
            'description' => 'Dung lượng RAM',
            'sort_order' => 10,
        ]);

        AttributeOption::insert([
            ['attribute_id' => $ram->id, 'value' => '2GB', 'sort_order' => 1],
            ['attribute_id' => $ram->id, 'value' => '4GB', 'sort_order' => 2],
            ['attribute_id' => $ram->id, 'value' => '6GB', 'sort_order' => 3],
            ['attribute_id' => $ram->id, 'value' => '8GB', 'sort_order' => 4],
            ['attribute_id' => $ram->id, 'value' => '12GB', 'sort_order' => 5],
            ['attribute_id' => $ram->id, 'value' => '16GB', 'sort_order' => 6],
            ['attribute_id' => $ram->id, 'value' => '32GB', 'sort_order' => 7],
        ]);

        $storage = Attribute::create([
            'name' => 'Bộ nhớ trong',
            'slug' => 'bo-nho-trong',
            'input_type' => 'select',
            'description' => 'Dung lượng bộ nhớ',
            'sort_order' => 11,
        ]);

        AttributeOption::insert([
            ['attribute_id' => $storage->id, 'value' => '32GB', 'sort_order' => 1],
            ['attribute_id' => $storage->id, 'value' => '64GB', 'sort_order' => 2],
            ['attribute_id' => $storage->id, 'value' => '128GB', 'sort_order' => 3],
            ['attribute_id' => $storage->id, 'value' => '256GB', 'sort_order' => 4],
            ['attribute_id' => $storage->id, 'value' => '512GB', 'sort_order' => 5],
            ['attribute_id' => $storage->id, 'value' => '1TB', 'sort_order' => 6],
            ['attribute_id' => $storage->id, 'value' => '2TB', 'sort_order' => 7],
        ]);

        $screenSize = Attribute::create([
            'name' => 'Kích thước màn hình',
            'slug' => 'kich-thuoc-man-hinh',
            'input_type' => 'select',
            'description' => 'Kích thước màn hình (inch)',
            'sort_order' => 12,
        ]);

        AttributeOption::insert([
            ['attribute_id' => $screenSize->id, 'value' => '5.5"', 'sort_order' => 1],
            ['attribute_id' => $screenSize->id, 'value' => '6.1"', 'sort_order' => 2],
            ['attribute_id' => $screenSize->id, 'value' => '6.5"', 'sort_order' => 3],
            ['attribute_id' => $screenSize->id, 'value' => '6.7"', 'sort_order' => 4],
            ['attribute_id' => $screenSize->id, 'value' => '13"', 'sort_order' => 5],
            ['attribute_id' => $screenSize->id, 'value' => '14"', 'sort_order' => 6],
            ['attribute_id' => $screenSize->id, 'value' => '15.6"', 'sort_order' => 7],
            ['attribute_id' => $screenSize->id, 'value' => '17"', 'sort_order' => 8],
        ]);

        $processor = Attribute::create([
            'name' => 'Bộ xử lý',
            'slug' => 'bo-xu-ly',
            'input_type' => 'text',
            'description' => 'CPU / Chip xử lý',
            'sort_order' => 13,
        ]);

        return compact('ram', 'storage', 'screenSize', 'processor');
    }

    /**
     * Create book-specific attributes.
     */
    private function createBooksAttributes(): array
    {
        $author = Attribute::create([
            'name' => 'Tác giả',
            'slug' => 'tac-gia',
            'input_type' => 'text',
            'description' => 'Tên tác giả',
            'sort_order' => 20,
        ]);

        $publisher = Attribute::create([
            'name' => 'Nhà xuất bản',
            'slug' => 'nha-xuat-ban',
            'input_type' => 'text',
            'description' => 'Nhà xuất bản sách',
            'sort_order' => 21,
        ]);

        $publishYear = Attribute::create([
            'name' => 'Năm xuất bản',
            'slug' => 'nam-xuat-ban',
            'input_type' => 'number',
            'description' => 'Năm xuất bản',
            'sort_order' => 22,
        ]);

        $language = Attribute::create([
            'name' => 'Ngôn ngữ',
            'slug' => 'ngon-ngu',
            'input_type' => 'select',
            'description' => 'Ngôn ngữ sách',
            'sort_order' => 23,
        ]);

        AttributeOption::insert([
            ['attribute_id' => $language->id, 'value' => 'Tiếng Việt', 'sort_order' => 1],
            ['attribute_id' => $language->id, 'value' => 'Tiếng Anh', 'sort_order' => 2],
            ['attribute_id' => $language->id, 'value' => 'Tiếng Trung', 'sort_order' => 3],
            ['attribute_id' => $language->id, 'value' => 'Tiếng Nhật', 'sort_order' => 4],
            ['attribute_id' => $language->id, 'value' => 'Tiếng Hàn', 'sort_order' => 5],
        ]);

        $pageCount = Attribute::create([
            'name' => 'Số trang',
            'slug' => 'so-trang',
            'input_type' => 'number',
            'description' => 'Số trang sách',
            'sort_order' => 24,
        ]);

        return compact('author', 'publisher', 'publishYear', 'language', 'pageCount');
    }

    /**
     * Create home & appliances attributes.
     */
    private function createHomeAppliancesAttributes(): array
    {
        $power = Attribute::create([
            'name' => 'Công suất',
            'slug' => 'cong-suat',
            'input_type' => 'text',
            'description' => 'Công suất tiêu thụ điện',
            'sort_order' => 30,
        ]);

        $capacity = Attribute::create([
            'name' => 'Dung tích',
            'slug' => 'dung-tich',
            'input_type' => 'select',
            'description' => 'Dung tích (lít)',
            'sort_order' => 31,
        ]);

        AttributeOption::insert([
            ['attribute_id' => $capacity->id, 'value' => '0.5L', 'sort_order' => 1],
            ['attribute_id' => $capacity->id, 'value' => '1L', 'sort_order' => 2],
            ['attribute_id' => $capacity->id, 'value' => '1.5L', 'sort_order' => 3],
            ['attribute_id' => $capacity->id, 'value' => '2L', 'sort_order' => 4],
            ['attribute_id' => $capacity->id, 'value' => '3L', 'sort_order' => 5],
        ]);

        $dimensions = Attribute::create([
            'name' => 'Kích thước',
            'slug' => 'kich-thuoc-san-pham',
            'input_type' => 'text',
            'description' => 'Kích thước sản phẩm (dài x rộng x cao)',
            'sort_order' => 32,
        ]);

        $weight = Attribute::create([
            'name' => 'Trọng lượng',
            'slug' => 'trong-luong',
            'input_type' => 'text',
            'description' => 'Trọng lượng sản phẩm',
            'sort_order' => 33,
        ]);

        return compact('power', 'capacity', 'dimensions', 'weight');
    }

    /**
     * Attach attributes to categories.
     */
    private function attachAttributesToCategories($common, $fashion, $electronics, $books, $home): void
    {
        // Find or create categories
        $fashionCategory = Category::firstOrCreate(
            ['slug' => 'thoi-trang'],
            ['category_name' => 'Thời trang', 'display_order' => 1, 'is_active' => true]
        );
        $electronicsCategory = Category::firstOrCreate(
            ['slug' => 'dien-tu'],
            ['category_name' => 'Điện tử', 'display_order' => 2, 'is_active' => true]
        );
        $booksCategory = Category::firstOrCreate(
            ['slug' => 'sach'],
            ['category_name' => 'Sách', 'display_order' => 3, 'is_active' => true]
        );
        $homeCategory = Category::firstOrCreate(
            ['slug' => 'gia-dung'],
            ['category_name' => 'Gia dụng', 'display_order' => 4, 'is_active' => true]
        );

        // Fashion category
        if ($fashionCategory) {
            $fashionCategory->attributes()->attach([
                $common['brand']->id => ['is_variant' => false, 'is_required' => false, 'is_filterable' => true, 'sort_order' => 1],
                $common['color']->id => ['is_variant' => true, 'is_required' => true, 'is_filterable' => true, 'sort_order' => 2],
                $fashion['size']->id => ['is_variant' => true, 'is_required' => true, 'is_filterable' => true, 'sort_order' => 3],
                $fashion['material']->id => ['is_variant' => false, 'is_required' => false, 'is_filterable' => true, 'sort_order' => 4],
                $fashion['gender']->id => ['is_variant' => false, 'is_required' => false, 'is_filterable' => true, 'sort_order' => 5],
            ]);
        }

        // Electronics category
        if ($electronicsCategory) {
            $electronicsCategory->attributes()->attach([
                $common['brand']->id => ['is_variant' => false, 'is_required' => true, 'is_filterable' => true, 'sort_order' => 1],
                $common['color']->id => ['is_variant' => true, 'is_required' => false, 'is_filterable' => true, 'sort_order' => 2],
                $electronics['ram']->id => ['is_variant' => true, 'is_required' => false, 'is_filterable' => true, 'sort_order' => 3],
                $electronics['storage']->id => ['is_variant' => true, 'is_required' => false, 'is_filterable' => true, 'sort_order' => 4],
                $electronics['screenSize']->id => ['is_variant' => false, 'is_required' => false, 'is_filterable' => true, 'sort_order' => 5],
                $electronics['processor']->id => ['is_variant' => false, 'is_required' => false, 'is_filterable' => false, 'sort_order' => 6],
                $common['warranty']->id => ['is_variant' => false, 'is_required' => false, 'is_filterable' => true, 'sort_order' => 99],
            ]);
        }

        // Books category
        if ($booksCategory) {
            $booksCategory->attributes()->attach([
                $books['author']->id => ['is_variant' => false, 'is_required' => true, 'is_filterable' => false, 'sort_order' => 1],
                $books['publisher']->id => ['is_variant' => false, 'is_required' => false, 'is_filterable' => true, 'sort_order' => 2],
                $books['publishYear']->id => ['is_variant' => false, 'is_required' => false, 'is_filterable' => true, 'sort_order' => 3],
                $books['language']->id => ['is_variant' => false, 'is_required' => false, 'is_filterable' => true, 'sort_order' => 4],
                $books['pageCount']->id => ['is_variant' => false, 'is_required' => false, 'is_filterable' => false, 'sort_order' => 5],
            ]);
        }

        // Home & Appliances category
        if ($homeCategory) {
            $homeCategory->attributes()->attach([
                $common['brand']->id => ['is_variant' => false, 'is_required' => false, 'is_filterable' => true, 'sort_order' => 1],
                $common['color']->id => ['is_variant' => true, 'is_required' => false, 'is_filterable' => true, 'sort_order' => 2],
                $home['power']->id => ['is_variant' => false, 'is_required' => false, 'is_filterable' => true, 'sort_order' => 3],
                $home['capacity']->id => ['is_variant' => true, 'is_required' => false, 'is_filterable' => true, 'sort_order' => 4],
                $home['dimensions']->id => ['is_variant' => false, 'is_required' => false, 'is_filterable' => false, 'sort_order' => 5],
                $home['weight']->id => ['is_variant' => false, 'is_required' => false, 'is_filterable' => false, 'sort_order' => 6],
                $common['warranty']->id => ['is_variant' => false, 'is_required' => false, 'is_filterable' => true, 'sort_order' => 99],
            ]);
        }
    }
}
