<?php

namespace Taopix\Connector\Shopify;

use Taopix\Connector\Shopify\Asset;

class Snippet extends Asset
{
	/**
	 * Pushes the tpx-product.liquid template to Shopify.
	 */
	public function pushTaopixProductLiquid(): void
	{
		$template = <<<EOT
		<style type="text/css">
          .field__input { width:250px; padding: 1.5rem; margin-bottom: 1.6rem; }

		  @media screen and (min-width: 990px) {
			.product:not(.product--no-media):not(.featured-product) .product__media-wrapper {
			  max-width: 54%;
			  width: calc(54% - 1rem / 2);
			}

			.product:not(.product--no-media):not(.featured-product) .product__info-wrapper {
			  padding-left: 4rem;
			  max-width: 46%;
			  width: calc(46% - 1rem / 2);
			}
		  }
        </style>

		{% if customer.id != null %}
			{% assign customer_id = customer.id | append: shop.metafields.taopix.secret | sha1 %}
		{% else %}
			{% assign customer_id = '' %}
		{% endif %}

		<form action="/tools/designer/create" method="POST" id="tpx-form" class="{{ form_classes }}">
			<input type="hidden" name="customerid" value="{{ customer_id }}" />
			<input type="hidden" name="customeremail" value="{{ customer.email }}" />
			<input type="hidden" name="customerfirstname" value="{{ customer.first_name }}" />
			<input type="hidden" name="customerlastname" value="{{ customer.last_name }}" />
			<input type="hidden" name="l" value="{{ shop.locale }}" />
          
            {% render 'tpx-product-dropdown' product: product, block: block %}

		</form>
EOT;

		$this->pushAsset('snippets/tpx-product.liquid', $template);
	}

	/**
	 * Pushes the tpx-product-dropdown.liquid template to Shopify.
	 */
	public function pushTaopixProductDropDownLiquid(): void
	{
		$template = <<<EOT
		<script type="text/javascript">  
			function addEventHandler(elem, eventType, handler) {
				if (elem.addEventListener)
				elem.addEventListener (eventType, handler, false);
				else if (elem.attachEvent)
				elem.attachEvent ('on' + eventType, handler); 
			}

			addEventHandler(document, 'DOMContentLoaded', function() {
				var theSelect = document.getElementById('ProductSelect-{{ section.id }}');
				addEventHandler(theSelect, 'change', function() {
				var selection = theSelect.value;
				var price = document.getElementById('price-'+selection).value;
				var descHTML = document.getElementById('description-'+selection).value;
				var priceField = document.getElementById('thePrice');
				if (priceField) {
					priceField.innerHTML = price;
				}

				var descriptionFields = document.getElementsByClassName('product__description rte');

				for (let i = 0; i < descriptionFields.length; i++) {
					descriptionFields[i].innerHTML = descHTML;
				} 
				
				var allVariantMetaFields = document.querySelectorAll("[data-type='variant_metafield']");
				
				for (let i = 0; i < allVariantMetaFields.length; i++) {
					allVariantMetaFields[i].style.display = 'none';
				}
				
				var variantmetafields = document.querySelectorAll("[data-productid='" + selection + "']");
				
				for (let i = 0; i < variantmetafields.length; i++) {
					variantmetafields[i].style.display = 'block';
				}
				});
			});

			function submitForm(e) {
				e.preventDefault();
				document.getElementById('tpx-form').submit(); 
			}
			</script>

			<variant-selects class="no-js-hidden" data-section="{{ section.id }}" data-url="{{ product.url }}" {{ block.shopify_attributes }}>
			<div class="product-form__input product-form__input--dropdown">
				<label class="form__label" for="Option-{{ section.id }}-{{ forloop.index0 }}">
				{{ 'taopix.variant_label' | t }}
				</label>
				<div class="select">
				<select id="ProductSelect-{{ section.id }}"
						class="select__select"
						name="product_id"
						>
					{% for variant in product.variants %}
					<option value="{{ variant.metafields.taopix.taopix_product_id }}" {%- if variant == product.selected_or_first_available_variant %}selected="selected"{%- endif -%}>
					{% assign variantTitleArr = variant.title | split: "(Layout Code: " %}
					{% assign variantDisplayTitle = variantTitleArr[0] %}

					{% if variantTitleArr[1] contains ') /' %}
					{% assign optionText = variantTitleArr[1] | split: ") /" %}
					{% assign variantDisplayTitle = variantDisplayTitle | append: ' / ' | append: optionText[1] %}
					{% endif variantTitleArr[] %}

					{{ variantDisplayTitle }}  {%- if variant.available == false %} - {{ 'products.product.sold_out' | t }}{% endif %}
						</option>
					{%- endfor -%}
				</select>
				{% render 'icon-caret' %}
				</div>
			</div>

			<script type="application/json">
						{{ product.variants | json }}
			</script>
			</variant-selects>

			{% for metafield in product.metafields.taopixcustomparam %}
			{% assign fieldID = "taopixcustomparam" | append: metafield[0] %}		
			{% assign fieldLabel = metafield[1] %}


			<label class="form__label" for="{{ fieldID }}">{{fieldLabel}}</label>
			<input placeholder="{{fieldLabel}}" class="field__input" type="text" id="{{ fieldID }}" name="{{ fieldID }}"><br>
			{% endfor %}

			{% for variant in product.variants %}
				<input type="hidden" id="price-{{ variant.metafields.taopix.taopix_product_id }}" value="{{ variant.price | money_with_currency }}" />
				<input type="hidden" id="description-{{ variant.metafields.taopix.taopix_product_id }}" value="{{ variant.metafields.taopix.taopix_description }}" />

				{% for metafield in variant.metafields.taopixcustomparam %}
				{% assign fieldID = "taopixcustomparam" | append: metafield[0] %}		
				{% assign fieldLabel = metafield[1] %}


				<label class="form__label" data-type="variant_metafield" data-productid="{{ variant.metafields.taopix.taopix_product_id }}" for="{{ fieldID }}">{{fieldLabel}}</label>
				<input placeholder="{{fieldLabel}}" class="field__input" data-type="variant_metafield" data-productid="{{ variant.metafields.taopix.taopix_product_id }}" type="text" id="{{ fieldID }}" name="{{ fieldID }}"><br>
				{% endfor %}
			{% endfor %}

			<br/>
			<button type="submit" onClick="submitForm(event)" class="button" value="create" data-add-to-cart>{{ 'taopix.create_now' | t }}</button>
EOT;

		$this->pushAsset('snippets/tpx-product-dropdown.liquid', $template);
	}

	/**
	 * Pushes the tpx-product-button.liquid template to Shopify.
	 */
	public function pushTaopixProductButtonLiquid(): void
	{
		$template = <<<EOT
		<style>
			.tpxul {
			display: inline-block;
			list-style-type: none;
			margin-left: -50px;
			}
			.tpxli {
			display: inline-block;
			margin: 10px;
			}
			input[type="radio"] {
			display: none;
			}
		</style>
		
		<script type="text/javascript">
			function chooseBtn(e, btn) {
			document.getElementById('product_id').value = btn.value;
			btn.classList.remove("button--secondary");
			
			var selection = btn.value;
			var price = document.getElementById('price-'+selection).value;
			var descHTML = document.getElementById('description-'+selection).value;
			var priceField = document.getElementById('thePrice');
			if (priceField) {
				priceField.innerHTML = price;
			}
		
			var descriptionFields = document.getElementsByClassName('product__description rte');
		
			for (let i = 0; i < descriptionFields.length; i++) {
				descriptionFields[i].innerHTML = descHTML;
			} 
			
			var allVariantMetaFields = document.querySelectorAll("[data-type='variant_metafield']");
				
			for (let i = 0; i < allVariantMetaFields.length; i++) {
				allVariantMetaFields[i].style.display = 'none';
			}
		
			var variantmetafields = document.querySelectorAll("[data-productid='" + selection + "']");
		
			for (let i = 0; i < variantmetafields.length; i++) {
				variantmetafields[i].style.display = 'block';
			}
			}
			function submitForm(e) {
			e.preventDefault();
				document.getElementById('tpx-form').submit(); 
			}
			
		</script>
		
		<variant-radios class="no-js-hidden" data-section="{{ section.id }}" data-url="{{ product.url }}" {{ block.shopify_attributes }}>
			{%- for option in product.options_with_values -%}
			{%- if option.name == 'Layout' -%}
			<fieldset class="js product-form__input">
			<legend class="form__label">{{ option.name }}</legend>
			{%- for value in option.values -%}
				{% for variant in product.variants %}
					{% if variant.option1 == value %}
						{% assign product_id = variant.metafields.taopix.taopix_product_id %}
					{% endif %}
				{% endfor %}
			
				{% assign TitleArr = value | split: "(Layout Code: " %}
				{% assign DisplayTitle = TitleArr[0] %}
			<input onClick="chooseBtn(event,this)" type="radio" id="{{ section.id }}-{{ option.name }}-{{ forloop.index0 }}"
					name="{{ option.name }}"
					value="{{ product_id | escape }}"
					form="product-form-{{ section.id }}"
					{% if option.selected_value == value %}checked{% endif %}
					>
			<label for="{{ section.id }}-{{ option.name }}-{{ forloop.index0 }}">
				{{ DisplayTitle }}
			</label>
			{%- endfor -%}
			</fieldset>
			{%- endif -%}
			{%- endfor -%}
			<script type="application/json">
							{{ product.variants | json }}
			</script>
		</variant-radios>
		<br />
		
		{% for metafield in product.metafields.taopixcustomparam %}
		
			{% assign fieldID = "taopixcustomparam" | append: metafield[0] %}		
			{% assign fieldLabel = metafield[1] %}
		
		
			<label class="form__label" for="{{ fieldID }}">{{fieldLabel}}</label>
			<input class="field__input" type="text" id="{{ fieldID }}" name="{{ fieldID }}"><br>
		
		{% endfor %}
		
		<input type="hidden" value="" id="product_id" name="product_id" />
		
		{% for variant in product.variants %}
			<input type="hidden" id="price-{{ variant.metafields.taopix.taopix_product_id }}" value="{{ variant.price | money_with_currency }}" />
			<input type="hidden" id="description-{{ variant.metafields.taopix.taopix_product_id }}" value="{{ variant.metafields.taopix.taopix_description }}" />
		
			{% for metafield in variant.metafields.taopixcustomparam %}
				{% assign fieldID = "taopixcustomparam" | append: metafield[0] %}		
				{% assign fieldLabel = metafield[1] %}
		
		
				<label class="form__label" data-type="variant_metafield" data-productid="{{ variant.metafields.taopix.taopix_product_id }}" for="{{ fieldID }}">{{fieldLabel}}</label>
				<input class="field__input" data-type="variant_metafield" data-productid="{{ variant.metafields.taopix.taopix_product_id }}" type="text" id="{{ fieldID }}" name="{{ fieldID }}"><br>
			{% endfor %}
		{% endfor %}
		
		<br />
		<button type="submit" onClick="submitForm(event)" class="button" value="create" data-add-to-cart>{{ 'taopix.create_now' | t }}</button>
EOT;

		$this->pushAsset('snippets/tpx-product-button.liquid', $template);
	}

