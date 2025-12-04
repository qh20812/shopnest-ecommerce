import { useState } from 'react';
import { Search, Grid, List, ChevronLeft, ChevronRight, ShoppingBag, Heart } from 'lucide-react';
import { Link } from '@inertiajs/react';
import TopNav from '../components/top-nav';
import Footer from '../components/footer';
import { ToastProvider } from '../lib/toastContext';
import Input from '../components/ui/input';

interface Product {
    id: number;
    name: string;
    image: string;
    price: number;
    originalPrice?: number;
    rating: number;
    reviews: number;
}

function SearchResultContent() {
    const [searchQuery, setSearchQuery] = useState('Đồng hồ thông minh');
    const [showSuggestions, setShowSuggestions] = useState(false);
    const [viewMode, setViewMode] = useState<'grid' | 'list'>('grid');
    const [selectedCategories, setSelectedCategories] = useState<string[]>([]);
    const [priceRange, setPriceRange] = useState({ min: 0, max: 20000000 });
    const [selectedBrands, setSelectedBrands] = useState<string[]>([]);
    const [sortBy, setSortBy] = useState('relevant');

    const suggestions = [
        { query: 'Đồng hồ thông minh Samsung', highlight: 'Samsung' },
        { query: 'Đồng hồ thông minh Apple Watch', highlight: 'Apple Watch' },
        { query: 'Đồng hồ thông minh Xiaomi', highlight: 'Xiaomi' },
        { query: 'Đồng hồ thông minh giá rẻ', highlight: 'giá rẻ' },
        { query: 'Đồng hồ thông minh chống nước', highlight: 'chống nước' },
    ];

    const products: Product[] = [
        {
            id: 1,
            name: 'Apple Watch Series 9 GPS 45mm',
            image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuA9uGUJM3fOiuvfyaQBWOm22jgY1q5pysCTNfHVYTUJZK5PRh0WMJkzxTBq0DFnSIujRMboVW7Cw1CWVEjVOIxGkElzFfag9xgMyfPPBgjoDST2mxglOz70oqHPwzmBXwnAzbr2hV98rDNZ4HEOoj2yfMYlc3tEFTYer-UUmVXykCYu5wxwFe47rsq-rSClwvzrlux2IKatu94GMsfXXcD7uVvzfbbjjiqSa5Bmx0SNqnq8pECwHe_L6mnDjed0CHoGb1EqaW2puh8',
            price: 9490000,
            originalPrice: 11490000,
            rating: 4.8,
            reviews: 234,
        },
        {
            id: 2,
            name: 'Samsung Galaxy Watch 6 Classic',
            image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuA9uGUJM3fOiuvfyaQBWOm22jgY1q5pysCTNfHVYTUJZK5PRh0WMJkzxTBq0DFnSIujRMboVW7Cw1CWVEjVOIxGkElzFfag9xgMyfPPBgjoDST2mxglOz70oqHPwzmBXwnAzbr2hV98rDNZ4HEOoj2yfMYlc3tEFTYer-UUmVXykCYu5wxwFe47rsq-rSClwvzrlux2IKatu94GMsfXXcD7uVvzfbbjjiqSa5Bmx0SNqnq8pECwHe_L6mnDjed0CHoGb1EqaW2puh8',
            price: 6990000,
            rating: 4.6,
            reviews: 189,
        },
        {
            id: 3,
            name: 'Xiaomi Watch S1 Active',
            image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuA9uGUJM3fOiuvfyaQBWOm22jgY1q5pysCTNfHVYTUJZK5PRh0WMJkzxTBq0DFnSIujRMboVW7Cw1CWVEjVOIxGkElzFfag9xgMyfPPBgjoDST2mxglOz70oqHPwzmBXwnAzbr2hV98rDNZ4HEOoj2yfMYlc3tEFTYer-UUmVXykCYu5wxwFe47rsq-rSClwvzrlux2IKatu94GMsfXXcD7uVvzfbbjjiqSa5Bmx0SNqnq8pECwHe_L6mnDjed0CHoGb1EqaW2puh8',
            price: 3490000,
            rating: 4.5,
            reviews: 156,
        },
        {
            id: 4,
            name: 'Huawei Watch GT 3 Pro',
            image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuA9uGUJM3fOiuvfyaQBWOm22jgY1q5pysCTNfHVYTUJZK5PRh0WMJkzxTBq0DFnSIujRMboVW7Cw1CWVEjVOIxGkElzFfag9xgMyfPPBgjoDST2mxglOz70oqHPwzmBXwnAzbr2hV98rDNZ4HEOoj2yfMYlc3tEFTYer-UUmVXykCYu5wxwFe47rsq-rSClwvzrlux2IKatu94GMsfXXcD7uVvzfbbjjiqSa5Bmx0SNqnq8pECwHe_L6mnDjed0CHoGb1EqaW2puh8',
            price: 5990000,
            rating: 4.7,
            reviews: 201,
        },
    ];

    const formatPrice = (price: number) =>
        new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND',
        }).format(price);

    return (
        <div className="relative flex min-h-screen w-full flex-col overflow-x-hidden bg-background text-foreground">
            <TopNav />

            <main className="flex-1">
                <div className="container mx-auto px-4 py-8 md:py-12">
                    {/* Search Header */}
                    <div className="mb-8 flex flex-col items-center justify-center text-center">
                        <div className="relative w-full max-w-xl">
                            <label className="flex w-full flex-col">
                                <div className="flex h-14 w-full items-stretch rounded-lg">
                                    <div className="flex h-full items-center justify-center rounded-l-lg bg-card pl-4 text-muted-foreground">
                                        <Search className="h-5 w-5" />
                                    </div>
                                    <Input
                                        value={searchQuery}
                                        onChange={(e) => setSearchQuery(e.target.value)}
                                        onFocus={() => setShowSuggestions(true)}
                                        onBlur={() => setTimeout(() => setShowSuggestions(false), 200)}
                                        className="h-full flex-1 rounded-l-none rounded-r-lg border-none bg-card px-4 text-base placeholder:text-muted-foreground focus:ring-2 focus:ring-primary/50"
                                        placeholder="Tìm kiếm sản phẩm..."
                                    />
                                </div>
                            </label>

                            {/* Suggestions Dropdown */}
                            {showSuggestions && (
                                <div className="absolute left-0 right-0 top-full z-20 mt-2 overflow-hidden rounded-xl border border-border bg-card shadow-lg">
                                    <ul className="py-2">
                                        {suggestions.map((suggestion, index) => (
                                            <li key={index}>
                                                <button
                                                    onClick={() => {
                                                        setSearchQuery(suggestion.query);
                                                        setShowSuggestions(false);
                                                    }}
                                                    className="flex w-full items-center gap-3 px-4 py-3 text-left transition-colors hover:bg-primary/10"
                                                >
                                                    <Search className="h-4 w-4 text-muted-foreground" />
                                                    <span className="text-sm text-foreground">
                                                        {suggestion.query}
                                                    </span>
                                                </button>
                                            </li>
                                        ))}
                                    </ul>
                                </div>
                            )}
                        </div>
                        <p className="mt-4 text-sm text-muted-foreground">
                            Hiển thị 12 kết quả cho "{searchQuery}"
                        </p>
                    </div>

                    <div className="flex flex-col gap-8 lg:flex-row">
                        {/* Filters Sidebar */}
                        <aside className="w-full flex-shrink-0 lg:w-64 xl:w-72">
                            <div className="sticky top-24 space-y-6">
                                {/* Categories Filter */}
                                <div className="rounded-xl bg-card p-5">
                                    <h3 className="mb-4 font-bold text-foreground">Danh mục</h3>
                                    <div className="space-y-2">
                                        {['Đồng hồ thông minh', 'Đồng hồ thời trang', 'Đồng hồ thể thao'].map(
                                            (category) => (
                                                <label
                                                    key={category}
                                                    className="flex cursor-pointer items-center gap-2"
                                                >
                                                    <input
                                                        type="checkbox"
                                                        checked={selectedCategories.includes(category)}
                                                        onChange={(e) => {
                                                            if (e.target.checked) {
                                                                setSelectedCategories([
                                                                    ...selectedCategories,
                                                                    category,
                                                                ]);
                                                            } else {
                                                                setSelectedCategories(
                                                                    selectedCategories.filter((c) => c !== category)
                                                                );
                                                            }
                                                        }}
                                                        className="h-4 w-4 rounded border-border text-primary focus:ring-primary"
                                                    />
                                                    <span className="text-sm text-foreground">{category}</span>
                                                </label>
                                            )
                                        )}
                                    </div>
                                </div>

                                {/* Price Range Filter */}
                                <div className="rounded-xl bg-card p-5">
                                    <h3 className="mb-4 font-bold text-foreground">Khoảng giá</h3>
                                    <div className="space-y-3">
                                        <Input
                                            type="number"
                                            placeholder="Từ"
                                            value={priceRange.min}
                                            onChange={(e) =>
                                                setPriceRange({ ...priceRange, min: Number(e.target.value) })
                                            }
                                            className="h-10"
                                        />
                                        <Input
                                            type="number"
                                            placeholder="Đến"
                                            value={priceRange.max}
                                            onChange={(e) =>
                                                setPriceRange({ ...priceRange, max: Number(e.target.value) })
                                            }
                                            className="h-10"
                                        />
                                    </div>
                                </div>

                                {/* Brands Filter */}
                                <div className="rounded-xl bg-card p-5">
                                    <h3 className="mb-4 font-bold text-foreground">Thương hiệu</h3>
                                    <div className="space-y-2">
                                        {['Apple', 'Samsung', 'Xiaomi', 'Huawei', 'Garmin'].map((brand) => (
                                            <label key={brand} className="flex cursor-pointer items-center gap-2">
                                                <input
                                                    type="checkbox"
                                                    checked={selectedBrands.includes(brand)}
                                                    onChange={(e) => {
                                                        if (e.target.checked) {
                                                            setSelectedBrands([...selectedBrands, brand]);
                                                        } else {
                                                            setSelectedBrands(
                                                                selectedBrands.filter((b) => b !== brand)
                                                            );
                                                        }
                                                    }}
                                                    className="h-4 w-4 rounded border-border text-primary focus:ring-primary"
                                                />
                                                <span className="text-sm text-foreground">{brand}</span>
                                            </label>
                                        ))}
                                    </div>
                                </div>
                            </div>
                        </aside>

                        {/* Products Grid */}
                        <div className="relative flex-1">
                            {/* Toolbar */}
                            <div className="mb-6 flex flex-col items-center justify-between gap-4 sm:flex-row">
                                <div className="flex items-center gap-2">
                                    <label className="text-sm font-medium text-muted-foreground">
                                        Sắp xếp theo:
                                    </label>
                                    <select
                                        value={sortBy}
                                        onChange={(e) => setSortBy(e.target.value)}
                                        className="h-10 rounded-lg border-border bg-card px-3 text-sm text-foreground focus:border-primary focus:ring-primary/50"
                                    >
                                        <option value="relevant">Liên quan</option>
                                        <option value="price-asc">Giá: Thấp đến Cao</option>
                                        <option value="price-desc">Giá: Cao đến Thấp</option>
                                        <option value="rating">Đánh giá</option>
                                        <option value="newest">Mới nhất</option>
                                    </select>
                                </div>
                                <div className="flex gap-2">
                                    <button
                                        onClick={() => setViewMode('grid')}
                                        className={`flex h-10 w-10 items-center justify-center rounded-lg transition-colors ${
                                            viewMode === 'grid'
                                                ? 'bg-primary text-white'
                                                : 'bg-card text-muted-foreground hover:bg-primary/10 hover:text-primary'
                                        }`}
                                    >
                                        <Grid className="h-5 w-5" />
                                    </button>
                                    <button
                                        onClick={() => setViewMode('list')}
                                        className={`flex h-10 w-10 items-center justify-center rounded-lg transition-colors ${
                                            viewMode === 'list'
                                                ? 'bg-primary text-white'
                                                : 'bg-card text-muted-foreground hover:bg-primary/10 hover:text-primary'
                                        }`}
                                    >
                                        <List className="h-5 w-5" />
                                    </button>
                                </div>
                            </div>

                            {/* Products Grid/List */}
                            <div
                                className={
                                    viewMode === 'grid'
                                        ? 'grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3'
                                        : 'flex flex-col gap-6'
                                }
                            >
                                {products.map((product) =>
                                    viewMode === 'grid' ? (
                                        // Grid View
                                        <Link
                                            key={product.id}
                                            href="#"
                                            className="group flex flex-col overflow-hidden rounded-xl border border-border bg-card transition-shadow hover:shadow-lg"
                                        >
                                            <div className="relative aspect-square overflow-hidden bg-gray-200">
                                                <img
                                                    src={product.image}
                                                    alt={product.name}
                                                    className="h-full w-full object-cover transition-transform group-hover:scale-105"
                                                />
                                            </div>
                                            <div className="flex flex-1 flex-col p-4">
                                                <h3 className="mb-2 font-bold text-foreground">{product.name}</h3>
                                                <div className="mb-2 flex items-center gap-2">
                                                    <div className="flex items-center gap-1">
                                                        <span className="text-sm font-medium text-foreground">
                                                            {product.rating}
                                                        </span>
                                                        <span className="text-sm text-muted-foreground">
                                                            ({product.reviews})
                                                        </span>
                                                    </div>
                                                </div>
                                                <div className="mt-auto flex items-center gap-2">
                                                    <span className="text-xl font-bold text-primary">
                                                        {formatPrice(product.price)}
                                                    </span>
                                                    {product.originalPrice && (
                                                        <span className="text-sm text-muted-foreground line-through">
                                                            {formatPrice(product.originalPrice)}
                                                        </span>
                                                    )}
                                                </div>
                                            </div>
                                        </Link>
                                    ) : (
                                        // List View
                                        <div
                                            key={product.id}
                                            className="group flex flex-col overflow-hidden rounded-lg border border-border bg-card p-4 transition-shadow hover:shadow-xl sm:flex-row sm:gap-4"
                                        >
                                            <div className="mb-4 aspect-square w-full flex-shrink-0 overflow-hidden rounded-lg bg-gray-200 sm:mb-0 sm:h-40 sm:w-40 sm:aspect-auto">
                                                <img
                                                    src={product.image}
                                                    alt={product.name}
                                                    className="h-full w-full object-cover"
                                                />
                                            </div>
                                            <div className="flex flex-1 flex-col gap-2">
                                                <h3 className="text-lg font-semibold text-foreground">
                                                    {product.name}
                                                </h3>
                                                <div className="flex items-center gap-1">
                                                    <span className="text-sm font-medium text-foreground">
                                                        {product.rating}
                                                    </span>
                                                    <span className="text-sm text-muted-foreground">
                                                        ({product.reviews})
                                                    </span>
                                                </div>
                                                <p className="line-clamp-2 text-sm text-muted-foreground">
                                                    Một chiếc đồng hồ thông minh tiên tiến với nhiều tính năng theo dõi
                                                    sức khỏe và thể thao, màn hình AMOLED sắc nét và thời lượng pin
                                                    dài.
                                                </p>
                                                <div className="mt-1 flex items-center gap-2">
                                                    <span className="text-xl font-bold text-primary">
                                                        {formatPrice(product.price)}
                                                    </span>
                                                    {product.originalPrice && (
                                                        <span className="text-sm text-muted-foreground line-through">
                                                            {formatPrice(product.originalPrice)}
                                                        </span>
                                                    )}
                                                </div>
                                            </div>
                                            <div className="mt-3 flex w-full flex-col items-stretch gap-3 sm:mt-0 sm:w-auto sm:items-end sm:justify-center">
                                                <button className="flex h-10 w-full cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-lg bg-secondary px-4 text-sm font-bold leading-normal tracking-[0.015em] text-white transition-colors hover:bg-secondary/90 sm:w-auto">
                                                    <ShoppingBag className="h-5 w-5" />
                                                    <span>Thêm vào giỏ</span>
                                                </button>
                                                <button className="flex h-10 w-full cursor-pointer items-center justify-center gap-2 overflow-hidden rounded-lg bg-card px-4 text-sm font-bold text-foreground transition-colors hover:bg-primary/10 hover:text-primary sm:w-auto">
                                                    <Heart className="h-5 w-5" />
                                                    <span>Yêu thích</span>
                                                </button>
                                            </div>
                                        </div>
                                    )
                                )}
                            </div>

                            {/* Pagination */}
                            <nav className="mt-12 flex items-center justify-center gap-2">
                                <button className="inline-flex h-10 items-center justify-center rounded-lg bg-card px-4 text-sm font-medium text-muted-foreground transition-colors hover:bg-primary/10 hover:text-primary">
                                    <ChevronLeft className="mr-1 h-4 w-4" />
                                    Trước
                                </button>
                                <button className="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-primary text-sm font-medium text-white">
                                    1
                                </button>
                                <button className="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-card text-sm font-medium text-foreground transition-colors hover:bg-primary/10 hover:text-primary">
                                    2
                                </button>
                                <button className="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-card text-sm font-medium text-foreground transition-colors hover:bg-primary/10 hover:text-primary">
                                    3
                                </button>
                                <span className="text-muted-foreground">...</span>
                                <button className="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-card text-sm font-medium text-foreground transition-colors hover:bg-primary/10 hover:text-primary">
                                    8
                                </button>
                                <button className="inline-flex h-10 items-center justify-center rounded-lg bg-card px-4 text-sm font-medium text-foreground transition-colors hover:bg-primary/10 hover:text-primary">
                                    Sau
                                    <ChevronRight className="ml-1 h-4 w-4" />
                                </button>
                            </nav>
                        </div>
                    </div>
                </div>
            </main>

            <Footer />
        </div>
    );
}

export default function SearchResult() {
    return (
        <ToastProvider>
            <SearchResultContent />
        </ToastProvider>
    );
}
