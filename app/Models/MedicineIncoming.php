<?php

namespace App\Models;

use App\Models\MedicineOutgoing;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MedicineIncoming extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    protected $fillable = [
        'medicine_id',
        'batch_no',
        'date',
        'quantity'
    ];

    public function outgoings()
    {
        return $this->hasMany(MedicineOutgoing::class, 'batch_no', 'batch_no');
    }
}