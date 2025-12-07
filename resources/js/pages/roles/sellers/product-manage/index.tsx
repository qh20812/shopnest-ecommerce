import React, { useState } from 'react';
import SellerLayout from '../../../../layouts/seller-layout';
import { Plus, Search, Edit, Trash2, Eye } from 'lucide-react';

interface ProductManageProps {
  user?: {
    full_name: string;
    email: string;
    avatar_url?: string;
  };
}

interface Product {
  id: string;
  image: string;
  name: string;
  price: string;
  stock: number;
  category: string;
  status: 'active' | 'inactive';
}

export default function Index({ user }: ProductManageProps) {
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedCategory, setSelectedCategory] = useState('all');
  const [selectedStatus, setSelectedStatus] = useState('all');

  // Sample product data
  const products: Product[] = [
    {
      id: '#89742',
      image: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=100&h=100&fit=crop',
      name: 'Tai nghe không dây',
      price: '1,250,000đ',
      stock: 120,
      category: 'Thiết bị điện tử',
      status: 'active',
    },
    {
      id: '#89743',
      image: 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=100&h=100&fit=crop',
      name: 'Đồng hồ thông minh',
      price: '2,490,000đ',
      stock: 85,
      category: 'Thiết bị điện tử',
      status: 'active',
    },
    {
      id: '#89744',
      image: 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=100&h=100&fit=crop',
      name: 'Giày thể thao nam',
      price: '890,000đ',
      stock: 45,
      category: 'Thời trang',
      status: 'inactive',
    },
    {
      id: '#89745',
      image: 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=100&h=100&fit=crop',
      name: 'Balo laptop',
      price: '450,000đ',
      stock: 200,
      category: 'Phụ kiện',
      status: 'active',
    },
    {
      id: '#89746',
      image: 'https://images.unsplash.com/photo-1585386959984-a4155224a1ad?w=100&h=100&fit=crop',
      name: 'Bình giữ nhiệt',
      price: '320,000đ',
      stock: 150,
      category: 'Đồ gia dụng',
      status: 'active',
    },
  ];

  return (
    <SellerLayout activePage="products" user={user}>
      <div className="container mx-auto px-6 py-8">
        {/* Header */}
        <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6">
          <h2 className="text-3xl font-bold text-text-primary-light dark:text-text-primary-dark">
            Quản lý sản phẩm
          </h2>
          <button className="mt-4 sm:mt-0 flex items-center justify-center gap-2 h-10 px-5 bg-primary text-white rounded-lg font-semibold text-sm hover:bg-primary/90 transition-colors">
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
                  onChange={(e) => setSelectedCategory(e.target.value)}
                  className="form-select w-full h-10 px-3 pr-8 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50 appearance-none"
                >
                  <option value="all">Tất cả danh mục</option>
                  <option value="electronics">Thiết bị điện tử</option>
                  <option value="fashion">Thời trang</option>
                  <option value="home">Đồ gia dụng</option>
                </select>
                <svg className="absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none w-5 h-5 text-text-secondary-light dark:text-text-secondary-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
                </svg>
              </div>

              {/* Status Filter */}
              <div className="relative w-full sm:w-48">
                <select
                  value={selectedStatus}
                  onChange={(e) => setSelectedStatus(e.target.value)}
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
                {products.map((product) => (
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
                        <button className="p-2 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors group">
                          <Eye className="w-4 h-4 text-text-secondary-light dark:text-text-secondary-dark group-hover:text-blue-500" />
                        </button>
                        <button className="p-2 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition-colors group">
                          <Edit className="w-4 h-4 text-text-secondary-light dark:text-text-secondary-dark group-hover:text-green-500" />
                        </button>
                        <button className="p-2 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors group">
                          <Trash2 className="w-4 h-4 text-text-secondary-light dark:text-text-secondary-dark group-hover:text-red-500" />
                        </button>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>

          {/* Pagination */}
          <div className="flex items-center justify-between pt-4">
            <span className="text-sm text-text-secondary-light dark:text-text-secondary-dark">
              Hiển thị 1-5 của 50 sản phẩm
            </span>
            <div className="inline-flex items-center -space-x-px">
              <a
                href="#"
                className="flex items-center justify-center px-3 h-8 leading-tight text-text-secondary-light bg-surface-light border border-border-light rounded-l-lg hover:bg-background-light hover:text-text-primary-light dark:bg-surface-dark dark:border-border-dark dark:text-text-secondary-dark dark:hover:bg-background-dark dark:hover:text-text-primary-dark"
              >
                Trước
              </a>
              <a
                href="#"
                className="flex items-center justify-center px-3 h-8 leading-tight text-text-secondary-light bg-surface-light border border-border-light hover:bg-background-light hover:text-text-primary-light dark:bg-surface-dark dark:border-border-dark dark:text-text-secondary-dark dark:hover:bg-background-dark dark:hover:text-text-primary-dark"
              >
                1
              </a>
              <a
                href="#"
                className="flex items-center justify-center px-3 h-8 leading-tight text-text-secondary-light bg-surface-light border border-border-light hover:bg-background-light hover:text-text-primary-light dark:bg-surface-dark dark:border-border-dark dark:text-text-secondary-dark dark:hover:bg-background-dark dark:hover:text-text-primary-dark"
              >
                2
              </a>
              <a
                href="#"
                aria-current="page"
                className="flex items-center justify-center px-3 h-8 text-primary bg-primary/10 border border-primary hover:bg-primary/20"
              >
                3
              </a>
              <a
                href="#"
                className="flex items-center justify-center px-3 h-8 leading-tight text-text-secondary-light bg-surface-light border border-border-light hover:bg-background-light hover:text-text-primary-light dark:bg-surface-dark dark:border-border-dark dark:text-text-secondary-dark dark:hover:bg-background-dark dark:hover:text-text-primary-dark"
              >
                4
              </a>
              <a
                href="#"
                className="flex items-center justify-center px-3 h-8 leading-tight text-text-secondary-light bg-surface-light border border-border-light hover:bg-background-light hover:text-text-primary-light dark:bg-surface-dark dark:border-border-dark dark:text-text-secondary-dark dark:hover:bg-background-dark dark:hover:text-text-primary-dark"
              >
                5
              </a>
              <a
                href="#"
                className="flex items-center justify-center px-3 h-8 leading-tight text-text-secondary-light bg-surface-light border border-border-light rounded-r-lg hover:bg-background-light hover:text-text-primary-light dark:bg-surface-dark dark:border-border-dark dark:text-text-secondary-dark dark:hover:bg-background-dark dark:hover:text-text-primary-dark"
              >
                Sau
              </a>
            </div>
          </div>
        </div>
      </div>
    </SellerLayout>
  );
}
