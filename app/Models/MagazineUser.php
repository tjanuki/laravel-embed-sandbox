<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MagazineUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'source_data',
    ];

    protected $casts = [
        'source_data' => 'array',
    ];

    public function getSourceWebsiteAttribute()
    {
        return $this->source_data['website'] ?? null;
    }

    public function getSourceUrlAttribute()
    {
        return $this->source_data['url'] ?? null;
    }

    public function getEmbedTypeAttribute()
    {
        return $this->source_data['embed_type'] ?? null;
    }

    public static function getSourceWebsiteStats()
    {
        return self::whereNotNull('source_data')
            ->get()
            ->groupBy(function($user) {
                return $user->source_data['website'] ?? 'Unknown';
            })
            ->map(function($users) {
                return $users->count();
            })
            ->sortDesc();
    }

    public static function getEmbedTypeStats()
    {
        return self::whereNotNull('source_data')
            ->get()
            ->groupBy(function($user) {
                return $user->source_data['embed_type'] ?? 'Manual';
            })
            ->map(function($users) {
                return $users->count();
            })
            ->sortDesc();
    }

    public static function getRecentSubscriptionsBySource($days = 30)
    {
        return self::where('created_at', '>=', now()->subDays($days))
            ->whereNotNull('source_data')
            ->get()
            ->groupBy(function($user) {
                return $user->source_data['website'] ?? 'Unknown';
            })
            ->map(function($users) {
                return $users->count();
            })
            ->sortDesc();
    }
}
