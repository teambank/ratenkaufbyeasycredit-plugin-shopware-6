<?php declare(strict_types=1);
/*
 * (c) NETZKOLLEKTIV GmbH <kontakt@netzkollektiv.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Netzkollektiv\EasyCredit\Controller;

use Netzkollektiv\EasyCredit\Api\CheckoutFactory;
use Netzkollektiv\EasyCredit\Helper\Quote as QuoteHelper;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"storefront"})
 */
class PaymentController extends StorefrontController
{
    private $checkoutFactory;

    private $quoteHelper;

    public function __construct(
        CheckoutFactory $checkoutFactory,
        QuoteHelper $quoteHelper
    ) {
        $this->checkoutFactory = $checkoutFactory;
        $this->quoteHelper = $quoteHelper;
    }

    /**
     * @Route("/easycredit/cancel", name="frontend.easycredit.cancel", options={"seo"="false"}, methods={"GET"})
     */
    public function cancel(SalesChannelContext $salesChannelContext): RedirectResponse
    {
        return $this->redirectToRoute('frontend.checkout.confirm.page');
    }

    /**
     * @Route("/easycredit/return", name="frontend.easycredit.return", options={"seo"="false"}, methods={"GET"})
     */
    public function return(SalesChannelContext $salesChannelContext): RedirectResponse
    {
        $checkout = $this->checkoutFactory->create($salesChannelContext);

        if (!$checkout->isInitialized()) {
            throw new \Exception(
                'Payment was not initialized.'
            );
        }

        $quote = $this->quoteHelper->getQuote($salesChannelContext);

        if (!$checkout->isAmountValid($quote)
            || !$checkout->verifyAddressNotChanged($quote)
        ) {
            throw new \Exception(
                'Raten müssen neu berechnet werden.'
            );
        }

        try {
            $approved = $checkout->isApproved();
            if (!$approved) {
                throw new \Exception('Ratenkauf wurde nicht genehmigt.');
            }

            $checkout->loadFinancingInformation();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        return $this->redirectToRoute('frontend.checkout.confirm.page');
    }

    /**
     * @Route("/easycredit/reject", name="frontend.easycredit.reject", options={"seo"="false"}, methods={"GET"})
     */
    public function reject(SalesChannelContext $salesChannelContext): RedirectResponse
    {
        return $this->redirectToRoute('frontend.checkout.confirm.page');
    }
}
