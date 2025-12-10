import { FormEvent } from 'react';
import { useForm } from '@inertiajs/react';
import SimpleAuthLayout from '../../components/auth/simple-auth-layout';
import AuthForm from '../../components/auth/auth-form';
import AuthInput from '../../components/auth/auth-input';
import AuthPasswordInput from '../../components/auth/auth-password-input';
import AuthButton from '../../components/auth/auth-button';
import AuthLink from '../../components/auth/auth-link';

export default function Register() {
    const { data, setData, post, processing, errors } = useForm({
        full_name: '',
        email: '',
        password: '',
        password_confirmation: '',
    });

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        post('/register');
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
                                name="full_name"
                                type="text"
                                autoComplete="name"
                                required
                                value={data.full_name}
                                onChange={(e) => setData('full_name', e.target.value)}
                                placeholder="Họ và tên"
                            />
                            {errors.full_name && (
                                <p className="text-sm text-red-600">{errors.full_name}</p>
                            )}

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
                                autoComplete="new-password"
                                required
                                value={data.password}
                                onChange={(e) => setData('password', e.target.value)}
                                placeholder="Mật khẩu"
                                error={errors.password}
                            />

                            {/* Confirm Password */}
                            <AuthPasswordInput
                                id="confirm-password"
                                label="Xác nhận mật khẩu"
                                name="password_confirmation"
                                autoComplete="new-password"
                                required
                                value={data.password_confirmation}
                                onChange={(e) => setData('password_confirmation', e.target.value)}
                                placeholder="Xác nhận mật khẩu"
                                error={errors.password_confirmation}
                            />
                        </div>

                        {/* Submit Button */}
                        <div>
                            <AuthButton type="submit" disabled={processing}>
                                {processing ? 'Đang xử lý...' : 'Đăng ký'}
                            </AuthButton>
                        </div>
                    </AuthForm>

                    {/* Login Link */}
                    <div className="text-center text-sm">
                        <span className="text-muted-foreground">Bạn đã có tài khoản? </span>
                        <AuthLink href="/login">
                            Đăng nhập ngay
                        </AuthLink>
                    </div>
                </SimpleAuthLayout>
    );
}
