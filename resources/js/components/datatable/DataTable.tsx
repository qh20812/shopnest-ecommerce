import React from 'react';

export interface Column<T> {
  header: string;
  accessor: keyof T | ((row: T) => React.ReactNode);
  className?: string;
  headerClassName?: string;
}

export interface Action<T> {
  icon: React.ComponentType<{ className?: string }>;
  onClick: (row: T) => void;
  title: string;
  variant?: 'primary' | 'success' | 'danger' | 'info';
}

export interface PaginationData<T = unknown> {
  data: T[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  from?: number;
  to?: number;
  links?: Array<{
    url: string | null;
    label: string;
    active: boolean;
  }>;
}

interface DataTableProps<T> {
  columns: Column<T>[];
  data: T[];
  pagination?: PaginationData<T>;
  actions?: Action<T>[];
  emptyMessage?: string;
  onPageChange?: (url: string) => void;
}

export default function DataTable<T extends Record<string, unknown>>({
  columns,
  data,
  pagination,
  actions,
  emptyMessage = 'Không có dữ liệu',
  onPageChange,
}: DataTableProps<T>) {
  const getActionVariantClass = (variant?: string) => {
    switch (variant) {
      case 'primary':
        return 'hover:bg-blue-50 dark:hover:bg-blue-900/20 group-hover:text-blue-500';
      case 'success':
        return 'hover:bg-green-50 dark:hover:bg-green-900/20 group-hover:text-green-500';
      case 'danger':
        return 'hover:bg-red-50 dark:hover:bg-red-900/20 group-hover:text-red-500';
      case 'info':
        return 'hover:bg-purple-50 dark:hover:bg-purple-900/20 group-hover:text-purple-500';
      default:
        return 'hover:bg-black/5 dark:hover:bg-white/5';
    }
  };

  const handlePageChange = (url: string | null) => {
    if (url && onPageChange) {
      onPageChange(url);
    }
  };

  const renderCellContent = (column: Column<T>, row: T): React.ReactNode => {
    if (typeof column.accessor === 'function') {
      return column.accessor(row);
    }

    const value = row[column.accessor as keyof T];

    if (value === null || value === undefined) {
      return null;
    }

    if (React.isValidElement(value)) {
      return value;
    }

    if (typeof value === 'object') {
      try {
        return JSON.stringify(value);
      } catch {
        return String(value);
      }
    }

    return String(value) as unknown as React.ReactNode;
  };

  const links = pagination?.links ?? [];

  return (
    <>
      {/* Table */}
      <div className="overflow-x-auto">
        <table className="w-full text-sm text-left text-text-secondary-light dark:text-text-secondary-dark">
          <thead className="text-xs text-text-secondary-light dark:text-text-secondary-dark uppercase bg-background-light dark:bg-background-dark">
            <tr>
              {columns.map((column, index) => (
                <th
                  key={index}
                  scope="col"
                  className={column.headerClassName || 'px-6 py-3'}
                >
                  {column.header}
                </th>
              ))}
              {actions && actions.length > 0 && (
                <th scope="col" className="px-6 py-3 text-center">
                  Hành động
                </th>
              )}
            </tr>
          </thead>
          <tbody>
            {data && data.length > 0 ? (
              data.map((row, rowIndex) => (
                <tr
                  key={rowIndex}
                  className="bg-surface-light dark:bg-surface-dark border-b dark:border-border-dark hover:bg-background-light dark:hover:bg-background-dark transition-colors"
                >
                  {columns.map((column, colIndex) => (
                    <td key={colIndex} className={column.className || 'px-6 py-4'}>
                      {renderCellContent(column, row)}
                    </td>
                  ))}
                  {actions && actions.length > 0 && (
                    <td className="px-6 py-4 text-center">
                      <div className="flex items-center justify-center gap-2">
                        {actions.map((action, actionIndex) => (
                          <button
                            key={actionIndex}
                            onClick={() => action.onClick(row)}
                            className={`p-2 rounded-lg transition-colors group ${getActionVariantClass(action.variant)}`}
                            title={action.title}
                          >
                            <action.icon className="w-4 h-4 text-text-secondary-light dark:text-text-secondary-dark" />
                          </button>
                        ))}
                      </div>
                    </td>
                  )}
                </tr>
              ))
            ) : (
              <tr>
                <td
                  colSpan={columns.length + (actions && actions.length > 0 ? 1 : 0)}
                  className="px-6 py-12 text-center text-text-secondary-light dark:text-text-secondary-dark"
                >
                  {emptyMessage}
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>

      {/* Pagination */}
      {pagination && pagination.links && (
        <div className="flex items-center justify-between pt-4">
          <span className="text-sm text-text-secondary-light dark:text-text-secondary-dark">
            Hiển thị{' '}
            {pagination.data?.length > 0
              ? (pagination.current_page - 1) * pagination.per_page + 1
              : 0}{' '}
            - {Math.min(pagination.current_page * pagination.per_page, pagination.total)} của{' '}
            {pagination.total} mục
          </span>
          <div className="inline-flex items-center -space-x-px">
            {links.map((link, index) => (
              <button
                key={index}
                onClick={() => handlePageChange(link.url)}
                disabled={!link.url || link.active}
                className={`flex items-center justify-center px-3 h-8 leading-tight text-sm border transition-colors ${
                  link.active
                    ? 'text-primary bg-primary/10 border-primary'
                    : 'text-text-secondary-light bg-surface-light border-border-light hover:bg-background-light hover:text-text-primary-light dark:bg-surface-dark dark:border-border-dark dark:text-text-secondary-dark dark:hover:bg-background-dark dark:hover:text-text-primary-dark'
                } ${index === 0 ? 'rounded-l-lg' : ''} ${
                  index === links.length - 1 ? 'rounded-r-lg' : ''
                } ${!link.url ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'}`}
                dangerouslySetInnerHTML={{ __html: link.label }}
              />
            ))}
          </div>
        </div>
      )}
    </>
  );
}
