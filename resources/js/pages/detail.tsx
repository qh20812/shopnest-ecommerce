import { useState } from 'react';
import { ShoppingCart, Heart, Star, Minus, Plus } from 'lucide-react';
import { Link } from '@inertiajs/react';
import TopNav from '../components/top-nav';
import Footer from '../components/footer';
import { ToastProvider, useToast } from '../lib/toastContext';

function DetailContent() {
    const { showSuccess } = useToast();
    
    // Sample product data - replace with real data from props/API
    const product = {
        id: 1,
        name: 'Ghế công thái học ErgoFlex',
        price: 3200000,
        rating: 4.5,
        reviewCount: 124,
        inStock: true,
        description: 'Ghế ErgoFlex được thiết kế để mang lại sự thoải mái và hỗ trợ tối đa trong suốt ngày làm việc của bạn. Với các tính năng có thể điều chỉnh và vật liệu thoáng khí, đây là sự bổ sung hoàn hảo cho bất kỳ không gian văn phòng nào.',
        images: [
            {
                id: 1,
                url: 'https://lh3.googleusercontent.com/aida-public/AB6AXuA_3Hg8MXQwtiERSX43afboTM1H1-41w4KBQxetVoKydoputfI3u-8Vr6lMZPGmmgK3Vj2a8FocftUvTeNfDtyuDk6vulQgy2XQ6sJD-C6NSeXYh2CYNUSAGjsHDVlALoSmMzJp2YqQhCYDHCKO7d1_eS61l4Ddw8y6y5pss7Yg3BsRbqUordq7yP-W65WHG38K4MqowehadHRhDBne6loSh9agztSqMoxAn5DQ2Ydqg9M5b4sBAP2tnRwZIE3rmUdiQ7c_TkDRAB4',
                alt: 'Main view of ergonomic chair',
            },
            {
                id: 2,
                url: 'https://lh3.googleusercontent.com/aida-public/AB6AXuCkNtXATLmOIJnKx70va9XhmPPxykO9u9z61UpaQOswvx19FxC4O9YTIgT28UBv1DdY8_1bGI4NMNOjX1ejRjtVDco-hrsEbd0JrXAmbv22na62uEkTlPEfo0xRzEXCH6yIv0d2m7U_3zPB3QAPmtySQwO3gTMDIQK4WKk45Pv684j7-FbtoDk8jLjG9t7unpJNwd5AfKtmjSbSaYLBYxVHgjeVgmbbhqG3lO3HjA046s2IdOGgjWTtANTlOtlQRQKpNL2qSeB2Kzg',
                alt: 'Ergonomic chair from the side',
            },
            {
                id: 3,
                url: 'https://lh3.googleusercontent.com/aida-public/AB6AXuB6uyEwMR3gMRUrklfZlBr28GDqPnN6aSosyUfSCXLKATasiOR79kKEokRDE7DWVQZGD1krMyfn6UC6yHiu_7DfW3skxKkGPE6PVY_xSte8uBwVY_zBymneYgS7NQmf66SgULay5fwl4TNA0v4_0Siq7NSXRJFENgFxhvFoqRk5B0zYRp27D0Z-kxFNtvp0nAlBLXHzAC0vMPBWaZHrt9vN1iWi-MTnGonNhdQRYFiCDixVZJ2DnAutSvTtOvIZ5hEydn4FmI4dUNA',
                alt: 'Ergonomic chair from the back',
            },
            {
                id: 4,
                url: 'https://lh3.googleusercontent.com/aida-public/AB6AXuAiKa3-APIYDn3nDm_R6fPxi11SYcKzkn80Jf7cEVw_7ejxEy4Tz1a7YqpZSymfbzQflEFAoD5jRdkak9mQCeP7rwJwNROp0Y6O2auBPYUkCY4mb-H_LH9PlTpmWSy69o74itY-rYpu3EJJUiNMckXcY5KACbxXBX0uLgiMXyS2Up0zqg5s5Q4mW4Of2omS1roDDqf5dUi-Y4vdva58Ngbo0nYRz_9fpN-feS_ORg3EypX32D-8VR4MFbVdtRzT9fhfXYrWC-5UR0Y',
                alt: "Close-up of the chair's fabric",
            },
            {
                id: 5,
                url: 'https://lh3.googleusercontent.com/aida-public/AB6AXuBpCzenaO_Gb7s5XLwpkJrdxwOSgIYbHplGmRjdqYYAkDM9I1qfwB7DggjljS8lqKH7yjnCnsQCumi0362GPLlv8VmSePM7EWOg7wh5btw0txS-5T0UCALxQdZKOHdIsuSg2sj68bBogiZcpPb5nHR5ZSXq3ZoYY77wjiF-pc-29U6P9W9tUg-rnGRkfElGuwufd9yNIQxWwzUQhDjB0OdLLZcD8CwRdAAaHBK9eXjiqFQJiOX2quWSKWZeGmNLgmWFh4GRMrxn8wc',
                alt: 'Chair in an office setting',
            },
        ],
        colors: [
            { id: 1, name: 'Đen', hex: '#1F2937', selected: true },
            { id: 2, name: 'Xám', hex: '#9CA3AF', selected: false },
            { id: 3, name: 'Xanh', hex: '#3B82F6', selected: false },
        ],
    };

    // Sample related products - replace with real data later
    const [relatedProducts, setRelatedProducts] = useState([
        {
            id: 2,
            name: 'Bàn làm việc đứng FlexiDesk',
            category: 'Văn phòng hiện đại',
            image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuBfD0wBvdH6m6eANTBqK0wmVndkuK5MPHmToxF_wbyvtHl11WJmY-oSiglrY7MN1yE5-brXKMP9sfnNO3RkKw2IaLfcA9fq0QeYEa9HNNVoOnCZsJPDnyhb6lRMR8c9rZd5APSgVnS0YClS0zaDvLjHXQcuLN4TSpY1RXZBiOZ1hoap1z4xl0dbXeZVeGm-APy3VpUwu9cRc5Cmwh7K91HRcuiCwNWeTyBm0SAI44ikAAaVq4-AEvv9L__Ibk5E1092gyqmc9HO5n4',
            price: 5500000,
            rating: 4,
            reviewCount: 89,
            isWishlisted: false,
        },
        {
            id: 3,
            name: 'Đèn bàn LED LumiTask',
            category: 'Chiếu sáng',
            image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuBfD0wBvdH6m6eANTBqK0wmVndkuK5MPHmToxF_wbyvtHl11WJmY-oSiglrY7MN1yE5-brXKMP9sfnNO3RkKw2IaLfcA9fq0QeYEa9HNNVoOnCZsJPDnyhb6lRMR8c9rZd5APSgVnS0YClS0zaDvLjHXQcuLN4TSpY1RXZBiOZ1hoap1z4xl0dbXeZVeGm-APy3VpUwu9cRc5Cmwh7K91HRcuiCwNWeTyBm0SAI44ikAAaVq4-AEvv9L__Ibk5E1092gyqmc9HO5n4',
            price: 1200000,
            rating: 5,
            reviewCount: 210,
            isWishlisted: false,
        },
        {
            id: 4,
            name: 'Kệ sách gỗ sồi Oaka',
            category: 'Lưu trữ',
            image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuBfD0wBvdH6m6eANTBqK0wmVndkuK5MPHmToxF_wbyvtHl11WJmY-oSiglrY7MN1yE5-brXKMP9sfnNO3RkKw2IaLfcA9fq0QeYEa9HNNVoOnCZsJPDnyhb6lRMR8c9rZd5APSgVnS0YClS0zaDvLjHXQcuLN4TSpY1RXZBiOZ1hoap1z4xl0dbXeZVeGm-APy3VpUwu9cRc5Cmwh7K91HRcuiCwNWeTyBm0SAI44ikAAaVq4-AEvv9L__Ibk5E1092gyqmc9HO5n4',
            price: 2800000,
            rating: 4.5,
            reviewCount: 154,
            isWishlisted: true,
        },
        {
            id: 5,
            name: 'Thảm lót sàn CozyStep',
            category: 'Trang trí',
            image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuBfD0wBvdH6m6eANTBqK0wmVndkuK5MPHmToxF_wbyvtHl11WJmY-oSiglrY7MN1yE5-brXKMP9sfnNO3RkKw2IaLfcA9fq0QeYEa9HNNVoOnCZsJPDnyhb6lRMR8c9rZd5APSgVnS0YClS0zaDvLjHXQcuLN4TSpY1RXZBiOZ1hoap1z4xl0dbXeZVeGm-APy3VpUwu9cRc5Cmwh7K91HRcuiCwNWeTyBm0SAI44ikAAaVq4-AEvv9L__Ibk5E1092gyqmc9HO5n4',
            price: 950000,
            rating: 5,
            reviewCount: 301,
            isWishlisted: false,
        },
    ]);

    const [selectedImageIndex, setSelectedImageIndex] = useState(0);
    const [quantity, setQuantity] = useState(1);
    const [activeTab, setActiveTab] = useState<'description' | 'specs' | 'reviews'>('description');

    const formatPrice = (price: number) =>
        new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND',
        }).format(price);

    const renderStars = (rating: number) => {
        const stars = [];
        const fullStars = Math.floor(rating);
        const hasHalfStar = rating % 1 !== 0;

        for (let i = 0; i < fullStars; i++) {
            stars.push(
                <Star key={`star-${i}`} className="h-5 w-5 fill-yellow-500 text-yellow-500" />
            );
        }
        if (hasHalfStar) {
            stars.push(
                <Star key="half-star" className="h-5 w-5 fill-yellow-500 text-yellow-500" />
            );
        }
        for (let i = stars.length; i < 5; i++) {
            stars.push(
                <Star key={`empty-${i}`} className="h-5 w-5 text-yellow-500" />
            );
        }
        return stars;
    };

    const handleQuantityChange = (delta: number) => {
        setQuantity((prev) => Math.max(1, prev + delta));
    };

    const handleAddToCart = () => {
        showSuccess(`Đã thêm ${quantity} sản phẩm vào giỏ hàng`);
    };

    const handleAddToWishlist = () => {
        showSuccess('Đã thêm vào danh sách yêu thích');
    };

    const handleToggleWishlist = (productId: number) => {
        setRelatedProducts((products) =>
            products.map((p) =>
                p.id === productId ? { ...p, isWishlisted: !p.isWishlisted } : p
            )
        );
        const product = relatedProducts.find((p) => p.id === productId);
        if (product) {
            showSuccess(
                product.isWishlisted
                    ? 'Đã xóa khỏi danh sách yêu thích'
                    : 'Đã thêm vào danh sách yêu thích'
            );
        }
    };

    const handleAddRelatedToCart = (productId: number) => {
        const product = relatedProducts.find((p) => p.id === productId);
        if (product) {
            showSuccess(`Đã thêm ${product.name} vào giỏ hàng`);
        }
    };

    return (
        <div className="relative flex min-h-screen w-full flex-col overflow-x-hidden bg-background text-foreground">
            <TopNav />
            
            <main className="flex-1">
                <div className="container mx-auto px-4 py-8 md:py-12">
                    {/* Product Grid */}
                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
                        {/* Image Gallery */}
                        <div className="flex flex-col gap-4">
                            {/* Main Image */}
                            <div className="aspect-square w-full rounded-xl overflow-hidden">
                                <div
                                    className="w-full h-full bg-center bg-cover"
                                    style={{
                                        backgroundImage: `url('${product.images[selectedImageIndex].url}')`,
                                    }}
                                />
                            </div>

                            {/* Thumbnail Gallery */}
                            <div className="grid grid-cols-5 gap-3">
                                {product.images.map((image, index) => (
                                    <div
                                        key={image.id}
                                        onClick={() => setSelectedImageIndex(index)}
                                        className={`aspect-square rounded-lg overflow-hidden cursor-pointer transition ${
                                            index === selectedImageIndex
                                                ? 'border-2 border-primary ring-2 ring-primary/50'
                                                : 'border border-border hover:border-primary'
                                        }`}
                                    >
                                        <div
                                            className="w-full h-full bg-center bg-cover"
                                            style={{ backgroundImage: `url('${image.url}')` }}
                                        />
                                    </div>
                                ))}
                            </div>
                        </div>

                        {/* Product Info */}
                        <div className="flex flex-col">
                            <h1 className="text-3xl md:text-4xl font-bold mb-3 leading-tight tracking-[-0.015em]">
                                {product.name}
                            </h1>

                            {/* Rating & Stock */}
                            <div className="flex items-center gap-4 mb-5">
                                <div className="flex items-center gap-1">
                                    {renderStars(product.rating)}
                                    <span className="text-sm text-muted-foreground ml-1.5 font-medium">
                                        {product.rating} ({product.reviewCount} đánh giá)
                                    </span>
                                </div>
                                <div className="h-5 w-px bg-border" />
                                <span className="text-sm font-medium text-secondary">
                                    {product.inStock ? 'Còn hàng' : 'Hết hàng'}
                                </span>
                            </div>

                            {/* Price */}
                            <p className="text-4xl font-bold text-primary mb-6">
                                {formatPrice(product.price)}
                            </p>

                            {/* Description */}
                            <p className="text-muted-foreground mb-8 leading-relaxed">
                                {product.description}
                            </p>

                            {/* Options */}
                            <div className="flex flex-col gap-6 mb-8">
                                {/* Color Selection */}
                                <div className="flex flex-col gap-3">
                                    <label className="text-sm font-semibold">Màu sắc</label>
                                    <div className="flex items-center gap-3">
                                        {product.colors.map((color) => (
                                            <button
                                                key={color.id}
                                                className={`h-8 w-8 rounded-full ring-2 ring-offset-2 ring-offset-background transition ${
                                                    color.selected
                                                        ? 'ring-primary'
                                                        : 'ring-transparent hover:ring-primary/50'
                                                }`}
                                                style={{ backgroundColor: color.hex }}
                                            />
                                        ))}
                                    </div>
                                </div>

                                {/* Quantity */}
                                <div className="flex flex-col gap-3">
                                    <label className="text-sm font-semibold">Số lượng</label>
                                    <div className="flex items-center border border-border rounded-lg w-fit">
                                        <button
                                            onClick={() => handleQuantityChange(-1)}
                                            className="h-10 w-10 text-muted-foreground hover:bg-card transition rounded-l-lg"
                                        >
                                            <Minus className="h-4 w-4 mx-auto" />
                                        </button>
                                        <input
                                            className="h-10 w-14 text-center border-x border-border bg-transparent focus:outline-none"
                                            type="text"
                                            value={quantity}
                                            readOnly
                                        />
                                        <button
                                            onClick={() => handleQuantityChange(1)}
                                            className="h-10 w-10 text-muted-foreground hover:bg-card transition rounded-r-lg"
                                        >
                                            <Plus className="h-4 w-4 mx-auto" />
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {/* Actions */}
                            <div className="flex flex-col sm:flex-row gap-4 mb-8">
                                <button
                                    onClick={handleAddToCart}
                                    className="flex flex-1 min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-14 px-6 bg-primary text-white text-base font-bold leading-normal tracking-[0.015em] hover:bg-primary/90 transition-colors gap-2"
                                >
                                    <ShoppingCart className="h-5 w-5" />
                                    <span className="truncate">Thêm vào giỏ hàng</span>
                                </button>
                                <button
                                    onClick={handleAddToWishlist}
                                    className="flex cursor-pointer items-center justify-center overflow-hidden rounded-lg h-14 w-14 bg-card text-foreground hover:bg-primary/10 hover:text-primary transition-colors"
                                >
                                    <Heart className="h-5 w-5" />
                                </button>
                            </div>

                            
                        </div>
                    </div>

                    {/* Product Details Tabs */}
                    <div className="border-t border-border pt-12 mt-12">
                        {/* Tab Navigation */}
                        <div className="flex items-center gap-8 border-b border-border mb-8">
                            <button
                                onClick={() => setActiveTab('description')}
                                className={`py-4 text-base font-semibold border-b-2 transition-colors ${
                                    activeTab === 'description'
                                        ? 'border-primary text-primary'
                                        : 'border-transparent text-muted-foreground hover:text-primary'
                                }`}
                            >
                                Mô tả chi tiết
                            </button>
                            <button
                                onClick={() => setActiveTab('specs')}
                                className={`py-4 text-base font-semibold border-b-2 transition-colors ${
                                    activeTab === 'specs'
                                        ? 'border-primary text-primary'
                                        : 'border-transparent text-muted-foreground hover:text-primary'
                                }`}
                            >
                                Thông số kỹ thuật
                            </button>
                            <button
                                onClick={() => setActiveTab('reviews')}
                                className={`py-4 text-base font-semibold border-b-2 transition-colors ${
                                    activeTab === 'reviews'
                                        ? 'border-primary text-primary'
                                        : 'border-transparent text-muted-foreground hover:text-primary'
                                }`}
                            >
                                Đánh giá ({product.reviewCount})
                            </button>
                        </div>

                        {/* Tab Content */}
                        <div className="prose prose-sm md:prose-base max-w-none text-muted-foreground leading-relaxed">
                            {activeTab === 'description' && (
                                <div>
                                    <p>
                                        Ghế ErgoFlex được thiết kế một cách khoa học để cung cấp sự hỗ trợ vượt trội cho lưng và thúc đẩy tư thế ngồi tốt. Lưng lưới thoáng khí cho phép không khí lưu thông để giữ cho bạn mát mẻ, trong khi đệm ngồi có đường viền giúp giảm áp lực lên chân.
                                    </p>
                                    <p>
                                        Với các tính năng có thể điều chỉnh linh hoạt bao gồm chiều cao ghế, tay vịn, và độ nghiêng, bạn có thể tùy chỉnh ErgoFlex để phù hợp hoàn hảo với cơ thể của mình. Cấu trúc chắc chắn đảm bảo độ bền và ổn định lâu dài.
                                    </p>
                                    <ul className="space-y-2">
                                        <li>Hỗ trợ thắt lưng có thể điều chỉnh</li>
                                        <li>Tay vịn 3D (lên/xuống, trước/sau, xoay)</li>
                                        <li>Cơ chế nghiêng đồng bộ với khóa đa vị trí</li>
                                        <li>Lưng lưới cao cấp và đệm ngồi bằng vải</li>
                                        <li>Chân đế nylon chắc chắn với bánh xe lăn êm</li>
                                    </ul>
                                </div>
                            )}
                            {activeTab === 'specs' && (
                                <div>
                                    <p>Thông số kỹ thuật đang được cập nhật...</p>
                                </div>
                            )}
                            {activeTab === 'reviews' && (
                                <div>
                                    <p>Đánh giá của khách hàng đang được cập nhật...</p>
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Related Products Section */}
                    <div className="pt-12 mt-12">
                        <h2 className="text-2xl md:text-3xl font-bold mb-8">Sản phẩm liên quan</h2>
                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            {relatedProducts.map((relatedProduct) => (
                                <div
                                    key={relatedProduct.id}
                                    className="group flex flex-col bg-card rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-shadow duration-300 border border-border"
                                >
                                    {/* Product Image */}
                                    <div className="relative">
                                        <Link href={`/products/${relatedProduct.id}`}>
                                            <div
                                                className="aspect-square w-full bg-cover bg-center"
                                                style={{
                                                    backgroundImage: `url('${relatedProduct.image}')`,
                                                }}
                                            />
                                        </Link>
                                        <div className="absolute top-3 right-3">
                                            <button
                                                onClick={() => handleToggleWishlist(relatedProduct.id)}
                                                className="h-9 w-9 flex items-center justify-center rounded-full bg-white/80 dark:bg-surface-dark/80 backdrop-blur-sm text-foreground hover:text-primary transition-colors"
                                            >
                                                <Heart
                                                    className={`h-5 w-5 ${
                                                        relatedProduct.isWishlisted ? 'fill-primary text-primary' : ''
                                                    }`}
                                                />
                                            </button>
                                        </div>
                                    </div>

                                    {/* Product Info */}
                                    <div className="p-4 flex flex-col flex-1">
                                        <Link href={`/products/${relatedProduct.id}`}>
                                            <h3 className="font-semibold text-base text-foreground mb-1 leading-snug hover:text-primary transition-colors">
                                                {relatedProduct.name}
                                            </h3>
                                        </Link>
                                        <p className="text-sm text-muted-foreground mb-3">
                                            {relatedProduct.category}
                                        </p>

                                        {/* Rating */}
                                        <div className="flex items-center gap-1 mb-4">
                                            {renderStars(relatedProduct.rating)}
                                            <span className="text-xs text-muted-foreground ml-1">
                                                ({relatedProduct.reviewCount})
                                            </span>
                                        </div>

                                        {/* Price & Cart */}
                                        <div className="flex justify-between items-center mt-auto">
                                            <p className="font-bold text-lg text-primary">
                                                {formatPrice(relatedProduct.price)}
                                            </p>
                                            <button
                                                onClick={() => handleAddRelatedToCart(relatedProduct.id)}
                                                className="h-9 w-9 flex items-center justify-center rounded-lg bg-primary/10 text-primary hover:bg-primary hover:text-white transition-colors"
                                            >
                                                <ShoppingCart className="h-5 w-5" />
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </main>

            <Footer />
        </div>
    );
}

export default function Detail() {
    return (
        <ToastProvider>
            <DetailContent />
        </ToastProvider>
    );
}
