# Product Attribute System - Frontend Integration Guide

## Data Structure cho Product Create/Update

### 1. Request Format khi tạo/cập nhật Product

```json
{
  "product_name": "iPhone 15 Pro Max",
  "category_id": 2,
  "description": "...",
  "base_price": 29990000,
  "status": "active",
  
  // Product-level attributes (specifications - không tạo SKU)
  "attributes": {
    "11": {  // brand attribute_id
      "attribute_option_id": 1  // Apple option
    },
    "13": {  // processor attribute_id (text input)
      "text_value": "Apple A17 Pro"
    },
    "3": {  // warranty attribute_id
      "attribute_option_id": 12  // 12 tháng
    }
  },
  
  // Variant-level attributes (tạo SKU combinations)
  "variants": [
    {
      "attributes": {
        "10": {  // color attribute_id
          "attribute_option_id": 4  // Titan Tự nhiên
        },
        "12": {  // storage attribute_id
          "attribute_option_id": 22  // 256GB
        }
      },
      "price": 29990000,
      "stock_quantity": 10,
      "sku": "IP15PM-TN-256",
      "images": [File, File, ...]
    },
    {
      "attributes": {
        "10": {  // color
          "attribute_option_id": 4  // Titan Tự nhiên
        },
        "12": {  // storage
          "attribute_option_id": 23  // 512GB
        }
      },
      "price": 34990000,
      "stock_quantity": 5,
      "sku": "IP15PM-TN-512",
      "images": [...]
    }
  ],
  
  "images": [File, File, ...]  // Product-level images
}
```

### 2. Response Format từ API `/api/attributes/category/{categoryId}`

```json
{
  "success": true,
  "data": {
    "variant_attributes": [
      {
        "id": 10,
        "name": "Màu sắc",
        "slug": "mau-sac",
        "input_type": "select",
        "description": "Màu sắc sản phẩm",
        "is_required": false,
        "is_filterable": true,
        "sort_order": 2,
        "options": [
          {
            "id": 1,
            "value": "Đen",
            "label": "Đen",
            "color_code": "#000000"
          },
          {
            "id": 4,
            "value": "Titan Tự nhiên",
            "label": "Titan Tự nhiên",
            "color_code": "#D4CFC9"
          }
        ]
      },
      {
        "id": 11,
        "name": "RAM",
        "slug": "ram",
        "input_type": "select",
        "description": "Dung lượng RAM",
        "is_required": false,
        "is_filterable": true,
        "sort_order": 3,
        "options": [
          {"id": 15, "value": "4GB", "label": "4GB"},
          {"id": 16, "value": "8GB", "label": "8GB"},
          {"id": 17, "value": "16GB", "label": "16GB"}
        ]
      }
    ],
    "specification_attributes": [
      {
        "id": 1,
        "name": "Thương hiệu",
        "slug": "thuong-hieu",
        "input_type": "select",
        "description": "Thương hiệu sản phẩm",
        "is_required": false,
        "is_filterable": true,
        "sort_order": 1,
        "options": [
          {"id": 1, "value": "Apple", "label": "Apple"},
          {"id": 2, "value": "Samsung", "label": "Samsung"}
        ]
      },
      {
        "id": 13,
        "name": "Bộ xử lý",
        "slug": "bo-xu-ly",
        "input_type": "text",  // Text input, không có options
        "description": "CPU / Chip xử lý",
        "is_required": false,
        "is_filterable": false,
        "sort_order": 13,
        "options": []
      }
    ]
  }
}
```

### 3. Response Format từ `/api/attributes/generate-variants`

**Request:**
```json
{
  "variant_attributes": {
    "10": [4, 5, 6],     // color: Titan Tự nhiên, Xanh, Trắng
    "12": [22, 23, 24]   // storage: 256GB, 512GB, 1TB
  }
}
```

**Response:**
```json
{
  "success": true,
  "combinations": [
    {
      "attributes": {
        "10": {"attribute_option_id": 4},  // Titan Tự nhiên
        "12": {"attribute_option_id": 22}  // 256GB
      },
      "display_name": "Titan Tự nhiên - 256GB"
    },
    {
      "attributes": {
        "10": {"attribute_option_id": 4},
        "12": {"attribute_option_id": 23}
      },
      "display_name": "Titan Tự nhiên - 512GB"
    },
    // ... 9 combinations total (3 colors × 3 storage)
  ]
}
```

## Input Types

| Type | Description | Example | Has Options |
|------|-------------|---------|-------------|
| `select` | Dropdown/select box | Color, Size, RAM | ✅ Yes |
| `text` | Text input | Product dimensions, Author name | ❌ No |
| `number` | Number input | Page count, Weight | ❌ No |
| `boolean` | Checkbox/toggle | Has warranty, Is waterproof | ❌ No |
| `date` | Date picker | Publish date, Manufacture date | ❌ No |
| `textarea` | Multi-line text | Technical specifications | ❌ No |
| `color` | Color picker | Custom color (hex code) | ❌ No |

