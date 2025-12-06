import { useState, FormEvent } from 'react';
import SimpleAuthLayout from '../../components/auth/simple-auth-layout';
import AuthForm from '../../components/auth/auth-form';
import AuthInput from '../../components/auth/auth-input';
import AuthButton from '../../components/auth/auth-button';
import AuthLink from '../../components/auth/auth-link';

export default function ForgotPassword() {
    const [email, setEmail] = useState('');

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        // Handle password reset logic here
        console.log('Reset password for:', email);
    };

    return (
        <SimpleAuthLayout
            title="Quên mật khẩu?"
            description="Đừng lo lắng! Nhập email của bạn và chúng tôi sẽ gửi cho bạn một liên kết để đặt lại mật khẩu."
        >
            <AuthForm onSubmit={handleSubmit}>
                        <div className="space-y-4 rounded-md shadow-sm">
                            {/* Email */}
                            <AuthInput
                                id="email-address"
                                label="Địa chỉ email"
                                name="email"
                                type="email"
                                autoComplete="email"
                                required
                                value={email}
                                onChange={(e) => setEmail(e.target.value)}
                                placeholder="Địa chỉ email"
                            />
                        </div>

                        {/* Submit Button */}
                        <div>
                            <AuthButton type="submit">
                                Gửi liên kết đặt lại
                            </AuthButton>
                        </div>
                    </AuthForm>

                    {/* Back to Login Link */}
                    <div className="text-center text-sm">
                        <AuthLink href="#">
                            Quay lại trang Đăng nhập
                        </AuthLink>
                    </div>
                </SimpleAuthLayout>
    );
}
