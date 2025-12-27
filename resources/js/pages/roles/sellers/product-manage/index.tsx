import React, { useState } from 'react';
import SellerLayout from '../../../../layouts/seller-layout';
import { Plus, Edit, Trash2, Eye } from 'lucide-react';
import { router } from '@inertiajs/react';
import { index, create, edit, destroy, show } from '../../../../routes/seller/products';
import DataTable, { Column, Action } from '../../../../components/datatable/DataTable';
import Filters, { FilterOption } from '../../../../components/datatable/Filters';

interface Category {
  id: number;
  category_name: string;
}

interface Product extends Record<string, unknown> {
  id: string;
  actual_id: number;
  image: string;
  name: string;
  price: string;
  stock: number;
  category: string;
  status: 'active' | 'inactive';
}

interface PaginatedProducts {
  data: Product[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  links: Array<{
    url: string | null;
    label: string;
    active: boolean;
  }>;
}

interface ProductManageProps {
  user?: {
    full_name: string;
    email: string;
    avatar_url?: string;
  };
  products: PaginatedProducts;
  categories: Category[];
  filters: {
    search: string;
    category: string;
    status: string;
  };
}

export default function Index({ user, products, categories, filters }: ProductManageProps) {
  const [searchQuery, setSearchQuery] = useState(filters.search || '');
  const [selectedCategory, setSelectedCategory] = useState(filters.category || 'all');
  const [selectedStatus, setSelectedStatus] = useState(filters.status || 'all');

  const handleFilterChange = () => {
    router.get(index.url(), {
      search: searchQuery,
      category: selectedCategory,
      status: selectedStatus,
    }, {
      preserveState: true,
      preserveScroll: true,
    });
  };

  const handleCreateProduct = () => {
    router.visit(create.url());
  };

  const handleEditProduct = (productId: number) => {
    router.visit(edit.url({ product: productId }));
  };

  const handleDeleteProduct = (productId: number, productName: string) => {
    if (confirm(`Bạn có chắc muốn xóa sản phẩm "${productName}"?`)) {
      router.delete(destroy.url({ product: productId }), {
        preserveScroll: true,
      });
    }
  };

  const handleViewProduct = (productId: number) => {
    router.visit(show.url({ product: productId }));
  };

  // Define columns for DataTable
  const columns: Column<Product>[] = [
    {
      header: 'ID Sản phẩm',
      accessor: 'id',
      className: 'px-6 py-4 font-medium text-text-primary-light dark:text-text-primary-dark whitespace-nowrap',
    },
    {
      header: 'Hình ảnh',
      accessor: (row) => (
        <img
          src={row.image}
          alt={row.name}
          className="w-12 h-12 object-cover rounded-lg"
          onError={(e) => {
            const target = e.target as HTMLImageElement;
            target.src = '/ShopNest3.png';
          }}
        />
      ),
      className: 'px-6 py-4',
    },
    {
      header: 'Tên sản phẩm',
      accessor: 'name',
      className: 'px-6 py-4 font-semibold text-text-primary-light dark:text-text-primary-dark',
    },
    {
      header: 'Giá',
      accessor: 'price',
      className: 'px-6 py-4',
    },
    {
      header: 'Tồn kho',
      accessor: 'stock',
      className: 'px-6 py-4',
    },
    {
      header: 'Danh mục',
      accessor: 'category',
      className: 'px-6 py-4',
    },
    {
      header: 'Trạng thái',
      accessor: (row) => (
        <span
          className={`px-2.5 py-0.5 rounded-full text-xs font-medium ${
            row.status === 'active'
              ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'
              : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-400'
          }`}
        >
          {row.status === 'active' ? 'Đang hiển thị' : 'Đã ẩn'}
        </span>
      ),
      className: 'px-6 py-4',
    },
  ];

  // Define actions for DataTable
  const actions: Action<Product>[] = [
    {
      icon: Eye,
      onClick: (row) => handleViewProduct(row.actual_id),
      title: 'Xem chi tiết',
      variant: 'primary',
    },
    {
      icon: Edit,
      onClick: (row) => handleEditProduct(row.actual_id),
      title: 'Chỉnh sửa',
      variant: 'success',
    },
    {
      icon: Trash2,
      onClick: (row) => handleDeleteProduct(row.actual_id, row.name),
      title: 'Xóa',
      variant: 'danger',
    },
  ];

  // Define filter options
  const categoryOptions: FilterOption[] = [
    { value: 'all', label: 'Tất cả danh mục' },
    ...categories.map((category) => ({
      value: category.id.toString(),
      label: category.category_name,
    })),
  ];

  const statusOptions: FilterOption[] = [
    { value: 'all', label: 'Tất cả trạng thái' },
    { value: 'active', label: 'Đang hiển thị' },
    { value: 'inactive', label: 'Đã ẩn' },
  ];

  return (
    <SellerLayout activePage="products" user={user}>
      <div className="container mx-auto px-6 py-8">
        {/* Header */}
        <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6">
          <h2 className="text-3xl font-bold text-text-primary-light dark:text-text-primary-dark">
            Quản lý sản phẩm
          </h2>
          <button 
            onClick={handleCreateProduct}
            className="mt-4 sm:mt-0 flex items-center justify-center gap-2 h-10 px-5 bg-primary text-white rounded-lg font-semibold text-sm hover:bg-primary/90 transition-colors"
          >
            <Plus className="w-5 h-5" />
            <span>Thêm sản phẩm mới</span>
          </button>
        </div>

        {/* Main Content Card */}
        <div className="bg-surface-light dark:bg-surface-dark p-6 rounded-xl border border-border-light dark:border-border-dark">
          {/* Filters */}
          <Filters
            searchValue={searchQuery}
            onSearchChange={setSearchQuery}
            onSearch={handleFilterChange}
            searchPlaceholder="Tìm kiếm theo tên sản phẩm..."
            filters={[
              {
                value: selectedCategory,
                onChange: (value) => {
                  setSelectedCategory(value);
                  router.get(
                    index.url(),
                    {
                      search: searchQuery,
                      category: value,
                      status: selectedStatus,
                    },
                    {
                      preserveState: true,
                      preserveScroll: true,
                    }
                  );
                },
                options: categoryOptions,
              },
              {
                value: selectedStatus,
                onChange: (value) => {
                  setSelectedStatus(value);
                  router.get(
                    index.url(),
                    {
                      search: searchQuery,
                      category: selectedCategory,
                      status: value,
                    },
                    {
                      preserveState: true,
                      preserveScroll: true,
                    }
                  );
                },
                options: statusOptions,
              },
            ]}
          />

          {/* Products DataTable */}
          <DataTable<Product>
            columns={columns}
            data={products.data || []}
            pagination={products}
            actions={actions}
            emptyMessage="Không có sản phẩm nào"
            onPageChange={(url) =>
              router.visit(url, { preserveState: true, preserveScroll: true })
            }
          />
        </div>
      </div>
    </SellerLayout>
  );
}
