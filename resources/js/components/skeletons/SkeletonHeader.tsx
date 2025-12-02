import React from 'react';

export const SkeletonHeader: React.FC = () => {
    return (
        <header className="w-full bg-background-light dark:bg-background-dark/80 backdrop-blur-sm sticky top-0 z-50 border-b border-surface-light dark:border-surface-dark/50">
            <div className="container mx-auto px-4">
                <div className="flex items-center justify-between whitespace-nowrap h-20">
                    <div className="flex items-center gap-8">
                        <div className="flex items-center gap-2">
                            <div className="w-10 h-10 bg-surface-light dark:bg-surface-dark rounded-md animate-pulse" />
                            <div className="w-24 h-6 bg-surface-light dark:bg-surface-dark rounded-md animate-pulse" />
                        </div>
                        <nav className="hidden md:flex items-center gap-9">
                            <div className="w-20 h-4 bg-surface-light dark:bg-surface-dark rounded-md animate-pulse" />
                            <div className="w-20 h-4 bg-surface-light dark:bg-surface-dark rounded-md animate-pulse" />
                            <div className="w-20 h-4 bg-surface-light dark:bg-surface-dark rounded-md animate-pulse" />
                            <div className="w-20 h-4 bg-surface-light dark:bg-surface-dark rounded-md animate-pulse" />
                        </nav>
                    </div>
                    <div className="flex flex-1 justify-end items-center gap-4">
                        <div className="hidden lg:flex w-64 h-10 bg-surface-light dark:bg-surface-dark rounded-lg animate-pulse" />
                        <div className="flex gap-2">
                            <div className="w-10 h-10 bg-surface-light dark:bg-surface-dark rounded-lg animate-pulse" />
                            <div className="w-10 h-10 bg-surface-light dark:bg-surface-dark rounded-lg animate-pulse" />
                        </div>
                        <div className="size-10 bg-surface-light dark:bg-surface-dark rounded-full animate-pulse" />
                    </div>
                </div>
            </div>
        </header>
    );
};

export default SkeletonHeader;
