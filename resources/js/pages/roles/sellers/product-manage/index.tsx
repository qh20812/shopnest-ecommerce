import React, { useState } from 'react';
import SellerLayout from '../../../../layouts/seller-layout';
import { Plus, Search, Edit, Trash2, Eye } from 'lucide-react';
import { router } from '@inertiajs/react';
import { index, create, edit, destroy, show } from '../../../../routes/seller/products';

interface Category {
  id: number;
  category_name: string;
}

interface Product {
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
          <div className="flex flex-col sm:flex-row items-center gap-4 mb-6">
            {/* Search Input */}
            <div className="flex-1 w-full sm:w-auto">
              <label className="relative flex items-center">
                <Search className="absolute left-3 w-5 h-5 text-text-secondary-light dark:text-text-secondary-dark" />
                <input
                  type="text"
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  onKeyDown={(e) => e.key === 'Enter' && handleFilterChange()}
                  className="form-input w-full h-10 pl-10 pr-4 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50"
                  placeholder="Tìm kiếm theo tên sản phẩm..."
                />
              </label>
            </div>

            {/* Filter Dropdowns */}
            <div className="flex items-center gap-4 w-full sm:w-auto">
              {/* Category Filter */}
              <div className="relative w-full sm:w-48">
                <select
                  value={selectedCategory}
                  onChange={(e) => {
                    setSelectedCategory(e.target.value);
                    router.get(index.url(), {
                      search: searchQuery,
                      category: e.target.value,
                      status: selectedStatus,
                    }, {
                      preserveState: true,
                      preserveScroll: true,
                    });
                  }}
                  className="form-select w-full h-10 px-3 pr-8 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50 appearance-none"
                >
                  <option value="all">Tất cả danh mục</option>
                  {categories.map((category) => (
                    <option key={category.id} value={category.id}>
                      {category.category_name}
                    </option>
                  ))}
                </select>
                <svg className="absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none w-5 h-5 text-text-secondary-light dark:text-text-secondary-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
                </svg>
              </div>

              {/* Status Filter */}
              <div className="relative w-full sm:w-48">
                <select
                  value={selectedStatus}
                  onChange={(e) => {
                    setSelectedStatus(e.target.value);
                    router.get(index.url(), {
                      search: searchQuery,
                      category: selectedCategory,
                      status: e.target.value,
                    }, {
                      preserveState: true,
                      preserveScroll: true,
                    });
                  }}
                  className="form-select w-full h-10 px-3 pr-8 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50 appearance-none"
                >
                  <option value="all">Tất cả trạng thái</option>
                  <option value="active">Đang hiển thị</option>
                  <option value="inactive">Đã ẩn</option>
                </select>
                <svg className="absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none w-5 h-5 text-text-secondary-light dark:text-text-secondary-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </div>
          </div>

