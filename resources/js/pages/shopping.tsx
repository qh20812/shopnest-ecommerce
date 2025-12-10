import { useState } from 'react';
import { Heart, ShoppingBag, MessageCircle } from 'lucide-react';
import { Link, router } from '@inertiajs/react';
import TopNav from '../components/top-nav';
import Footer from '../components/footer';
import { ToastProvider, useToast } from '../lib/toastContext';
import Input from '../components/ui/input';

interface Product {
    id: number;
    name: string;
    image: string;
    price: number;
    originalPrice?: number;
    rating: number;
    reviews: number;
    brand: string;
}

interface Category {
    id: number | string;
    name: string;
}

interface Brand {
    id: number;
    name: string;
}

interface Pagination {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
}

interface Filters {
    category: string;
    min_price: string | null;
    max_price: string | null;
    selected_brands: number[];
    sort_by: string;
}

interface ShoppingProps {
    products: Product[];
    pagination: Pagination;
    categories: Category[];
    brands: Brand[];
    filters: Filters;
}

function ShoppingContent({ products, pagination, categories, brands, filters }: ShoppingProps) {
    const { showSuccess } = useToast();
    const [selectedCategory, setSelectedCategory] = useState(filters.category);
    const [minPrice, setMinPrice] = useState(filters.min_price || '');
    const [maxPrice, setMaxPrice] = useState(filters.max_price || '');
    const [selectedBrands, setSelectedBrands] = useState<number[]>(filters.selected_brands);
    const [sortBy, setSortBy] = useState(filters.sort_by);

    const formatPrice = (price: number) =>
        new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND',
        }).format(price);

    const handleBrandChange = (brandId: number) => {
        if (selectedBrands.includes(brandId)) {
            setSelectedBrands(selectedBrands.filter((b) => b !== brandId));
        } else {
            setSelectedBrands([...selectedBrands, brandId]);
        }
    };

    const applyFilters = () => {
        router.get('/shopping', {
            category: selectedCategory !== 'all' ? selectedCategory : undefined,
            min_price: minPrice || undefined,
            max_price: maxPrice || undefined,
            brands: selectedBrands.length > 0 ? selectedBrands : undefined,
            sort_by: sortBy,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const goToPage = (page: number) => {
        router.get('/shopping', {
            category: selectedCategory !== 'all' ? selectedCategory : undefined,
            min_price: minPrice || undefined,
            max_price: maxPrice || undefined,
            brands: selectedBrands.length > 0 ? selectedBrands : undefined,
            sort_by: sortBy,
            page: page,
        }, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const handleAddToCart = () => {
        showSuccess('Đã thêm sản phẩm vào giỏ hàng');
    };

    const handleAddToWishlist = () => {
        showSuccess('Đã thêm vào danh sách yêu thích');
    };

    return (
        <div className="relative flex min-h-screen w-full flex-col overflow-x-hidden bg-background text-foreground">
            <TopNav />

            <main className="flex-1">
                <div className="container mx-auto px-4 py-8 md:py-12">
                    {/* Page Header */}
                    <div className="mb-6">
                        <h1 className="mb-2 text-3xl font-bold text-foreground md:text-4xl">
                            Tất cả sản phẩm
                        </h1>
                        <p className="text-muted-foreground">
                            Khám phá bộ sưu tập sản phẩm được tuyển chọn của chúng tôi.
                        </p>
                    </div>

                    <div className="flex flex-col gap-8 lg:flex-row">
                        {/* Sidebar Filters */}
                        <aside className="w-full lg:w-1/4 xl:w-1/5">
                            <div className="space-y-6">
                                {/* Categories */}
                                <div>
                                    <h3 className="mb-4 text-lg font-bold text-foreground">Danh mục</h3>
                                    <ul className="space-y-2">
                                        {categories.map((category) => (
                                            <li key={category.id}>
                                                <button
                                                    onClick={() => {
                                                        setSelectedCategory(String(category.id));
                                                        router.get('/shopping', {
                                                            category: category.id !== 'all' ? category.id : undefined,
                                                            min_price: minPrice || undefined,
                                                            max_price: maxPrice || undefined,
                                                            brands: selectedBrands.length > 0 ? selectedBrands : undefined,
                                                            sort_by: sortBy,
                                                        }, {
                                                            preserveState: true,
                                                            preserveScroll: true,
                                                        });
                                                    }}
                                                    className={`text-sm transition-colors ${
                                                        selectedCategory === String(category.id)
                                                            ? 'font-medium text-primary'
                                                            : 'text-muted-foreground hover:text-primary'
                                                    }`}
                                                >
                                                    {category.name}
                                                </button>
                                            </li>
                                        ))}
                                    </ul>
                                </div>

                                {/* Price Filter */}
                                <div className="border-t border-border pt-6">
                                    <h3 className="mb-4 text-lg font-bold text-foreground">Lọc theo giá</h3>
                                    <div className="space-y-2">
                                        <div className="flex items-center gap-2">
                                            <Input
                                                type="text"
                                                placeholder="Tối thiểu"
                                                value={minPrice}
                                                onChange={(e) => setMinPrice(e.target.value)}
                                                className="h-10"
                                            />
                                            <span className="text-muted-foreground">-</span>
                                            <Input
                                                type="text"
                                                placeholder="Tối đa"
                                                value={maxPrice}
                                                onChange={(e) => setMaxPrice(e.target.value)}
                                                className="h-10"
                                            />
                                        </div>
                                        <button
                                            onClick={applyFilters}
                                            className="w-full rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-primary/90"
                                        >
                                            Áp dụng
                                        </button>
                                    </div>
                                </div>

                                {/* Brands */}
                                <div className="border-t border-border pt-6">
                                    <h3 className="mb-4 text-lg font-bold text-foreground">Thương hiệu</h3>
                                    <div className="space-y-2">
                                        <ul className="space-y-2">
                                            {brands.map((brand) => (
                                                <li key={brand.id} className="flex items-center">
                                                    <input
                                                        type="checkbox"
                                                        id={`brand-${brand.id}`}
                                                        checked={selectedBrands.includes(brand.id)}
                                                        onChange={() => handleBrandChange(brand.id)}
                                                        className="h-4 w-4 rounded border-border text-primary focus:ring-primary/50"
                                                    />
                                                    <label
                                                        htmlFor={`brand-${brand.id}`}
                                                        className="ml-2 cursor-pointer text-sm text-foreground"
                                                    >
                                                        {brand.name}
                                                    </label>
                                                </li>
                                            ))}
                                        </ul>
                                        <button
                                            onClick={applyFilters}
                                            className="w-full rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-primary/90"
                                        >
                                            Áp dụng
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </aside>

                        {/* Products Grid */}
                        <div className="w-full lg:w-3/4 xl:w-4/5">
                            {/* Toolbar */}
                            <div className="mb-6 flex flex-col items-center justify-between rounded-lg bg-card p-4 sm:flex-row">
                                <p className="mb-2 text-sm text-muted-foreground sm:mb-0">
                                    Hiển thị {pagination.from}–{pagination.to} của {pagination.total} sản phẩm
                                </p>
                                <div className="flex items-center gap-2">
                                    <span className="text-sm font-medium text-foreground">Sắp xếp theo:</span>
                                    <select
                                        value={sortBy}
                                        onChange={(e) => {
                                            setSortBy(e.target.value);
                                            router.get('/shopping', {
                                                category: selectedCategory !== 'all' ? selectedCategory : undefined,
                                                min_price: minPrice || undefined,
                                                max_price: maxPrice || undefined,
                                                brands: selectedBrands.length > 0 ? selectedBrands : undefined,
                                                sort_by: e.target.value,
                                            }, {
                                                preserveState: true,
                                                preserveScroll: true,
                                            });
                                        }}
                                        className="rounded-lg border-border bg-background text-sm text-foreground focus:border-primary focus:ring-primary/50"
                                    >
                                        <option value="popular">Phổ biến nhất</option>
                                        <option value="newest">Mới nhất</option>
                                        <option value="price-asc">Giá: Thấp đến Cao</option>
                                        <option value="price-desc">Giá: Cao đến Thấp</option>
                                    </select>
                                </div>
                            </div>

                            {/* Products Grid */}
                            <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">
                                {products.map((product) => (
                                    <div
                                        key={product.id}
                                        className="group flex flex-col overflow-hidden rounded-lg border border-border bg-card transition-shadow hover:shadow-xl"
                                    >
                                        <div className="relative">
                                            <Link href={`/product/${product.id}`} className="block aspect-square w-full overflow-hidden bg-gray-200">
                                                <img
                                                    src={product.image}
                                                    alt={product.name}
                                                    className="h-full w-full object-cover"
                                                />
                                            </Link>
                                            <button
                                                onClick={handleAddToWishlist}
                                                className="absolute right-3 top-3 flex h-10 w-10 items-center justify-center rounded-full bg-white/90 text-muted-foreground shadow-md transition-colors hover:bg-white hover:text-primary z-10"
                                            >
                                                <Heart className="h-5 w-5" />
                                            </button>
                                        </div>
                                        <div className="flex flex-col gap-4 p-4">
                                            <div>
                                                <Link href={`/product/${product.id}`} className="group-hover:text-primary">
                                                    <h3 className="mb-1 text-lg font-bold text-foreground">
                                                        {product.name}
                                                    </h3>
                                                </Link>
                                                <p className="mb-2 text-sm text-muted-foreground">
                                                    {product.brand}
                                                </p>
                                                <div className="mb-2 flex items-center gap-1">
                                                    <span className="text-sm font-medium text-foreground">
                                                        {product.rating}
                                                    </span>
                                                    <span className="text-sm text-muted-foreground">
                                                        ({product.reviews} đánh giá)
                                                    </span>
                                                </div>
                                            </div>
                                            <div className="flex items-center justify-between">
                                                <div className="flex flex-col">
                                                    <span className="text-xl font-bold text-primary">
                                                        {formatPrice(product.price)}
                                                    </span>
                                                    {product.originalPrice && (
                                                        <span className="text-sm text-muted-foreground line-through">
                                                            {formatPrice(product.originalPrice)}
                                                        </span>
                                                    )}
                                                </div>
                                                <button
                                                    onClick={(e) => { e.preventDefault(); handleAddToCart(); }}
                                                    className="flex h-10 w-10 items-center justify-center rounded-lg bg-secondary text-white transition-colors hover:bg-secondary/90"
                                                >
                                                    <ShoppingBag className="h-5 w-5" />
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>

                            {/* Pagination */}
                            {pagination.last_page > 1 && (
                                <nav className="mt-10 flex justify-center">
                                    <ul className="-space-x-px flex h-10 items-center text-base">
                                        <li>
                                            <button
                                                onClick={() => goToPage(pagination.current_page - 1)}
                                                disabled={pagination.current_page === 1}
                                                className="ms-0 flex h-10 items-center justify-center rounded-s-lg border border-border bg-background px-4 leading-tight text-muted-foreground transition-colors hover:bg-card disabled:cursor-not-allowed disabled:opacity-50"
                                            >
                                                Trang trước
                                            </button>
                                        </li>
                                        {Array.from({ length: pagination.last_page }, (_, i) => i + 1)
                                            .filter(page => {
                                                const current = pagination.current_page;
                                                return page === 1 || page === pagination.last_page || Math.abs(page - current) <= 2;
                                            })
                                            .map((page, index, array) => (
                                                <li key={page}>
                                                    {index > 0 && array[index - 1] !== page - 1 && (
                                                        <span className="flex h-10 items-center justify-center border border-border bg-background px-4 leading-tight text-muted-foreground">
                                                            ...
                                                        </span>
                                                    )}
                                                    <button
                                                        onClick={() => goToPage(page)}
                                                        aria-current={pagination.current_page === page ? 'page' : undefined}
                                                        className={`flex h-10 items-center justify-center border px-4 leading-tight transition-colors ${
                                                            pagination.current_page === page
                                                                ? 'border-primary bg-primary text-white'
                                                                : 'border-border bg-background text-muted-foreground hover:bg-card'
                                                        }`}
                                                    >
                                                        {page}
                                                    </button>
                                                </li>
                                            ))}
                                        <li>
                                            <button
                                                onClick={() => goToPage(pagination.current_page + 1)}
                                                disabled={pagination.current_page === pagination.last_page}
                                                className="flex h-10 items-center justify-center rounded-e-lg border border-border bg-background px-4 leading-tight text-muted-foreground transition-colors hover:bg-card disabled:cursor-not-allowed disabled:opacity-50"
                                            >
                                                Trang kế tiếp
                                            </button>
                                        </li>
                                    </ul>
                                </nav>
                            )}
                        </div>
                    </div>
                </div>
            </main>

            <Footer />

            {/* Floating Chat Button */}
            <button className="fixed bottom-6 right-6 z-50 flex h-16 w-16 cursor-pointer items-center justify-center rounded-full bg-secondary text-white shadow-lg transition-transform hover:scale-110">
                <MessageCircle className="h-8 w-8" />
            </button>
        </div>
    );
}

export default function Shopping(props: ShoppingProps) {
    return (
        <ToastProvider>
            <ShoppingContent {...props} />
        </ToastProvider>
    );
}
