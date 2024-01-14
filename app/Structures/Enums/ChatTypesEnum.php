<?php

namespace App\Structures\Enums;

enum ChatTypesEnum: int
{
    case PERSONAL = 0;
    case GROUP = 1;
    case CHANNEL = 2;
}
