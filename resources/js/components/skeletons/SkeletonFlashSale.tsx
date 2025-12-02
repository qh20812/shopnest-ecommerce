import React from 'react';
import { SkeletonCard } from './SkeletonCard';

export const SkeletonFlashSale: React.FC = () => {
    return (
        <section className="bg-surface-light dark:bg-surface-dark rounded-xl p-6 md:p-8 mb-12 md:mb-16 animate-pulse">
            <div className="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-6">
                <div className="h-8 w-48 bg-gray-200 dark:bg-gray-700 rounded-md" />
                <div className="h-10 w-64 bg-gray-200 dark:bg-gray-700 rounded-md" />
            </div>
            <div className="flex gap-4 overflow-hidden">
                {Array.from({ length: 4 }).map((_, i) => (
                    <SkeletonCard key={i} variant="flash-sale" />
                ))}
            </div>
        </section>
    );
};

export default SkeletonFlashSale;
