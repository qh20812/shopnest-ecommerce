import { useState, FormEvent } from 'react';
import { Link } from '@inertiajs/react';
import { ShoppingBag } from 'lucide-react';

export default function LoginPage() {
    const [formData, setFormData] = useState({
        email: '',
        password: '',
        remember: false,
    });

    const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const { name, type, checked, value } = e.target;
        setFormData({
            ...formData,
            [name]: type === 'checkbox' ? checked : value,
        });
    };

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        // Handle login logic here
        console.log('Login:', formData);
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
                            Đăng nhập vào tài khoản
                        </h2>
                        <p className="mt-2 text-center text-sm text-muted-foreground">
                            Chào mừng trở lại! Vui lòng đăng nhập để tiếp tục.
                        </p>
                    </div>

                    {/* Login Form */}
                    <form onSubmit={handleSubmit} className="mt-8 space-y-6">
                        <div className="space-y-4 rounded-md shadow-sm">
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
                                    autoComplete="current-password"
                                    required
                                    value={formData.password}
                                    onChange={handleChange}
                                    className="relative block w-full appearance-none rounded-lg border border-border bg-background px-3 py-3 text-foreground placeholder-muted-foreground focus:z-10 focus:border-primary focus:outline-none focus:ring-primary sm:text-sm"
                                    placeholder="Mật khẩu"
                                />
                            </div>
                        </div>

                        {/* Remember Me & Forgot Password */}
                        <div className="flex items-center justify-between">
                            <div className="flex items-center">
                                <input
                                    id="remember"
                                    name="remember"
                                    type="checkbox"
                                    checked={formData.remember}
                                    onChange={handleChange}
                                    className="h-4 w-4 rounded border-border text-primary focus:ring-primary"
                                />
                                <label htmlFor="remember" className="ml-2 block text-sm text-foreground">
                                    Ghi nhớ đăng nhập
                                </label>
                            </div>

                            <div className="text-sm">
                                <Link href="#" className="font-medium text-primary hover:text-primary/90">
                                    Quên mật khẩu?
                                </Link>
                            </div>
                        </div>

                        {/* Submit Button */}
                        <div>
                            <button
                                type="submit"
                                className="group relative flex w-full justify-center rounded-lg border border-transparent bg-primary px-4 py-3 text-sm font-bold text-white transition-colors hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                            >
                                Đăng nhập
                            </button>
                        </div>
                    </form>

                    {/* Register Link */}
                    <div className="text-center text-sm">
                        <span className="text-muted-foreground">Chưa có tài khoản? </span>
                        <Link href="#" className="font-medium text-primary hover:text-primary/90">
                            Đăng ký ngay
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    );
}
