<?php

namespace App\Enums;

enum StoreRole: string
{
    case Owner = 'owner';
    case Staff = 'staff';
}
