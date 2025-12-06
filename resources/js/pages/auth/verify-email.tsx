import { MailCheck } from 'lucide-react';
import SimpleAuthLayout from '../../components/auth/simple-auth-layout';
import AuthButton from '../../components/auth/auth-button';
import AuthLink from '../../components/auth/auth-link';

export default function VerifyEmail() {
    const handleResendEmail = () => {
        // Handle resend verification email logic here
        console.log('Resending verification email...');
    };

    return (
        <SimpleAuthLayout
            title="Vui lòng xác minh địa chỉ email của bạn"
            description="Chúng tôi đã gửi một email xác minh đến địa chỉ email của bạn. Vui lòng kiểm tra hộp thư đến và nhấp vào liên kết để hoàn tất việc đăng ký."
            centerContent
        >
            {/* Email Icon */}
            <div className="flex justify-center">
                <div className="flex h-20 w-20 items-center justify-center rounded-full bg-secondary/10 text-secondary">
                    <MailCheck className="h-12 w-12" />
                </div>
            </div>

                    {/* Resend Button */}
                    <div className="mt-8">
                        <AuthButton
                            onClick={handleResendEmail}
                            type="button"
                            className="shadow-md shadow-primary/30 transition-all duration-300 ease-in-out dark:focus:ring-offset-background"
                        >
                            Gửi lại email xác minh
                        </AuthButton>
                    </div>

                    {/* Help Text */}
                    <div className="mt-6 text-center text-sm">
                        <span className="text-muted-foreground">
                            Chưa nhận được email? Kiểm tra thư mục spam của bạn hoặc{' '}
                        </span>
                        <AuthLink href="#">
                            liên hệ hỗ trợ.
                        </AuthLink>
                    </div>
                </SimpleAuthLayout>
    );
}
