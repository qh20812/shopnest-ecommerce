<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsReport extends Model
{
    protected $fillable = [
        'report_type',
        'title',
        'date_from',
        'date_to',
        'data',
        'summary',
        'generated_by'
    ];

    protected $casts = [
        'data' => 'array',
        'summary' => 'array',
        'date_from' => 'date',
        'date_to' => 'date',
        'generated_at' => 'datetime',
    ];

    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    // Scope: Báo cáo doanh thu
    public function scopeRevenue($query)
    {
        return $query->where('report_type', 'like', '%revenue%')
            ->orWhere('report_type', 'like', '%sales%');
    }
}
