const { Component } = Shopware;
const { Criteria } = Shopware.Data;

Component.extend('easycredit-payment-status-select', 'sw-entity-single-select', {
    methods: {
        createdComponent() {
            this.criteria.addAssociation('stateMachine');
            this.criteria.addAssociation('toStateMachineTransitions.fromStateMachineState');

            this.criteria.addFilter(
                Criteria.equals('stateMachine.technicalName', 'order_transaction.state')
            );

            // important for merchants understanding, but will not change the payment status in Netzkollektiv\EasyCredit\Payment\StateHandler
            this.criteria.addFilter(
                Criteria.multi('OR',[
                    Criteria.equals('toStateMachineTransitions.fromStateMachineState.technicalName', 'open'),
                    Criteria.equals('technicalName','open')
                ])
            );

            this.loadSelected();
        }
    }
});