	/**
	 * Pushes the tpx-cart.liquid template to Shopify.
	 */
	public function pushTaopixCartLiquid(): void
	{
		$template = <<<EOT
		<style type="text/css">
			.tpx_component {
				font-size: 14px;
			}
			.tpx_component,
			.tpx_metadata {
				padding-left: 10px;
			}
			.tpx_componentitem {
				margin-bottom: 20px;
				font-weight: bold;
			}
			.tpx_metadataitem {
				color: #999;
				font-weight: normal;
			}
			.tpx_description {
				border-width: 0px;
				border-color: #bfbfbf40;
			}
			.tpx_details {
				width:100%;
				border: 0;
				box-shadow: 0;
			}
			.cart-item {
				border-bottom: .1rem solid rgba(var(--color-foreground),.08);
			}
			</style>

			<script type="text/javascript">
			
			{%if item.product.metafields.taopix.taopix_project_thumbnail.value %}
				
				var cartItem = document.getElementById("CartItem-{{ item.index | plus: 1 }}");
				var cartItemMedia = cartItem.querySelector('.cart-item__media');
			
				var newImage = document.createElement("img");
				newImage.src = "{{ item.product.metafields.taopix.taopix_project_thumbnail.value | replace: "files/", "" | file_url  }}";
				newImage.classList.add("cart-item__image");
				newImage.width = "150";
				newImage.height = "100";
				newImage.loading = "lazy";
			
				cartItemMedia.appendChild(newImage);
				
			{% endif %}
			
		</script>

		{% if customer.id != null %}
			{% assign customer_id = customer.id | append: shop.metafields.taopix.secret | sha1 %}
		{% else %}
			{% assign customer_id = '' %}
		{% endif %}

		{% if item.properties.__taopix_project_id %}
            <p>
                {{ item.properties.__taopix_project_name }}  
            </p>

            {{ item.product.description }}

              <ul class="list-unstyled">
                <li>
                    <a href="/tools/designer/edit?projectref={{item.properties.__taopix_project_id}}&customerid={{customer_id}}&l={{ shop.locale }}&tempid={{ item.product.id }}" class="link link--text">{{ 'taopix.edit_project' | t }}</a>
                </li>
                <li>
                    <a href="/tools/designer/duplicate?projectref={{item.properties.__taopix_project_id}}&customerid={{customer_id}}&projectname={{item.title | url_encode}}&l={{ shop.locale }}" class="link link--text">{{ 'taopix.duplicate_project' | t }}</a>
                </li>
			</ul>

		{% endif %}
EOT;

		$this->pushAsset('snippets/tpx-cart.liquid', $template);
	}

	/**
	 * Pushes the tpx-myprojects.liquid template to Shopify.
	 */
	public function pushTaopixMyProjectsLiquid(): void
	{
		$template = <<<EOT
		<div class="grid__item">
      		<h2>{{ 'taopix.my_projects' | t }}</h2>
      		<p>
			  <a href="/tools/designer/projects?customer={{ customer.id | append: shop.metafields.taopix.secret | sha1 }}&l={{ shop.locale }}">{{ 'taopix.manage_personalised_products' | t }}</a>
			</p>
    	</div>
EOT;

		$this->pushAsset('snippets/tpx-myprojects.liquid', $template);
	}

	/**
	 * Pushes the tpx-product-tile.liquid template to Shopify.
	 */
	public function pushTaopixProductTileLiquid(): void
	{
		$template = <<<EOT
		<style>
			.tpxul {
				display: inline-block;
				list-style-type: none;
			}
			.tpxli {
				display: inline-block;
			}
			input[type="radio"] {
				display: none;
			}
			.tpxlabel {
				border: 0px solid #fff;
				padding: 20px;
				display: block;
				position: relative;
				margin: 10px;
				cursor: pointer;
				-webkit-touch-callout: none;
				-webkit-user-select: none;
				-khtml-user-select: none;
				-moz-user-select: none;
				-ms-user-select: none;
				user-select: none;
			}
			.tpxlabel::before {
				background-color: white;
				color: white;
				content: " ";
				display: block;
				border-radius: 50%;
				border: 1px solid grey;
				position: absolute;
				top: -12px;
				left: -12px;
				width: 25px;
				height: 25px;
				text-align: center;
				line-height: 25px;
				transition-duration: 0.4s;
				transform: scale(0);
			}
			.tpxlabel .tpximg {
				width: 200px;
				transition-duration: 0.2s;
				transform-origin: 50% 50%;
			}
			.tpxtitle {
				display:block;
				width: 240px;
				height: 50px;
				min-height: 50px;
				max-height: 50px;
				overflow: hidden;
			}
			.thumbnails-wrapper {
				display:none; 
			}
			</style>

			<script type="text/javascript">  
			function addEventHandler(elem, eventType, handler) {
				if (elem.addEventListener)
				elem.addEventListener (eventType, handler, false);
				else if (elem.attachEvent)
				elem.attachEvent ('on' + eventType, handler); 
			}

			addEventHandler(document, 'DOMContentLoaded', function() {
				var theRadios = document.getElementsByName('Layout');

				for (let index = 0; index < theRadios.length; ++index) {

				addEventHandler(theRadios[index], 'change', function() {
					var selection = theRadios[index].value;
					document.getElementById('product_id').value = selection;
					var price = document.getElementById('price-'+selection).value;
					var descHTML = document.getElementById('description-'+selection).value;
					var priceField = document.getElementById('thePrice');
					if (priceField) {
					priceField.innerHTML = price;
					}

					var descriptionFields = document.getElementsByClassName('product__description rte');

					for (let i = 0; i < descriptionFields.length; i++) {
					descriptionFields[i].innerHTML = descHTML;
					} 
					
					var allVariantMetaFields = document.querySelectorAll("[data-type='variant_metafield']");
				
					for (let i = 0; i < allVariantMetaFields.length; i++) {
					allVariantMetaFields[i].style.display = 'none';
					}

					var variantmetafields = document.querySelectorAll("[data-productid='" + selection + "']");

					for (let i = 0; i < variantmetafields.length; i++) {
					variantmetafields[i].style.display = 'block';
					}
				});
				}

			});

			function submitForm(e) {
				e.preventDefault();
				document.getElementById('tpx-form').submit(); 
			}
			</script>


			<variant-radios class="no-js-hidden" data-section="{{ section.id }}" data-url="{{ product.url }}" {{ block.shopify_attributes }}>
			{%- for option in product.options_with_values -%}
			{%- if option.name == 'Layout' -%}
			<fieldset class="js product-form__input">
				<ul class="tpxul">
				{%- for value in option.values -%}
					{% for variant in product.variants %}
					{% if variant.option1 == value %}
						{% assign product_id = variant.metafields.taopix.taopix_product_id %}
						{% assign variantImg = variant.image.src | img_url: 'medium' %}
						{% assign fromPrice = variant.price | money %}
					{% endif %}
					{% endfor %}
				
					{% assign TitleArr = value | split: "(Layout Code: " %}
					{% assign DisplayTitle = TitleArr[0] %}
				<li class="tpxli">
				<input type="radio" id="{{ section.id }}-{{ option.name }}-{{ forloop.index0 }}"
						name="{{ option.name }}"
						value="{{ product_id | escape }}"
						form="product-form-{{ section.id }}"
						{% if option.selected_value == value %}checked{% endif %}
						>
				<label for="{{ section.id }}-{{ option.name }}-{{ forloop.index0 }}">
					<img class="tpximg" src="{{ variantImg }}" alt="{{ DisplayTitle }}"/>
					<span class="tpxtitle">
						{{ DisplayTitle }} <br/> 
						{{ 'taopix.price_from' | t }} {{ fromPrice }}
					</span>
				</label>
				</li>
				{%- endfor -%}
				</ul>
			</fieldset>
			{% endif %}
			{%- endfor -%}
			<script type="application/json">
								{{ product.variants | json }}
			</script>
			</variant-radios>

			{% for metafield in product.metafields.taopixcustomparam %}
			{% assign fieldID = "taopixcustomparam" | append: metafield[0] %}		
			{% assign fieldLabel = metafield[1] %}


			<label  class="form__label" for="{{ fieldID }}">{{fieldLabel}}</label>
			<input class="field__input" type="text" id="{{ fieldID }}" name="{{ fieldID }}"><br>
			{% endfor %}

			{% for variant in product.variants %}
				<input type="hidden" id="price-{{ variant.metafields.taopix.taopix_product_id }}" value="{{ variant.price | money_with_currency }}" />
				<input type="hidden" id="description-{{ variant.metafields.taopix.taopix_product_id }}" value="{{ variant.metafields.taopix.taopix_description }}" />

				{% for metafield in variant.metafields.taopixcustomparam %}
				{% assign fieldID = "taopixcustomparam" | append: metafield[0] %}		
				{% assign fieldLabel = metafield[1] %}


				<label class="form__label" data-type="variant_metafield" data-productid="{{ variant.metafields.taopix.taopix_product_id }}" for="{{ fieldID }}">{{fieldLabel}}</label>
				<input class="field__input" data-type="variant_metafield" data-productid="{{ variant.metafields.taopix.taopix_product_id }}" type="text" id="{{ fieldID }}" name="{{ fieldID }}"><br>
				{% endfor %}
			{% endfor %}

			<br/>
			<div style="clear:both;"></div>
			<input type="hidden" value="{{ product.selected_or_first_available_variant.metafields.taopix.taopix_product_id }}" id="product_id" name="product_id" />
			<button type="submit" onClick="submitForm(event)" class="button" value="create" data-add-to-cart>{{ 'taopix.create_now' | t }}</button>
EOT;

		$this->pushAsset('snippets/tpx-product-tile.liquid', $template);
	}	

