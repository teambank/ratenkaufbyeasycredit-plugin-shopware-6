<?php declare(strict_types=1);

namespace Netzkollektiv\EasyCredit\Cart;

use Shopware\Core\Checkout\Cart\Error\Error;

class InterestError extends Error
{
    private const KEY = 'installments-must-be-recalculated';

    public function __construct()
    {
        parent::__construct('');
    }

    public function getId(): string
    {
        return self::KEY;
    }

    public function getMessageKey(): string
    {
        return self::KEY;
    }

    public function getLevel(): int
    {
        return self::LEVEL_ERROR;
    }

    public function blockOrder(): bool
    {
        return true;
    }

    public function getParameters(): array
    {
        return [];
    }
}
