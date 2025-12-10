import { useState } from 'react';
import { ShoppingCart, Heart, Star, Minus, Plus } from 'lucide-react';
import { Link, usePage, router } from '@inertiajs/react';
import TopNav from '../components/top-nav';
import Footer from '../components/footer';
import { ToastProvider, useToast } from '../lib/toastContext';

interface ProductImage {
    id: number;
    url: string;
    alt: string;
}

interface ProductVariant {
    variant_id: number;
    sku: string;
    price: number;
    compare_price: number | null;
    final_price: number;
    stock_quantity: number;
    available_quantity: number;
    attribute_values: Array<{
        attribute_id: number;
        attribute_name: string;
        value_id: number;
        value: string;
    }>;
    in_stock: boolean;
}

interface ProductAttribute {
    attribute_id: number;
    name: string;
    values: Array<{
        value_id: number;
        value: string;
    }>;
}

interface Product {
    id: number;
    name: string;
    description: string;
    price: number;
    minPrice: number;
    maxPrice: number;
    images: ProductImage[];
    variants: ProductVariant[];
    attributes: ProductAttribute[];
    default_variant_id: number | null;
    inStock: boolean;
    category: {
        id: number;
        name: string;
    } | null;
    brand: {
        id: number;
        name: string;
    } | null;
    shop: {
        id: number;
        name: string;
        logo: string;
        rating: number;
    } | null;
}

interface RelatedProduct {
    id: number;
    name: string;
    category: string;
    image: string;
    price: number;
    rating: number;
    reviewCount: number;
    isWishlisted: boolean;
}

interface Rating {
    average: number;
    count: number;
    breakdown: Array<{
        rating: number;
        count: number;
        percentage: number;
    }>;
}

interface Review {
    id: number;
    rating: number;
    comment: string;
    created_at: string;
    user: {
        name: string;
        avatar: string | null;
    };
}

interface Reviews {
    data: Review[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

interface DetailProps {
    product: Product;
    rating: Rating;
    soldCount: number;
    relatedProducts: RelatedProduct[];
    reviews: Reviews;
    user: {
        id: number;
        name: string;
        email: string;
        avatar: string | null;
    } | null;
    cartCount: number;
    [key: string]: unknown;
}

function DetailContent() {
    const { showSuccess } = useToast();
    const { product, rating, soldCount, relatedProducts: initialRelatedProducts, reviews } = usePage<DetailProps>().props;
    
    const [relatedProducts, setRelatedProducts] = useState(initialRelatedProducts);
    const [selectedImageIndex, setSelectedImageIndex] = useState(0);
    const [quantity, setQuantity] = useState(1);
    const [selectedVariantId, setSelectedVariantId] = useState(product.default_variant_id);
    const [activeTab, setActiveTab] = useState<'description' | 'specs' | 'reviews'>('description');

    // Get selected variant with fallback
    const selectedVariant = product.variants.find(v => v.variant_id === selectedVariantId) || product.variants[0] || {
        variant_id: 0,
        sku: '',
        price: product.price || 0,
        compare_price: null,
        final_price: product.price || 0,
        stock_quantity: 0,
        available_quantity: 0,
        attribute_values: [],
        in_stock: false,
    };

    const formatPrice = (price: number) =>
        new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND',
        }).format(price);

    const renderStars = (ratingValue: number) => {
        const stars = [];
        const fullStars = Math.floor(ratingValue);
        const hasHalfStar = ratingValue % 1 !== 0;

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
        const maxQuantity = selectedVariant.available_quantity || 1;
        setQuantity((prev) => Math.max(1, Math.min(maxQuantity, prev + delta)));
    };

    const handleAddToCart = () => {
        if (!selectedVariantId) {
            showSuccess('Vui lòng chọn loại sản phẩm');
            return;
        }
        
        router.post(`/product/${product.id}/cart`, {
            variant_id: selectedVariantId,
            quantity: quantity,
        }, {
            preserveScroll: true,
            onSuccess: () => {
                showSuccess(`Đã thêm ${quantity} sản phẩm vào giỏ hàng`);
            },
        });
    };

