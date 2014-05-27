{% if enabledVariations.count == 0 %}
    {% trans from 'SonataProductBundle' %}no_variations_available{% endtrans %}
{% else %}
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <div class="row">
                    <div class="col-md-7 col-md-offset-3">
                        <h4 class="modal-title">{% trans from 'SonataBasketBundle' %}sonata.basket.product.variations.label{% endtrans %}</h4>
                    </div>
                </div>
            </div>
        {% for variation in enabledVariations %}
            <div class="modal-header">
                {% block product_details %}
                    <div class="row" itemtype="http://schema.org/Product">
                        <div class="col-sm-6">
                            {% block product_image %}
                                {% thumbnail variation.image, 'small' with {'itemprop':'image', 'class': 'img-rounded img-responsive'} %}
                            {% endblock%}
                        </div>

                        <div class="col-sm-6">
                            <h4 itemprop="name" style="margin-top: 0px;">{% block product_title %}{{ variation.name }}{% endblock %}</h4>
                                
                            {% block product_sku %}
                                <dl class="dl-horizontal" style="margin-bottom: 0;">
                                    <dt style="width: auto;">{{ 'sonata.product.sku'|trans([], 'SonataProductBundle') }}</dt>
                                    <dd style="margin-left: 110px; word-wrap: break-word;">{{ variation.sku }}</dd>
                                </dl>
                            {% endblock %}
                                
                            <dl class="dl-horizontal" style="margin-bottom: 0;">
                                <dt style="width: auto;">{{ 'header_basket_unit_price'|trans([], 'SonataBasketBundle') }}</dt>
                                <dd style="margin-left: 110px;">{{ sonata_product_price(variation, currency, true)|number_format_currency(currency) }}</dd>
                            </dl>
                                
                            {% block product_properties %}
                                {{ render(controller(provider.baseControllerName ~ ':renderProperties', {product: variation})) }}
                            {% endblock %}
                        </div>
                    </div>
                {% endblock %}
            </div>
        {% endfor %}
        </div>
    </div>
{% endif %}    
