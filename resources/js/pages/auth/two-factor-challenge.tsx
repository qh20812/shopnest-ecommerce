import { useState, useRef, FormEvent, KeyboardEvent } from 'react';
import { ShoppingBag } from 'lucide-react';

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
        <div className="relative flex min-h-screen w-full flex-col overflow-x-hidden bg-card dark:bg-background">
            <div className="flex flex-1 items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
                <div className="w-full max-w-sm space-y-8 text-center">
                    {/* Logo & Header */}
                    <div>
                        <div className="flex items-center justify-center gap-2 text-foreground">
                            <div className="text-primary">
                                <ShoppingBag className="h-12 w-12" />
                            </div>
                            <h1 className="text-4xl font-bold tracking-tight">ShopNest</h1>
                        </div>
                        <h2 className="mt-6 text-2xl font-bold tracking-tight text-foreground">
                            Nhập Mã Xác Minh
                        </h2>
                        <p className="mt-2 text-sm text-muted-foreground">
                            Chúng tôi đã gửi mã xác minh đến email của bạn.
                        </p>
                    </div>

                    {/* Verification Form */}
                    <form onSubmit={handleSubmit} className="mt-8 space-y-6">
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
                            <button
                                type="submit"
                                className="group relative flex w-full justify-center rounded-lg border border-transparent bg-primary px-4 py-3 text-sm font-bold text-white transition-colors hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                            >
                                Xác minh
                            </button>
                        </div>
                    </form>

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
                </div>
            </div>
        </div>
    );
}
