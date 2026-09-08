<?php

namespace App\Enums;

enum Role: string
{
    case Client = 'client';
    case Gestionnaire = 'gestionnaire';
    case Admin = 'admin';
    case SuperAdmin = 'super_admin';
}
