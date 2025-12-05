import { useState } from 'react';
import { Camera } from 'lucide-react';
import CustomerLayout from '../layouts/customer-layout';
import Input from '../components/ui/input';
import { useToast } from '../lib/toastContext';

function CustomerProfileContent() {
    const { showSuccess } = useToast();
    const [formData, setFormData] = useState({
        fullName: 'Nguyễn Văn A',
        email: 'nguyenvana@example.com',
        phone: '0123456789',
        dateOfBirth: '1990-01-01',
        gender: 'male',
        address: '123 Đường ABC, Quận 1',
        city: 'Hồ Chí Minh',
        district: 'Quận 1',
        ward: 'Phường Bến Nghé',
    });

    const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
        setFormData({
            ...formData,
            [e.target.name]: e.target.value,
        });
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        showSuccess('Cập nhật thông tin thành công');
    };

    return (
        <div className="rounded-xl bg-background p-6 shadow-md md:p-8">
            <h2 className="mb-2 text-2xl font-bold text-foreground md:text-3xl">
                Thông tin cá nhân
            </h2>
                <p className="mb-8 text-muted-foreground">
                    Quản lý thông tin tài khoản của bạn để có trải nghiệm mua sắm tốt nhất.
                </p>

                {/* Avatar Section */}
                <div className="mb-8 flex flex-col items-start gap-6 md:flex-row md:items-center">
                    <div className="relative">
                        <div
                            className="h-24 w-24 rounded-full bg-cover bg-center"
                            style={{
                                backgroundImage:
                                    'url("https://lh3.googleusercontent.com/aida-public/AB6AXuBtD5o_3TsXWSdeCTCkNbMMXnrlIK7-fPHBZwbxSxq3k3hlzpnUs0OWbJLSZM5yL61gQpf7NAAyJFInpJRenu1LDculgknnbATzIXGlmd8lW90k5VU_IWHt-irI9Y91JoIGiRbx5NMHwC8CkvrpOcWYa_o2c1E5rh2bL8e2CYzAYUkcg9oaIkVQrmlewjsKjq4xWsz9GXEfpkOKwnfDBqjeDm0aJ54bbfSiU82ob7z1EYgzuyfEZwfq4rC6saIF6pdj1Sp8RqE4ptM")',
                            }}
                        />
                        <button className="absolute bottom-0 right-0 flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-primary text-white shadow-lg transition-transform hover:scale-110">
                            <Camera className="h-4 w-4" />
                        </button>
                    </div>
                    <div>
                        <h3 className="mb-1 text-lg font-bold text-foreground">{formData.fullName}</h3>
                        <p className="text-sm text-muted-foreground">{formData.email}</p>
                    </div>
                </div>

                {/* Profile Form */}
                <form onSubmit={handleSubmit} className="space-y-6">
                    <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                        {/* Full Name */}
                        <div>
                            <label
                                htmlFor="fullName"
                                className="mb-2 block text-sm font-medium text-foreground"
                            >
                                Họ và tên
                            </label>
                            <Input
                                type="text"
                                id="fullName"
                                name="fullName"
                                value={formData.fullName}
                                onChange={handleChange}
                                className="h-11"
                            />
                        </div>

                        {/* Email */}
                        <div>
                            <label htmlFor="email" className="mb-2 block text-sm font-medium text-foreground">
                                Email
                            </label>
                            <Input
                                type="email"
                                id="email"
                                name="email"
                                value={formData.email}
                                onChange={handleChange}
                                className="h-11"
                            />
                        </div>

                        {/* Phone */}
                        <div>
                            <label htmlFor="phone" className="mb-2 block text-sm font-medium text-foreground">
                                Số điện thoại
                            </label>
                            <Input
                                type="tel"
                                id="phone"
                                name="phone"
                                value={formData.phone}
                                onChange={handleChange}
                                className="h-11"
                            />
                        </div>

                        {/* Date of Birth */}
                        <div>
                            <label
                                htmlFor="dateOfBirth"
                                className="mb-2 block text-sm font-medium text-foreground"
                            >
                                Ngày sinh
                            </label>
                            <Input
                                type="date"
                                id="dateOfBirth"
                                name="dateOfBirth"
                                value={formData.dateOfBirth}
                                onChange={handleChange}
                                className="h-11"
                            />
                        </div>

                        {/* Gender */}
                        <div>
                            <label htmlFor="gender" className="mb-2 block text-sm font-medium text-foreground">
                                Giới tính
                            </label>
                            <select
                                id="gender"
                                name="gender"
                                value={formData.gender}
                                onChange={handleChange}
                                className="h-11 w-full rounded-lg border-border bg-background px-4 text-foreground focus:border-primary focus:ring-primary/50"
                            >
                                <option value="male">Nam</option>
                                <option value="female">Nữ</option>
                                <option value="other">Khác</option>
                            </select>
                        </div>

                        {/* Address */}
                        <div className="md:col-span-2">
                            <label htmlFor="address" className="mb-2 block text-sm font-medium text-foreground">
                                Địa chỉ
                            </label>
                            <Input
                                type="text"
                                id="address"
                                name="address"
                                value={formData.address}
                                onChange={handleChange}
                                className="h-11"
                            />
                        </div>

                        {/* City */}
                        <div>
                            <label htmlFor="city" className="mb-2 block text-sm font-medium text-foreground">
                                Tỉnh/Thành phố
                            </label>
                            <select
                                id="city"
                                name="city"
                                value={formData.city}
                                onChange={handleChange}
                                className="h-11 w-full rounded-lg border-border bg-background px-4 text-foreground focus:border-primary focus:ring-primary/50"
                            >
                                <option value="Hồ Chí Minh">Hồ Chí Minh</option>
                                <option value="Hà Nội">Hà Nội</option>
                                <option value="Đà Nẵng">Đà Nẵng</option>
                            </select>
                        </div>

                        {/* District */}
                        <div>
                            <label htmlFor="district" className="mb-2 block text-sm font-medium text-foreground">
                                Quận/Huyện
                            </label>
                            <select
                                id="district"
                                name="district"
                                value={formData.district}
                                onChange={handleChange}
                                className="h-11 w-full rounded-lg border-border bg-background px-4 text-foreground focus:border-primary focus:ring-primary/50"
                            >
                                <option value="Quận 1">Quận 1</option>
                                <option value="Quận 2">Quận 2</option>
                                <option value="Quận 3">Quận 3</option>
                            </select>
                        </div>

                        {/* Ward */}
                        <div>
                            <label htmlFor="ward" className="mb-2 block text-sm font-medium text-foreground">
                                Phường/Xã
                            </label>
                            <select
                                id="ward"
                                name="ward"
                                value={formData.ward}
                                onChange={handleChange}
                                className="h-11 w-full rounded-lg border-border bg-background px-4 text-foreground focus:border-primary focus:ring-primary/50"
                            >
                                <option value="Phường Bến Nghé">Phường Bến Nghé</option>
                                <option value="Phường Bến Thành">Phường Bến Thành</option>
                                <option value="Phường Cô Giang">Phường Cô Giang</option>
                            </select>
                        </div>
                    </div>

                    {/* Submit Button */}
                    <div className="flex justify-end">
                        <button
                            type="submit"
                            className="flex h-11 cursor-pointer items-center justify-center rounded-lg bg-primary px-8 text-sm font-bold leading-normal tracking-[0.015em] text-white transition-colors hover:bg-primary/90"
                        >
                            Lưu thay đổi
                        </button>
                    </div>
                </form>
            </div>
    );
}

export default function CustomerProfile() {
    return (
        <CustomerLayout>
            <CustomerProfileContent />
        </CustomerLayout>
    );
}
