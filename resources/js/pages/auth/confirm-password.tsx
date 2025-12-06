import { useState, FormEvent } from 'react';
import SimpleAuthLayout from '../../components/auth/simple-auth-layout';
import AuthForm from '../../components/auth/auth-form';
import AuthInput from '../../components/auth/auth-input';
import AuthButton from '../../components/auth/auth-button';
import AuthLink from '../../components/auth/auth-link';

export default function ConfirmPassword() {
    const [password, setPassword] = useState('');

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        // Handle password confirmation logic here
        console.log('Confirm password:', password);
    };

    return (
        <SimpleAuthLayout
            title="Xác nhận mật khẩu của bạn"
            description="Vui lòng nhập mật khẩu hiện tại của bạn để tiếp tục."
        >
            <AuthForm onSubmit={handleSubmit}>
                        <div className="space-y-4 rounded-md">
                            {/* Current Password */}
                            <AuthInput
                                id="current-password"
                                label="Mật khẩu hiện tại"
                                name="current-password"
                                type="password"
                                autoComplete="current-password"
                                required
                                value={password}
                                onChange={(e) => setPassword(e.target.value)}
                                placeholder="Mật khẩu hiện tại"
                            />
                        </div>

                        {/* Submit Button */}
                        <div>
                            <AuthButton type="submit">
                                Xác nhận
                            </AuthButton>
                        </div>
                    </AuthForm>

                    {/* Back Link */}
                    <div className="text-center text-sm">
                        <AuthLink href="#">
                            Quay lại
                        </AuthLink>
                    </div>
                </SimpleAuthLayout>
    );
}
