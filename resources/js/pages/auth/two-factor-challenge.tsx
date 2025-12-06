import { useState, useRef, FormEvent, KeyboardEvent } from 'react';
import SimpleAuthLayout from '../../components/auth/simple-auth-layout';
import AuthForm from '../../components/auth/auth-form';
import AuthButton from '../../components/auth/auth-button';

export default function TwoFactorChallenge() {
    const [code, setCode] = useState(['', '', '', '', '', '']);
    const inputRefs = useRef<(HTMLInputElement | null)[]>([]);

    const handleChange = (index: number, value: string) => {
        if (value.length > 1) {
            value = value[0];
        }

        const newCode = [...code];
        newCode[index] = value;
        setCode(newCode);

        // Auto-focus next input
        if (value && index < 5) {
            inputRefs.current[index + 1]?.focus();
        }
    };

    const handleKeyDown = (index: number, e: KeyboardEvent<HTMLInputElement>) => {
        if (e.key === 'Backspace' && !code[index] && index > 0) {
            inputRefs.current[index - 1]?.focus();
        }
    };

    const handleSubmit = (e: FormEvent) => {
        e.preventDefault();
        const verificationCode = code.join('');
        console.log('Verification code:', verificationCode);
    };

    const handleResend = () => {
        console.log('Resending code...');
    };

    return (
        <SimpleAuthLayout
            title="Nhập Mã Xác Minh"
            description="Chúng tôi đã gửi mã xác minh đến email của bạn."
            maxWidth="sm"
            centerContent
        >
            <AuthForm onSubmit={handleSubmit}>
                        <div className="space-y-4">
                            <label htmlFor="verification-code-1" className="sr-only">
                                Mã xác minh
                            </label>
                            <div className="flex justify-center gap-2">
                                {code.map((digit, index) => (
                                    <input
                                        key={index}
                                        ref={(el) => {
                                            inputRefs.current[index] = el;
                                        }}
                                        id={index === 0 ? 'verification-code-1' : undefined}
                                        type="number"
                                        maxLength={1}
                                        value={digit}
                                        onChange={(e) => handleChange(index, e.target.value)}
                                        onKeyDown={(e) => handleKeyDown(index, e)}
                                        required
                                        className="h-14 w-12 rounded-lg border border-border bg-background text-center text-2xl font-bold text-foreground focus:border-primary focus:ring-primary [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none"
                                    />
                                ))}
                            </div>
                        </div>

                        {/* Submit Button */}
                        <div>
                            <AuthButton type="submit">
                                Xác minh
                            </AuthButton>
                        </div>
                    </AuthForm>

                    {/* Resend Link */}
                    <div className="text-center text-sm">
                        <p className="text-muted-foreground">
                            Không nhận được mã?{' '}
                            <button
                                onClick={handleResend}
                                className="font-medium text-primary hover:text-primary/90"
                            >
                                Gửi lại
                            </button>
                        </p>
                    </div>
                </SimpleAuthLayout>
    );
}
