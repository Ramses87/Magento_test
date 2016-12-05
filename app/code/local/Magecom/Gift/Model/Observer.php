<?php

class Magecom_Gift_Model_Observer
{
    public function addGift()
    {
    		
	$price = (int)trim(Mage::getStoreConfig('Magecom/Magecom_group/Magecom_inputone',Mage::app()->getStore())); //  акционное значение суммы товаров, после - добавляем подарок, устанавливаем в админке
	$giftId = (int)trim(Mage::getStoreConfig('Magecom/Magecom_group/Magecom_inputtwo',Mage::app()->getStore())); //  идентификатор подарка (id product), устанавливаем в админке
	$total = Mage::getSingleton('checkout/cart')->getQuote()->getGrandTotal();// Получаю суммарную цену товаров в квоте

    	if ($total >= $price) {                                                      //проверяю условие сумма больше или равна акц. цене

            $cart = Mage::getSingleton('checkout/cart')->getQuote(); 
            foreach ($cart->getAllVisibleItems() as $item) {  
                if ($item->getProductId() == $giftId) {
                    return;
                }
            }
          
      	    $product = Mage::getModel('catalog/product')->load($giftId); // проверяем существует ли подарок
      			if (!$product->getId()) {
      				return;
      			}

      	    $quote = Mage::getSingleton('checkout/cart')->getQuote();   //добавляем подарок с 0 ценой
            $quoteItem = $quote->addProduct($product, 1);
      	    $quoteItem->setCustomPrice(0);
      	    $quoteItem->setOriginalCustomPrice(0);
    	 } else {                                                       // в другом случае ( сумма квоты меньше акц.цены) 
            $cart = Mage::getSingleton('checkout/cart')->getQuote();       //проверяем наличие подарка  и удаляем подарок из квоты
            foreach ($cart->getAllVisibleItems() as $item) {

                if ($item->getProductId() == $giftId) {
                    Mage::getSingleton('checkout/cart')->getQuote()->removeItem($item->getItemId())->save();
                }
            }
        }
    }

}
