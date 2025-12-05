import { ShoppingBag, MailCheck } from 'lucide-react';
import { Link } from '@inertiajs/react';

export default function VerifyEmail() {
    const handleResendEmail = () => {
        // Handle resend verification email logic here
        console.log('Resending verification email...');
    };

    return (
        <div className="relative flex min-h-screen w-full overflow-x-hidden bg-card dark:bg-background">
            <div className="flex flex-1 items-center justify-center p-4">
                <div className="w-full max-w-md">
                    <div className="flex flex-col items-center text-center">
                        {/* Logo */}
                        <div className="flex items-center justify-center gap-2 text-foreground">
                            <div className="text-primary">
                                <ShoppingBag className="h-12 w-12" />
                            </div>
                            <h1 className="text-4xl font-bold tracking-tight">ShopNest</h1>
                        </div>

                        {/* Email Icon */}
                        <div className="mt-8 flex h-20 w-20 items-center justify-center rounded-full bg-secondary/10 text-secondary">
                            <MailCheck className="h-12 w-12" />
                        </div>

                        {/* Title & Description */}
                        <h2 className="mt-6 text-2xl font-bold tracking-tight text-foreground">
                            Vui lòng xác minh địa chỉ email của bạn
                        </h2>
                        <p className="mt-4 max-w-sm text-sm text-muted-foreground">
                            Chúng tôi đã gửi một email xác minh đến địa chỉ email của bạn. Vui lòng kiểm tra hộp thư đến và
                            nhấp vào liên kết để hoàn tất việc đăng ký.
                        </p>
                    </div>

                    {/* Resend Button */}
                    <div className="mt-8">
                        <button
                            onClick={handleResendEmail}
                            className="group relative flex w-full justify-center rounded-lg border border-transparent bg-primary px-4 py-3 text-sm font-bold text-white shadow-md shadow-primary/30 transition-all duration-300 ease-in-out hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 dark:focus:ring-offset-background"
                            type="button"
                        >
                            Gửi lại email xác minh
                        </button>
                    </div>

                    {/* Help Text */}
                    <div className="mt-6 text-center text-sm">
                        <span className="text-muted-foreground">
                            Chưa nhận được email? Kiểm tra thư mục spam của bạn hoặc{' '}
                        </span>
                        <Link href="#" className="font-medium text-primary hover:text-primary/90">
                            liên hệ hỗ trợ.
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    );
}