          {/* Products Table */}
          <div className="overflow-x-auto">
            <table className="w-full text-sm text-left text-text-secondary-light dark:text-text-secondary-dark">
              <thead className="text-xs text-text-secondary-light dark:text-text-secondary-dark uppercase bg-background-light dark:bg-background-dark">
                <tr>
                  <th scope="col" className="px-6 py-3">
                    ID Sản phẩm
                  </th>
                  <th scope="col" className="px-6 py-3">
                    Hình ảnh
                  </th>
                  <th scope="col" className="px-6 py-3">
                    Tên sản phẩm
                  </th>
                  <th scope="col" className="px-6 py-3">
                    Giá
                  </th>
                  <th scope="col" className="px-6 py-3">
                    Tồn kho
                  </th>
                  <th scope="col" className="px-6 py-3">
                    Danh mục
                  </th>
                  <th scope="col" className="px-6 py-3">
                    Trạng thái
                  </th>
                  <th scope="col" className="px-6 py-3 text-center">
                    Hành động
                  </th>
                </tr>
              </thead>
              <tbody>
                {products.data && products.data.length > 0 ? (
                  products.data.map((product) => (
                    <tr
                      key={product.id}
                      className="bg-surface-light dark:bg-surface-dark border-b dark:border-border-dark hover:bg-background-light dark:hover:bg-background-dark transition-colors"
                    >
                      <td className="px-6 py-4 font-medium text-text-primary-light dark:text-text-primary-dark whitespace-nowrap">
                        {product.id}
                      </td>
                      <td className="px-6 py-4">
                        <img
                          src={product.image}
                          alt={product.name}
                          className="w-12 h-12 object-cover rounded-lg"
                          onError={(e) => {
                            const target = e.target as HTMLImageElement;
                            target.src = '/ShopNest3.png';
                          }}
                        />
                      </td>
                      <td className="px-6 py-4 font-semibold text-text-primary-light dark:text-text-primary-dark">
                        {product.name}
                      </td>
                      <td className="px-6 py-4">{product.price}</td>
                      <td className="px-6 py-4">{product.stock}</td>
                      <td className="px-6 py-4">{product.category}</td>
                      <td className="px-6 py-4">
                        <span
                          className={`px-2.5 py-0.5 rounded-full text-xs font-medium ${
                            product.status === 'active'
                              ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'
                              : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-400'
                          }`}
                        >
                          {product.status === 'active' ? 'Đang hiển thị' : 'Đã ẩn'}
                        </span>
                      </td>
                    <td className="px-6 py-4 text-center">
                      <div className="flex items-center justify-center gap-2">
                        <button 
                          onClick={() => handleViewProduct(product.actual_id)}
                          className="p-2 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors group"
                          title="Xem chi tiết"
                        >
                          <Eye className="w-4 h-4 text-text-secondary-light dark:text-text-secondary-dark group-hover:text-blue-500" />
                        </button>
                        <button 
                          onClick={() => handleEditProduct(product.actual_id)}
                          className="p-2 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition-colors group"
                          title="Chỉnh sửa"
                        >
                          <Edit className="w-4 h-4 text-text-secondary-light dark:text-text-secondary-dark group-hover:text-green-500" />
                        </button>
                        <button 
                          onClick={() => handleDeleteProduct(product.actual_id, product.name)}
                          className="p-2 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors group"
                          title="Xóa"
                        >
                          <Trash2 className="w-4 h-4 text-text-secondary-light dark:text-text-secondary-dark group-hover:text-red-500" />
                        </button>
                      </div>
                    </td>
                  </tr>
                ))
                ) : (
                  <tr>
                    <td colSpan={8} className="px-6 py-12 text-center text-text-secondary-light dark:text-text-secondary-dark">
                      Không có sản phẩm nào
                    </td>
                  </tr>
                )}
              </tbody>
            </table>
          </div>

          {/* Pagination */}
          <div className="flex items-center justify-between pt-4">
            <span className="text-sm text-text-secondary-light dark:text-text-secondary-dark">
              Hiển thị {products.data?.length > 0 ? ((products.current_page - 1) * products.per_page + 1) : 0} - {Math.min(products.current_page * products.per_page, products.total)} của {products.total} sản phẩm
            </span>
            <div className="inline-flex items-center -space-x-px">
              {products.links?.map((link, index) => (
                <button
                  key={index}
                  onClick={() => link.url && router.visit(link.url, { preserveState: true, preserveScroll: true })}
                  disabled={!link.url || link.active}
                  className={`flex items-center justify-center px-3 h-8 leading-tight text-sm border transition-colors ${
                    link.active 
                      ? 'text-primary bg-primary/10 border-primary'
                      : 'text-text-secondary-light bg-surface-light border-border-light hover:bg-background-light hover:text-text-primary-light dark:bg-surface-dark dark:border-border-dark dark:text-text-secondary-dark dark:hover:bg-background-dark dark:hover:text-text-primary-dark'
                  } ${index === 0 ? 'rounded-l-lg' : ''} ${index === products.links.length - 1 ? 'rounded-r-lg' : ''} ${!link.url ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'}`}
                  dangerouslySetInnerHTML={{ __html: link.label }}
                />
              ))}
            </div>
          </div>
        </div>
      </div>
    </SellerLayout>
  );
}
