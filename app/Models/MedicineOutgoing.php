<?php

namespace App\Models;

use App\Models\Unit;
use App\Models\Medicine;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MedicineOutgoing extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    protected $fillable = [
        'medicine_id',
        'unit_id',
        'batch_no',
        'exp_date',
        'quantity',
        'date',
    ];

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function getColumns()
    {
        return [
            'medicine_outgoings.id',
            'medicine_outgoings.medicine_id as id_medicine',
            'medicine_outgoings.batch_no',
            'medicine_outgoings.exp_date',
            'medicine_outgoings.quantity',
            'medicine_outgoings.date',
            'units.id as id_unit',
            'units.name as unit_name',
        ];
    }


    public function getDataById($id)
    {
        return MedicineOutgoing::with(['medicine' => function ($query) {
                $query->withTrashed();
            }])
            ->select($this->getColumns())
            ->join('units', 'units.id', '=', 'medicine_outgoings.unit_id')
            ->where('medicine_outgoings.id', $id)
            ->first();
    }
}
