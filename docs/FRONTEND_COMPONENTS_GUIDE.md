# Product Attribute System - Frontend Components

## Components Created

### 1. AttributeSelector

**Location:** `resources/js/components/product/AttributeSelector.tsx`

**Purpose:** Dynamic form component that renders different input types based on attribute configuration.

**Props:**
```typescript
interface AttributeSelectorProps {
  attributes: Attribute[];           // List of attributes to render
  values: Record<string, AttributeValue>;  // Current values
  onChange: (attributeId: number, value: AttributeValue) => void;
  errors?: Record<string, string>;   // Validation errors
}
```

**Supported Input Types:**
- `select` - Dropdown with predefined options
- `text` - Text input field
- `number` - Number input field
- `boolean` - Checkbox
- `date` - Date picker
- `textarea` - Multi-line text input
- `color` - Color picker with hex input

**Features:**
- ✅ Required field indicator (red asterisk)
- ✅ Description tooltip
- ✅ Error message display
- ✅ Dark mode support
- ✅ Accessible form controls

**Usage Example:**
```tsx
<AttributeSelector
  attributes={specificationAttributes}
  values={productAttributes}
  onChange={(attrId, value) => {
    setProductAttributes(prev => ({
      ...prev,
      [attrId]: value
    }));
  }}
  errors={errors}
/>
```

---

### 2. VariantMatrixGenerator

**Location:** `resources/js/components/product/VariantMatrixGenerator.tsx`

**Purpose:** Interactive component to generate and manage product variant combinations with price, stock, and images.

**Props:**
```typescript
interface VariantMatrixGeneratorProps {
  variantAttributes: Attribute[];    // Attributes that create SKUs
  basePrice: string;                 // Default price for new variants
  variants: ProductVariant[];        // Current variant list
  onChange: (variants: ProductVariant[]) => void;
}
```

**Features:**
- ✅ Multi-select option buttons for each variant attribute
- ✅ API integration with `/api/attributes/generate-variants`
- ✅ Cartesian product generation
- ✅ Preserve existing variant data when regenerating
- ✅ Per-variant price, stock, SKU inputs
- ✅ Per-variant image upload with preview
- ✅ Remove variant functionality
- ✅ Display variant name from selected options

**Workflow:**
1. User selects options for each variant attribute (e.g., Color: Red, Blue; Size: M, L)
2. User clicks "Tạo biến thể" button
3. Component calls API to generate all combinations (Red-M, Red-L, Blue-M, Blue-L)
4. Renders variant matrix with input fields for each combination
5. User fills in price, stock, uploads images for each variant

**Usage Example:**
```tsx
<VariantMatrixGenerator
  variantAttributes={variantAttributes}
  basePrice={basePrice}
  variants={variants}
  onChange={setVariants}
/>
```

---

## Integration in ProductCreateForm

**File:** `resources/js/pages/roles/sellers/product-manage/create.tsx`

### Changes Made:

1. **Added Imports:**
```tsx
import AttributeSelector, { Attribute, AttributeValue } from '../../../../components/product/AttributeSelector';
import VariantMatrixGenerator from '../../../../components/product/VariantMatrixGenerator';
```

2. **Updated Category Interface:**
```tsx
interface Category {
  id: number;
  category_name: string;
  slug: string;
  attributes?: Attribute[];  // Added
}
```

3. **Updated ProductVariant Interface:**
```tsx
interface ProductVariant {
  attributes: Record<string, AttributeValue>;  // Changed from size/color
  price: string;
  stock_quantity: number;
  sku?: string;
  images: File[];
}
```

4. **Added Form Data:**
```tsx
const { data, setData } = useForm({
  // ... existing fields
  attributes: {} as Record<string, AttributeValue>,  // NEW
  variants: [] as ProductVariant[],
});
```

5. **Category-based Attribute Filtering:**
```tsx
const selectedCategory = categories.find(cat => cat.id === parseInt(data.category_id.toString()));
const specificationAttributes = selectedCategory?.attributes?.filter(attr => !attr.is_variant) || [];
const variantAttributes = selectedCategory?.attributes?.filter(attr => attr.is_variant) || [];
```

6. **Reset on Category Change:**
```tsx
useEffect(() => {
  setData(prev => ({
    ...prev,
    attributes: {},
    variants: [],
  }));
}, [data.category_id]);
```

