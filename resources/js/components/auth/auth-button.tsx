import { ButtonHTMLAttributes, ReactNode } from 'react';

interface AuthButtonProps extends ButtonHTMLAttributes<HTMLButtonElement> {
    children: ReactNode;
    variant?: 'primary' | 'secondary';
    fullWidth?: boolean;
}

export default function AuthButton({
    children,
    variant = 'primary',
    fullWidth = true,
    className = '',
    type = 'submit',
    ...props
}: AuthButtonProps) {
    const baseClasses = 'group relative flex justify-center rounded-lg border px-4 py-3 text-sm font-bold transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2';
    const widthClass = fullWidth ? 'w-full' : '';
    
    const variantClasses = {
        primary: 'border-transparent bg-primary text-white hover:bg-primary/90 focus:ring-primary',
        secondary: 'border-border bg-background text-foreground hover:bg-muted focus:ring-primary',
    };

    return (
        <button
            type={type}
            className={`${baseClasses} ${widthClass} ${variantClasses[variant]} ${className}`}
            {...props}
        >
            {children}
        </button>
    );
}
