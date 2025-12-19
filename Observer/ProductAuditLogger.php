<?php 

/**
 * @author  Kallel Khaled
 * @created 2025-12-19
 * @package Melkart_ProductAuditLog
 */


namespace Melkart\ProductAuditLog\Observer;

use Magento\Framework\Event\ObserverInterface;
use Melkart\ProductAuditLog\Logger\MelkartLogger;
use Melkart\ProductAuditLog\Helper\Data;

class ProductAuditLogger implements ObserverInterface
{
    
    protected $logger;
    protected $configData;

    
    protected $ignoredAttributes = [
        
    ];

    public function __construct(
        MelkartLogger $logger,
        Data    $configData
    ) {
        $this->logger       = $logger;
        $this->configData   = $configData;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {

        if (!$this->configData->isEnabled()) {
            return;
        }

        $product = $observer->getEvent()->getData('product');

        /* Retrieve all informations changes in product */
        $changes = $this->getChangeInformations($product);

        if(is_array($changes) && !empty($changes)){
            
            $this->logger->info('Product changes detected'. 
                print_r(['product_id' => (int)$product->getId(),
                'sku' => (string)$product->getSku(),
                'Product Type' => (string)$product->getTypeId(),
                'changes' => $changes,
                ],true)
            );

        }else{
            $this->logger->info(
                sprintf(
                    'Product with SKU %s was saved without changes',
                    $product->getSku()
                )
            );
        }
        
    }

    protected function getChangeInformations($product){
        $changes = [];

        foreach ($product->getOrigData() as $attributeCode => $oldValue) {

            if(in_array($attributeCode, $this->ignoredAttributes, true)){
                continue;
            }
        
            if (!$product->dataHasChangedFor($attributeCode)) {
                continue;
            }

            $newValue = $product->getData($attributeCode);
        
            $changes[$attributeCode] = [
                'from' => $this->normalizeValue($oldValue),
                'to'   => $this->normalizeValue($newValue),
            ];
        }

        return $changes;
    }

    private function normalizeValue($value): string
    {
        if (is_array($value)) {
            return json_encode($value);
        }

        if (is_object($value)) {
            return '[object]';
        }

        return (string)$value;
    }
}