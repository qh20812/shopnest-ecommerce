import React from 'react';

interface SkeletonCardProps {
    className?: string;
    variant?: 'default' | 'product' | 'flash-sale' | 'category';
}

export const SkeletonCard: React.FC<SkeletonCardProps> = ({ className = '', variant = 'default' }) => {
    if (variant === 'product') {
        return (
            <div className={`animate-pulse ${className}`}>
                <div className="aspect-square bg-surface-light dark:bg-surface-dark rounded-lg" />
                <div className="mt-4 space-y-3">
                    <div className="h-4 bg-surface-light dark:bg-surface-dark rounded" />
                    <div className="h-4 w-1/2 bg-surface-light dark:bg-surface-dark rounded" />
                </div>
            </div>
        );
    }

    if (variant === 'flash-sale') {
        return (
            <div className="min-w-[260px] w-[260px] animate-pulse">
                <div className="w-full aspect-square bg-gray-200 dark:bg-gray-700 rounded-t-lg" />
                <div className="p-4 space-y-3">
                    <div className="h-4 bg-gray-200 dark:bg-gray-700 rounded" />
                    <div className="h-4 w-1/2 bg-gray-200 dark:bg-gray-700 rounded" />
                </div>
            </div>
        );
    }

    if (variant === 'category') {
        return (
            <div className={`h-32 bg-surface-light dark:bg-surface-dark rounded-lg animate-pulse ${className}`} />
        );
    }

    return (
        <div className={`animate-pulse ${className}`}>
            <div className="h-full w-full bg-surface-light dark:bg-surface-dark rounded" />
        </div>
    );
};

export default SkeletonCard;
