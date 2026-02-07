<?php

namespace App\Enums;

enum GradingStrategy: string
{
    case AVERAGE = "average";
    case FIRST = "first";
    case LAST = "last";
    case BEST = "best";
    //
}
