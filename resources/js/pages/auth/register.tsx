import { useState, FormEvent } from 'react';
import SimpleAuthLayout from '../../components/auth/simple-auth-layout';
import AuthForm from '../../components/auth/auth-form';
import AuthInput from '../../components/auth/auth-input';
import AuthButton from '../../components/auth/auth-button';
import AuthLink from '../../components/auth/auth-link';

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
        <SimpleAuthLayout
            title="Tạo tài khoản mới"
            description="Tham gia cùng chúng tôi và bắt đầu mua sắm!"
        >
            <AuthForm onSubmit={handleSubmit}>
                        <div className="space-y-4 rounded-md shadow-sm">
                            {/* Full Name */}
                            <AuthInput
                                id="full-name"
                                label="Họ và tên"
                                name="fullName"
                                type="text"
                                autoComplete="name"
                                required
                                value={formData.fullName}
                                onChange={handleChange}
                                placeholder="Họ và tên"
                            />

                            {/* Email */}
                            <AuthInput
                                id="email-address"
                                label="Địa chỉ email"
                                name="email"
                                type="email"
                                autoComplete="email"
                                required
                                value={formData.email}
                                onChange={handleChange}
                                placeholder="Địa chỉ email"
                            />

                            {/* Password */}
                            <AuthInput
                                id="password"
                                label="Mật khẩu"
                                name="password"
                                type="password"
                                autoComplete="new-password"
                                required
                                value={formData.password}
                                onChange={handleChange}
                                placeholder="Mật khẩu"
                            />

                            {/* Confirm Password */}
                            <AuthInput
                                id="confirm-password"
                                label="Xác nhận mật khẩu"
                                name="confirmPassword"
                                type="password"
                                autoComplete="new-password"
                                required
                                value={formData.confirmPassword}
                                onChange={handleChange}
                                placeholder="Xác nhận mật khẩu"
                            />
                        </div>

                        {/* Submit Button */}
                        <div>
                            <AuthButton type="submit">
                                Đăng ký
                            </AuthButton>
                        </div>
                    </AuthForm>

                    {/* Login Link */}
                    <div className="text-center text-sm">
                        <span className="text-muted-foreground">Bạn đã có tài khoản? </span>
                        <AuthLink href="#">
                            Đăng nhập ngay
                        </AuthLink>
                    </div>
                </SimpleAuthLayout>
    );
}
