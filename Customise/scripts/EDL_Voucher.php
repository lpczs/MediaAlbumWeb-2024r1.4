<?php

require_once('../Utils/UtilsVoucher.php');

/* ==============================================================
 * Promotional Scripted Vouchers Examples
 *
 *   PROMOEXAMPLE001: Discount the price of the item by 10.00 if the item is configured with a black leather cover.
 *
 *   PROMOEXAMPLE002: Discount the line item by 5.00 if the item is configured with a slip case.
 *
 *
 * Scripted Vouchers Examples
 *
 *   SCRIPTED0001: Discount the line item by 5.00 if the item is configured with a slip case.
 *
 *   SCRIPTED0002: Set the price of textured paper to 10.00.
 *
 *   SCRIPTED0003: Discount the line item by 25% of the price of the metallic paper component,
 *                 if metallic paper is included on the item.
 *
 *   SCRIPTED0004: Offer a free bellissimo box if the item has both a white leather cover and a bellissimo box.
 *
 *   SCRIPTED0005: Apply a discount of 20.00 off the order total, if an item is configured with "Don't panic" embossing.
 *
 *   SCRIPTED0006: Discount the shipping by 10.00 if an item in the order is a grey leather covers and the item quantity is 3 or more.
 *
 *   SCRIPTED0007: Apply a specific discount based on the configuration of the line item, the first matching combination is used to calculate the discount.
 *                1. Free metallic paper with red leather cover.
 *                2. 5.00 off embossing with grey leather cover.
 *                3. Free textured paper.
 *
 *   SCRIPTED0008: Discount the shipping by 10.00 when ordering an item with a quantity greater than 2.
 *
 *   SCRIPTED0009: Reduce the price of a metallic paper to be the same price as the textured paper.
 *
 *   SCRIPTED0010: Discount the item, offering 10 free metallic paper pages.
 *
 *   SCRIPTED0011: Offer 2 free items if the quantity of the line is 5 or more (Buy 5 and get 2 free).
 *
 *   SCRIPTED0012: Offer free shipping when metallic paper is used.
 *
 *   SCRIPTED0013: Discount the 25.00 off order for 20.00, only if the cart has 1 item, or it includes specific product.
 *
 *   SCRIPTED0014: 10% off a photo print order which uses glossy paper of size 10cm x 15cm.
 *
 *   SCRIPTED0015: Return different discounts based on the quantity of the item ordered.
 *               1. 20.00 off order when quantity is more than 10.
 *               2. 5.00 off shipping when quantity is more than 7.
 *               3. 2.00 off item if quantity is more than 5.
 *
 *   SCRIPTED0016: Reduce the price of the first 30 matt pages of a photobook to 5.00. Extra pages are charged at regular price.
 *
 *   SCRIPTED0017: Offer a free photobook with gloss paper.
 *
 *   SCRIPTED0018: 20% off the item total (scripted version of a 'Product' voucher).
 *
 *   SCRIPTED0019: 30% off shipping total (scripted version of a 'Shipping' voucher).
 *
 *   SCRIPTED0020: 10% off order total (scripted version of a 'Total' voucher).
 *
 *   SCRIPTED0021: Discount the cheapest item in the order by 50%.
 *
 *   SCRIPTED0022: Offer new customers a discount of 50% off shipping and 25% off the items.
 *
 * ============================================================== */