	/**
	 * Pushes the product-variant-card.liquid template to Shopify.
	 */
	public function pushTaopixVariantCardLiquid(): void
	{
		$template = <<<EOT
		{{ 'component-rating.css' | asset_url | stylesheet_tag }}
		{%- assign varianturl = product_card_product.url | append: '&view=variant' -%}

		{% assign variantTitleArr = product_card_product.option1 | split: "(Layout Code: " %}
		{% assign variantDisplayTitle = variantTitleArr[0] %}

		<div class="card-wrapper">
			<div class="card-information">
				<div class="card-information__wrapper">
				{%- if show_vendor -%}
					<span class="visually-hidden">{{ 'accessibility.vendor' | t }}</span>
					<div class="caption-with-letter-spacing light">{{ product_card_product.vendor }}</div>
				{%- endif -%}

				{%- if product_card_product.featured_media -%}
					<h3 class="card-information__text h5">
					<a href="{{ varianturl | default: '#' }}" class="full-unstyled-link">
						{{ variantDisplayTitle | escape }}
                        {%- if product_card_product.option2 -%}
                      		<br/ >
                      		{{ product_card_product.option2 | escape }}
                      	{%- endif -%}
                      	{%- if product_card_product.option3 -%}
                      		<br/ >
                      		{{ product_card_product.option3 | escape }}
                      	{%- endif -%}
					</a>
					</h3>
				{%- endif -%}

				{% comment %} TODO: metafield {% endcomment %}
				<span class="caption-large light">{{ block.settings.description | escape }}</span>
				{%- if show_rating and product_card_product.metafields.reviews.rating.value != blank -%}
					{% liquid
					assign rating_decimal = 0 
					assign decimal = product_card_product.metafields.reviews.rating.value.rating | modulo: 1 
					if decimal >= 0.3 and decimal <= 0.7
						assign rating_decimal = 0.5
					elsif decimal > 0.7
						assign rating_decimal = 1
					endif 
					%}
					<div class="rating" role="img" aria-label="{{ 'accessibility.star_reviews_info' | t: rating_value: product_card_product.metafields.reviews.rating.value, rating_max: product_card_product.metafields.reviews.rating.value.scale_max }}">
					<span aria-hidden="true" class="rating-star color-icon-{{ settings.accent_icons }}" style="--rating: {{ product_card_product.metafields.reviews.rating.value.rating | floor }}; --rating-max: {{ product_card_product.metafields.reviews.rating.value.scale_max }}; --rating-decimal: {{ rating_decimal }};"></span>
					</div>
					<p class="rating-text caption">
					<span aria-hidden="true">{{ product_card_product.metafields.reviews.rating.value }} / {{ product_card_product.metafields.reviews.rating.value.scale_max }}</span>
					</p>
					<p class="rating-count caption">
					<span aria-hidden="true">({{ product_card_product.metafields.reviews.rating_count }})</span>
					<span class="visually-hidden">{{ product_card_product.metafields.reviews.rating_count }} {{ "accessibility.total_reviews" | t }}</span>
					</p>
				{%- endif -%}
				{% render 'price', product: product_card_product, price_class: '' %}
				</div>
			</div>

			<div class="card card--product{% if product_card_product.featured_media == nil %} card--text-only card--soft{% endif %}{% if product_card_product.featured_media != nil and show_image_outline %} card--outline{% endif %}" tabindex="-1">
				<div class="card__inner">
				{%- if product_card_product.featured_media -%}
					{%- liquid
					assign featured_media_aspect_ratio = product_card_product.featured_media.aspect_ratio

					if product_card_product.featured_media.aspect_ratio == nil
						assign featured_media_aspect_ratio = 1
					endif
					-%}

					<div{% if add_image_padding %} class="card__media-full-spacer"{% endif %}>
					<div class="media media--transparent media--{{ media_size }} media--hover-effect"
						{% if media_size == 'adapt' and product_card_product.featured_media %} style="padding-bottom: {{ 1 | divided_by: featured_media_aspect_ratio | times: 100 }}%;"{% endif %}
					>
						<img
						srcset="{%- if product_card_product.featured_media.width >= 165 -%}{{ product_card_product.featured_media | img_url: '165x' }} 165w,{%- endif -%}
							{%- if product_card_product.featured_media.width >= 360 -%}{{ product_card_product.featured_media | img_url: '360x' }} 360w,{%- endif -%}
							{%- if product_card_product.featured_media.width >= 533 -%}{{ product_card_product.featured_media | img_url: '533x' }} 533w,{%- endif -%}
							{%- if product_card_product.featured_media.width >= 720 -%}{{ product_card_product.featured_media | img_url: '720x' }} 720w,{%- endif -%}
							{%- if product_card_product.featured_media.width >= 940 -%}{{ product_card_product.featured_media | img_url: '940x' }} 940w,{%- endif -%}
							{%- if product_card_product.featured_media.width >= 1066 -%}{{ product_card_product.featured_media | img_url: '1066x' }} 1066w,{%- endif -%}
							{{ product_card_product.featured_media | img_url: 'master' }} {{ product_card_product.featured_media.width }}w"
						src="{{ product_card_product.featured_media | img_url: '533x' }}"
						sizes="(min-width: {{ settings.page_width }}px) {{ settings.page_width | minus: 130 | divided_by: 4 }}px, (min-width: 990px) calc((100vw - 130px) / 4), (min-width: 750px) calc((100vw - 120px) / 3), calc((100vw - 35px) / 2)"
						alt="{{ product_card_product.featured_media.alt | escape }}"
						loading="lazy"
						class="motion-reduce"
						width="{{ product_card_product.featured_media.width }}"
						height="{{ product_card_product.featured_media.height }}"
						>

						{%- if product_card_product.media[1] != nil and show_secondary_image -%}
						<img
							srcset="{%- if product_card_product.media[1].width >= 165 -%}{{ product_card_product.media[1] | img_url: '165x' }} 165w,{%- endif -%}
							{%- if product_card_product.media[1].width >= 360 -%}{{ product_card_product.media[1] | img_url: '360x' }} 360w,{%- endif -%}
							{%- if product_card_product.media[1].width >= 533 -%}{{ product_card_product.media[1] | img_url: '533x' }} 533w,{%- endif -%}
							{%- if product_card_product.media[1].width >= 720 -%}{{ product_card_product.media[1] | img_url: '720x' }} 720w,{%- endif -%}
							{%- if product_card_product.media[1].width >= 940 -%}{{ product_card_product.media[1] | img_url: '940x' }} 940w,{%- endif -%}
							{%- if product_card_product.media[1].width >= 1066 -%}{{ product_card_product.media[1] | img_url: '1066x' }} 1066w,{%- endif -%}
							{{ product_card_product.media[1] | img_url: 'master' }} {{ product_card_product.media[1].width }}w"
							src="{{ product_card_product.media[1] | img_url: '533x' }}"
							sizes="(min-width: {{ settings.page_width }}px) {{ settings.page_width | minus: 130 | divided_by: 4 }}px, (min-width: 990px) calc((100vw - 130px) / 4), (min-width: 750px) calc((100vw - 120px) / 3), calc((100vw - 35px) / 2)"
							alt="{{ product_card_product.media[1].alt | escape }}"
							loading="lazy"
							class="motion-reduce"
						width="{{ product_card_product.media[1].width }}"
						height="{{ product_card_product.media[1].height }}"
						>
						{%- endif -%}
					</div>
					</div>
				{%- else -%}
					<div class="card__content">
					<h2 class="card__text h3">
						<a href="{{ varianturl | default: '#' }}" class="full-unstyled-link">
						{{ variantDisplayTitle | escape }}
						</a>
					</h2>
					</div>
				{%- endif -%}

				<div class="card__badge">
					{%- if product_card_product.available == false -%}
					<span class="badge badge--bottom-left color-{{ settings.sold_out_badge_color_scheme }}">{{ 'products.product.sold_out' | t }}</span>
					{%- elsif product_card_product.compare_at_price > product_card_product.price and product_card_product.available -%}
					<span class="badge badge--bottom-left color-{{ settings.sale_badge_color_scheme }}">{{ 'products.product.on_sale' | t }}</span>
					{%- endif -%}
				</div>
				</div>
			</div>
		</div>
EOT;

		$this->pushAsset('snippets/product-variant-card.liquid', $template);
	}

	/**
	 * Pushes the tpx-product-form-variant.liquid template to Shopify.
	 */
	public function pushTaopixProductFormVariant(): void
	{
		$template = <<<EOT
		<style type="text/css">
          .field__input { width:250px; padding: 1.5rem; margin-bottom: 1.6rem; }
        </style>

		{% if customer.id != null %}
			{% assign customer_id = customer.id | append: shop.metafields.taopix.secret | sha1 %}
		{% else %}
			{% assign customer_id = '' %}
		{% endif %}

		<form action="/tools/designer/create" method="POST" id="tpx-form" class="{{ form_classes }}">
			<input type="hidden" name="customerid" value="{{ customer_id }}" />
			<input type="hidden" name="customeremail" value="{{ customer.email }}" />
			<input type="hidden" name="customerfirstname" value="{{ customer.first_name }}" />
			<input type="hidden" name="customerlastname" value="{{ customer.last_name }}" />
			<input type="hidden" name="l" value="{{ shop.locale }}" />
          
          	{% render 'tpx-variant' product: product, block: block %}
		</form>
EOT;

		$this->pushAsset('snippets/tpx-product-form-variant.liquid', $template);
	}

