import { useState, FormEvent } from 'react';
import { Link } from '@inertiajs/react';
import { ShoppingBag } from 'lucide-react';

export default function Register() {
    const [formData, setFormData] = useState({
        fullName: '',
        email: '',
        password: '',
        confirmPassword: '',
    });

    const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        setFormData({
            ...formData,
            [e.target.name]: e.target.value,
        });
    };

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        // Handle registration logic here
        console.log('Register:', formData);
    };

    return (
        <div className="relative flex min-h-screen w-full flex-col overflow-x-hidden bg-card dark:bg-background">
            <div className="flex flex-1 items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
                <div className="w-full max-w-md space-y-8">
                    {/* Logo & Header */}
                    <div>
                        <div className="flex items-center justify-center gap-2 text-foreground">
                            <div className="text-primary">
                                <ShoppingBag className="h-12 w-12" />
                            </div>
                            <h1 className="text-4xl font-bold tracking-tight">ShopNest</h1>
                        </div>
                        <h2 className="mt-6 text-center text-2xl font-bold tracking-tight text-foreground">
                            Tạo tài khoản mới
                        </h2>
                        <p className="mt-2 text-center text-sm text-muted-foreground">
                            Tham gia cùng chúng tôi và bắt đầu mua sắm!
                        </p>
                    </div>

                    {/* Registration Form */}
                    <form onSubmit={handleSubmit} className="mt-8 space-y-6">
                        <div className="space-y-4 rounded-md shadow-sm">
                            {/* Full Name */}
                            <div>
                                <label htmlFor="full-name" className="sr-only">
                                    Họ và tên
                                </label>
                                <input
                                    id="full-name"
                                    name="fullName"
                                    type="text"
                                    autoComplete="name"
                                    required
                                    value={formData.fullName}
                                    onChange={handleChange}
                                    className="relative block w-full appearance-none rounded-lg border border-border bg-background px-3 py-3 text-foreground placeholder-muted-foreground focus:z-10 focus:border-primary focus:outline-none focus:ring-primary sm:text-sm"
                                    placeholder="Họ và tên"
                                />
                            </div>

                            {/* Email */}
                            <div>
                                <label htmlFor="email-address" className="sr-only">
                                    Địa chỉ email
                                </label>
                                <input
                                    id="email-address"
                                    name="email"
                                    type="email"
                                    autoComplete="email"
                                    required
                                    value={formData.email}
                                    onChange={handleChange}
                                    className="relative block w-full appearance-none rounded-lg border border-border bg-background px-3 py-3 text-foreground placeholder-muted-foreground focus:z-10 focus:border-primary focus:outline-none focus:ring-primary sm:text-sm"
                                    placeholder="Địa chỉ email"
                                />
                            </div>

                            {/* Password */}
                            <div>
                                <label htmlFor="password" className="sr-only">
                                    Mật khẩu
                                </label>
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    autoComplete="new-password"
                                    required
                                    value={formData.password}
                                    onChange={handleChange}
                                    className="relative block w-full appearance-none rounded-lg border border-border bg-background px-3 py-3 text-foreground placeholder-muted-foreground focus:z-10 focus:border-primary focus:outline-none focus:ring-primary sm:text-sm"
                                    placeholder="Mật khẩu"
                                />
                            </div>

                            {/* Confirm Password */}
                            <div>
                                <label htmlFor="confirm-password" className="sr-only">
                                    Xác nhận mật khẩu
                                </label>
                                <input
                                    id="confirm-password"
                                    name="confirmPassword"
                                    type="password"
                                    autoComplete="new-password"
                                    required
                                    value={formData.confirmPassword}
                                    onChange={handleChange}
                                    className="relative block w-full appearance-none rounded-lg border border-border bg-background px-3 py-3 text-foreground placeholder-muted-foreground focus:z-10 focus:border-primary focus:outline-none focus:ring-primary sm:text-sm"
                                    placeholder="Xác nhận mật khẩu"
                                />
                            </div>
                        </div>

                        {/* Submit Button */}
                        <div>
                            <button
                                type="submit"
                                className="group relative flex w-full justify-center rounded-lg border border-transparent bg-primary px-4 py-3 text-sm font-bold text-white transition-colors hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                            >
                                Đăng ký
                            </button>
                        </div>
                    </form>

                    {/* Login Link */}
                    <div className="text-center text-sm">
                        <span className="text-muted-foreground">Bạn đã có tài khoản? </span>
                        <Link href="#" className="font-medium text-primary hover:text-primary/90">
                            Đăng nhập ngay
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    );
}
