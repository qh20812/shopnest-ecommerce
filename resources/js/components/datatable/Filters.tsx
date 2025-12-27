import React from 'react';
import { Search } from 'lucide-react';

export interface FilterOption {
  value: string;
  label: string;
}

interface FiltersProps {
  searchValue: string;
  onSearchChange: (value: string) => void;
  onSearch?: () => void;
  searchPlaceholder?: string;
  filters?: {
    value: string;
    onChange: (value: string) => void;
    options: FilterOption[];
    label?: string;
  }[];
}

export default function Filters({
  searchValue,
  onSearchChange,
  onSearch,
  searchPlaceholder = 'Tìm kiếm...',
  filters = [],
}: FiltersProps) {
  return (
    <div className="flex flex-col sm:flex-row items-center gap-4 mb-6">
      {/* Search Input */}
      <div className="flex-1 w-full sm:w-auto">
        <label className="relative flex items-center">
          <Search className="absolute left-3 w-5 h-5 text-text-secondary-light dark:text-text-secondary-dark" />
          <input
            type="text"
            value={searchValue}
            onChange={(e) => onSearchChange(e.target.value)}
            onKeyDown={(e) => e.key === 'Enter' && onSearch && onSearch()}
            className="form-input w-full h-10 pl-10 pr-4 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50"
            placeholder={searchPlaceholder}
          />
        </label>
      </div>

      {/* Filter Dropdowns */}
      {filters.length > 0 && (
        <div className="flex items-center gap-4 w-full sm:w-auto">
          {filters.map((filter, index) => (
            <div key={index} className="relative w-full sm:w-48">
              <select
                value={filter.value}
                onChange={(e) => filter.onChange(e.target.value)}
                className="form-select w-full h-10 px-3 pr-8 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50 appearance-none"
              >
                {filter.options.map((option) => (
                  <option key={option.value} value={option.value}>
                    {option.label}
                  </option>
                ))}
              </select>
              <svg
                className="absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none w-5 h-5 text-text-secondary-light dark:text-text-secondary-dark"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  strokeWidth={2}
                  d="M19 9l-7 7-7-7"
                />
              </svg>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