	/**
	 * Pushes the tpx-variant.liquid template to Shopify.
	 */
	public function pushTaopixVariant(): void
	{
		$template = <<<EOT
		<script type="text/javascript">   
			document.addEventListener("DOMContentLoaded", function()
										{
				var selection = document.getElementById('product_id').value;
				var descriptionFields = document.getElementsByClassName('product__description rte');    
				var descHTML = document.getElementById('description-'+selection).value;

				if (descHTML != ''){   
				for (let i = 0; i < descriptionFields.length; i++) {
					descriptionFields[i].innerHTML = descHTML;
				}   
				}
			});

			function submitForm(e) {
				e.preventDefault();
				document.getElementById('tpx-form').submit(); 
			}
		</script>

		<input type="hidden" id="product_id" name="product_id" value="{{ product.selected_or_first_available_variant.metafields.taopix.taopix_product_id }}"/>

		<input type="hidden" id="description-{{ product.selected_or_first_available_variant.metafields.taopix.taopix_product_id}}" value="{{ product.selected_or_first_available_variant.metafields.taopix.taopix_description }}"/>

		{%- for option in product.options_with_values -%}
			{%- if option.name == 'pagestyle' -%}
				<input type="hidden" id="taopixcustomparam_{{option.name}}" name="taopixcustomparam_{{option.name}}" value="{{option.selected_value}}">
			{%- endif -%}
		{%- endfor -%}

		{% for metafield in product.metafields.taopixcustomparam %}

		{% assign fieldID = "taopixcustomparam" | append: metafield[0] %}		
		{% assign fieldLabel = metafield[1] %}


		<label class="form__label" for="{{ fieldID }}">{{fieldLabel}}</label>
		<input class="field__input" type="text" id="{{ fieldID }}" name="{{ fieldID }}"><br>

		{% endfor %}

		{% for metafield in product.selected_or_first_available_variant.metafields.taopixcustomparam %}
		{% assign fieldID = "taopixcustomparam" | append: metafield[0] %}		
		{% assign fieldLabel = metafield[1] %}


		<label class="form__label" data-type="variant_metafield" data-productid="{{ variant.metafields.taopix.taopix_product_id }}" for="{{ fieldID }}">{{fieldLabel}}</label>
		<input class="field__input" data-type="variant_metafield" data-productid="{{ variant.metafields.taopix.taopix_product_id }}" type="text" id="{{ fieldID }}" name="{{ fieldID }}"><br>
		{% endfor %}

		<button type="submit" onClick="submitForm(event)" class="button" value="create" data-add-to-cart>{{ 'taopix.create_now' | t }}</button>

EOT;

		$this->pushAsset('snippets/tpx-variant.liquid', $template);
	}	

	/**
	 * Pushes the main-list-collections-byvariant.liquid template to Shopify.
	 */
	public function pushTaopixCollectionsByVariant(): void
	{
		$template = <<<EOT
		{{ 'template-collection.css' | asset_url | stylesheet_tag }}
		{{ 'component-loading-overlay.css' | asset_url | stylesheet_tag }}
		{{ 'component-card.css' | asset_url | stylesheet_tag }}
		{{ 'component-price.css' | asset_url | stylesheet_tag }}
		{{ 'component-product-grid.css' | asset_url | stylesheet_tag }}
		
		<link rel="preload" href="{{ 'component-rte.css' | asset_url }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
		
		<noscript>{{ 'component-rte.css' | asset_url | stylesheet_tag }}</noscript>
		
		{%- if section.settings.enable_filtering or section.settings.enable_sorting -%}
		  {{ 'component-facets.css' | asset_url | stylesheet_tag }}
		  <script src="{{ 'facets.js' | asset_url }}" defer="defer"></script>
		
		  <div class="page-width" id="main-collection-filters" data-id="{{ section.id }}">
			{% render 'facets', results: collection, enable_filtering: section.settings.enable_filtering, enable_sorting: section.settings.enable_sorting, collapse_on_larger_devices: section.settings.collapse_on_larger_devices %}
		  </div>
		{%- endif -%}
		
		<div id="ProductGridContainer">
		  
			{%- if collection.products.size == 0 -%}
			  <div class="collection collection--empty page-width" id="product-grid" data-id="{{ section.id }}">
				<div class="loading-overlay gradient"></div>
				<div class="title-wrapper center">
				  <h2 class="title title--primary">
					{{ 'sections.collection_template.empty' | t }}<br>
					{{ 'sections.collection_template.use_fewer_filters_html' | t: link: collection.url, class: "underlined-link link" }}
				  </h2>
				</div>
			  </div>
			{%- else -%}
			  <div class="collection page-width">
				<div class="loading-overlay gradient"></div>
		
				<ul id="product-grid" data-id="{{ section.id }}" class="grid grid--2-col negative-margin product-grid grid--3-col-tablet grid--one-third-max grid--4-col-desktop grid--quarter-max">
				  
				  
				  {% assign productList = "" | split: "" %}
				  {% assign counter = 0 %}
				  {% assign variantLimit = 16 %}
		
				  {% for product in collection.products %}
					  {%- unless product.tags contains 'taopix_hidden_product' -%}
						{% assign productList = productList | concat: product.variants %}
						{% assign counter = counter | plus: product.variants.size %}
					  {%- endunless -%}
				  {% endfor %}
		
				  {% assign maxSize = productList.size %}
		
				  {% assign start = current_page | minus: 1 | times: variantLimit %}
				  {% assign end = current_page | times: variantLimit | minus: 1 %}
		
				  {% if end > maxSize %}
					{% assign end = maxSize %}
				  {% endif %}
		
				  {% assign slice = "" | split: "" %}
				  {% for i in (start..end) %}
					  {%if productList[i] %}
					{% assign temp = productList[i] | where: "available", true %}
					{% assign slice = slice | concat: temp %}
					  {% endif %}
				  {% endfor %}
		
				  {% assign productList = slice %}
					  {%- for variant in productList -%}
						<li class="grid__item">
						  {% render 'product-variant-card',
							product_card_product: variant,
							media_size: section.settings.image_ratio,
							show_secondary_image: section.settings.show_secondary_image,
							add_image_padding: section.settings.add_image_padding,
							show_vendor: section.settings.show_vendor,
							show_image_outline: section.settings.show_image_outline,
							show_rating: section.settings.show_rating
						  %}
						</li>
					  {%- endfor -%}
				</ul>
				</div>
		  </div>
		<link rel="stylesheet" href="{{ 'component-pagination.css' | asset_url }}" media="print" onload="this.media='all'">
		<div class="pagination-wrapper">
			<nav class="pagination" role="navigation" aria-label="{{ 'general.pagination.label' | t }}">
			  <ul class="pagination__list list-unstyled" role="list">
				
				{% unless current_page == 1 %}
					<li>
					  <a href="?page={{current_page | minus: 1}}" class="pagination__item pagination__item--next pagination__item-arrow link motion-reduce" aria-label="{{ 'general.pagination.previous' | t }}">
						{% render 'icon-caret' %}
					  </a>
					</li>
					<li>
					  <a class="pagination__item link" href="?page={{current_page | minus: 1}}">
						  {{current_page | minus: 1}}  
					  </a>
					</li>
				 {% endunless %}
				
				<li>
					<span class="pagination__item pagination__item--current" aria-current="page" aria-label="{{ 'general.pagination.page' | t: number: current_page }}">{{current_page}}</span>
				</li>
				
				 {% unless end >= maxSize %}
					<li>
					  <a class="pagination__item link" href="?page={{current_page | plus: 1}}"> 
						{{current_page | plus: 1}}
					  </a>
					</li>
					<li>
					  <a href="?page={{current_page | plus: 1}}" class="pagination__item pagination__item--prev pagination__item-arrow link motion-reduce" aria-label="{{ 'general.pagination.next' | t }}" >
						{%- render 'icon-caret' -%}
					  </a>
					</li>
					
				 {% endunless %}
			   
			  </ul>
		  </nav>
		</div>
		
			{%- endif -%}
		
		
		{% schema %}
		{
		  "name": "t:sections.main-collection-product-grid.name",
		  "class": "spaced-section collection-grid-section",
		  "settings": [
			{
			  "type": "range",
			  "id": "products_per_page",
			  "min": 8,
			  "max": 24,
			  "step": 4,
			  "default": 16,
			  "label": "t:sections.main-collection-product-grid.settings.products_per_page.label"
			},
			{
			  "type": "header",
			  "content": "t:sections.main-collection-product-grid.settings.header__3.content"
			},
			{
			  "type": "select",
			  "id": "image_ratio",
			  "options": [
				{
				  "value": "adapt",
				  "label": "t:sections.main-collection-product-grid.settings.image_ratio.options__1.label"
				},
				{
				  "value": "portrait",
				  "label": "t:sections.main-collection-product-grid.settings.image_ratio.options__2.label"
				},
				{
				  "value": "square",
				  "label": "t:sections.main-collection-product-grid.settings.image_ratio.options__3.label"
				}
			  ],
			  "default": "adapt",
			  "label": "t:sections.main-collection-product-grid.settings.image_ratio.label"
			},
			{
			  "type": "checkbox",
			  "id": "show_secondary_image",
			  "default": false,
			  "label": "t:sections.main-collection-product-grid.settings.show_secondary_image.label"
			},
			{
			  "type": "checkbox",
			  "id": "add_image_padding",
			  "default": false,
			  "label": "t:sections.main-collection-product-grid.settings.add_image_padding.label"
			},
			{
			  "type": "checkbox",
			  "id": "show_image_outline",
			  "default": true,
			  "label": "t:sections.main-collection-product-grid.settings.show_image_outline.label"
			},
			{
			  "type": "checkbox",
			  "id": "show_vendor",
			  "default": false,
			  "label": "t:sections.main-collection-product-grid.settings.show_vendor.label"
			},
			{
			  "type": "checkbox",
			  "id": "show_rating",
			  "default": false,
			  "label": "t:sections.main-collection-product-grid.settings.show_rating.label",
			  "info": "t:sections.main-collection-product-grid.settings.show_rating.info"
			},
			{
			  "type": "header",
			  "content": "t:sections.main-collection-product-grid.settings.header__1.content"
			},
			{
			  "type": "checkbox",
			  "id": "enable_filtering",
			  "default": true,
			  "label": "t:sections.main-collection-product-grid.settings.enable_filtering.label",
			  "info": "t:sections.main-collection-product-grid.settings.enable_filtering.info"
			},
			{
			  "type": "checkbox",
			  "id": "enable_sorting",
			  "default": true,
			  "label": "t:sections.main-collection-product-grid.settings.enable_sorting.label"
			},
			{
			  "type": "checkbox",
			  "id": "collapse_on_larger_devices",
			  "default": false,
			  "label": "t:sections.main-collection-product-grid.settings.collapse_on_larger_devices.label"
			}
		  ]
		}
		{% endschema %}		
EOT;

		$this->pushAsset('sections/main-list-collections-byvariant.liquid', $template);
	}

