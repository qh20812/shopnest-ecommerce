import React, { useState } from 'react'
import SettingsLayout from '../../../../layouts/settings-layout'
import { Smartphone, Mail, CheckCircle2 } from 'lucide-react'

interface SecurityProps {
    user?: {
        name: string
        email: string
    }
}

export default function Security({ user }: SecurityProps) {
    const [twoFactorEnabled, setTwoFactorEnabled] = useState(false)

    const handleToggle2FA = () => {
        setTwoFactorEnabled(!twoFactorEnabled)
        console.log('2FA toggled:', !twoFactorEnabled)
    }

    const handleClearSessions = () => {
        console.log('Clear all sessions')
    }

    const handleSave = (e: React.FormEvent) => {
        e.preventDefault()
        console.log('Save security settings')
    }

    return (
        <SettingsLayout activeTab="security" user={user}>
            <div className="space-y-12">
                <section id="security">
                    <header className="mb-6">
                        <h3 className="text-xl font-bold text-text-primary-light dark:text-text-primary-dark">
                            Bảo mật
                        </h3>
                        <p className="text-sm text-text-secondary-light dark:text-text-secondary-dark mt-1">
                            Quản lý cài đặt bảo mật, xác thực hai yếu tố, và các phương thức khác để giữ an toàn cho tài khoản của bạn.
                        </p>
                    </header>
                    <div className="bg-surface-light dark:bg-surface-dark rounded-xl border border-border-light dark:border-border-dark">
                        <form onSubmit={handleSave}>
                            {/* Two-Factor Authentication */}
                            <div className="p-6">
                                <div className="flex items-center justify-between">
                                    <div>
                                        <h4 className="font-semibold text-text-primary-light dark:text-text-primary-dark">
                                            Xác thực hai yếu tố (2FA)
                                        </h4>
                                        <p className="text-sm text-text-secondary-light dark:text-text-secondary-dark mt-1">
                                            Thêm một lớp bảo mật bổ sung cho tài khoản của bạn.
                                        </p>
                                    </div>
                                    <label className="switch relative inline-block w-[38px] h-[22px]">
                                        <input
                                            type="checkbox"
                                            checked={twoFactorEnabled}
                                            onChange={handleToggle2FA}
                                            className="opacity-0 w-0 h-0"
                                        />
                                        <span
                                            className={`slider absolute cursor-pointer top-0 left-0 right-0 bottom-0 rounded-full transition-all duration-400 ${
                                                twoFactorEnabled
                                                    ? 'bg-primary'
                                                    : 'bg-gray-300 dark:bg-[#3A3A3A]'
                                            } before:absolute before:content-[''] before:h-4 before:w-4 before:left-[3px] before:bottom-[3px] before:rounded-full before:transition-all before:duration-400 ${
                                                twoFactorEnabled
                                                    ? 'before:translate-x-4 before:bg-white'
                                                    : 'before:bg-white dark:before:bg-[#B0B0B0]'
                                            }`}
                                        ></span>
                                    </label>
                                </div>
                            </div>

                            <hr className="border-border-light dark:border-border-dark" />

                            {/* Authentication Methods */}
                            <div className="p-6">
                                <h4 className="font-semibold text-text-primary-light dark:text-text-primary-dark mb-4">
                                    Phương thức xác thực
                                </h4>
                                <div className="space-y-4">
                                    <div className="flex items-center gap-4">
                                        <div className="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                                            <Smartphone size={20} className="text-primary" />
                                        </div>
                                        <div className="flex-1">
                                            <div className="flex items-center gap-2">
                                                <p className="font-medium text-text-primary-light dark:text-text-primary-dark">
                                                    Ứng dụng xác thực
                                                </p>
                                                <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">
                                                    <CheckCircle2 size={12} />
                                                    Đã kích hoạt
                                                </span>
                                            </div>
                                            <p className="text-sm text-text-secondary-light dark:text-text-secondary-dark">
                                                Sử dụng ứng dụng xác thực để nhận mã xác thực.
                                            </p>
                                        </div>
                                    </div>
                                    <div className="flex items-center gap-4">
                                        <div className="w-10 h-10 rounded-full bg-secondary/10 flex items-center justify-center">
                                            <Mail size={20} className="text-secondary" />
                                        </div>
                                        <div className="flex-1">
                                            <div className="flex items-center gap-2">
                                                <p className="font-medium text-text-primary-light dark:text-text-primary-dark">
                                                    Email
                                                </p>
                                                <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">
                                                    <CheckCircle2 size={12} />
                                                    Đã kích hoạt
                                                </span>
                                            </div>
                                            <p className="text-sm text-text-secondary-light dark:text-text-secondary-dark">
                                                Nhận mã xác thực qua email.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr className="border-border-light dark:border-border-dark" />

                            {/* Active Sessions */}
                            <div className="p-6">
                                <div className="flex items-start md:items-center justify-between flex-col md:flex-row gap-4">
                                    <div>
                                        <h4 className="font-semibold text-text-primary-light dark:text-text-primary-dark">
                                            Phiên đăng nhập hoạt động
                                        </h4>
                                        <p className="text-sm text-text-secondary-light dark:text-text-secondary-dark mt-1">
                                            Đăng xuất từ tất cả các thiết bị khác.
                                        </p>
                                    </div>
                                    <button
                                        type="button"
                                        onClick={handleClearSessions}
                                        className="inline-flex items-center justify-center h-9 px-4 rounded-lg border border-primary text-primary text-sm font-medium hover:bg-primary/10 transition-colors"
                                    >
                                        Xóa tất cả phiên
                                    </button>
                                </div>
                            </div>

                            <footer className="bg-black/5 dark:bg-white/5 px-6 py-4 rounded-b-xl flex justify-end">
                                <button
                                    type="submit"
                                    className="inline-flex items-center justify-center gap-2 h-9 px-4 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary/90 transition-colors"
                                >
                                    Lưu thay đổi
                                </button>
                            </footer>
                        </form>
                    </div>
                </section>
            </div>
        </SettingsLayout>
    )
}
