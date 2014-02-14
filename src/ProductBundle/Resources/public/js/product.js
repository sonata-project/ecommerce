// This file is part of the Sonata package.
//
// (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
//
// For the full copyright and license information, please view the LICENSE
// file that was distributed with this source code.

var Sonata = Sonata || {};

Sonata.Product = {

    /**
     * URLs to use when performing ajax operations
     *
     * @var Object
     */
    url: {
        infoStockPrice: null
    },

    /**
     * Object events sources & targets
     *
     * @var Object
     */
    targets: {
        inputAddBasket:            null,
        productPrice:              null,
        submitBasketBtn:           null,
        addBasketError:            null,
    },

    variations: {
        fields:           null,
        form:             null,
        unavailableError: null
    },

    init: function(options) {
        this.setOptions(options);
        this.initAddBasket();
    },

    setOptions: function(options) {
        options = options || [];
        for (property in options) {
            this[property] = options[property];
        }
    },

    initVariation: function() {
        for (field in this.variations.fields) {
            this.variations.fields[field].change(jQuery.proxy(this.changeVariation, this));
        }
    },

    changeVariation: function(event) {
        this.variations.unavailableError.text("");
        this.variations.unavailableError.css('display', 'none');

        var url = this.variations.form.attr("action");

        jQuery.ajax({
            url: url,
            type: 'GET',
            data: this.variations.form.serialize(),
            success: jQuery.proxy(this.processChangeVariationResults, this)
        });
    },

    processChangeVariationResults: function(data) {
        if (data.error) {
            this.variations.unavailableError.text(data.error);
            this.variations.unavailableError.css('display', 'block');
        } else {
            window.location.href = data.variation_url;
        }
    },

    initAddBasket: function() {
        this.targets.inputAddBasket.on('input', jQuery.proxy(this.changeAddBasket, this));
    },

    changeAddBasket: function(event) {
        this.resetElements();

        var url = this.buildInfoStockPriceUrl(event.currentTarget.value);
        jQuery.getJSON(url, jQuery.proxy(this.processAddBasketResults, this));
    },

    resetElements: function() {
        this.targets.addBasketError.css('display', 'none');
        this.targets.submitBasketBtn.removeClass('disabled');
        this.targets.submitBasketBtn.prop('disabled', false);
    },

    processAddBasketResults: function(data) {
        if (data.errors.stock) {
            jQuery.proxy(this.addBasketAddErrors(data.errors), this);
        }
        jQuery.proxy(this.addBasketProcessPrice(data.price_text), this);
    },

    buildInfoStockPriceUrl: function(quantity) {
        return this.url.infoStockPrice+'?quantity='+quantity;
    },

    addBasketAddErrors: function(errors) {
        this.targets.addBasketError.text(errors.stock);
        this.targets.addBasketError.css('display', 'block');
        this.targets.submitBasketBtn.addClass('disabled');
        this.targets.submitBasketBtn.prop('disabled', true);
    },

    addBasketProcessPrice: function(price) {
        this.targets.productPrice.text(price);
    }
}


