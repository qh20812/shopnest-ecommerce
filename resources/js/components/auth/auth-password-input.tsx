import { useState } from 'react';
import { Eye, EyeOff } from 'lucide-react';

interface AuthPasswordInputProps {
    id: string;
    label: string;
    name: string;
    autoComplete?: string;
    required?: boolean;
    value: string;
    onChange: (e: React.ChangeEvent<HTMLInputElement>) => void;
    placeholder?: string;
    error?: string;
}

export default function AuthPasswordInput({
    id,
    // label,
    name,
    autoComplete = 'current-password',
    required = false,
    value,
    onChange,
    placeholder,
    error,
}: AuthPasswordInputProps) {
    const [showPassword, setShowPassword] = useState(false);

    const togglePasswordVisibility = () => {
        setShowPassword(!showPassword);
    };

    return (
        <div>
            {/* <label htmlFor={id} className="block text-sm font-medium text-foreground mb-1.5">
                {label}
            </label> */}
            <div className="relative">
                <input
                    id={id}
                    name={name}
                    type={showPassword ? 'text' : 'password'}
                    autoComplete={autoComplete}
                    required={required}
                    value={value}
                    onChange={onChange}
                    placeholder={placeholder}
                    className={`block w-full rounded-lg border ${
                        error ? 'border-red-500' : 'border-border'
                    } bg-background px-4 py-3 pr-12 text-foreground placeholder-muted-foreground focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-colors`}
                />
                <button
                    type="button"
                    onClick={togglePasswordVisibility}
                    className="absolute inset-y-0 right-0 flex items-center pr-3 text-muted-foreground hover:text-foreground transition-colors"
                    aria-label={showPassword ? 'Ẩn mật khẩu' : 'Hiện mật khẩu'}
                >
                    {showPassword ? (
                        <EyeOff className="h-5 w-5" />
                    ) : (
                        <Eye className="h-5 w-5" />
                    )}
                </button>
            </div>
            {error && (
                <p className="mt-1.5 text-sm text-red-600">{error}</p>
            )}
        </div>
    );
}
