import { useState, FormEvent } from 'react';
import SimpleAuthLayout from '../../components/auth/simple-auth-layout';
import AuthForm from '../../components/auth/auth-form';
import AuthInput from '../../components/auth/auth-input';
import AuthButton from '../../components/auth/auth-button';
import AuthLink from '../../components/auth/auth-link';

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
        <SimpleAuthLayout
            title="Đăng nhập vào tài khoản"
            description="Chào mừng trở lại! Vui lòng đăng nhập để tiếp tục."
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
                                autoComplete="current-password"
                                required
                                value={formData.password}
                                onChange={handleChange}
                                placeholder="Mật khẩu"
                            />
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
                                <AuthLink href="#">
                                    Quên mật khẩu?
                                </AuthLink>
                            </div>
                        </div>

                        {/* Submit Button */}
                        <div>
                            <AuthButton type="submit">
                                Đăng nhập
                            </AuthButton>
                        </div>
                    </AuthForm>

                    {/* Register Link */}
                    <div className="text-center text-sm">
                        <span className="text-muted-foreground">Chưa có tài khoản? </span>
                        <AuthLink href="#">
                            Đăng ký ngay
                        </AuthLink>
                    </div>
                </SimpleAuthLayout>
    );
}
