import { FormHTMLAttributes, ReactNode } from 'react';

interface AuthFormProps extends FormHTMLAttributes<HTMLFormElement> {
    children: ReactNode;
}

export default function AuthForm({ children, className = '', ...props }: AuthFormProps) {
    return (
        <form className={`mt-8 space-y-6 ${className}`} {...props}>
            {children}
        </form>
    );
}
