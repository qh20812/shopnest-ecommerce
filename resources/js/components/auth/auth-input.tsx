import { InputHTMLAttributes, forwardRef } from 'react';

interface AuthInputProps extends InputHTMLAttributes<HTMLInputElement> {
    label: string;
}

const AuthInput = forwardRef<HTMLInputElement, AuthInputProps>(
    ({ label, id, className = '', ...props }, ref) => {
        return (
            <div>
                <label htmlFor={id} className="sr-only">
                    {label}
                </label>
                <input
                    ref={ref}
                    id={id}
                    className={`relative block w-full appearance-none rounded-lg border border-border bg-background px-3 py-3 text-foreground placeholder-muted-foreground focus:z-10 focus:border-primary focus:outline-none focus:ring-primary sm:text-sm ${className}`}
                    {...props}
                />
            </div>
        );
    }
);

AuthInput.displayName = 'AuthInput';

export default AuthInput;
