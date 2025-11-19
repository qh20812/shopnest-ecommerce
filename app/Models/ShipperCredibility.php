<?php
// app/Models/ShipperCredibility.php

namespace App\Models;

use App\Enums\ShipperCredibilityLevel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipperCredibility extends Model
{
    protected $table = 'shipper_credibilities';
    protected $primaryKey = 'shipper_id';
    public $incrementing = false;

    protected $fillable = [
        'shipper_id',
        'success_rate',
        'average_rating',
        'review_count',
        'complaint_count',
        'late_delivery_count',
        'credibility_score',
        'level',
        'admin_note'
    ];

    protected $casts = [
        'success_rate' => 'decimal:2',
        'average_rating' => 'decimal:2',
        'credibility_score' => 'integer',
        'level' => ShipperCredibilityLevel::class,
    ];

    public function shipper(): BelongsTo
    {
        return $this->belongsTo(Shipper::class);
    }

    // Tự động tính lại điểm uy tín (có thể chạy bằng Job hàng ngày)
    public static function recalculateForShipper($shipperId)
    {
        $shipper = Shipper::findOrFail($shipperId);

        $total = $shipper->assignments()->count();
        $success = $shipper->assignments()->where('status', 'delivered')->count();
        $reviews = $shipper->reviews()->where('is_hidden', false);
        $avgRating = $reviews->avg('rating') ?: 5;
        $reviewCount = $reviews->count();
        $complaints = Dispute::whereHas('order.deliveryAssignment', function ($q) use ($shipperId) {
            $q->where('shipper_id', $shipperId);
        })->count();

        $successRate = $total > 0 ? round(($success / $total) * 100, 2) : 100;

        // Công thức điểm uy tín (có thể tùy chỉnh)
        $score = round(
            ($successRate * 0.5) +
                ($avgRating * 10) +
                (max(0, 100 - $complaints * 5))
        );

        $score = min(100, max(0, $score));

        $level = match (true) {
            $score >= 95 => 'excellent',
            $score >= 85 => 'good',
            $score >= 70 => 'average',
            $score >= 50 => 'warning',
            default => 'poor'
        };

        static::updateOrCreate(
            ['shipper_id' => $shipperId],
            [
                'success_rate' => $successRate,
                'average_rating' => $avgRating,
                'review_count' => $reviewCount,
                'complaint_count' => $complaints,
                'credibility_score' => $score,
                'level' => $level,
                'last_calculated_at' => now(),
            ]
        );
    }
}