    const handleBuyNow = () => {
        if (!selectedVariantId) {
            showSuccess('Vui lòng chọn loại sản phẩm');
            return;
        }
        
        router.post(`/product/${product.id}/buy-now`, {
            variant_id: selectedVariantId,
            quantity: quantity,
        });
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
        const relatedProduct = relatedProducts.find((p) => p.id === productId);
        if (relatedProduct) {
            showSuccess(
                relatedProduct.isWishlisted
                    ? 'Đã xóa khỏi danh sách yêu thích'
                    : 'Đã thêm vào danh sách yêu thích'
            );
        }
    };

    const handleAddRelatedToCart = (productId: number) => {
        const relatedProduct = relatedProducts.find((p) => p.id === productId);
        if (relatedProduct) {
            showSuccess(`Đã thêm ${relatedProduct.name} vào giỏ hàng`);
        }
    };

    const handleAttributeChange = (attributeId: number, valueId: number) => {
        // Find variant matching the selected attribute values
        const currentValues = selectedVariant.attribute_values || [];
        const newValues = currentValues.map(av => 
            av.attribute_id === attributeId ? { ...av, value_id: valueId } : av
        );
        
        const matchingVariant = product.variants.find(variant =>
            newValues.every(nv =>
                variant.attribute_values.some(av =>
                    av.attribute_id === nv.attribute_id && av.value_id === nv.value_id
                )
            )
        );
        
        if (matchingVariant) {
            setSelectedVariantId(matchingVariant.variant_id);
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
                                    {renderStars(rating.average)}
                                    <span className="text-sm text-muted-foreground ml-1.5 font-medium">
                                        {rating.average.toFixed(1)} ({rating.count} đánh giá)
                                    </span>
                                </div>
                                <div className="h-5 w-px bg-border" />
                                <span className="text-sm font-medium text-secondary">
                                    {selectedVariant.in_stock ? `Còn ${selectedVariant.available_quantity} sản phẩm` : 'Hết hàng'}
                                </span>
                                {soldCount > 0 && (
                                    <>
                                        <div className="h-5 w-px bg-border" />
                                        <span className="text-sm text-muted-foreground">Đã bán {soldCount}</span>
                                    </>
                                )}
                            </div>

                            {/* Price */}
                            <div className="mb-6">
                                <p className="text-4xl font-bold text-primary">
                                    {formatPrice(selectedVariant.final_price)}
                                </p>
                                {selectedVariant.compare_price && selectedVariant.compare_price > selectedVariant.final_price && (
                                    <div className="flex items-center gap-2 mt-2">
                                        <span className="text-lg text-muted-foreground line-through">
                                            {formatPrice(selectedVariant.compare_price)}
                                        </span>
                                        <span className="text-sm font-semibold text-red-500 bg-red-50 dark:bg-red-950 px-2 py-0.5 rounded">
                                            -{Math.round((1 - selectedVariant.final_price / selectedVariant.compare_price) * 100)}%
                                        </span>
                                    </div>
                                )}
                            </div>

                            {/* Description */}
                            <p className="text-muted-foreground mb-8 leading-relaxed">
                                {product.description}
                            </p>

                            {/* Shop Info */}
                            {product.shop && (
                                <div className="flex items-center gap-3 p-4 mb-8 border border-border rounded-lg">
                                    <div className="h-12 w-12 rounded-full bg-muted overflow-hidden">
                                        {product.shop.logo && (
                                            <img src={product.shop.logo} alt={product.shop.name} className="w-full h-full object-cover" />
                                        )}
                                    </div>
                                    <div>
                                        <p className="font-semibold">{product.shop.name}</p>
                                        <div className="flex items-center gap-1 text-sm text-muted-foreground">
                                            <Star className="h-3.5 w-3.5 fill-yellow-500 text-yellow-500" />
                                            <span>{product.shop.rating.toFixed(1)}</span>
                                        </div>
                                    </div>
                                </div>
                            )}

                            {/* Options */}
                            <div className="flex flex-col gap-6 mb-8">
                                {/* Attribute Selection */}
                                {product.attributes.map((attribute) => (
                                    <div key={attribute.attribute_id} className="flex flex-col gap-3">
                                        <label className="text-sm font-semibold">{attribute.name}</label>
                                        <div className="flex items-center gap-3 flex-wrap">
                                            {attribute.values.map((value) => {
                                                const isSelected = selectedVariant.attribute_values.some(
                                                    av => av.attribute_id === attribute.attribute_id && av.value_id === value.value_id
                                                );
                                                return (
                                                    <button
                                                        key={value.value_id}
                                                        onClick={() => handleAttributeChange(attribute.attribute_id, value.value_id)}
                                                        className={`px-4 py-2 rounded-lg border-2 transition ${
                                                            isSelected
                                                                ? 'border-primary bg-primary/10 text-primary font-semibold'
                                                                : 'border-border hover:border-primary'
                                                        }`}
                                                    >
                                                        {value.value}
                                                    </button>
                                                );
                                            })}
                                        </div>
                                    </div>
                                ))}

                                {/* Quantity */}
                                <div className="flex flex-col gap-3">
                                    <label className="text-sm font-semibold">Số lượng</label>
                                    <div className="flex items-center border border-border rounded-lg w-fit">
                                        <button
                                            onClick={() => handleQuantityChange(-1)}
                                            disabled={quantity <= 1}
                                            className="h-10 w-10 text-muted-foreground hover:bg-card transition rounded-l-lg disabled:opacity-50 disabled:cursor-not-allowed"
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
                                            disabled={quantity >= selectedVariant.available_quantity}
                                            className="h-10 w-10 text-muted-foreground hover:bg-card transition rounded-r-lg disabled:opacity-50 disabled:cursor-not-allowed"
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
                                    disabled={!selectedVariant.in_stock}
                                    className="flex flex-1 min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-14 px-6 bg-primary text-white text-base font-bold leading-normal tracking-[0.015em] hover:bg-primary/90 transition-colors gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <ShoppingCart className="h-5 w-5" />
                                    <span className="truncate">Thêm vào giỏ hàng</span>
                                </button>
                                <button
                                    onClick={handleBuyNow}
                                    disabled={!selectedVariant.in_stock}
                                    className="flex flex-1 min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-14 px-6 bg-secondary text-white text-base font-bold leading-normal tracking-[0.015em] hover:bg-secondary/90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <span className="truncate">Mua ngay</span>
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
                                Đánh giá ({rating.count})
                            </button>
                        </div>

                        {/* Tab Content */}
                        <div className="prose prose-sm md:prose-base max-w-none text-muted-foreground leading-relaxed">
                            {activeTab === 'description' && (
                                <div className="whitespace-pre-line">
                                    {product.description}
                                </div>
                            )}
                            {activeTab === 'specs' && (
                                <div>
                                    <table className="w-full">
                                        <tbody>
                                            {product.brand && (
                                                <tr className="border-b border-border">
                                                    <td className="py-3 font-semibold w-1/3">Thương hiệu</td>
                                                    <td className="py-3">{product.brand.name}</td>
                                                </tr>
                                            )}
                                            {product.category && (
                                                <tr className="border-b border-border">
                                                    <td className="py-3 font-semibold w-1/3">Danh mục</td>
                                                    <td className="py-3">{product.category.name}</td>
                                                </tr>
                                            )}
                                            <tr className="border-b border-border">
                                                <td className="py-3 font-semibold w-1/3">SKU</td>
                                                <td className="py-3">{selectedVariant.sku}</td>
                                            </tr>
                                            <tr className="border-b border-border">
                                                <td className="py-3 font-semibold w-1/3">Tình trạng</td>
                                                <td className="py-3">{selectedVariant.in_stock ? 'Còn hàng' : 'Hết hàng'}</td>
                                            </tr>
                                            {selectedVariant.attribute_values.map((av) => (
                                                <tr key={av.attribute_id} className="border-b border-border">
                                                    <td className="py-3 font-semibold w-1/3">{av.attribute_name}</td>
                                                    <td className="py-3">{av.value}</td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            )}
                            {activeTab === 'reviews' && (
                                <div>
                                    {/* Rating Summary */}
                                    <div className="mb-8 p-6 bg-card rounded-lg border border-border">
                                        <div className="flex items-start gap-8">
                                            <div className="text-center">
                                                <div className="text-5xl font-bold text-primary mb-2">
                                                    {rating.average.toFixed(1)}
                                                </div>
                                                <div className="flex items-center gap-1 justify-center mb-1">
                                                    {renderStars(rating.average)}
                                                </div>
                                                <div className="text-sm text-muted-foreground">
                                                    {rating.count} đánh giá
                                                </div>
                                            </div>
                                            <div className="flex-1">
                                                {rating.breakdown.map((item) => (
                                                    <div key={item.rating} className="flex items-center gap-3 mb-2">
                                                        <div className="flex items-center gap-1 w-16">
                                                            <span className="text-sm font-medium">{item.rating}</span>
                                                            <Star className="h-4 w-4 fill-yellow-500 text-yellow-500" />
                                                        </div>
                                                        <div className="flex-1 h-2 bg-muted rounded-full overflow-hidden">
                                                            <div 
                                                                className="h-full bg-yellow-500" 
                                                                style={{ width: `${item.percentage}%` }}
                                                            />
                                                        </div>
                                                        <span className="text-sm text-muted-foreground w-16 text-right">
                                                            {item.count}
                                                        </span>
                                                    </div>
                                                ))}
                                            </div>
                                        </div>
                                    </div>

                                    {/* Reviews List */}
                                    <div className="space-y-6">
                                        {reviews.data.length > 0 ? (
                                            reviews.data.map((review) => (
                                                <div key={review.id} className="border-b border-border pb-6 last:border-0">
                                                    <div className="flex items-start gap-4 mb-3">
                                                        <div className="h-10 w-10 rounded-full bg-muted flex items-center justify-center overflow-hidden">
                                                            {review.user.avatar ? (
                                                                <img src={review.user.avatar} alt={review.user.name} className="w-full h-full object-cover" />
                                                            ) : (
                                                                <span className="text-sm font-semibold">{review.user.name.charAt(0).toUpperCase()}</span>
                                                            )}
                                                        </div>
                                                        <div className="flex-1">
                                                            <div className="flex items-center gap-2 mb-1">
                                                                <span className="font-semibold">{review.user.name}</span>
                                                                <span className="text-sm text-muted-foreground">
                                                                    {new Date(review.created_at).toLocaleDateString('vi-VN')}
                                                                </span>
                                                            </div>
                                                            <div className="flex items-center gap-1 mb-2">
                                                                {renderStars(review.rating)}
                                                            </div>
                                                            <p className="text-sm leading-relaxed">{review.comment}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            ))
                                        ) : (
                                            <p className="text-center text-muted-foreground py-8">
                                                Chưa có đánh giá nào cho sản phẩm này
                                            </p>
                                        )}
                                    </div>

                                    {/* Pagination */}
                                    {reviews.last_page > 1 && (
                                        <div className="flex justify-center gap-2 mt-8">
                                            {Array.from({ length: reviews.last_page }, (_, i) => i + 1).map((page) => (
                                                <Link
                                                    key={page}
                                                    href={`/product/${product.id}?page=${page}`}
                                                    className={`px-4 py-2 rounded-lg transition ${
                                                        page === reviews.current_page
                                                            ? 'bg-primary text-white'
                                                            : 'bg-card border border-border hover:border-primary'
                                                    }`}
                                                >
                                                    {page}
                                                </Link>
                                            ))}
                                        </div>
                                    )}
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
                                        <Link href={`/product/${relatedProduct.id}`}>
                                            <div
                                                className="aspect-square w-full bg-cover bg-center"
                                                style={{
                                                    backgroundImage: `url('${relatedProduct.image}')`,
                                                }}
                                            />
                                        </Link>
                                        <div className="absolute top-3 right-3">
                                            <button
                                                onClick={(e) => { e.stopPropagation(); handleToggleWishlist(relatedProduct.id); }}
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
                                        <Link href={`/product/${relatedProduct.id}`}>
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
                                                onClick={(e) => { e.stopPropagation(); handleAddRelatedToCart(relatedProduct.id); }}
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
