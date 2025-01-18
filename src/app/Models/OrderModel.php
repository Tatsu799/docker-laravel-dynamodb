<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use BaoPham\DynamoDb\DynamoDbModel;

class OrderModel extends DynamoDbModel
{
    use HasFactory;

    protected $table = 'orders';
    protected $fillable = [
        'store_id',
        'order_id',
        'remarks',
        // 'newRemarks',
    ];
    protected $primaryKey = 'store_id';
}
