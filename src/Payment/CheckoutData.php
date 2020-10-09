<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Payment;

use Shopware\Core\Framework\Struct\Struct;

class CheckoutData extends Struct
{
    /**
     * @var string
     */
    protected $paymentMethodId;

    /**
     * @var bool
     */
    protected $isSelected;

    /**
     * @var string
     */
    protected $agreement;

    /**
     * @var string
     */
    protected $paymentPlan;

    /**
     * @var string
     */
    protected $error;

    public function getPaymentMethodId(): string
    {
        return $this->paymentMethodId;
    }

    public function getIsSelected(): bool
    {
        return $this->isSelected;
    }

    public function getAgreement(): string
    {
        return $this->agreement;
    }

    public function getPaymentPlan(): ?string
    {
        return $this->paymentPlan;
    }

    public function getError(): ?string
    {
        return $this->error;
    }
}
