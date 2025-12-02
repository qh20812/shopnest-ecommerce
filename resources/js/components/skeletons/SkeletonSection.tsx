import React from 'react';
import { SkeletonCard } from './SkeletonCard';

interface SkeletonSectionProps {
    title?: boolean;
    cards?: number;
    variant?: 'default' | 'product' | 'flash-sale' | 'category';
    layout?: 'grid' | 'flex';
    className?: string;
}

export const SkeletonSection: React.FC<SkeletonSectionProps> = ({
    title = true,
    cards = 4,
    variant = 'product',
    layout = 'grid',
    className = '',
}) => {
    const containerClass = layout === 'grid'
        ? variant === 'category' 
            ? 'grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4'
            : 'grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6'
        : variant === 'flash-sale'
            ? 'flex gap-4'
            : 'flex gap-6';

    return (
        <section className={`mb-12 md:mb-16 ${className}`}>
            {title && (
                <div className="h-8 w-1/3 bg-surface-light dark:bg-surface-dark rounded-md animate-pulse mb-5" />
            )}
            <div className={containerClass}>
                {Array.from({ length: cards }).map((_, i) => (
                    <SkeletonCard key={i} variant={variant} />
                ))}
            </div>
        </section>
    );
};

export default SkeletonSection;
