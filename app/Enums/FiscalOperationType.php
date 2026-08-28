<?php

namespace App\Enums;

enum FiscalOperationType: string
{
    case Sale = 'sale';
    case FutureDelivery = 'future_delivery';
    case Shipment = 'shipment';
    case Return = 'return';
    case Transfer = 'transfer';
    case Bonus = 'bonus';
    case Export = 'export';
}
