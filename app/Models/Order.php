<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    const ORDER_STATUS_INITIAL = 'initial';
    const ORDER_STATUS_CANCELED = 'canceled';
    const ORDER_STATUS_FINISHED = 'finished';

    const ORDER_STATUS = [
        self::ORDER_STATUS_INITIAL,
        self::ORDER_STATUS_CANCELED,
        self::ORDER_STATUS_FINISHED
    ];
}
