<?php
// app/Models/Kegiatan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Kegiatan extends Model
{
    use HasFactory;

    protected $table = 't_detail_kegiatan';
    protected $primaryKey = 'id_kegiatan';

    protected $fillable = [
        'nama_kegiatan',
        'person_in_charge',
        'anggota',
        'tanggal_mulai',
        'tanggal_selesai',
        'jam_mulai',
        'jam_selesai',
        'nama_tempat',
        'id_unit_kerja',
        'is_archived',
        'archived_at'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'is_archived' => 'boolean',
        'archived_at' => 'datetime'
    ];

    // Custom accessor for formatted date range
    public function getTanggalFormattedAttribute()
    {
        if (!$this->tanggal_mulai) return '-';
        
        $startDate = Carbon::parse($this->tanggal_mulai)->format('d/m/Y');
        
        // If no end date or same date, show single date
        if (!$this->tanggal_selesai || $this->tanggal_mulai->equalTo($this->tanggal_selesai)) {
            return $startDate;
        }
        
        // Different dates, show range
        $endDate = Carbon::parse($this->tanggal_selesai)->format('d/m/Y');
        return $startDate . ' s/d ' . $endDate;
    }

    // Custom accessor for duration in days
    public function getDurationDaysAttribute()
    {
        if (!$this->tanggal_mulai || !$this->tanggal_selesai) return 1;
        
        return $this->tanggal_mulai->diffInDays($this->tanggal_selesai) + 1;
    }

    // Custom accessors for time fields to ensure proper format
    public function getJamMulaiAttribute($value)
    {
        if (!$value) return null;
        
        // If it's already in HH:MM format, return as is
        if (strlen($value) === 5 && strpos($value, ':') === 2) {
            return $value;
        }
        
        // If it's in HH:MM:SS format, extract HH:MM
        if (strlen($value) === 8 && substr_count($value, ':') === 2) {
            return substr($value, 0, 5);
        }
        
        // Try to parse as time and format
        try {
            $time = Carbon::createFromFormat('H:i:s', $value);
            return $time->format('H:i');
        } catch (\Exception $e) {
            try {
                $time = Carbon::createFromFormat('H:i', $value);
                return $time->format('H:i');
            } catch (\Exception $e) {
                return $value; // Return original if can't parse
            }
        }
    }

    public function getJamSelesaiAttribute($value)
    {
        if (!$value) return null;
        
        // If it's already in HH:MM format, return as is
        if (strlen($value) === 5 && strpos($value, ':') === 2) {
            return $value;
        }
        
        // If it's in HH:MM:SS format, extract HH:MM
        if (strlen($value) === 8 && substr_count($value, ':') === 2) {
            return substr($value, 0, 5);
        }
        
        // Try to parse as time and format
        try {
            $time = Carbon::createFromFormat('H:i:s', $value);
            return $time->format('H:i');
        } catch (\Exception $e) {
            try {
                $time = Carbon::createFromFormat('H:i', $value);
                return $time->format('H:i');
            } catch (\Exception $e) {
                return $value; // Return original if can't parse
            }
        }
    }

    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class, 'id_unit_kerja', 'id_unit_kerja');
    }

    // Scope for active (non-archived) schedules
    public function scopeActive($query)
    {
        return $query->where('is_archived', false);
    }

    // Scope for archived schedules
    public function scopeArchived($query)
    {
        return $query->where('is_archived', true);
    }

    // Scope for schedules by date range
    public function scopeDateRange($query, $startDate = null, $endDate = null)
    {
        if ($startDate) {
            $query->where('tanggal_selesai', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('tanggal_mulai', '<=', $endDate);
        }
        
        return $query;
    }

    // Scope for outdated schedules (past their end date)
    public function scopeOutdated($query)
    {
        return $query->where('tanggal_selesai', '<', now()->format('Y-m-d'));
    }

    // Scope for current/ongoing schedules
    public function scopeOngoing($query)
    {
        $today = now()->format('Y-m-d');
        return $query->where('tanggal_mulai', '<=', $today)
                    ->where('tanggal_selesai', '>=', $today);
    }

    // Scope for future schedules
    public function scopeFuture($query)
    {
        return $query->where('tanggal_mulai', '>', now()->format('Y-m-d'));
    }

    // Check if the schedule is multi-day
    public function isMultiDay()
    {
        return $this->tanggal_mulai && $this->tanggal_selesai && 
               !$this->tanggal_mulai->equalTo($this->tanggal_selesai);
    }

    // Check if the schedule is currently ongoing
    public function isOngoing()
    {
        $today = now()->format('Y-m-d');
        return $this->tanggal_mulai && $this->tanggal_selesai &&
               $this->tanggal_mulai->format('Y-m-d') <= $today &&
               $this->tanggal_selesai->format('Y-m-d') >= $today;
    }
}