import { ReactNode } from 'react';
import TopNav from '../components/top-nav';
import Footer from '../components/footer';
import CustomerSidebar from '../components/customer-sidebar';
import { ToastProvider } from '../lib/toastContext';

interface CustomerLayoutProps {
    children: ReactNode;
    activePage?: 'profile' | 'orders' | 'notifications';
}

function CustomerLayoutContent({ children, activePage = 'profile' }: CustomerLayoutProps) {
    return (
        <div className="relative flex min-h-screen w-full flex-col overflow-x-hidden bg-background text-foreground">
            <TopNav />

            <main className="flex-1 bg-card/40">
                <div className="container mx-auto px-4 py-8 md:py-12">
                    <div className="flex flex-col gap-8 md:flex-row lg:gap-12">
                        <CustomerSidebar activePage={activePage} />
                        <div className="flex-1">{children}</div>
                    </div>
                </div>
            </main>

            <Footer />
        </div>
    );
}

export default function CustomerLayout({ children, activePage = 'profile' }: CustomerLayoutProps) {
    return (
        <ToastProvider>
            <CustomerLayoutContent activePage={activePage}>{children}</CustomerLayoutContent>
        </ToastProvider>
    );
}
