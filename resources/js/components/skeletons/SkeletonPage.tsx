import React from 'react';
import { SkeletonHeader } from './SkeletonHeader';
import { SkeletonHero } from './SkeletonHero';
import { SkeletonSection } from './SkeletonSection';
import { SkeletonFlashSale } from './SkeletonFlashSale';

interface SkeletonPageProps {
    variant?: 'welcome' | 'shop' | 'default';
}

export const SkeletonPage: React.FC<SkeletonPageProps> = ({ variant = 'default' }) => {
    if (variant === 'welcome') {
        return (
            <div className="relative flex h-auto min-h-screen w-full flex-col">
                <SkeletonHeader />
                <main className="flex-1">
                    <div className="container mx-auto px-4 py-8 md:py-12">
                        <SkeletonHero />
                        <SkeletonSection title cards={6} variant="category" layout="grid" />
                        <SkeletonFlashSale />
                        <SkeletonSection title cards={4} variant="product" layout="grid" />
                        <SkeletonSection title cards={4} variant="product" layout="grid" />
                    </div>
                </main>
            </div>
        );
    }

    if (variant === 'shop') {
        return (
            <div className="relative flex h-auto min-h-screen w-full flex-col">
                <SkeletonHeader />
                <main className="flex-1">
                    <div className="container mx-auto px-4 py-8 md:py-12">
                        <SkeletonSection title cards={8} variant="product" layout="grid" />
                    </div>
                </main>
            </div>
        );
    }

    return (
        <div className="relative flex h-auto min-h-screen w-full flex-col">
            <SkeletonHeader />
            <main className="flex-1">
                <div className="container mx-auto px-4 py-8 md:py-12">
                    <SkeletonSection title cards={4} variant="product" layout="grid" />
                </div>
            </main>
        </div>
    );
};

export default SkeletonPage;
