import React, { useEffect, useRef } from 'react';
import SellerLayout from '../../../layouts/seller-layout';
import { TrendingUp, TrendingDown, DollarSign, ShoppingCart, Package, Users } from 'lucide-react';

interface DashboardProps {
  user?: {
    full_name: string;
    email: string;
    avatar_url?: string;
  };
}

export default function Dashboard({ user }: DashboardProps) {
  const chartRef = useRef<HTMLCanvasElement>(null);

  useEffect(() => {
    if (!chartRef.current) return;

    // Dynamically import Chart.js only on client side
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    import('chart.js/auto').then((ChartModule: any) => {
      const Chart = ChartModule.default;
      const ctx = chartRef.current?.getContext('2d');
      if (!ctx) return;

      const salesChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7'],
          datasets: [
            {
              label: 'Doanh thu (triệu đ)',
              data: [45, 52, 60, 55, 70, 68, 75],
              backgroundColor: 'rgba(255, 107, 107, 0.2)',
              borderColor: '#FF6B6B',
              borderWidth: 2,
              pointBackgroundColor: '#FF6B6B',
              pointBorderColor: '#fff',
              pointHoverBackgroundColor: '#fff',
              pointHoverBorderColor: '#FF6B6B',
              tension: 0.4,
              fill: true,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true,
              grid: {
                color: document.documentElement.classList.contains('dark')
                  ? 'rgba(255, 255, 255, 0.1)'
                  : 'rgba(0, 0, 0, 0.1)',
              },
              ticks: {
                color: document.documentElement.classList.contains('dark') ? '#B0B0B0' : '#6B6B6B',
              },
            },
            x: {
              grid: {
                display: false,
              },
              ticks: {
                color: document.documentElement.classList.contains('dark') ? '#B0B0B0' : '#6B6B6B',
              },
            },
          },
          plugins: {
            legend: {
              display: false,
            },
          },
        },
      });

      // Update chart colors on theme change
      const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
          if (mutation.attributeName === 'class') {
            const isDarkMode = document.documentElement.classList.contains('dark');
            salesChart.options.scales!.y!.grid!.color = isDarkMode
              ? 'rgba(255, 255, 255, 0.1)'
              : 'rgba(0, 0, 0, 0.1)';
            salesChart.options.scales!.y!.ticks!.color = isDarkMode ? '#B0B0B0' : '#6B6B6B';
            salesChart.options.scales!.x!.ticks!.color = isDarkMode ? '#B0B0B0' : '#6B6B6B';
            salesChart.update();
          }
        });
      });

      observer.observe(document.documentElement, {
        attributes: true,
      });

      return () => {
        salesChart.destroy();
        observer.disconnect();
      };
    });
  }, []);

  const statCards = [
    {
      label: 'Doanh thu',
      value: '250.6M đ',
      change: '+12.5%',
      isPositive: true,
      icon: <DollarSign className="w-6 h-6" />,
      bgColor: 'bg-blue-500/10',
      iconColor: 'text-blue-500',
    },
    {
      label: 'Đơn hàng',
      value: '1,254',
      change: '+8.2%',
      isPositive: true,
      icon: <ShoppingCart className="w-6 h-6" />,
      bgColor: 'bg-green-500/10',
      iconColor: 'text-green-500',
    },
    {
      label: 'Sản phẩm',
      value: '3,520',
      change: '-2.4%',
      isPositive: false,
      icon: <Package className="w-6 h-6" />,
      bgColor: 'bg-orange-500/10',
      iconColor: 'text-orange-500',
    },
    {
      label: 'Khách hàng',
      value: '25,8K',
      change: '+15.3%',
      isPositive: true,
      icon: <Users className="w-6 h-6" />,
      bgColor: 'bg-purple-500/10',
      iconColor: 'text-purple-500',
    },
  ];

  const topProducts = [
    {
      name: 'Áo thun nam cotton',
      sales: 234,
      revenue: '23.4M đ',
      image: 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=100&h=100&fit=crop',
    },
    {
      name: 'Quần jean nữ',
      sales: 189,
      revenue: '18.9M đ',
      image: 'https://images.unsplash.com/photo-1542272604-787c3835535d?w=100&h=100&fit=crop',
    },
    {
      name: 'Giày thể thao',
      sales: 156,
      revenue: '15.6M đ',
      image: 'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=100&h=100&fit=crop',
    },
    {
      name: 'Túi xách da',
      sales: 142,
      revenue: '14.2M đ',
      image: 'https://images.unsplash.com/photo-1590874103328-eac38a683ce7?w=100&h=100&fit=crop',
    },
    {
      name: 'Đồng hồ thông minh',
      sales: 128,
      revenue: '12.8M đ',
      image: 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=100&h=100&fit=crop',
    },
  ];

  return (
    <SellerLayout activePage="dashboard" user={user}>
      <div className="container mx-auto px-6 py-8">
        <h2 className="text-3xl font-bold text-text-primary-light dark:text-text-primary-dark mb-6">Tổng quan</h2>

        {/* Stats Cards */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          {statCards.map((stat, index) => (
            <div
              key={index}
              className="bg-surface-light dark:bg-surface-dark p-6 rounded-xl border border-border-light dark:border-border-dark"
            >
              <div className="flex items-center justify-between mb-4">
                <p className="text-sm font-medium text-text-secondary-light dark:text-text-secondary-dark">
                  {stat.label}
                </p>
                <div className={`p-2 rounded-lg ${stat.bgColor}`}>
                  <span className={stat.iconColor}>{stat.icon}</span>
                </div>
              </div>
              <p className="text-3xl font-bold text-text-primary-light dark:text-text-primary-dark">{stat.value}</p>
              <p
                className={`text-sm flex items-center mt-1 ${
                  stat.isPositive ? 'text-green-500' : 'text-red-500'
                }`}
              >
                {stat.isPositive ? <TrendingUp className="w-4 h-4 mr-1" /> : <TrendingDown className="w-4 h-4 mr-1" />}
                <span>{stat.change} so với tháng trước</span>
              </p>
            </div>
          ))}
        </div>

        {/* Chart and Top Products */}
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          {/* Sales Chart */}
          <div className="lg:col-span-2 bg-surface-light dark:bg-surface-dark p-6 rounded-xl border border-border-light dark:border-border-dark">
            <h3 className="text-lg font-bold text-text-primary-light dark:text-text-primary-dark mb-4">
              Thống kê doanh số
            </h3>
            <div className="h-80">
              <canvas ref={chartRef} id="salesChart"></canvas>
            </div>
          </div>

          {/* Top Products */}
          <div className="bg-surface-light dark:bg-surface-dark p-6 rounded-xl border border-border-light dark:border-border-dark">
            <h3 className="text-lg font-bold text-text-primary-light dark:text-text-primary-dark mb-4">
              Sản phẩm bán chạy nhất
            </h3>
            <ul className="space-y-4">
              {topProducts.map((product, index) => (
                <li key={index} className="flex items-center gap-3">
                  <img
                    src={product.image}
                    alt={product.name}
                    className="w-12 h-12 rounded-lg object-cover"
                  />
                  <div className="flex-1 min-w-0">
                    <p className="text-sm font-semibold text-text-primary-light dark:text-text-primary-dark truncate">
                      {product.name}
                    </p>
                    <p className="text-xs text-text-secondary-light dark:text-text-secondary-dark">
                      {product.sales} đã bán
                    </p>
                  </div>
                  <p className="text-sm font-bold text-text-primary-light dark:text-text-primary-dark">
                    {product.revenue}
                  </p>
                </li>
              ))}
            </ul>
          </div>
        </div>
      </div>
    </SellerLayout>
  );
}
