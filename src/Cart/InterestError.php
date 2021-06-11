<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
        return static::KEY;
    }

    public function isPersistent(): bool
    {
        return false;
    }

    public function getMessageKey(): string
    {
        return static::KEY;
    }

    public function getLevel(): int
    {
        return static::LEVEL_WARNING;
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
