import { useState, FormEvent } from 'react';
import { Link } from '@inertiajs/react';
import { ShoppingBag } from 'lucide-react';

export default function ConfirmPassword() {
    const [password, setPassword] = useState('');

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        // Handle password confirmation logic here
        console.log('Confirm password:', password);
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
                            Xác nhận mật khẩu của bạn
                        </h2>
                        <p className="mt-2 text-center text-sm text-muted-foreground">
                            Vui lòng nhập mật khẩu hiện tại của bạn để tiếp tục.
                        </p>
                    </div>

                    {/* Confirm Password Form */}
                    <form onSubmit={handleSubmit} className="mt-8 space-y-6">
                        <div className="space-y-4 rounded-md">
                            {/* Current Password */}
                            <div>
                                <label htmlFor="current-password" className="sr-only">
                                    Mật khẩu hiện tại
                                </label>
                                <input
                                    id="current-password"
                                    name="current-password"
                                    type="password"
                                    autoComplete="current-password"
                                    required
                                    value={password}
                                    onChange={(e) => setPassword(e.target.value)}
                                    className="relative block w-full appearance-none rounded-lg border border-border bg-background px-3 py-3 text-foreground placeholder-muted-foreground focus:z-10 focus:border-primary focus:outline-none focus:ring-primary sm:text-sm"
                                    placeholder="Mật khẩu hiện tại"
                                />
                            </div>
                        </div>

                        {/* Submit Button */}
                        <div>
                            <button
                                type="submit"
                                className="group relative flex w-full justify-center rounded-lg border border-transparent bg-primary px-4 py-3 text-sm font-bold text-white transition-colors hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                            >
                                Xác nhận
                            </button>
                        </div>
                    </form>

                    {/* Back Link */}
                    <div className="text-center text-sm">
                        <Link href="#" className="font-medium text-primary hover:text-primary/90">
                            Quay lại
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    );
}
