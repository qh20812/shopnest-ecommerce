import React, { useState, useRef, useEffect } from 'react';
import { Search, Bell, Sun, Moon } from 'lucide-react';

interface DashboardNavbarProps {
  user?: {
    full_name: string;
    email: string;
    avatar_url?: string;
  };
}

export default function DashboardNavbar({ user }: DashboardNavbarProps) {
  const [isDropdownOpen, setIsDropdownOpen] = useState(false);
  const getInitialDarkMode = () => {
    if (typeof window === 'undefined') return false;
    const stored = localStorage.getItem('darkMode');
    if (stored !== null) {
      return stored === 'true';
    }
    return window.matchMedia('(prefers-color-scheme: dark)').matches;
  };
  const [isDarkMode, setIsDarkMode] = useState(getInitialDarkMode);
  const dropdownRef = useRef<HTMLDivElement>(null);

  // Apply dark mode class on mount
  useEffect(() => {
    if (isDarkMode) {
      document.documentElement.classList.add('dark');
    } else {
      document.documentElement.classList.remove('dark');
    }
  }, [isDarkMode]);

  // Toggle dark mode
  const toggleDarkMode = () => {
    const newMode = !isDarkMode;
    setIsDarkMode(newMode);
    localStorage.setItem('darkMode', newMode.toString());
    if (newMode) {
      document.documentElement.classList.add('dark');
    } else {
      document.documentElement.classList.remove('dark');
    }
  };

  // Close dropdown when clicking outside
  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      if (dropdownRef.current && !dropdownRef.current.contains(event.target as Node)) {
        setIsDropdownOpen(false);
      }
    };

    if (isDropdownOpen) {
      document.addEventListener('mousedown', handleClickOutside);
    }

    return () => {
      document.removeEventListener('mousedown', handleClickOutside);
    };
  }, [isDropdownOpen]);

  return (
    <header className="w-full bg-surface-light dark:bg-surface-dark/80 backdrop-blur-sm sticky top-0 z-40 border-b border-border-light dark:border-border-dark">
      <div className="px-6">
        <div className="flex items-center justify-between h-20">
          {/* Search Bar */}
          <label className="flex flex-col min-w-40 !h-10 max-w-96 flex-1">
            <div className="flex w-full flex-1 items-stretch rounded-lg h-full">
              <div className="text-text-secondary-light dark:text-text-secondary-dark flex bg-background-light dark:bg-background-dark items-center justify-center pl-3 rounded-l-lg border-r-0">
                <Search className="w-5 h-5" />
              </div>
              <input
                className="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-text-primary-light dark:text-text-primary-dark focus:outline-0 focus:ring-2 focus:ring-primary/50 border-none bg-background-light dark:bg-background-dark h-full placeholder:text-text-secondary-light dark:placeholder:text-text-secondary-dark px-4 rounded-l-none border-l-0 pl-2 text-sm font-normal leading-normal"
                placeholder="Tìm kiếm..."
                defaultValue=""
              />
            </div>
          </label>

          {/* Right Actions */}
          <div className="flex items-center gap-4 ml-6">
            {/* Notifications */}
            <button className="flex cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 w-10 bg-transparent text-text-secondary-light dark:text-text-secondary-dark hover:bg-black/5 dark:hover:bg-white/5 hover:text-text-primary-light dark:hover:text-text-primary-dark transition-colors relative">
              <Bell className="w-5 h-5" />
              <span className="absolute top-2 right-2.5 w-2 h-2 bg-primary rounded-full"></span>
            </button>

            {/* Dark Mode Toggle */}
            <button
              onClick={toggleDarkMode}
              className="flex cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 w-10 bg-transparent text-text-secondary-light dark:text-text-secondary-dark hover:bg-black/5 dark:hover:bg-white/5 hover:text-text-primary-light dark:hover:text-text-primary-dark transition-colors"
            >
              {isDarkMode ? <Sun className="w-5 h-5" /> : <Moon className="w-5 h-5" />}
            </button>

            {/* User Dropdown */}
            <div className="relative" ref={dropdownRef}>
              <button
                onClick={() => setIsDropdownOpen(!isDropdownOpen)}
                className="bg-center bg-no-repeat aspect-square bg-cover rounded-full size-10 cursor-pointer"
                style={{
                  backgroundImage: user?.avatar_url
                    ? `url("${user.avatar_url}")`
                    : `url("https://ui-avatars.com/api/?name=${encodeURIComponent(user?.full_name || 'User')}&background=FF6B6B&color=fff")`,
                }}
              />

              {/* Dropdown Menu */}
              {isDropdownOpen && (
                <div className="absolute right-0 top-full mt-2 w-64 bg-surface-light dark:bg-surface-dark rounded-xl shadow-lg border border-border-light dark:border-border-dark p-2 z-10">
                  <div className="flex items-center gap-3 p-2 mb-2">
                    <div
                      className="bg-center bg-no-repeat aspect-square bg-cover rounded-full size-12"
                      style={{
                        backgroundImage: user?.avatar_url
                          ? `url("${user.avatar_url}")`
                          : `url("https://ui-avatars.com/api/?name=${encodeURIComponent(user?.full_name || 'User')}&background=FF6B6B&color=fff")`,
                      }}
                    />
                    <div>
                      <p className="font-bold text-text-primary-light dark:text-text-primary-dark">
                        {user?.full_name || 'Người dùng'}
                      </p>
                      <p className="text-sm text-text-secondary-light dark:text-text-secondary-dark">
                        {user?.email || 'user@shopnest.com'}
                      </p>
                    </div>
                  </div>
                  <hr className="my-2 border-border-light dark:border-border-dark" />
                  <a
                    className="flex items-center gap-3 px-3 py-2.5 text-sm text-primary hover:bg-primary/10 rounded-lg transition-colors"
                    href="/logout"
                  >
                    <span>Đăng xuất</span>
                  </a>
                </div>
              )}
            </div>
          </div>
        </div>
      </div>
    </header>
  );
}
