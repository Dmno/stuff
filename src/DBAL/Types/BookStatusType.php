<?php


namespace App\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class BookStatusType extends AbstractEnumType
{
    public const RESERVED = true;
    public const AVAILABLE = false;

    protected static $choices = [
        self::RESERVED => true,
        self::AVAILABLE => false
    ];
}