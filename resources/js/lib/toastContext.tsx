import React, { createContext, useContext, useState, useCallback, useEffect } from 'react';

type ToastType = 'success' | 'error' | 'info';

export type Toast = {
    id: number;
    type: ToastType;
    title?: string;
    message: string;
    ttl?: number; // milliseconds
};

type ToastContextValue = {
    showSuccess: (message: string, title?: string) => void;
    showError: (message: string, title?: string) => void;
    showInfo: (message: string, title?: string) => void;
};

const ToastContext = createContext<ToastContextValue | undefined>(undefined);

let nextId = 1;

export const ToastProvider: React.FC<React.PropsWithChildren<Record<string, unknown>>> = ({ children }) => {
    const [toasts, setToasts] = useState<Toast[]>([]);

    const push = useCallback((toast: Omit<Toast, 'id'>) => {
        const id = nextId++;
        setToasts((prev) => [...prev, { id, ...toast }]);
        return id;
    }, []);

    const remove = useCallback((id: number) => {
        setToasts((prev) => prev.filter((t) => t.id !== id));
    }, []);

    useEffect(() => {
        // Add auto dismiss
        const timers = toasts.map((t) => {
            const ttl = typeof t.ttl === 'number' ? t.ttl : 4000;
            const timer = window.setTimeout(() => remove(t.id), ttl);
            return timer;
        });
        return () => timers.forEach((t) => clearTimeout(t));
    }, [toasts, remove]);

    const showSuccess = useCallback((message: string, title?: string) => {
        push({ type: 'success', title, message, ttl: 4000 });
    }, [push]);

    const showError = useCallback((message: string, title?: string) => {
        push({ type: 'error', title, message, ttl: 6000 });
    }, [push]);

    const showInfo = useCallback((message: string, title?: string) => {
        push({ type: 'info', title, message, ttl: 4500 });
    }, [push]);

    return (
        <ToastContext.Provider value={{ showSuccess, showError, showInfo }}>
            {children}
            <div aria-live="polite" aria-atomic="true" className="fixed top-6 right-6 z-[9999] flex w-full max-w-sm flex-col gap-4">
                {toasts.map((t) => (
                    <div key={t.id} className={`pointer-events-auto flex w-full max-w-sm overflow-hidden rounded-lg shadow-lg backdrop-blur-sm text-white ${t.type === 'success' ? 'bg-success/90' : t.type === 'error' ? 'bg-danger/90' : 'bg-info/90'}`}>
                        <div className={`flex w-12 items-center justify-center ${t.type === 'success' ? 'bg-success' : t.type === 'error' ? 'bg-danger' : 'bg-info'}`}>
                            {/* icon placeholders - keep consistent with design */}
                            <span className="text-xl">{t.type === 'success' ? '✓' : t.type === 'error' ? '!' : 'i'}</span>
                        </div>
                        <div className="flex-1 p-4 text-sm">
                            {t.title ? <h4 className="font-bold">{t.title}</h4> : null}
                            <p>{t.message}</p>
                        </div>
                        <button onClick={() => remove(t.id)} className="shrink-0 px-3 opacity-80 hover:opacity-100 text-white">
                            ×
                        </button>
                    </div>
                ))}
            </div>
        </ToastContext.Provider>
    );
};

export const useToast = (): ToastContextValue => {
    const ctx = useContext(ToastContext);
    if (!ctx) throw new Error('useToast must be used within a ToastProvider');
    return ctx;
};

export default ToastProvider;