## Attribute Flags

- **`is_variant`**: `true` = tạo SKU, `false` = chỉ hiển thị thông số
- **`is_required`**: Bắt buộc nhập khi tạo product
- **`is_filterable`**: Hiển thị trong filter sidebar

## UI Flow

### Create Product Page

1. **User chọn Category** → Trigger API call:
   ```javascript
   const response = await fetch(`/api/attributes/category/${categoryId}`);
   const { variant_attributes, specification_attributes } = response.data;
   ```

2. **Render Specification Attributes Section** (không tạo variant):
   - Duyệt qua `specification_attributes`
   - Render input theo `input_type`
   - Lưu vào `attributes` object trong request

3. **Render Variant Attributes Section** (tạo SKU):
   - Cho user chọn giá trị cho mỗi variant attribute
   - Khi user click "Generate Variants" → Call `/api/attributes/generate-variants`
   - Hiển thị variant matrix với price, stock, images inputs

4. **Submit Form**:
   - Format data theo structure trên
   - POST to `/seller/products/store`

### Edit Product Page

1. Load existing product with attributes:
   ```javascript
   // ProductController.edit() returns
   {
     product: {
       id: 123,
       product_name: "iPhone 15 Pro Max",
       category_id: 2,
       attributes: {
         "11": {  // brand attribute_id
           attribute_option_id: 1  // Apple
         },
         "13": {  // processor attribute_id
           text_value: "Apple A17 Pro"
         }
       },
       variants: [
         {
           id: 456,
           variant_name: "Titan Tự nhiên - 256GB",
           sku: "IP15PM-TN-256",
           price: 29990000,
           stock_quantity: 10,
           attributes: {
             "10": { attribute_option_id: 4 },  // color
             "12": { attribute_option_id: 22 }  // storage
           },
           images: [...]
         }
       ],
       images: [...]
     },
     categories: [...] // Same structure as create()
   }
   ```

2. Pre-fill form với existing values
3. Update theo flow tương tự create

### Show/Read Product Page

Product detail view returns formatted attribute values for display:

```javascript
// ProductController.show() returns
{
  product: {
    product_id: 123,
    product_name: "iPhone 15 Pro Max",
    category: { category_name: "Điện tử" },
    attributes: [
      { attribute_name: "Thương hiệu", value: "Apple" },
      { attribute_name: "Bộ xử lý", value: "Apple A17 Pro" },
      { attribute_name: "Bảo hành", value: "12 tháng" }
    ],
    variants: [
      {
        variant_name: "Titan Tự nhiên - 256GB",
        sku: "IP15PM-TN-256",
        price: 29990000,
        stock_quantity: 10,
        attributes: [
          { attribute_name: "Màu sắc", value: "Titan Tự nhiên" },
          { attribute_name: "Bộ nhớ trong", value: "256GB" }
        ],
        images: [...]
      }
    ],
    images: [...]
  }
}
```

Use `display_value` accessor for user-friendly display.

## Database Schema Reference

```
attributes
├─ id, name, slug, input_type, description, sort_order, is_active

attribute_options
├─ id, attribute_id, value, label, color_code, sort_order

category_attribute (pivot)
├─ category_id, attribute_id, is_variant, is_required, is_filterable, sort_order

product_attribute_values (specifications)
├─ product_id, attribute_id, attribute_option_id, text_value

product_variant_attribute_values (variants)
├─ product_variant_id, attribute_id, attribute_option_id, text_value
```

## Example Categories

| Category | Variant Attributes | Specification Attributes |
|----------|-------------------|-------------------------|
| **Thời trang** | Color, Size | Brand, Material, Gender |
| **Điện tử** | Color, RAM, Storage | Brand, Processor, Screen Size, Warranty |
| **Sách** | - | Author, Publisher, Publish Year, Language, Page Count |
| **Gia dụng** | Color, Capacity | Brand, Power, Dimensions, Weight, Warranty |

## Validation Rules

- Variant attributes phải có ít nhất 1 combination
- Required attributes phải được điền
- Select-type attributes chỉ chấp nhận `attribute_option_id` từ options list
- Text-type attributes lưu vào `text_value`
- Không được tạo duplicate variant combinations

## Error Handling

Backend sẽ trả về errors nếu:
- Duplicate variant combination
- Missing required attributes
- Invalid attribute_option_id
- Wrong input type (VD: text_value cho select-type attribute)

Format error response:
```json
{
  "message": "Validation failed",
  "errors": {
    "attributes.11": ["Attribute option không hợp lệ"],
    "variants.0.attributes": ["Combination này đã tồn tại"]
  }
}
```
