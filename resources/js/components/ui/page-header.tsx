import React from 'react';
import { LucideIcon } from 'lucide-react';
import { Button } from './button';

interface ActionButton {
    label: string;
    icon: LucideIcon;
    onClick: () => void;
    variant?: 'default' | 'secondary' | 'outline' | 'ghost' | 'destructive';
}

interface PageHeaderProps {
    title: string;
    actions?: ActionButton[];
}

export function PageHeader({ title, actions = [] }: PageHeaderProps) {
    return (
        <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
            <h2 className="text-3xl md:text-4xl font-bold text-foreground tracking-tight">
                {title}
            </h2>
            {actions.length > 0 && (
                <div className="flex items-center gap-2">
                    {actions.map((action, index) => (
                        <Button
                            key={index}
                            onClick={action.onClick}
                            variant={action.variant || 'secondary'}
                            className="flex min-w-[84px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 text-sm font-bold leading-normal tracking-[0.015em]"
                        >
                            <action.icon className="mr-2 h-5 w-5" />
                            <span className="truncate">{action.label}</span>
                        </Button>
                    ))}
                </div>
            )}
        </div>
    );
}