class EDL_VoucherScriptObj extends _Voucher
{
    /* ==============================================================
     *
     *  This is the initial test to make sure the voucher code entered by the user can be applied to the order.
     *
     *  $pPromoCode: if the voucher is part of a promotion, test to see if the promotion can be applied to the order.
     *  $pVoucherCode: test to see if the specified scripted voucher can be applied to the order.
     *  $pCurrentLine: item to be checked against the criteria of the voucher.
     *
     *  Return Values - array containing if the voucher can be applied or not:
	 *		- valid: boolean (true or false) if the voucher can be applied or not.
	 *      - message: string holding a custom error message if required.
     *
     * ============================================================== */
    public static function validate($pPromoCode, $pVoucherCode, $pCurrentLine)
    {
        global $gSession;
        // Get the item information from the order using $pCurrentLine.
        $lineItem = self::getItemByLine($pCurrentLine);

        // Set a default return value for use if no useable scripted vouchers are found.
        $returnValue = array('valid' => false, 'message' => '');

        // Is a promotion voucher being used?
        if ($pPromoCode != '')
        {
            // A promotion voucher has been used.
			// Test Voucher codes to determine which has been used.
            switch ($pPromoCode)
            {
                case 'YATE202010' :
                case 'YATE1020TEST' : 
                    {

                        $productCode = $lineItem['itemproductcode'];
    
                        //120 x 240mm 水晶禮盒連印相 $479
                        if (preg_match('/^Y479[A-Z0-9]{10}$/',$pVoucherCode) && $productCode == 'DVALPVCBABY03-8X8') {
                            $returnValue['valid'] = true;
                        }
    
                         //120 x 120mm 水晶禮盒連印相 $369
                         if (preg_match('/^Y369[A-Z0-9]{5}$/',$pVoucherCode) && $productCode == 'CRYSTAL_2020_120X120') {
                            $returnValue['valid'] = true;
                        }
    
                        //380 x 380mm 4格布相架連印相 $888
                        if (preg_match('/^Y888[A-Z0-9]{5}$/',$pVoucherCode) && $productCode == 'FGC_2020') {
                            $returnValue['valid'] = true;
                        }
    
                        //8 x 10in 3本相冊連書套 $388 单本
                        if (preg_match('/^Y1468[A-Z0-9]{4}$/',$pVoucherCode) && in_array($productCode,array("UBB-B2C-PBH_8X10_H","UBB-B2C-PBH_8X10_V"))) {
                            $returnValue['valid'] = true;
                        }
    
    
    
                        break;
                        
                        
    
    
                    }

  
            }
        }
        else
        {
            // A standard voucher has been used
			// Test Voucher codes to determine which has been used.
            switch ($pVoucherCode)
            {
                
                case 'FREESP':
                    {
                        // freeshipping when orderTotal equal orgreater than 300?
                        $orderTotal = self::getOrderTotal();
                        $country = $gSession['shipping'][0]['shippingcustomercountrycode'];
                        if($orderTotal >= 300 && $country == 'HK')
                        {
                            $returnValue['valid'] = true;
                        }
    
                        // stop testing for other voucher codes
                        break;
                    }  

                default:
                {
                    // Stop testing for other voucher codes.
                    break;
                }
            }
        }

        return $returnValue;
    }


    /* ==============================================================
     *
     *  Used to calculate the discount which is applied to the order on initial application of the voucher,
     *  or anytime a change is made to the order.
     *
     *  $pPromoCode: if the voucher is part of a promotion, test to see if the promotion can be applied to the order.
     *  $pVoucherCode: test to see if the specified scripted voucher can be applied to the order.
     *  $pCurrentLine: item to be checked against the criteria of the voucher.
     *
     *  Return Values
     *      Array containing discount information:
     *      - discountname: description to be shown in the cart summary, can be '' in which case it will use the entry configured in the Administration
     *
     *      - discountvalue: discount to be applied to the item
     *      - ordertotaldiscountvalue: discount to be applied to the order
     *      - shippingdiscountvalue: discount to be applied to the shipping
     *      (only one of the above will be returned with a value, this is a result of the discount function used)
     *
     * ============================================================== */
    public static function calcDiscountedValue($pPromoCode, $pVoucherCode, $pCurrentLine)
    {
        global $gSession;
        // Get the item information from the order using $pCurrentLine.
        $lineItem = self::getItemByLine($pCurrentLine);

        // Set a default return value for use if no useable scripted vouchers are found.
        $returnArray = array();

        // Is a promotion voucher being used?
        if ($pPromoCode != '')
        {
			// Test promotion code to determine which has been used.
            switch ($pPromoCode)
            {
                case 'YATE202010':
                case 'YATE1020TEST' :
                    {

    
                        $productPrice = $lineItem['itemproductunitsell'];
                        
                        //voucherCode Matched
                        if (preg_match('/^Y(479|369|888|1468)[A-Z0-9]{4,10}$/',$pVoucherCode)) {
                           $returnArray = self::returnPriceAndDescription($productPrice, 'en '."Yate:$pVoucherCode");
                           $returnArray['shippingdiscountvalue'] = self::getShippingCost();
    
                        }
    
                        
                        break;
                    }


            }
        }
        else
        {
            // A standard voucher has been used.
			// Test Voucher code to determine which has been used.
            switch ($pVoucherCode)
            {
	            // freeshipping when orderTotal is equal or greater than 300
                case 'FREESP':
                    {
    
                        $orderTotal = self::getOrderTotal();
                        $country = $gSession['shipping'][0]['shippingcustomercountrycode'];
                         
                        if($orderTotal >= 300 && $country == 'HK')
                        {
                            // a slip box was found, build the discount details
                            $valueOff = self::getShippingCost();
    
                            // set a description
                            $Desc = 'Free shipping';
    
                            // return the voucher details
                            $returnArray = self::returnShippingAndDescription($valueOff, $Desc);                    
                        }
    
                        // stop testing for other voucher codes
                        break;
                    }

				default:
                {
                    // Stop testing for other voucher codes.
                    break;
                }
            }
        }

        return $returnArray;
    }
}

?>
