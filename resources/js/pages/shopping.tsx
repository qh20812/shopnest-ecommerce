import { useState } from 'react';
import { Heart, ShoppingBag, MessageCircle } from 'lucide-react';
import { Link } from '@inertiajs/react';
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

function ShoppingContent() {
    const { showSuccess } = useToast();
    const [selectedCategory, setSelectedCategory] = useState('all');
    const [minPrice, setMinPrice] = useState('');
    const [maxPrice, setMaxPrice] = useState('');
    const [selectedBrands, setSelectedBrands] = useState<string[]>([]);
    const [sortBy, setSortBy] = useState('popular');

    const categories = [
        { id: 'all', name: 'Tất cả sản phẩm' },
        { id: 'electronics', name: 'Điện tử' },
        { id: 'fashion', name: 'Thời trang' },
        { id: 'home', name: 'Nhà & Vườn' },
        { id: 'beauty', name: 'Sắc đẹp' },
        { id: 'sports', name: 'Thể thao' },
        { id: 'toys', name: 'Đồ chơi' },
    ];

    const brands = ['ErgoFlex', 'ProShot', 'SkyRider', 'TechPro'];

    const products: Product[] = [
        {
            id: 1,
            name: 'Ghế văn phòng hiện đại',
            image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuA_3Hg8MXQwtiERSX43afboTM1H1-41w4KBQxetVoKydoputfI3u-8Vr6lMZPGmmgK3Vj2a8FocftUvTeNfDtyuDk6vulQgy2XQ6sJD-C6NSeXYh2CYNUSAGjsHDVlALoSmMzJp2YqQhCYDHCKO7d1_eS61l4Ddw8y6y5pss7Yg3BsRbqUordq7yP-W65WHG38K4MqowehadHRhDBne6loSh9agztSqMoxAn5DQ2Ydqg9M5b4sBAP2tnRwZIE3rmUdiQ7c_TkDRAB4',
            price: 2499000,
            originalPrice: 3499000,
            rating: 4.5,
            reviews: 128,
            brand: 'ErgoFlex',
        },
        {
            id: 2,
            name: 'Ba lô thời trang',
            image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuAPisgy3KZaPmgOSMmh9sqM49-D8D7yiH8T_Kt_mJWPnXtCcQLjHDsov7tlNdKZR1GkS87Lfefou5CCJFEmBS2owo8stPVx_XXkHyQjdC0IY7mJQ9TJysBiirwxJ65XeIz_lkMcPuRKURv-EwbLHd5yvpWx2NyexTj4fHwtkP2BzKsdHfL6Nq-JhcrlYJstUiODv9638JXtmN7k7NoqwpuQcf4q0YIf56ecOwMDSZV9LcXGX12VZAfPrWPMND7nXDjBM7E-D1DQCk4',
            price: 599000,
            rating: 4.8,
            reviews: 256,
            brand: 'SkyRider',
        },
        {
            id: 3,
            name: 'Bộ cốc sứ cao cấp',
            image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuB0Dx4PFCAlmfHbtZ9gWiy5A4YWIhbVWT0zGZ_PTYfWgZ_bDm0UyQS0MKko39pu3odEXa9wb2Tkh2JEqu66uUHOKkPUkkJjG_Rce83f9jWwegBC95wzkgFJPnD3iDmEJlfrRpsLvwQR9L4pDCjE5F0ZEsp1DoT6BOe1ZSxT28oR_RA-0e5c4eU9YN8NsGdQZyrwwPhi9KbR54oJu66zfhOGeBmUhAM1kUfkeL8IK45jkLlGwYjeoi65L8y3qNl4Zy-oo2064pw3KKo',
            price: 349000,
            rating: 4.3,
            reviews: 89,
            brand: 'ErgoFlex',
        },
        {
            id: 4,
            name: 'Loa bluetooth di động',
            image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuALOGGvwadBs8vhjIFBZyG4TjM7DyT6Wzqn7eGOl5dssX7O0GeYHMprL5MYWxiyeY843X4LnczV7YowlUoEDA7uozJO5NiMMLnQk1SX6EbqpUlUCcbOqxRh8034s3AzsdPhIur7N4JCSMaxXm1vqiUeDKSQBeeTdMWBO9n7-UVkhFKfV7Xpp7Gjbp_pRXuKJvbliG5BxKFDNRUAT5dPILqfPj7_51E33Zvnf_Vn4RYjQ1kr9EQuVHmSTfd9aERR6yqXoyt_O8lH3b0',
            price: 1299000,
            rating: 4.7,
            reviews: 342,
            brand: 'ProShot',
        },
        {
            id: 5,
            name: 'Máy ảnh chuyên nghiệp',
            image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDkOT3NXftcHBYXJWdTJQoBIeEU5MFdARt6E2VA-vX8-RzpULjYGjqM5vkZD7728lKkHbXLaJYkjMeRC_HA1Yrpy7-E57-n3zCp0lDvZrGES07xg0ZDnfko8k96BKQIIDrhoTAYK1-HCMTEgBRB4S4PQ8yOEyVwKsL6oy55qXQpW6FOA2Y-7-6bLUBgVIJe74FNMvpiIdk0OqRTbwSsrEQd6002owa3TH0dYP3nE-SfbXorxPdHht1mAL2UDU_7Whrzyvv2L3L_tlI',
            price: 15999000,
            originalPrice: 18999000,
            rating: 4.9,
            reviews: 167,
            brand: 'ProShot',
        },
        {
            id: 6,
            name: 'Máy pha cà phê thông minh',
            image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuD0ywpht2YQSrweNwuUan-skozVSnnD2O767vO8LEwBf_nTGJzsQEX_pSbnOrbhjrZAcJX_lUE3wF9E826v6KbxgW7GdgxmLS2oIJGuTWpUsmVkmGOlynzkZkZ5LZcxWM3Us5Usrx-hagsCKRZdzZ22mbpdXUzxzPknF8H4YKwgwtza1tKGqnBHXkJ6m6CLwjRd-L0gus_grmfHEwU8oVVPRGVWCpyyJUUUk5KkxMjBOZ-0WZ-JvdQMAgX-9TcdeqQv52T3ZCFQdw0',
            price: 4299000,
            rating: 4.6,
            reviews: 203,
            brand: 'TechPro',
        },
    ];

    const formatPrice = (price: number) =>
        new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND',
        }).format(price);

    const handleBrandChange = (brand: string) => {
        if (selectedBrands.includes(brand)) {
            setSelectedBrands(selectedBrands.filter((b) => b !== brand));
        } else {
            setSelectedBrands([...selectedBrands, brand]);
        }
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
                                                    onClick={() => setSelectedCategory(category.id)}
                                                    className={`text-sm transition-colors ${
                                                        selectedCategory === category.id
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
                                    <div className="mb-2 flex items-center gap-2">
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
                                </div>

                                {/* Brands */}
                                <div className="border-t border-border pt-6">
                                    <h3 className="mb-4 text-lg font-bold text-foreground">Thương hiệu</h3>
                                    <ul className="space-y-2">
                                        {brands.map((brand) => (
                                            <li key={brand} className="flex items-center">
                                                <input
                                                    type="checkbox"
                                                    id={`brand-${brand}`}
                                                    checked={selectedBrands.includes(brand)}
                                                    onChange={() => handleBrandChange(brand)}
                                                    className="h-4 w-4 rounded border-border text-primary focus:ring-primary/50"
                                                />
                                                <label
                                                    htmlFor={`brand-${brand}`}
                                                    className="ml-2 cursor-pointer text-sm text-foreground"
                                                >
                                                    {brand}
                                                </label>
                                            </li>
                                        ))}
                                    </ul>
                                </div>
                            </div>
                        </aside>

                        {/* Products Grid */}
                        <div className="w-full lg:w-3/4 xl:w-4/5">
                            {/* Toolbar */}
                            <div className="mb-6 flex flex-col items-center justify-between rounded-lg bg-card p-4 sm:flex-row">
                                <p className="mb-2 text-sm text-muted-foreground sm:mb-0">
                                    Hiển thị 1–12 của 48 sản phẩm
                                </p>
                                <div className="flex items-center gap-2">
                                    <span className="text-sm font-medium text-foreground">Sắp xếp theo:</span>
                                    <select
                                        value={sortBy}
                                        onChange={(e) => setSortBy(e.target.value)}
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
                                        <div className="relative aspect-square w-full overflow-hidden bg-gray-200">
                                            <img
                                                src={product.image}
                                                alt={product.name}
                                                className="h-full w-full object-cover"
                                            />
                                            <button
                                                onClick={handleAddToWishlist}
                                                className="absolute right-3 top-3 flex h-10 w-10 items-center justify-center rounded-full bg-white/90 text-muted-foreground shadow-md transition-colors hover:bg-white hover:text-primary"
                                            >
                                                <Heart className="h-5 w-5" />
                                            </button>
                                        </div>
                                        <div className="flex flex-col gap-4 p-4">
                                            <div>
                                                <Link href="#" className="group-hover:text-primary">
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
                                                    onClick={handleAddToCart}
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
                            <nav className="mt-10 flex justify-center">
                                <ul className="-space-x-px flex h-10 items-center text-base">
                                    <li>
                                        <button className="ms-0 flex h-10 items-center justify-center rounded-s-lg border border-border bg-background px-4 leading-tight text-muted-foreground transition-colors hover:bg-card">
                                            Trang trước
                                        </button>
                                    </li>
                                    <li>
                                        <button
                                            aria-current="page"
                                            className="flex h-10 items-center justify-center border border-primary bg-primary px-4 leading-tight text-white"
                                        >
                                            1
                                        </button>
                                    </li>
                                    <li>
                                        <button className="flex h-10 items-center justify-center border border-border bg-background px-4 leading-tight text-muted-foreground transition-colors hover:bg-card">
                                            2
                                        </button>
                                    </li>
                                    <li>
                                        <button className="flex h-10 items-center justify-center border border-border bg-background px-4 leading-tight text-muted-foreground transition-colors hover:bg-card">
                                            3
                                        </button>
                                    </li>
                                    <li>
                                        <button className="flex h-10 items-center justify-center border border-border bg-background px-4 leading-tight text-muted-foreground transition-colors hover:bg-card">
                                            4
                                        </button>
                                    </li>
                                    <li>
                                        <button className="flex h-10 items-center justify-center rounded-e-lg border border-border bg-background px-4 leading-tight text-muted-foreground transition-colors hover:bg-card">
                                            Trang kế tiếp
                                        </button>
                                    </li>
                                </ul>
                            </nav>
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

export default function Shopping() {
    return (
        <ToastProvider>
            <ShoppingContent />
        </ToastProvider>
    );
}
