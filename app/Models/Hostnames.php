<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hostnames extends Model
{
    use HasFactory;
    protected $table = 'hostnames';
    protected $fillable = [
        'fqdn',
        'redirect_to',
        'force_https',
        'under_maintenance_since',
        'website_id',
    ];
}
