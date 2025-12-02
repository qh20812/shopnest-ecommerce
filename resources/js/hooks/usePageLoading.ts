import { useState, useEffect } from 'react';

interface UsePageLoadingOptions {
    delay?: number;
    minLoadingTime?: number;
}

export const usePageLoading = (options: UsePageLoadingOptions = {}) => {
    const { delay = 0, minLoadingTime = 800 } = options;
    const [isLoading, setIsLoading] = useState(true);

    useEffect(() => {
        const startTime = Date.now();
        
        const timer = setTimeout(() => {
            const elapsedTime = Date.now() - startTime;
            const remainingTime = Math.max(0, minLoadingTime - elapsedTime);
            
            setTimeout(() => {
                setIsLoading(false);
            }, remainingTime);
        }, delay);

        return () => clearTimeout(timer);
    }, [delay, minLoadingTime]);

    return isLoading;
};

export default usePageLoading;
