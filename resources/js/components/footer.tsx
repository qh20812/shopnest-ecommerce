import { ShoppingBag } from 'lucide-react';
import { Link } from '@inertiajs/react';

export default function Footer() {
    return (
        <footer className="bg-card">
            <div className="container mx-auto px-4 py-12">
                <div className="grid grid-cols-1 gap-8 md:grid-cols-4">
                    <div className="col-span-1 md:col-span-2 lg:col-span-1">
                        <div className="mb-4 flex items-center gap-2 text-foreground">
                            <div className="text-primary">
                                <ShoppingBag className="h-8 w-8" />
                            </div>
                            <h1 className="text-xl font-bold">ShopNest</h1>
                        </div>
                        <p className="text-sm text-muted-foreground">
                            Điểm đến mua sắm trực tuyến của bạn cho mọi thứ bạn
                            cần.
                        </p>
                    </div>
                    <div>
                        <h4 className="mb-4 font-bold text-foreground">
                            Về chúng tôi
                        </h4>
                        <ul className="space-y-2">
                            <li>
                                <Link
                                    href="#"
                                    className="text-sm text-muted-foreground hover:text-primary"
                                >
                                    Câu chuyện
                                </Link>
                            </li>
                            <li>
                                <Link
                                    href="#"
                                    className="text-sm text-muted-foreground hover:text-primary"
                                >
                                    Tuyển dụng
                                </Link>
                            </li>
                            <li>
                                <Link
                                    href="#"
                                    className="text-sm text-muted-foreground hover:text-primary"
                                >
                                    Báo chí
                                </Link>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h4 className="mb-4 font-bold text-foreground">
                            Chăm sóc khách hàng
                        </h4>
                        <ul className="space-y-2">
                            <li>
                                <Link
                                    href="#"
                                    className="text-sm text-muted-foreground hover:text-primary"
                                >
                                    Liên hệ
                                </Link>
                            </li>
                            <li>
                                <Link
                                    href="#"
                                    className="text-sm text-muted-foreground hover:text-primary"
                                >
                                    Câu hỏi thường gặp
                                </Link>
                            </li>
                            <li>
                                <Link
                                    href="#"
                                    className="text-sm text-muted-foreground hover:text-primary"
                                >
                                    Chính sách đổi trả
                                </Link>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h4 className="mb-4 font-bold text-foreground">
                            Chính sách
                        </h4>
                        <ul className="space-y-2">
                            <li>
                                <Link
                                    href="#"
                                    className="text-sm text-muted-foreground hover:text-primary"
                                >
                                    Điều khoản dịch vụ
                                </Link>
                            </li>
                            <li>
                                <Link
                                    href="#"
                                    className="text-sm text-muted-foreground hover:text-primary"
                                >
                                    Chính sách bảo mật
                                </Link>
                            </li>
                            <li>
                                <Link
                                    href="#"
                                    className="text-sm text-muted-foreground hover:text-primary"
                                >
                                    Chính sách Cookie
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>
                <div className="mt-12 flex flex-col items-center justify-between border-t border-border pt-8 text-sm text-muted-foreground md:flex-row">
                    <p>© 2024 ShopNest. Đã đăng ký bản quyền.</p>
                    <div className="mt-4 flex space-x-4 md:mt-0">
                        <Link href="#" className="hover:text-primary">
                            FB
                        </Link>
                        <Link href="#" className="hover:text-primary">
                            IG
                        </Link>
                        <Link href="#" className="hover:text-primary">
                            TW
                        </Link>
                    </div>
                </div>
            </div>
        </footer>
    );
}
