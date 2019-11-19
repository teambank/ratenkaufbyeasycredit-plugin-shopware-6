<?php declare(strict_types=1);

namespace Netzkollektiv\EasyCredit\Controller;

use Netzkollektiv\EasyCredit\Api\CheckoutFactory;
use Netzkollektiv\EasyCredit\Helper\Quote as QuoteHelper;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"storefront"})
 */
class PaymentController extends StorefrontController
{
    /**
     * @var CheckoutFactory
     */
    private $checkoutFactory;

    /**
     * @var Quote
     */
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
    public function cancel(SalesChannelContext $salesChannelContext)
    {
        return $this->redirectToRoute('frontend.checkout.confirm.page');
    }

    /**
     * @Route("/easycredit/return", name="frontend.easycredit.return", options={"seo"="false"}, methods={"GET"})
     */
    public function return(SalesChannelContext $salesChannelContext)
    {
        $checkout = $this->checkoutFactory->create($salesChannelContext);

        if (!$checkout->isInitialized()) {
            throw new \Exception(
                $transactionId,
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
                throw new \Exception($this->getPlugin()->getLabel() . ' wurde nicht genehmigt.');
            }

            $checkout->loadFinancingInformation();
        } catch (\Exception $e) {
            throw new \Exception($e->getException());
        }

        return $this->redirectToRoute('frontend.checkout.confirm.page');
    }

    /**
     * @Route("/easycredit/reject", name="frontend.easycredit.reject", options={"seo"="false"}, methods={"GET"})
     */
    public function reject(SalesChannelContext $salesChannelContext)
    {
        return $this->redirectToRoute('frontend.checkout.confirm.page');
    }
}
