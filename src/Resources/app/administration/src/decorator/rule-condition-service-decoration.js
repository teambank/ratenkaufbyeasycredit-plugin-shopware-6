Shopware.Application.addServiceProviderDecorator('ruleConditionDataProviderService', (ruleConditionService) => {
    ruleConditionService.addCondition('cartCartAmountWithoutInterest', {
        component: 'sw-condition-cart-amount',
        label: 'easycredit.rule.cartAmountRule',
        scopes: ['cart'],
        group: 'cart',
    });
    ruleConditionService.addCondition('cartPositionPriceWithoutInterest', {
        component: 'sw-condition-cart-position-price',
        label: 'easycredit.rule.cartPositionPrice',
        scopes: ['cart'],
        group: 'cart',
    });

    const systemConfigApiService = Shopware.Service('systemConfigApiService');
    systemConfigApiService.getValues('EasyCreditRatenkauf')
        .then((cfg) => {
            if (typeof cfg['EasyCreditRatenkauf.config.webshopInfo'] === 'object' &&
                cfg['EasyCreditRatenkauf.config.webshopInfo'].flexprice
            ) {
                ruleConditionService.addModuleType({
                        id: 'easycredit-flexprice',
                        name: 'easycredit.rule.flexPriceType',
                });
            }
        })
        .catch((error) => {
            // fail silently
        });
    return ruleConditionService;
});