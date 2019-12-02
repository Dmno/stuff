<?php


namespace App\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class BookStatusType extends AbstractEnumType
{
    public const RESERVED = 'Reserved';
    public const AVAILABLE = 'Available';
    public const DELETED = 'Deleted';

    protected static $choices = [
        self::RESERVED => 'Reserved',
        self::AVAILABLE => 'Available',
        self::DELETED => 'Deleted'
    ];
}