7. **UI Sections:**
```tsx
{/* Product Attributes (Specifications) */}
{specificationAttributes.length > 0 && (
  <div className="...">
    <h3>Thông số kỹ thuật</h3>
    <AttributeSelector
      attributes={specificationAttributes}
      values={data.attributes}
      onChange={handleAttributeChange}
      errors={errors}
    />
  </div>
)}

{/* Product Variants */}
{variantAttributes.length > 0 && (
  <div className="...">
    <h3>Biến thể sản phẩm</h3>
    <VariantMatrixGenerator
      variantAttributes={variantAttributes}
      basePrice={data.base_price}
      variants={data.variants}
      onChange={handleVariantsChange}
    />
  </div>
)}
```

---

## Data Flow

```
User selects Category
    ↓
ProductController.create() returns categories with attributes
    ↓
Frontend filters attributes by is_variant flag
    ├─ Specification Attributes → AttributeSelector
    └─ Variant Attributes → VariantMatrixGenerator
        ↓
User selects variant options (e.g., Color: Red, Blue)
        ↓
User clicks "Tạo biến thể"
        ↓
POST /api/attributes/generate-variants
        ↓
API returns combinations with display names
        ↓
Component renders variant matrix
        ↓
User fills price, stock, uploads images
        ↓
Form submit with data structure:
{
  attributes: {
    "11": { attribute_option_id: 1 },  // Brand
    "13": { text_value: "A17 Pro" }     // Processor
  },
  variants: [
    {
      attributes: {
        "10": { attribute_option_id: 4 },  // Color
        "12": { attribute_option_id: 22 }  // Storage
      },
      price: "29990000",
      stock_quantity: 10,
      images: [File, File, ...]
    }
  ]
}
```

---

## API Endpoints Used

### GET /api/attributes/category/{categoryId}
Returns variant + specification attributes for category.

**Response:**
```json
{
  "success": true,
  "data": {
    "variant_attributes": [...],
    "specification_attributes": [...]
  }
}
```

### POST /api/attributes/generate-variants
Generates all variant combinations from selected options.

**Request:**
```json
{
  "variant_attributes": {
    "10": [4, 5, 6],     // color option IDs
    "12": [22, 23, 24]   // storage option IDs
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
        "10": { "attribute_option_id": 4 },
        "12": { "attribute_option_id": 22 }
      },
      "display_name": "Titan Tự nhiên - 256GB"
    },
    // ... more combinations
  ]
}
```

---

## Testing

### Manual Test Steps:

1. **Navigate to Product Create Page**
   - Go to `/seller/products/create`

2. **Select Category**
   - Choose "Điện tử" (Electronics)
   - Verify: Specification attributes appear (Brand, Processor, etc.)
   - Verify: Variant attributes appear (Color, RAM, Storage)

3. **Fill Specification Attributes**
   - Select Brand: Apple
   - Enter Processor: A17 Pro
   - Verify values are captured in form data

4. **Generate Variants**
   - Select Color options: Titan Tự nhiên, Xanh
   - Select Storage options: 256GB, 512GB
   - Click "Tạo biến thể"
   - Verify: 4 variants generated (2 colors × 2 storage)

5. **Fill Variant Data**
   - Enter price for each variant
   - Enter stock quantity
   - Upload images for each variant
   - Verify: All data captured correctly

6. **Submit Form**
   - Click "Lưu sản phẩm"
   - Verify: FormData sent with attributes + variants
   - Check network tab: multipart/form-data request

---

## Known Limitations

1. **Text-type Variant Attributes:** Currently only select-type attributes are fully supported for variant generation. Text-type variant attributes would require manual input.

2. **Image Preview Memory:** Image preview URLs are created with `URL.createObjectURL()` and should be revoked when component unmounts. Consider adding cleanup in useEffect.

3. **Large Combination Sets:** Generating 100+ variants may cause performance issues. Consider adding pagination or lazy rendering.

4. **SKU Auto-generation:** SKU field is optional and not auto-generated from attributes. Backend handles this.

---

## Future Enhancements

- [ ] Bulk price/stock update for all variants
- [ ] Import variants from CSV
- [ ] Variant templates (save common combinations)
- [ ] Drag-and-drop image reordering
- [ ] Real-time price calculation (e.g., base price + option surcharge)
- [ ] Validation preview before form submit
- [ ] Undo/redo for variant changes
