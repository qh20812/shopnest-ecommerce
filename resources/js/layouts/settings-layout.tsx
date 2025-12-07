import React from 'react'
import { router } from '@inertiajs/react'
import SellerLayout from './seller-layout'

interface SettingsLayoutProps {
    children: React.ReactNode
    activeTab: 'profile' | 'password' | 'notifications' | 'security'
    user?: {
        name: string
        email: string
    }
}

export default function SettingsLayout({ children, activeTab, user }: SettingsLayoutProps) {
    const tabs = [
        { id: 'profile', label: 'Hồ sơ', path: '/seller/settings/profile' },
        { id: 'password', label: 'Mật khẩu', path: '/seller/settings/password' },
        { id: 'security', label: 'Bảo mật', path: '/seller/settings/security' },
    ]

    const handleTabClick = (path: string) => {
        router.visit(path)
    }

    return (
        <SellerLayout activePage="settings" user={user}>
            <div className="container mx-auto px-6 py-8">
                <div className="max-w-4xl mx-auto">
                    <div className="flex border-b border-border-light dark:border-border-dark mb-8">
                        {tabs.map((tab) => (
                            <button
                                key={tab.id}
                                onClick={() => handleTabClick(tab.path)}
                                className={`px-4 py-3 text-sm font-semibold transition-colors ${
                                    activeTab === tab.id
                                        ? 'border-b-2 border-primary text-primary'
                                        : 'text-text-secondary-light dark:text-text-secondary-dark hover:text-text-primary-light dark:hover:text-text-primary-dark hover:border-b-2 hover:border-text-secondary-light dark:hover:border-text-secondary-dark'
                                }`}
                            >
                                {tab.label}
                            </button>
                        ))}
                    </div>
                    {children}
                </div>
            </div>
        </SellerLayout>
    )
}