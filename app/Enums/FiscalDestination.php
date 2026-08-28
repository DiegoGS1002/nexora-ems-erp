<?php

namespace App\Enums;

enum FiscalDestination: int
{
    case Internal = 1;
    case Interstate = 2;
    case Exterior = 3;
}
