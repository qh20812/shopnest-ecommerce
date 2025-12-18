<?php

namespace App\Http\Controllers\Sellers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display seller dashboard with statistics
     */
    public function index(): Response
    {
        $user = Auth::user();
        
        // Get seller's shop
        $shop = $user->shops()->first();
        
        if (!$shop) {
            return Inertia::render('roles/sellers/dashboard', [
                'stats' => [
                    'revenue' => 0,
                    'revenue_change' => 0,
                    'orders' => 0,
                    'orders_change' => 0,
                    'products' => 0,
                    'products_change' => 0,
                    'customers' => 0,
                    'customers_change' => 0,
                ],
                'sales_chart' => [],
                'top_products' => [],
            ]);
        }
        
        // Calculate statistics
        $stats = $this->calculateStats($shop->id);
        $salesChart = $this->getSalesChartData($shop->id);
        $topProducts = $this->getTopProducts($shop->id);
        
        return Inertia::render('roles/sellers/dashboard', [
            'stats' => $stats,
            'sales_chart' => $salesChart,
            'top_products' => $topProducts,
        ]);
    }
    
    /**
     * Calculate dashboard statistics
     */
    private function calculateStats(int $shopId): array
    {
        // Current month dates
        $currentMonthStart = now()->startOfMonth();
        $currentMonthEnd = now()->endOfMonth();
        $lastMonthStart = now()->subMonth()->startOfMonth();
        $lastMonthEnd = now()->subMonth()->endOfMonth();
        
        // Revenue statistics
        $currentRevenue = Order::where('shop_id', $shopId)
            ->whereIn('payment_status', ['paid', 'partially_refunded'])
            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->sum('total_amount');
            
        $lastRevenue = Order::where('shop_id', $shopId)
            ->whereIn('payment_status', ['paid', 'partially_refunded'])
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
            ->sum('total_amount');
            
        $revenueChange = $lastRevenue > 0 
            ? (($currentRevenue - $lastRevenue) / $lastRevenue) * 100 
            : ($currentRevenue > 0 ? 100.0 : 0.0);
        
        // Orders statistics
        $currentOrders = Order::where('shop_id', $shopId)
            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->count();
            
        $lastOrders = Order::where('shop_id', $shopId)
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
            ->count();
            
        $ordersChange = $lastOrders > 0 
            ? (($currentOrders - $lastOrders) / $lastOrders) * 100 
            : ($currentOrders > 0 ? 100.0 : 0.0);
        
        // Products statistics
        $currentProducts = Product::where('shop_id', $shopId)
            ->where('status', 'active')
            ->count();
            
        $lastProducts = Product::where('shop_id', $shopId)
            ->where('status', 'active')
            ->where('created_at', '<', $currentMonthStart)
            ->count();
            
        $productsChange = $lastProducts > 0 
            ? (($currentProducts - $lastProducts) / $lastProducts) * 100 
            : ($currentProducts > 0 ? 100.0 : 0.0);
        
        // Customers statistics (unique customers who ordered)
        $currentCustomers = Order::where('shop_id', $shopId)
            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->distinct('customer_id')
            ->count('customer_id');
            
        $lastCustomers = Order::where('shop_id', $shopId)
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
            ->distinct('customer_id')
            ->count('customer_id');
            
        $customersChange = $lastCustomers > 0 
            ? (($currentCustomers - $lastCustomers) / $lastCustomers) * 100 
            : ($currentCustomers > 0 ? 100.0 : 0.0);
        
        return [
            'revenue' => $currentRevenue,
            'revenue_change' => (float) round($revenueChange, 1),
            'orders' => $currentOrders,
            'orders_change' => (float) round($ordersChange, 1),
            'products' => $currentProducts,
            'products_change' => (float) round($productsChange, 1),
            'customers' => $currentCustomers,
            'customers_change' => (float) round($customersChange, 1),
        ];
    }
    
    /**
     * Get sales chart data for last 7 months
     */
    private function getSalesChartData(int $shopId): array
    {
        $months = [];
        $revenues = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $monthStart = now()->subMonths($i)->startOfMonth();
            $monthEnd = now()->subMonths($i)->endOfMonth();
            
            $revenue = Order::where('shop_id', $shopId)
                ->whereIn('payment_status', ['paid', 'partially_refunded'])
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('total_amount');
            
            $months[] = 'Tháng ' . $monthStart->format('n');
            $revenues[] = (float) round($revenue / 1000000, 2); // Convert to millions
        }
        
        return [
            'labels' => $months,
            'data' => $revenues,
        ];
    }
    
    /**
     * Get top 5 selling products
     */
    private function getTopProducts(int $shopId): array
    {
        $products = Product::where('shop_id', $shopId)
            ->where('status', 'active')
            ->with('images')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get();
        
        return $products->map(function ($product) {
            $revenue = $product->total_sold * $product->base_price;
            
            return [
                'name' => $product->product_name,
                'sales' => $product->total_sold,
                'revenue' => number_format($revenue, 0, ',', '.') . ' đ',
                'image' => $product->images->first()?->image_url ?? '/ShopNest3.png',
            ];
        })->toArray();
    }
}