	/**
	 * Pushes the sections/main-product-variant.liquid template to Shopify.
	 */
	public function pushTaopixMainProductVariant(): void
	{
		$template = <<<EOT
		{% comment %}theme-check-disable TemplateLength{% endcomment %}
		{{ 'section-main-product.css' | asset_url | stylesheet_tag }}
		{{ 'component-accordion.css' | asset_url | stylesheet_tag }}
		{{ 'component-price.css' | asset_url | stylesheet_tag }}
		{{ 'component-rte.css' | asset_url | stylesheet_tag }}
		{{ 'component-slider.css' | asset_url | stylesheet_tag }}
		{{ 'component-rating.css' | asset_url | stylesheet_tag }}
		{{ 'component-loading-overlay.css' | asset_url | stylesheet_tag }}
		
		<link rel="stylesheet" href="{{ 'component-deferred-media.css' | asset_url }}" media="print" onload="this.media='all'">
		
		<script src="{{ 'product-form.js' | asset_url }}" defer="defer"></script>
		
		{%- assign first_3d_model = product.media | where: "media_type", "model" | first -%}
		{%- if first_3d_model -%}
		  {{ 'component-product-model.css' | asset_url | stylesheet_tag }}
		  <link id="ModelViewerStyle" rel="stylesheet" href="https://cdn.shopify.com/shopifycloud/model-viewer-ui/assets/v1.0/model-viewer-ui.css" media="print" onload="this.media='all'">
		  <link id="ModelViewerOverride" rel="stylesheet" href="{{ 'component-model-viewer-ui.css' | asset_url }}" media="print" onload="this.media='all'">
		{%- endif -%}
		
		<section class="page-width">
		  <div class="product grid grid--1-col {% if product.media.size > 0 %}grid--2-col-tablet{% else %}product--no-media{% endif %}">
			<div class="grid__item product__media-wrapper">
			  <slider-component class="slider-mobile-gutter">
				<a class="skip-to-content-link button visually-hidden" href="#ProductInfo-{{ section.id }}">
				  {{ "accessibility.skip_to_product_info" | t }}
				</a>
				<ul class="product__media-list grid grid--peek list-unstyled slider slider--mobile" role="list">
				  {%- assign variant_images = product.images | where: 'attached_to_variant?', true | map: 'src' -%}
				  {%- if product.selected_or_first_available_variant.featured_media != null -%}
					{%- assign media = product.selected_or_first_available_variant.featured_media -%}
					<li class="product__media-item grid__item slider__slide{% if media.media_type != 'image' %} product__media-item--full{% endif %}{% if section.settings.hide_variants and variant_images contains media.src %} product__media-item--variant{% endif %}" data-media-id="{{ section.id }}-{{ media.id }}">
					  {% render 'product-thumbnail', media: media, position: 'featured', loop: section.settings.enable_video_looping, modal_id: section.id, xr_button: true %}
					</li>
				  {%- endif -%}
				  {%- for media in product.media -%}
					{%- unless media.id == product.selected_or_first_available_variant.featured_media.id -%}
					  <li class="product__media-item grid__item slider__slide{% if media.media_type != 'image' %} product__media-item--full{% endif %}{% if section.settings.hide_variants and variant_images contains media.src %} product__media-item--variant{% endif %}" data-media-id="{{ section.id }}-{{ media.id }}">
						{% render 'product-thumbnail', media: media, position: forloop.index, loop: section.settings.enable_video_looping, modal_id: section.id, xr_button: true %}
					  </li>
					{%- endunless -%}
				  {%- endfor -%}
				</ul>
				<div class="slider-buttons no-js-hidden{% if product.media.size < 2 %} small-hide{% endif %}">
				  <button type="button" class="slider-button slider-button--prev" name="previous" aria-label="{{ 'accessibility.previous_slide' | t }}">{% render 'icon-caret' %}</button>
				  <div class="slider-counter caption">
					<span class="slider-counter--current">1</span>
					<span aria-hidden="true"> / </span>
					<span class="visually-hidden">{{ 'accessibility.of' | t }}</span>
					<span class="slider-counter--total">{% if section.settings.hide_variants %}{{ product.media.size | minus: variant_images.size | plus: 1 }}{% else %}{{ product.media.size }}{% endif %}</span>
				  </div>
				  <button type="button" class="slider-button slider-button--next" name="next" aria-label="{{ 'accessibility.next_slide' | t }}">{% render 'icon-caret' %}</button>
				</div>
			  </slider-component>
			  {%- if first_3d_model -%}
				<button
				  class="button button--full-width product__xr-button"
				  type="button"
				  aria-label="{{ 'products.product.xr_button_label' | t }}"
				  data-shopify-xr
				  data-shopify-model3d-id="{{ first_3d_model.id }}"
				  data-shopify-title="{{ product.title | escape }}"
				  data-shopify-xr-hidden
				>
				  {% render 'icon-3d-model' %}
				  {{ 'products.product.xr_button' | t }}
				</button>
			  {%- endif -%}
			</div>
			<div class="product__info-wrapper grid__item">
			  
			  <div id="ProductInfo-{{ section.id }}" class="product__info-container{% if section.settings.enable_sticky_info %} product__info-container--sticky{% endif %}">
				{%- assign product_form_id = 'product-form-' | append: section.id -%}
				{%- for block in section.blocks -%}
				  {%- case block.type -%}
				  {%- when '@app' -%}
					{% render block %}
				  {%- when 'text' -%}
					<p class="product__text{% if block.settings.text_style == 'uppercase' %} caption-with-letter-spacing{% elsif block.settings.text_style == 'subtitle' %} subtitle{% endif %}" {{ block.shopify_attributes }}>
					  {{- block.settings.text -}}
					</p>
				  {%- when 'title' -%}
					<h1 class="product__title" {{ block.shopify_attributes }}>
					  {{ product.title | escape }}
					</h1>
				  {%- when 'price' -%}
					<div class="no-js-hidden" id="price-{{ section.id }}" {{ block.shopify_attributes }}>
					  {%- render 'price', product: product, use_variant: true, show_badges: true, price_class: 'price--large' -%}
					</div>
					{%- if shop.taxes_included or shop.shipping_policy.body != blank -%}
					  <div class="product__tax caption rte">
						{%- if shop.taxes_included -%}
						  {{ 'products.product.include_taxes' | t }}
						{%- endif -%}
						{%- if shop.shipping_policy.body != blank -%}
						  {{ 'products.product.shipping_policy_html' | t: link: shop.shipping_policy.url }}
						{%- endif -%}
					  </div>
					{%- endif -%}
					<div {{ block.shopify_attributes }}>
					  {%- form 'product', product, id: 'product-form-installment', class: 'installment caption-large' -%}
						<input type="hidden" name="id" value="{{ product.selected_or_first_available_variant.id }}">
						{{ form | payment_terms }}
					  {%- endform -%}
					</div>
				  {%- when 'description' -%}
					{%- if product.description != blank -%}
					  <div class="product__description rte">
						{{ product.description }}
					  </div>
					{%- endif -%}
				  {%- when 'custom_liquid' -%}
					{{ block.settings.custom_liquid }}
				  {%- when 'collapsible_tab' -%}
					<div class="product__accordion accordion" {{ block.shopify_attributes }}>
					  <details>
						<summary>
						  <div class="summary__title">
							{% render 'icon-accordion', icon: block.settings.icon %}
							<h2 class="h4 accordion__title">
							  {{ block.settings.heading | default: block.settings.page.title }}
							</h2>
						  </div>
						  {% render 'icon-caret' %}
						</summary>
						<div class="accordion__content rte">
						  {{ block.settings.content }}
						  {{ block.settings.page.content }}
						</div>
					  </details>
					</div>
				  {%- when 'quantity_selector' -%}
					{% unless product.tags contains 'taopix' or product.tags contains 'taopix_hidden_product' %}
					<div class="product-form__input product-form__quantity" {{ block.shopify_attributes }}>
					  <label class="form__label" for="Quantity-{{ section.id }}">
						{{ 'products.product.quantity.label' | t }}
					  </label>
					  
					  <quantity-input class="quantity">
						<button class="quantity__button no-js-hidden" name="minus" type="button">
						  <span class="visually-hidden">{{ 'products.product.quantity.decrease' | t: product: product.title | escape }}</span>
						  {% render 'icon-minus' %}
						</button>
						<input class="quantity__input"
							type="number"
							name="quantity"
							id="Quantity-{{ section.id }}"
							min="1"
							value="1"
							form="product-form-{{ section.id }}"
						  >
						<button class="quantity__button no-js-hidden" name="plus" type="button">
						  <span class="visually-hidden">{{ 'products.product.quantity.increase' | t: product: product.title | escape }}</span>
						  {% render 'icon-plus' %}
						</button>
					  </quantity-input>
					</div>
					{% endunless %}
				  {%- when 'popup' -%}
					  <modal-opener class="product-popup-modal__opener no-js-hidden" data-modal="#PopupModal-{{ block.id }}" {{ block.shopify_attributes }}>
						<button id="ProductPopup-{{ block.id }}" class="product-popup-modal__button link" type="button" aria-haspopup="dialog">{{ block.settings.text | default: block.settings.page.title }}</button>
					  </modal-opener>
					  <a href="{{ block.settings.page.url }}" class="product-popup-modal__button link no-js">{{ block.settings.text }}</a>
				  {%- when 'share' -%}
					{% unless product.tags contains 'taopix_hidden_product' %}
					<share-button class="share-button" {{ block.shopify_attributes }}>
					  <button class="share-button__button hidden">
						{% render 'icon-share' %}
						{{ block.settings.share_label | escape }}
					  </button>
					  <details>
						<summary class="share-button__button">
						  {% render 'icon-share' %}
						  {{ block.settings.share_label | escape }}
						</summary>
						<div id="Product-share-{{ section.id }}" class="share-button__fallback motion-reduce">
						  <div class="field">
							<span id="ShareMessage-{{ section.id }}" class="share-button__message hidden" role="status">
							</span>
							<input type="text"
								  class="field__input"
								  id="url"
								  value="{{ shop.url | append: product.url }}"
								  placeholder="{{ 'general.share.share_url' | t }}"
								  onclick="this.select();"
								  readonly
							>
							<label class="field__label" for="url">{{ 'general.share.share_url' | t }}</label>
						  </div>
						  <button class="share-button__close hidden no-js-hidden">
							{% render 'icon-close' %}
							<span class="visually-hidden">{{ 'general.share.close' | t }}</span>
						  </button>
						  <button class="share-button__copy no-js-hidden">
							{% render 'icon-clipboard' %}
							<span class="visually-hidden">{{ 'general.share.copy_to_clipboard' | t }}</span>
						  </button>
						</div>
					  </details>
					</share-button>
					{% endunless %}
					<script src="{{ 'share.js' | asset_url }}" defer="defer"></script>
				  {%- when 'variant_picker' -%}
					{% unless product.tags contains 'taopix' %}
					{%- unless product.has_only_default_variant -%}
					  {%- if block.settings.picker_type == 'button' -%}
						<variant-radios class="no-js-hidden" data-section="{{ section.id }}" data-url="{{ product.url }}" {{ block.shopify_attributes }}>
						  {%- for option in product.options_with_values -%}
							  <fieldset class="js product-form__input">
								<legend class="form__label">{{ option.name }}</legend>
								{%- for value in option.values -%}
								  <input type="radio" id="{{ section.id }}-{{ option.name }}-{{ forloop.index0 }}"
										name="{{ option.name }}"
										value="{{ value | escape }}"
										form="product-form-{{ section.id }}"
										{% if option.selected_value == value %}checked{% endif %}
								  >
								  <label for="{{ section.id }}-{{ option.name }}-{{ forloop.index0 }}">
									{{ value }}
								  </label>
								{%- endfor -%}
							  </fieldset>
						  {%- endfor -%}
						  <script type="application/json">
							{{ product.variants | json }}
						  </script>
						</variant-radios>
					  {%- else -%}
						<variant-selects class="no-js-hidden" data-section="{{ section.id }}" data-url="{{ product.url }}" {{ block.shopify_attributes }}>
						  {%- for option in product.options_with_values -%}
							<div class="product-form__input product-form__input--dropdown">
							  <label class="form__label" for="Option-{{ section.id }}-{{ forloop.index0 }}">
								{{ option.name }}
							  </label>
							  <div class="select">
								<select id="Option-{{ section.id }}-{{ forloop.index0 }}"
								  class="select__select"
								  name="options[{{ option.name | escape }}]"
								>
								  {%- for value in option.values -%}
									<option value="{{ value | escape }}" {% if option.selected_value == value %}selected="selected"{% endif %}>
									  {{ value }}
									</option>
								  {%- endfor -%}
								</select>
								{% render 'icon-caret' %}
							  </div>
							</div>
						  {%- endfor -%}
		
						  <script type="application/json">
							{{ product.variants | json }}
						  </script>
						</variant-selects>
					  {%- endif -%}
					{%- endunless -%}
				{%- endunless -%}
		
					<noscript class="product-form__noscript-wrapper-{{ section.id }}">
					  <div class="product-form__input{% if product.has_only_default_variant %} hidden{% endif %}">
						<label class="form__label" for="Variants-{{ section.id }}">{{ 'products.product.product_variants' | t }}</label>
						<div class="select">
						  <select name="id" id="Variants-{{ section.id }}" class="select__select" form="{{ product_form_id }}">
							{%- for variant in product.variants -%}
							  <option
								{% if variant == product.selected_or_first_available_variant %}selected="selected"{% endif %}
								{% if variant.available == false %}disabled{% endif %}
								value="{{ variant.id }}"
							  >
								{{ variant.title }}
								{%- if variant.available == false %} - {{ 'products.product.sold_out' | t }}{% endif %}
								- {{ variant.price | money | strip_html }}
							  </option>
							{%- endfor -%}
						  </select>
						  {% render 'icon-caret' %}
						</div>
					  </div>
					</noscript>
				  {%- when 'buy_buttons' -%}
					<div {{ block.shopify_attributes }}>
					  <product-form class="product-form">
						<div class="product-form__error-message-wrapper" role="alert" hidden>
						  <svg aria-hidden="true" focusable="false" role="presentation" class="icon icon-error" viewBox="0 0 13 13">
							<circle cx="6.5" cy="6.50049" r="5.5" stroke="white" stroke-width="2"/>
							<circle cx="6.5" cy="6.5" r="5.5" fill="#EB001B" stroke="#EB001B" stroke-width="0.7"/>
							<path d="M5.87413 3.52832L5.97439 7.57216H7.02713L7.12739 3.52832H5.87413ZM6.50076 9.66091C6.88091 9.66091 7.18169 9.37267 7.18169 9.00504C7.18169 8.63742 6.88091 8.34917 6.50076 8.34917C6.12061 8.34917 5.81982 8.63742 5.81982 9.00504C5.81982 9.37267 6.12061 9.66091 6.50076 9.66091Z" fill="white"/>
							<path d="M5.87413 3.17832H5.51535L5.52424 3.537L5.6245 7.58083L5.63296 7.92216H5.97439H7.02713H7.36856L7.37702 7.58083L7.47728 3.537L7.48617 3.17832H7.12739H5.87413ZM6.50076 10.0109C7.06121 10.0109 7.5317 9.57872 7.5317 9.00504C7.5317 8.43137 7.06121 7.99918 6.50076 7.99918C5.94031 7.99918 5.46982 8.43137 5.46982 9.00504C5.46982 9.57872 5.94031 10.0109 6.50076 10.0109Z" fill="white" stroke="#EB001B" stroke-width="0.7">
						  </svg>
						  <span class="product-form__error-message"></span>
						</div>
						{%- if product.tags contains 'taopix' -%}
						  
						  {% for tpxblock in section.blocks %}
							  {% if tpxblock.type == 'variant_picker' %}
								  {% assign theBlock = tpxblock %}
							  {% endif %}
						  
						  {% endfor %}
						  
							{%- render 'tpx-product-form-variant' product: product, block: theBlock -%}
						  
						{%- else -%}
						  
						  {%- if product.tags contains 'taopix_hidden_product' -%}             
						  
							 {% comment %}Don't allow access to buy from temp product{% endcomment %}
						  
						  {%- else -%}
		
							  {%- form 'product', product, id: product_form_id, class: 'form', novalidate: 'novalidate', data-type: 'add-to-cart-form' -%}
								<input type="hidden" name="id" value="{{ product.selected_or_first_available_variant.id }}" disabled>
								<div class="product-form__buttons">
								  <button
									type="submit"
									name="add"
									class="product-form__submit button button--full-width {% if block.settings.show_dynamic_checkout and product.selling_plan_groups == empty %}button--secondary{% else %}button--primary{% endif %}"
								  {% if product.selected_or_first_available_variant.available == false %}disabled{% endif %}
								  >
									  <span>
										{%- if product.selected_or_first_available_variant.available -%}
										  {{ 'products.product.add_to_cart' | t }}
										{%- else -%}
										  {{ 'products.product.sold_out' | t }}
										{%- endif -%}
									  </span>
									  <div class="loading-overlay__spinner hidden">
										<svg aria-hidden="true" focusable="false" role="presentation" class="spinner" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
										  <circle class="path" fill="none" stroke-width="6" cx="33" cy="33" r="30"></circle>
										</svg>
									  </div>
								  </button>
								  {%- if block.settings.show_dynamic_checkout -%}
									{{ form | payment_button }}
								  {%- endif -%}
								</div>
							   {%- endform -%}
						  
						  {%- endif -%}
						{%- endif -%}
		
					  </product-form>
		
					  {{ 'component-pickup-availability.css' | asset_url | stylesheet_tag }}
		
					  {%- assign pick_up_availabilities = product.selected_or_first_available_variant.store_availabilities | where: 'pick_up_enabled', true -%}
		
					  <pickup-availability class="product__pickup-availabilities no-js-hidden"
						{% if product.selected_or_first_available_variant.available and pick_up_availabilities.size > 0 %} available{% endif %}
						data-base-url="{{ shop.url }}{{ routes.root_url }}"
						data-variant-id="{{ product.selected_or_first_available_variant.id }}"
						data-has-only-default-variant="{{ product.has_only_default_variant }}"
					  >
						<template>
						  <pickup-availability-preview class="pickup-availability-preview">
							{% render 'icon-unavailable' %}
							<div class="pickup-availability-info">
							  <p class="caption-large">{{ 'products.product.pickup_availability.unavailable' | t }}</p>
							  <button class="pickup-availability-button link link--text underlined-link">{{ 'products.product.pickup_availability.refresh' | t }}</button>
							</div>
						  </pickup-availability-preview>
						</template>
					  </pickup-availability>
					</div>
		
					<script src="{{ 'pickup-availability.js' | asset_url }}" defer="defer"></script>
				  {%- when 'rating' -%}
					{%- if product.metafields.reviews.rating.value != blank -%}
					  {% liquid
					   assign rating_decimal = 0
					   assign decimal = product.metafields.reviews.rating.value.rating | modulo: 1
					   if decimal >= 0.3 and decimal <= 0.7
						assign rating_decimal = 0.5
					  elsif decimal > 0.7
						assign rating_decimal = 1
					   endif
					  %}
					  <div class="rating" role="img" aria-label="{{ 'accessibility.star_reviews_info' | t: rating_value: product.metafields.reviews.rating.value, rating_max: product.metafields.reviews.rating.value.scale_max }}">
						<span aria-hidden="true" class="rating-star color-icon-{{ settings.accent_icons }}" style="--rating: {{ product.metafields.reviews.rating.value.rating | floor }}; --rating-max: {{ product.metafields.reviews.rating.value.scale_max }}; --rating-decimal: {{ rating_decimal }};"></span>
					  </div>
					  <p class="rating-text caption">
						<span aria-hidden="true">{{ product.metafields.reviews.rating.value }} / {{ product.metafields.reviews.rating.value.scale_max }}</span>
					  </p>
					  <p class="rating-count caption">
						<span aria-hidden="true">({{ product.metafields.reviews.rating_count }})</span>
						<span class="visually-hidden">{{ product.metafields.reviews.rating_count }} {{ "accessibility.total_reviews" | t }}</span>
					  </p>
					{%- endif -%}
				  {%- endcase -%}
				{%- endfor -%}
			  </div>
			</div>
		  </div>
		
		  <product-modal id="ProductModal-{{ section.id }}" class="product-media-modal media-modal">
			<div class="product-media-modal__dialog" role="dialog" aria-label="{{ 'products.modal.label' | t }}" aria-modal="true" tabindex="-1">
			  <button id="ModalClose-{{ section.id }}" type="button" class="product-media-modal__toggle" aria-label="{{ 'accessibility.close' | t }}">{% render 'icon-close' %}</button>
		
			  <div class="product-media-modal__content" role="document" aria-label="{{ 'products.modal.label' | t }}" tabindex="0">
				{%- liquid
				  if product.selected_or_first_available_variant.featured_media != null
					assign media = product.selected_or_first_available_variant.featured_media
					render 'product-media', media: media, loop: section.settings.enable_video_looping, variant_image: section.settings.hide_variants
				  endif
				-%}
		
				{%- for media in product.media -%}
				  {%- liquid
					if section.settings.hide_variants and variant_images contains media.src
					  assign variant_image = true
					else
					  assign variant_image = false
					endif
		
					unless media.id == product.selected_or_first_available_variant.featured_media.id
					  render 'product-media', media: media, loop: section.settings.enable_video_looping, variant_image: variant_image
					endunless
				  -%}
				{%- endfor -%}
			  </div>
			</div>
		  </product-modal>
		
		  {% assign popups = section.blocks | where: "type", "popup" %}
		  {%- for block in popups -%}
			<modal-dialog id="PopupModal-{{ block.id }}" class="product-popup-modal" {{ block.shopify_attributes }}>
			  <div role="dialog" aria-label="{{ block.settings.text }}" aria-modal="true" class="product-popup-modal__content" tabindex="-1">
				<button id="ModalClose-{{ block.id }}" type="button" class="product-popup-modal__toggle" aria-label="{{ 'accessibility.close' | t }}">{% render 'icon-close' %}</button>
				<div class="product-popup-modal__content-info">
				  <h1 class="h2">{{ block.settings.page.title }}</h1>
				  {{ block.settings.page.content }}
				</div>
			  </div>
			</modal-dialog>
		  {%- endfor -%}
		</section>
		
		{% javascript %}
		  class ProductModal extends ModalDialog {
			constructor() {
			  super();
			}
		
			hide() {
			  super.hide();
			}
		
			show(opener) {
			  super.show(opener);
			  this.showActiveMedia();
			}
		
			showActiveMedia() {
			  this.querySelectorAll(`[data-media-id]:not([data-media-id="\${this.openedBy.getAttribute("data-media-id")}"])`).forEach((element) => {
				  element.classList.remove('active');
				}
			  )
			  const activeMedia = this.querySelector(`[data-media-id="\${this.openedBy.getAttribute("data-media-id")}"]`);
			  const activeMediaTemplate = activeMedia.querySelector('template');
			  const activeMediaContent = activeMediaTemplate ? activeMediaTemplate.content : null;
			  activeMedia.classList.add('active');
			  activeMedia.scrollIntoView();
		
			  const container = this.querySelector('[role="document"]');
			  container.scrollLeft = (activeMedia.width - container.clientWidth) / 2;
		
			  if (activeMedia.nodeName == 'DEFERRED-MEDIA' && activeMediaContent && activeMediaContent.querySelector('.js-youtube'))
				activeMedia.loadContent();
			}
		  }
		
		  customElements.define('product-modal', ProductModal);
		{% endjavascript %}
		
		<script>
		  document.addEventListener('DOMContentLoaded', function() {
			function isIE() {
			  const ua = window.navigator.userAgent;
			  const msie = ua.indexOf('MSIE ');
			  const trident = ua.indexOf('Trident/');
		
			  return (msie > 0 || trident > 0);
			}
		
			if (!isIE()) return;
			const hiddenInput = document.querySelector('#{{ product_form_id }} input[name="id"]');
			const noScriptInputWrapper = document.createElement('div');
			const variantSwitcher = document.querySelector('variant-radios[data-section="{{ section.id }}"]') || document.querySelector('variant-selects[data-section="{{ section.id }}"]');
			noScriptInputWrapper.innerHTML = document.querySelector('.product-form__noscript-wrapper-{{ section.id }}').textContent;
			variantSwitcher.outerHTML = noScriptInputWrapper.outerHTML;
		
			document.querySelector('#Variants-{{ section.id }}').addEventListener('change', function(event) {
			  hiddenInput.value = event.currentTarget.value;
			});
		  });
		</script>
		
		{%- if first_3d_model -%}
		  <script type="application/json" id="ProductJSON-{{ product.id }}">
			{{ product.media | where: 'media_type', 'model' | json }}
		  </script>
		
		  <script src="{{ 'product-model.js' | asset_url }}" defer></script>
		{%- endif -%}
		
		<script type="application/ld+json">
		  {
			"@context": "http://schema.org/",
			"@type": "Product",
			"name": {{ product.title | json }},
			"url": {{ shop.url | append: product.url | json }},
			{%- if product.selected_or_first_available_variant.featured_media -%}
			  {%- assign media_size = product.selected_or_first_available_variant.featured_media.preview_image.width | append: 'x' -%}
			  "image": [
				{{ product.selected_or_first_available_variant.featured_media | img_url: media_size | prepend: "https:" | json }}
			  ],
			{%- endif -%}
			"description": {{ product.description | strip_html | json }},
			{%- if product.selected_or_first_available_variant.sku != blank -%}
			  "sku": {{ product.selected_or_first_available_variant.sku | json }},
			{%- endif -%}
			"brand": {
			  "@type": "Thing",
			  "name": {{ product.vendor | json }}
			},
			"offers": [
			  {%- for variant in product.variants -%}
				{
				  "@type" : "Offer",
				  {%- if variant.sku != blank -%}
					"sku": {{ variant.sku | json }},
				  {%- endif -%}
				  "availability" : "http://schema.org/{% if variant.available %}InStock{% else %}OutOfStock{% endif %}",
				  "price" : {{ variant.price | divided_by: 100.00 | json }},
				  "priceCurrency" : {{ cart.currency.iso_code | json }},
				  "url" : {{ shop.url | append: variant.url | json }}
				}{% unless forloop.last %},{% endunless %}
			  {%- endfor -%}
			]
		  }
		</script>
		
		{% schema %}
		{
		  "name": "t:sections.main-product.name",
		  "tag": "section",
		  "class": "product-section spaced-section",
		  "blocks": [
			{
			  "type": "@app"
			},
			{
			  "type": "text",
			  "name": "t:sections.main-product.blocks.text.name",
			  "settings": [
				{
				  "type": "text",
				  "id": "text",
				  "default": "Text block",
				  "label": "t:sections.main-product.blocks.text.settings.text.label"
				},
				{
				  "type": "select",
				  "id": "text_style",
				  "options": [
					{
					  "value": "body",
					  "label": "t:sections.main-product.blocks.text.settings.text_style.options__1.label"
					},
					{
					  "value": "subtitle",
					  "label": "t:sections.main-product.blocks.text.settings.text_style.options__2.label"
					},
					{
					  "value": "uppercase",
					  "label": "t:sections.main-product.blocks.text.settings.text_style.options__3.label"
					}
				  ],
				  "default": "body",
				  "label": "t:sections.main-product.blocks.text.settings.text_style.label"
				}
			  ]
			},
			{
			  "type": "title",
			  "name": "t:sections.main-product.blocks.title.name",
			  "limit": 1
			},
			{
			  "type": "price",
			  "name": "t:sections.main-product.blocks.price.name",
			  "limit": 1
			},
			{
			  "type": "quantity_selector",
			  "name": "t:sections.main-product.blocks.quantity_selector.name",
			  "limit": 1
			},
			{
			  "type": "variant_picker",
			  "name": "t:sections.main-product.blocks.variant_picker.name",
			  "limit": 1,
			  "settings": [
				{
				  "type": "select",
				  "id": "picker_type",
				  "options": [
					{
					  "value": "dropdown",
					  "label": "t:sections.main-product.blocks.variant_picker.settings.picker_type.options__1.label"
					},
					{
					  "value": "button",
					  "label": "t:sections.main-product.blocks.variant_picker.settings.picker_type.options__2.label"
					},
					{
					  "value": "tile",
					  "label": "t:sections.main-product.blocks.variant_picker.settings.picker_type.options__3.label"
					}
				  ],
				  "default": "button",
				  "label": "t:sections.main-product.blocks.variant_picker.settings.picker_type.label"
				}
			  ]
			},
			{
			  "type": "buy_buttons",
			  "name": "t:sections.main-product.blocks.buy_buttons.name",
			  "limit": 1,
			  "settings": [
				{
				  "type": "checkbox",
				  "id": "show_dynamic_checkout",
				  "default": true,
				  "label": "t:sections.main-product.blocks.buy_buttons.settings.show_dynamic_checkout.label",
				  "info": "t:sections.main-product.blocks.buy_buttons.settings.show_dynamic_checkout.info"
				}
			  ]
			},
			{
			  "type": "description",
			  "name": "t:sections.main-product.blocks.description.name",
			  "limit": 1
			},
			{
			  "type": "share",
			  "name": "t:sections.main-product.blocks.share.name",
			  "limit": 1,
			  "settings": [
				{
				  "type": "text",
				  "id": "share_label",
				  "label": "t:sections.main-product.blocks.share.settings.text.label",
				  "default": "Share"
				},
				{
				  "type": "paragraph",
				  "content": "t:sections.main-product.blocks.share.settings.featured_image_info.content"
				},
				{
				  "type": "paragraph",
				  "content": "t:sections.main-product.blocks.share.settings.title_info.content"
				}
			  ]
			},
			{
			  "type": "custom_liquid",
			  "name": "t:sections.main-product.blocks.custom_liquid.name",
			  "settings": [
				{
				  "type": "liquid",
				  "id": "custom_liquid",
				  "label": "t:sections.main-product.blocks.custom_liquid.settings.custom_liquid.label",
				  "info": "t:sections.main-product.blocks.custom_liquid.settings.custom_liquid.info"
				}
			  ]
			},
			{
			  "type": "collapsible_tab",
			  "name": "t:sections.main-product.blocks.collapsible_tab.name",
			  "settings": [
				{
				  "type": "text",
				  "id": "heading",
				  "default": "Collapsible tab",
				  "info": "t:sections.main-product.blocks.collapsible_tab.settings.heading.info",
				  "label": "t:sections.main-product.blocks.collapsible_tab.settings.heading.label"
				},
				{
				  "type": "richtext",
				  "id": "content",
				  "label": "t:sections.main-product.blocks.collapsible_tab.settings.content.label"
				},
				{
				  "type": "page",
				  "id": "page",
				  "label": "t:sections.main-product.blocks.collapsible_tab.settings.page.label"
				},
				{
				  "type": "select",
				  "id": "icon",
				  "options": [
					{
					  "value": "none",
					  "label": "t:sections.main-product.blocks.collapsible_tab.settings.icon.options__1.label"
					},
					{
					  "value": "box",
					  "label": "t:sections.main-product.blocks.collapsible_tab.settings.icon.options__2.label"
					},
					{
					  "value": "chat_bubble",
					  "label": "t:sections.main-product.blocks.collapsible_tab.settings.icon.options__3.label"
					},
					{
					  "value": "check_mark",
					  "label": "t:sections.main-product.blocks.collapsible_tab.settings.icon.options__4.label"
					},
					{
					  "value": "dryer",
					  "label": "t:sections.main-product.blocks.collapsible_tab.settings.icon.options__5.label"
					},
					{
					  "value": "eye",
					  "label": "t:sections.main-product.blocks.collapsible_tab.settings.icon.options__6.label"
					},
					{
					  "value": "heart",
					  "label": "t:sections.main-product.blocks.collapsible_tab.settings.icon.options__7.label"
					},
					{
					  "value": "iron",
					  "label": "t:sections.main-product.blocks.collapsible_tab.settings.icon.options__8.label"
					},
					{
					  "value": "leaf",
					  "label": "t:sections.main-product.blocks.collapsible_tab.settings.icon.options__9.label"
					},
					{
					  "value": "leather",
					  "label": "t:sections.main-product.blocks.collapsible_tab.settings.icon.options__10.label"
					},
					{
					  "value": "lock",
					  "label": "t:sections.main-product.blocks.collapsible_tab.settings.icon.options__11.label"
					},
					{
					  "value": "map_pin",
					  "label": "t:sections.main-product.blocks.collapsible_tab.settings.icon.options__12.label"
					},
					{
					  "value": "pants",
					  "label": "t:sections.main-product.blocks.collapsible_tab.settings.icon.options__13.label"
					},
					{
					  "value": "plane",
					  "label": "t:sections.main-product.blocks.collapsible_tab.settings.icon.options__14.label"
					},
					{
					  "value": "price_tag",
					  "label": "t:sections.main-product.blocks.collapsible_tab.settings.icon.options__15.label"
					},
					{
					  "value": "question_mark",
					  "label": "t:sections.main-product.blocks.collapsible_tab.settings.icon.options__16.label"
					},
					{
					  "value": "return",
					  "label": "t:sections.main-product.blocks.collapsible_tab.settings.icon.options__17.label"
					},
					{
					  "value": "ruler",
					  "label": "t:sections.main-product.blocks.collapsible_tab.settings.icon.options__18.label"
					},
					{
					  "value": "shirt",
					  "label": "t:sections.main-product.blocks.collapsible_tab.settings.icon.options__19.label"
					},
					{
					  "value": "shoe",
					  "label": "t:sections.main-product.blocks.collapsible_tab.settings.icon.options__20.label"
					},
					{
					  "value": "silhouette",
					  "label": "t:sections.main-product.blocks.collapsible_tab.settings.icon.options__21.label"
					},
					{
					  "value": "star",
					  "label": "t:sections.main-product.blocks.collapsible_tab.settings.icon.options__22.label"
					},
					{
					  "value": "truck",
					  "label": "t:sections.main-product.blocks.collapsible_tab.settings.icon.options__23.label"
					},
					{
					  "value": "washing",
					  "label": "t:sections.main-product.blocks.collapsible_tab.settings.icon.options__24.label"
					}
				  ],
				  "default": "check_mark",
				  "label": "t:sections.main-product.blocks.collapsible_tab.settings.icon.label"
				}
			  ]
			},
			{
			  "type": "popup",
			  "name": "t:sections.main-product.blocks.popup.name",
			  "settings": [
				{
				  "type": "text",
				  "id": "text",
				  "default": "Pop-up link text",
				  "label": "t:sections.main-product.blocks.popup.settings.link_label.label"
				},
				{
				  "id": "page",
				  "type": "page",
				  "label": "t:sections.main-product.blocks.popup.settings.page.label"
				}
			  ]
			},
			{
			  "type": "rating",
			  "name": "t:sections.main-product.blocks.rating.name",
			  "limit": 1,
			  "settings": [
				{
				  "type": "paragraph",
				  "content": "t:sections.main-product.blocks.rating.settings.paragraph.content"
				}
			  ]
			}
		  ],
		  "settings": [
			{
			  "type": "checkbox",
			  "id": "enable_sticky_info",
			  "default": true,
			  "label": "t:sections.main-product.settings.enable_sticky_info.label"
			},
			{
			  "type": "header",
			  "content": "t:sections.main-product.settings.header.content",
			  "info": "t:sections.main-product.settings.header.info"
			},
			{
			  "type": "checkbox",
			  "id": "hide_variants",
			  "default": false,
			  "label": "t:sections.main-product.settings.hide_variants.label"
			},
			{
			  "type": "checkbox",
			  "id": "enable_video_looping",
			  "default": false,
			  "label": "t:sections.main-product.settings.enable_video_looping.label"
			}
		  ]
		}
		{% endschema %}		
EOT;

		$this->pushAsset('sections/main-product-variant.liquid', $template);
	}	

