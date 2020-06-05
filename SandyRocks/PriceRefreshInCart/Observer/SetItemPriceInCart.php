<?php
namespace SandyRocks\PriceRefreshInCart\Observer;
 

 
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
 
 
class SetItemPriceInCart implements ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */

    protected $_customerSession;

    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
    ) {
        $this->_customerSession = $customerSession;
        $this->_customerRepositoryInterface = $customerRepositoryInterface; 
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customerGroup = 0;

         
        if($this->_customerSession->isLoggedIn()){
            $customerGroup=$this->_customerSession->getCustomer()->getGroupId();
        } 

        $quoteItem = $observer->getQuoteItem();

        $quoteItem = ( $quoteItem->getParentItem() ? $quoteItem->getParentItem() : $quoteItem );

        
        $quote = $quoteItem->getQuote(); 

        $customer_id = $quote->getCustomerId();

        if($customerGroup){

        }else{
            $customer = $this->_customerRepositoryInterface->getById($customer_id); 

            $customerGroup = $customer->getGroupId();
        }

        
        $product = $observer->getProduct();

      
        $qty = $quoteItem->getQty();


        $product->setCustomerGroupId($customerGroup);
        

        $finalprice = $product->getPriceModel()->getFinalPrice($qty, $product);

        $price = $quoteItem->getPrice();
    
        /*    $quoteItem->setArePricesChanged(7);  
            $quoteItem->setPreorder(7);  

*/
        if($price == $finalprice){
            //do nothing
            //$quoteItem->setPreorder(1);  e
            $quoteItem->setArePricesChanged(0);  

        }else{
            $quoteItem->getProduct()->setIsSuperMode(true);

     
            $quoteItem->setCustomPrice($finalprice);  

            $quoteItem->setPrice($finalprice);  

            $quoteItem->setOriginalCustomPrice($finalprice);

            $quoteItem->calcRowTotal();

            $quoteItem->setArePricesChanged(1);  

        } 
    }
}