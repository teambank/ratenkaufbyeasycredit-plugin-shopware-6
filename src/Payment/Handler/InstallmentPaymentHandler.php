<?php

declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Payment\Handler;

class InstallmentPaymentHandler extends AbstractHandler
{
    public function getPaymentType()
    {
        return 'INSTALLMENT';
    }
}
