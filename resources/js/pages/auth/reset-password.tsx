import { useState, FormEvent } from 'react';
import SimpleAuthLayout from '../../components/auth/simple-auth-layout';
import AuthForm from '../../components/auth/auth-form';
import AuthPasswordInput from '../../components/auth/auth-password-input';
import AuthButton from '../../components/auth/auth-button';
import AuthLink from '../../components/auth/auth-link';

export default function ResetPassword() {
    const [formData, setFormData] = useState({
        newPassword: '',
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
        // Handle password reset logic here
        console.log('Reset password:', formData);
    };

    return (
        <SimpleAuthLayout
            title="Đặt lại mật khẩu của bạn"
            description="Tạo một mật khẩu mới mạnh mẽ và an toàn cho tài khoản của bạn."
        >
            <AuthForm onSubmit={handleSubmit}>
                        <div className="space-y-4 rounded-md">
                            {/* New Password */}
                            <AuthPasswordInput
                                id="new-password"
                                label="Mật khẩu mới"
                                name="newPassword"
                                autoComplete="new-password"
                                required
                                value={formData.newPassword}
                                onChange={handleChange}
                                placeholder="Mật khẩu mới"
                            />

                            {/* Confirm Password */}
                            <AuthPasswordInput
                                id="confirm-password"
                                label="Xác nhận mật khẩu mới"
                                name="confirmPassword"
                                autoComplete="new-password"
                                required
                                value={formData.confirmPassword}
                                onChange={handleChange}
                                placeholder="Xác nhận mật khẩu mới"
                            />
                        </div>

                        {/* Submit Button */}
                        <div>
                            <AuthButton type="submit">
                                Đặt lại mật khẩu
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
