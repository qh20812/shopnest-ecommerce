import React, { useState } from 'react'
import SettingsLayout from '../../../../layouts/settings-layout'
import { CheckCircle2, Circle } from 'lucide-react'

interface PasswordProps {
    user?: {
        name: string
        email: string
    }
}

export default function Password({ user }: PasswordProps) {
    const [currentPassword, setCurrentPassword] = useState('')
    const [newPassword, setNewPassword] = useState('')
    const [confirmPassword, setConfirmPassword] = useState('')

    // Password validation checks
    const hasMinLength = newPassword.length >= 8
    const hasUpperCase = /[A-Z]/.test(newPassword)
    const hasLowerCase = /[a-z]/.test(newPassword)
    const hasNumber = /[0-9]/.test(newPassword)
    const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(newPassword)

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault()
        console.log('Update password:', { currentPassword, newPassword, confirmPassword })
    }

    return (
        <SettingsLayout activeTab="password" user={user}>
            <div className="space-y-12">
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div className="lg:col-span-1">
                        <h3 className="text-xl font-semibold text-text-primary-light dark:text-text-primary-dark">
                            Thay đổi mật khẩu
                        </h3>
                        <p className="text-text-secondary-light dark:text-text-secondary-dark mt-1">
                            Để bảo mật tài khoản, vui lòng không chia sẻ mật khẩu của bạn cho người khác.
                        </p>
                    </div>
                    <div className="lg:col-span-2">
                        <div className="bg-surface-light dark:bg-surface-dark p-6 rounded-xl border border-border-light dark:border-border-dark">
                            <form onSubmit={handleSubmit}>
                                <div className="space-y-6">
                                    <div>
                                        <label
                                            className="block text-sm font-medium text-text-secondary-light dark:text-text-secondary-dark mb-1.5"
                                            htmlFor="current-password"
                                        >
                                            Mật khẩu hiện tại
                                        </label>
                                        <input
                                            type="password"
                                            id="current-password"
                                            value={currentPassword}
                                            onChange={(e) => setCurrentPassword(e.target.value)}
                                            placeholder="Nhập mật khẩu hiện tại"
                                            className="w-full h-10 px-3 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50 text-sm text-text-primary-light dark:text-text-primary-dark"
                                        />
                                    </div>
                                    <div>
                                        <label
                                            className="block text-sm font-medium text-text-secondary-light dark:text-text-secondary-dark mb-1.5"
                                            htmlFor="new-password"
                                        >
                                            Mật khẩu mới
                                        </label>
                                        <input
                                            type="password"
                                            id="new-password"
                                            value={newPassword}
                                            onChange={(e) => setNewPassword(e.target.value)}
                                            placeholder="Nhập mật khẩu mới"
                                            className="w-full h-10 px-3 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50 text-sm text-text-primary-light dark:text-text-primary-dark"
                                        />
                                    </div>
                                    <div>
                                        <label
                                            className="block text-sm font-medium text-text-secondary-light dark:text-text-secondary-dark mb-1.5"
                                            htmlFor="confirm-password"
                                        >
                                            Xác nhận mật khẩu mới
                                        </label>
                                        <input
                                            type="password"
                                            id="confirm-password"
                                            value={confirmPassword}
                                            onChange={(e) => setConfirmPassword(e.target.value)}
                                            placeholder="Xác nhận mật khẩu mới"
                                            className="w-full h-10 px-3 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50 text-sm text-text-primary-light dark:text-text-primary-dark"
                                        />
                                    </div>
                                    <div className="mt-4 space-y-3">
                                        <p className="text-sm font-medium text-text-secondary-light dark:text-text-secondary-dark">
                                            Yêu cầu mật khẩu:
                                        </p>
                                        <ul className="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 text-sm text-text-secondary-light dark:text-text-secondary-dark">
                                            <li className="flex items-center gap-2">
                                                {hasMinLength ? (
                                                    <CheckCircle2 size={20} className="text-green-500" />
                                                ) : (
                                                    <Circle size={20} className="text-gray-400 dark:text-gray-500" />
                                                )}
                                                <span>Tối thiểu 8 ký tự</span>
                                            </li>
                                            <li className="flex items-center gap-2">
                                                {hasUpperCase ? (
                                                    <CheckCircle2 size={20} className="text-green-500" />
                                                ) : (
                                                    <Circle size={20} className="text-gray-400 dark:text-gray-500" />
                                                )}
                                                <span>Ít nhất một chữ hoa (A-Z)</span>
                                            </li>
                                            <li className="flex items-center gap-2">
                                                {hasLowerCase ? (
                                                    <CheckCircle2 size={20} className="text-green-500" />
                                                ) : (
                                                    <Circle size={20} className="text-gray-400 dark:text-gray-500" />
                                                )}
                                                <span>Ít nhất một chữ thường (a-z)</span>
                                            </li>
                                            <li className="flex items-center gap-2">
                                                {hasNumber ? (
                                                    <CheckCircle2 size={20} className="text-green-500" />
                                                ) : (
                                                    <Circle size={20} className="text-gray-400 dark:text-gray-500" />
                                                )}
                                                <span>Ít nhất một số (0-9)</span>
                                            </li>
                                            <li className="flex items-center gap-2">
                                                {hasSpecialChar ? (
                                                    <CheckCircle2 size={20} className="text-green-500" />
                                                ) : (
                                                    <Circle size={20} className="text-gray-400 dark:text-gray-500" />
                                                )}
                                                <span>Ít nhất một ký tự đặc biệt (!@#$%)</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div className="mt-8 pt-6 border-t border-border-light dark:border-border-dark flex justify-end">
                                    <button
                                        type="submit"
                                        className="inline-flex items-center justify-center gap-2 h-10 px-5 rounded-lg bg-primary text-white font-semibold hover:bg-primary/90 transition-colors"
                                    >
                                        Cập nhật mật khẩu
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </SettingsLayout>
    )
}
