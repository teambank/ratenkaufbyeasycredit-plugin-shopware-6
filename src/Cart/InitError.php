<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Cart;

use Shopware\Core\Checkout\Cart\Error\Error;

class InitError extends Error
{
    private const KEY = 'init-error';

    /**
     * @var string
     */
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->message = sprintf(
            '%s Please correct your order and click "Recalculate installments"',
            $name
        );
        parent::__construct($this->message);
    }

    public function getId(): string
    {
        return sprintf('%s-%s', $this->getMessageKey(), $this->name);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getParameters(): array
    {
        return ['name' => $this->name];
    }

    public function getMessageKey(): string
    {
        return static::KEY;
    }

    public function getLevel(): int
    {
        return static::LEVEL_ERROR;
    }

    public function blockOrder(): bool
    {
        return true;
    }
}
