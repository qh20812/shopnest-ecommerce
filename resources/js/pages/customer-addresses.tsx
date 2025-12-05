import { Plus, Edit, Trash2 } from 'lucide-react';
import CustomerLayout from '../layouts/customer-layout';

interface Address {
    id: string;
    name: string;
    phone: string;
    address: string;
    isShippingDefault: boolean;
    isBillingDefault: boolean;
}

function CustomerAddressesContent() {
    // Sample addresses data
    const addresses: Address[] = [
        {
            id: '1',
            name: 'Nguyễn Văn A',
            phone: '0912345678',
            address: '123 Đường ABC, Phường 1, Quận 2, Thành phố Hồ Chí Minh',
            isShippingDefault: true,
            isBillingDefault: true,
        },
        {
            id: '2',
            name: 'Trần Thị B',
            phone: '0987654321',
            address: '456 Đường XYZ, Phường 3, Quận 4, Thành phố Hà Nội',
            isShippingDefault: false,
            isBillingDefault: false,
        },
    ];

    return (
        <div className="rounded-xl bg-background p-6 shadow-md md:p-8">
            {/* Header */}
            <div className="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 className="mb-2 text-2xl font-bold text-foreground md:text-3xl">
                        Địa chỉ
                    </h2>
                    <p className="text-muted-foreground">
                        Quản lý địa chỉ giao hàng và thanh toán của bạn.
                    </p>
                </div>
                <button
                    className="mt-4 flex h-11 min-w-[84px] max-w-fit cursor-pointer items-center justify-center overflow-hidden rounded-lg bg-primary px-6 text-base font-bold leading-normal tracking-[0.015em] text-white transition-colors hover:bg-primary/90 sm:mt-0"
                    type="button"
                >
                    <Plus className="mr-2 h-5 w-5" />
                    <span className="truncate">Thêm địa chỉ mới</span>
                </button>
            </div>

            {/* Addresses List */}
            <div className="space-y-6">
                {addresses.map((address) => (
                    <div
                        key={address.id}
                        className="rounded-lg border border-border p-5"
                    >
                        <div className="mb-4 flex flex-col justify-between sm:flex-row sm:items-start">
                            <div>
                                <div className="mb-2 flex items-center gap-4">
                                    <h3 className="text-lg font-bold text-foreground">
                                        {address.name}
                                    </h3>
                                    <div className="flex gap-2">
                                        {address.isShippingDefault && (
                                            <span className="rounded-full bg-secondary/20 px-2.5 py-1 text-xs font-medium text-secondary">
                                                Giao hàng
                                            </span>
                                        )}
                                        {address.isBillingDefault && (
                                            <span className="rounded-full bg-primary/10 px-2.5 py-1 text-xs font-medium text-primary">
                                                Thanh toán
                                            </span>
                                        )}
                                    </div>
                                </div>
                                <p className="text-sm text-muted-foreground">
                                    {address.phone}
                                </p>
                                <p className="text-sm text-muted-foreground">
                                    {address.address}
                                </p>
                            </div>
                            <div className="mt-4 flex items-center gap-2 sm:mt-0">
                                <button className="flex h-9 w-9 cursor-pointer items-center justify-center overflow-hidden rounded-lg bg-card text-foreground transition-colors hover:bg-primary/10 hover:text-primary">
                                    <Edit className="h-5 w-5" />
                                </button>
                                <button className="flex h-9 w-9 cursor-pointer items-center justify-center overflow-hidden rounded-lg bg-card text-foreground transition-colors hover:bg-primary/10 hover:text-primary">
                                    <Trash2 className="h-5 w-5" />
                                </button>
                            </div>
                        </div>
                        <div className="flex flex-col gap-4 border-t border-border pt-4 md:flex-row md:items-center">
                            <button
                                className={`flex h-9 min-w-[84px] max-w-fit cursor-pointer items-center justify-center overflow-hidden rounded-lg px-4 text-sm font-bold leading-normal tracking-[0.015em] transition-colors ${
                                    address.isShippingDefault
                                        ? 'bg-primary text-white opacity-50'
                                        : 'border border-border bg-background text-foreground hover:bg-card'
                                }`}
                                disabled={address.isShippingDefault}
                                type="button"
                            >
                                <span className="truncate">
                                    {address.isShippingDefault
                                        ? 'Mặc định giao hàng'
                                        : 'Đặt làm mặc định giao hàng'}
                                </span>
                            </button>
                            {(address.isBillingDefault ||
                                address.isShippingDefault) && (
                                <button
                                    className={`flex h-9 min-w-[84px] max-w-fit cursor-pointer items-center justify-center overflow-hidden rounded-lg px-4 text-sm font-bold leading-normal tracking-[0.015em] transition-colors ${
                                        address.isBillingDefault
                                            ? 'bg-primary text-white opacity-50'
                                            : 'border border-border bg-background text-foreground hover:bg-card'
                                    }`}
                                    disabled={address.isBillingDefault}
                                    type="button"
                                >
                                    <span className="truncate">
                                        {address.isBillingDefault
                                            ? 'Mặc định thanh toán'
                                            : 'Đặt làm mặc định thanh toán'}
                                    </span>
                                </button>
                            )}
                        </div>
                    </div>
                ))}
            </div>
        </div>
    );
}

export default function CustomerAddresses() {
    return (
        <CustomerLayout activePage="addresses">
            <CustomerAddressesContent />
        </CustomerLayout>
    );
}