import React, { useState } from 'react'
import SettingsLayout from '../../../../layouts/settings-layout'
import { Upload, Trash2 } from 'lucide-react'

interface ProfileProps {
    user?: {
        name: string
        email: string
        avatar?: string
    }
}

export default function Profile({ user }: ProfileProps) {
    const [name, setName] = useState(user?.name || 'Admin User')
    const [email, setEmail] = useState(user?.email || 'admin@shopnest.com')
    const [avatar, setAvatar] = useState(
        user?.avatar ||
            'https://lh3.googleusercontent.com/aida-public/AB6AXuBtD5o_3TsXWSdeCTCkNbMMXnrlIK7-fPHBZwbxSxq3k3hlzpnUs0OWbJLSZM5yL61gQpf7NAAyJFInpJRenu1LDculgknnbATzIXGlmd8lW90k5VU_IWHt-irI9Y91JoIGiRbx5NMHwC8CkvrpOcWYa_o2c1E5rh2bL8e2CYzAYUkcg9oaIkVQrmlewjsKjq4xWsz9GXEfpkOKwnfDBqjeDm0aJ54bbfSiU82ob7z1EYgzuyfEZwfq4rC6saIF6pdj1Sp8RqE4ptM'
    )

    const handleUploadAvatar = () => {
        console.log('Upload avatar clicked')
    }

    const handleDeleteAvatar = () => {
        console.log('Delete avatar clicked')
    }

    const handleSave = (e: React.FormEvent) => {
        e.preventDefault()
        console.log('Save profile:', { name, email, avatar })
    }

    return (
        <SettingsLayout activeTab="profile" user={user}>
            <div className="space-y-12">
                <section id="profile">
                    <header className="mb-6">
                        <h3 className="text-xl font-bold text-text-primary-light dark:text-text-primary-dark">
                            Thông tin cá nhân
                        </h3>
                        <p className="text-sm text-text-secondary-light dark:text-text-secondary-dark mt-1">
                            Cập nhật thông tin hồ sơ và địa chỉ email của bạn.
                        </p>
                    </header>
                    <div className="bg-surface-light dark:bg-surface-dark p-6 rounded-xl border border-border-light dark:border-border-dark">
                        <form onSubmit={handleSave}>
                            <div className="space-y-6">
                                <div className="grid grid-cols-1 md:grid-cols-3 gap-2 items-center">
                                    <label
                                        className="font-medium text-sm md:col-span-1"
                                        htmlFor="admin-name"
                                    >
                                        Họ và tên
                                    </label>
                                    <div className="md:col-span-2">
                                        <input
                                            type="text"
                                            id="admin-name"
                                            value={name}
                                            onChange={(e) => setName(e.target.value)}
                                            className="w-full h-10 px-4 rounded-lg border border-border-light dark:border-border-dark bg-surface-light dark:bg-surface-dark text-text-primary-light dark:text-text-primary-dark focus:outline-none focus:ring-2 focus:ring-primary"
                                        />
                                    </div>
                                </div>

                                <hr className="border-border-light dark:border-border-dark -mx-6" />

                                <div className="grid grid-cols-1 md:grid-cols-3 gap-2 items-center">
                                    <label
                                        className="font-medium text-sm md:col-span-1"
                                        htmlFor="admin-email"
                                    >
                                        Địa chỉ email
                                    </label>
                                    <div className="md:col-span-2">
                                        <input
                                            type="email"
                                            id="admin-email"
                                            value={email}
                                            onChange={(e) => setEmail(e.target.value)}
                                            className="w-full h-10 px-4 rounded-lg border border-border-light dark:border-border-dark bg-surface-light dark:bg-surface-dark text-text-primary-light dark:text-text-primary-dark focus:outline-none focus:ring-2 focus:ring-primary"
                                        />
                                    </div>
                                </div>

                                <hr className="border-border-light dark:border-border-dark -mx-6" />

                                <div className="grid grid-cols-1 md:grid-cols-3 gap-2 pt-2 items-start">
                                    <div className="md:col-span-1">
                                        <label className="font-medium text-sm" htmlFor="admin-avatar">
                                            Ảnh đại diện
                                        </label>
                                        <p className="text-xs text-text-secondary-light dark:text-text-secondary-dark mt-1">
                                            Hiển thị công khai trên trang web.
                                        </p>
                                    </div>
                                    <div className="md:col-span-2 flex items-center gap-4">
                                        <img
                                            alt="Admin Avatar"
                                            className="w-16 h-16 object-cover rounded-full"
                                            src={avatar}
                                        />
                                        <div className="flex gap-2">
                                            <button
                                                type="button"
                                                onClick={handleUploadAvatar}
                                                className="inline-flex items-center justify-center gap-2 h-9 px-4 rounded-lg border border-border-light dark:border-border-dark bg-surface-light dark:bg-surface-dark text-sm font-medium hover:bg-black/5 dark:hover:bg-white/5 transition-colors"
                                            >
                                                <Upload size={16} />
                                                Tải lên
                                            </button>
                                            <button
                                                type="button"
                                                onClick={handleDeleteAvatar}
                                                className="inline-flex items-center justify-center gap-2 h-9 px-4 rounded-lg border border-border-light dark:border-border-dark bg-surface-light dark:bg-surface-dark text-sm font-medium hover:bg-black/5 dark:hover:bg-white/5 transition-colors text-primary"
                                            >
                                                <Trash2 size={16} />
                                                Xóa
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <footer className="mt-6 flex justify-end">
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
