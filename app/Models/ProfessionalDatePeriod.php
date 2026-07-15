<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class ProfessionalDatePeriod extends Model
{
	use HasFactory;
	protected $fillable = [
	    'professional_id',
	    'date',
	    'start_time',
	    'end_time',
	];
}
