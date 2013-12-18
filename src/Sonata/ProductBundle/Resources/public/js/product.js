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
        inputAddBasket:  null,
        productPrice:    null,
        submitBasketBtn: null,
        addBasketError:  null
    },
    
    init: function(options) {
        options = options || [];
        for (property in options) {
            this[property] = options[property];
        }

        this.initAddBasket();
    },
    
    initAddBasket: function() {
        this.targets.inputAddBasket.change(jQuery.proxy(this.changeAddBasket, this));
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
    },

    getUriFromTree: function(productTree, selectsList) {
        selectsList.change(function() {
            selectedValues = {};

            selectsList.each(function() {
                name = cleanFieldId($(this).attr('id'));
                selectedValues[name] = $(this).val();
            });

            uri = null;

            for (var name in selectedValues) {
                if (typeof productTree[name][selectedValues[name]] == 'undefined') {
                    break;
                }

                uri = productTree[name][selectedValues[name]];
            }

            if (typeof uri['uri'][0] == 'undefined') {
                return false;
            }

            return document.location = uri['uri'][0];
        });

        function cleanFieldId(name) {
            return name.substring('sonata_product_training_'.length);
        }
    }
}


