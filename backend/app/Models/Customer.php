<?php

namespace App\Models;

use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';

    protected $fillable = [
        'name',
        'email',
        'cpf_cnpj',
        'phone',
        'address',
        'external_id'
    ];

    protected static function newFactory(): Factory|CustomerFactory
    {
        return CustomerFactory::new();
    }
}
