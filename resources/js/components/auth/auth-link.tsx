import { Link } from '@inertiajs/react';
import { ReactNode } from 'react';

interface AuthLinkProps {
    href: string;
    children: ReactNode;
    className?: string;
}

export default function AuthLink({ href, children, className = '' }: AuthLinkProps) {
    return (
        <Link
            href={href}
            className={`font-medium text-primary hover:text-primary/90 ${className}`}
        >
            {children}
        </Link>
    );
}
