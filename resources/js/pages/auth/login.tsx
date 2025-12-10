import { FormEvent } from 'react';
import { useForm } from '@inertiajs/react';
import SimpleAuthLayout from '../../components/auth/simple-auth-layout';
import AuthForm from '../../components/auth/auth-form';
import AuthInput from '../../components/auth/auth-input';
import AuthPasswordInput from '../../components/auth/auth-password-input';
import AuthButton from '../../components/auth/auth-button';
import AuthLink from '../../components/auth/auth-link';

export default function LoginPage() {
    const { data, setData, post, processing, errors } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        post('/login');
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
                                value={data.email}
                                onChange={(e) => setData('email', e.target.value)}
                                placeholder="Địa chỉ email"
                            />
                            {errors.email && (
                                <p className="text-sm text-red-600">{errors.email}</p>
                            )}

                            {/* Password */}
                            <AuthPasswordInput
                                id="password"
                                label="Mật khẩu"
                                name="password"
                                autoComplete="current-password"
                                required
                                value={data.password}
                                onChange={(e) => setData('password', e.target.value)}
                                placeholder="Mật khẩu"
                                error={errors.password}
                            />
                        </div>

                        {/* Remember Me & Forgot Password */}
                        <div className="flex items-center justify-between">
                            <div className="flex items-center">
                                <input
                                    id="remember"
                                    name="remember"
                                    type="checkbox"
                                    checked={data.remember}
                                    onChange={(e) => setData('remember', e.target.checked)}
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
                            <AuthButton type="submit" disabled={processing}>
                                {processing ? 'Đang xử lý...' : 'Đăng nhập'}
                            </AuthButton>
                        </div>
                    </AuthForm>

                    {/* Register Link */}
                    <div className="text-center text-sm">
                        <span className="text-muted-foreground">Chưa có tài khoản? </span>
                        <AuthLink href={"/register"}>
                            Đăng ký ngay
                        </AuthLink>
                    </div>
                </SimpleAuthLayout>
    );
}
