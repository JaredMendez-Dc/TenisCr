<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teni extends Model
{
    /** @use HasFactory<\Database\Factories\TeniFactory> */
    use HasFactory;
    protected $fillable = ['color','talla','costo','marca_id','categoria','imagen'];

}
