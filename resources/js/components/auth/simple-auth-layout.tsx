import { ReactNode } from 'react';
import { Link } from '@inertiajs/react';

interface SimpleAuthLayoutProps {
    children: ReactNode;
    title: string;
    description: string;
    maxWidth?: 'sm' | 'md';
    centerContent?: boolean;
}

export default function SimpleAuthLayout({ 
    children, 
    title, 
    description,
    maxWidth = 'md',
    centerContent = false
}: SimpleAuthLayoutProps) {
    const maxWidthClass = maxWidth === 'sm' ? 'max-w-sm' : 'max-w-md';
    const contentAlign = centerContent ? 'text-center' : '';

    return (
        <div className="relative flex min-h-screen w-full flex-col overflow-x-hidden bg-card dark:bg-background">
            <div className="flex flex-1 items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
                <div className={`w-full ${maxWidthClass} space-y-8 ${contentAlign}`}>
                    {/* Logo & Header */}
                    <div>
                        <Link href="/" aria-label="ShopNest Home" className="flex items-center justify-center gap-2 text-foreground">
                            <img src="/ShopNest2.png" alt="ShopNest Logo" className='h-14 w-auto' />
                            <h1 className="text-4xl font-bold tracking-tight">ShopNest</h1>
                        </Link>
                        <h2 className="mt-6 text-center text-2xl font-bold tracking-tight text-foreground">
                            {title}
                        </h2>
                        <p className="mt-2 text-center text-sm text-muted-foreground">
                            {description}
                        </p>
                    </div>

                    {/* Content */}
                    {children}
                </div>
            </div>
        </div>
    );
}
