/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'jquery',
    'underscore',
    'mage/mage',
    'mage/decorate'
], function (Component, customerData, $, _) {
    'use strict';

    var sidebarInitialized = false,
        compareProductsReloaded = false;

    /**
     * Initialize sidebar
     */
    function initSidebar() {
        if (sidebarInitialized) {
            return;
        }

        sidebarInitialized = true;
        $('[data-role=compare-product-sidebar]').decorate('list', true);
    }

    return Component.extend({
        /** @inheritdoc */
        initialize: function () {
            this._super();
            this.compareProducts = customerData.get('compare-product');
            if (!compareProductsReloaded
                && !_.isEmpty(this.compareProducts())
                //Expired section names are reloaded on page load
                && _.indexOf(customerData.getExpiredSectionNames(), 'compare-product') === -1
                && window.checkout
                && window.checkout.websiteId
                && window.checkout.websiteId !== this.compareProducts().websiteId
            ) {
                //set count to 0 to prevent "compared product" blocks and count to show with wrong count and items
                this.compareProducts().count = 0;
                customerData.reload(['compare-product'], false);
                compareProductsReloaded = true;
            }
            initSidebar();
        }
    });
});
