<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class GetObservation extends Model
{
    protected $guarded = [];

    // Scope untuk filter berdasarkan grade
    public function scopeFilterByGrade($query, $grades)
    {
        if (!empty($grades)) {
            return $query->whereIn('grade', $grades);
        }
        return $query;
    }

    // Scope untuk filter berdasarkan media
    public function scopeFilterByMedia($query, $hasMedia, $mediaType = null)
    {
        if ($hasMedia) {
            $query->where('has_media', true);
            if ($mediaType) {
                $query->whereHas('media', function($q) use ($mediaType) {
                    $q->where('media_type', $mediaType);
                });
            }
        }
        return $query;
    }

    // Scope untuk filter berdasarkan lokasi
    public function scopeFilterByLocation($query, $lat, $lng, $radius)
    {
        if ($lat && $lng && $radius) {
            return $query->whereRaw("
                ST_Distance_Sphere(
                    point(longitude, latitude),
                    point(?, ?)
                ) <= ?
            ", [$lng, $lat, $radius * 1000]);
        }
        return $query;
    }

    // Scope untuk filter berdasarkan tanggal
    public function scopeFilterByDate($query, $startDate, $endDate)
    {
        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }
        return $query;
    }

    // Scope untuk pencarian
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function($q) use ($search) {
                $q->where('nameId', 'like', "%{$search}%")
                  ->orWhere('nameLat', 'like', "%{$search}%")
                  ->orWhere('observer_name', 'like', "%{$search}%");
            });
        }
        return $query;
    }
}
