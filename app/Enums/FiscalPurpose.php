<?php

namespace App\Enums;

enum FiscalPurpose: int
{
    case Normal = 1;
    case Complementary = 2;
    case Adjustment = 3;
    case Return = 4;
}