	/**
	 * Pushes the templates/collection.byvariant.json template to Shopify.
	 */
	public function pushTaopixCollectionByVariantJSON(): void
	{
		$template = <<<EOT
		{
			"sections": {
				"main": {
					"type": "main-list-collections-byvariant",
					"settings": {}
				}
			},
			"order": [
				"main"
			]
		}
EOT;

		$this->pushAsset('templates/collection.byvariant.json', $template);
	}

	/**
	 * Pushes the templates/product.variant.json template to Shopify.
	 */
	public function pushTaopixProductVariantJSON(): void
	{
		$template = <<<EOT
		{
			"sections": {
				"main": {
				"type": "main-product-variant",
				"blocks": {
					"title": {
					"type": "title",
					"settings": {
					}
					},
					"subtitle": {
					"type": "text",
					"settings": {
						"text": "{{ product.metafields.descriptors.subtitle.value }}",
						"text_style": "subtitle"
					}
					},
					"price": {
					"type": "price",
					"settings": {
					}
					},
					"variant_picker": {
					"type": "variant_picker",
					"settings": {
						"picker_type": "dropdown"
					}
					},
					"quantity_selector": {
					"type": "quantity_selector",
					"settings": {
					}
					},
					"buy_buttons": {
					"type": "buy_buttons",
					"settings": {
						"show_dynamic_checkout": true
					}
					},
					"description": {
					"type": "description",
					"settings": {
					}
					},
					"share": {
					"type": "share",
					"settings": {
						"share_label": "Share"
					}
					}
				},
				"block_order": [
					"title",
					"subtitle",
					"price",
					"variant_picker",
					"quantity_selector",
					"buy_buttons",
					"description",
					"share"
				],
				"settings": {
					"enable_sticky_info": true,
					"hide_variants": false,
					"enable_video_looping": false
				}
				},
				"product-recommendations": {
				"type": "product-recommendations",
				"settings": {
					"heading": "You may also like",
					"image_ratio": "adapt",
					"show_secondary_image": false,
					"add_image_padding": false,
					"show_image_outline": true,
					"show_vendor": false,
					"show_rating": false
				}
				}
			},
			"order": [
				"main",
				"product-recommendations"
			]
		}
EOT;

		$this->pushAsset('templates/product.variant.json', $template);
	}
}

