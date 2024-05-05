<?php

namespace App\Enums;

enum ProductStatus :string
{
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
}
