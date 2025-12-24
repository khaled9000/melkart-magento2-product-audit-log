<?php 

/**
 * @author  Kallel Khaled
 * @created 2025-12-19
 * @package Melkart_ProductAuditLog
 */


namespace Melkart\ProductAuditLog\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Backend\Model\Auth\Session as AdminSession;
use Magento\Catalog\Model\ProductFactory as Products;
use Melkart\ProductAuditLog\Logger\MelkartLogger;
use Melkart\ProductAuditLog\Helper\Data;

class ProductAuditLogger implements ObserverInterface
{
    
    protected $logger;
    protected $adminSession;
    protected $configData;
    protected $productFactory;

    public function __construct(
        MelkartLogger $logger,
        AdminSession  $adminSession,
        Data    $configData,
        Products $productFactory
    ) {
        $this->logger         = $logger;
        $this->adminSession   = $adminSession;
        $this->configData     = $configData;
        $this->productFactory = $productFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {

        // Check if Module is enabled
        if (!$this->configData->isEnabled()) {
            return;
        }

        // Check if Product Trace is enabled
        if (!$this->configData->isEnabledProductTrace()){
            return;
        }
        
        // Retrieve Attributs To Excluded
        $excludedAttribute = [];
        $excludedAttribute = explode(',', $this->configData->productExcludedAttribute());
        
        // Excluded Stock Item 
        // Stock Item isn't a attribut product 
        $excludedAttribute[] = 'stock_item';

        // Check if Logging Website Ids
        if(!$this->configData->loggingWebsiteIds()){
            $excludedAttribute[] = 'website_ids';
        }

        // Check if Adminstrator Trace is enabled
        if($this->configData->isEnabledAdminTrace()){
            $loggerAdminUser = $this->getAdminInformations();

            $this->logger->info(
                'Admin User : '. PHP_EOL .
                print_r($loggerAdminUser,true)
            );
        }

        // Retrieve Product Datas
        $product = $observer->getEvent()->getData('product');

        // Retrieve all informations changes in product 
        $changes = $this->getChangeInformations($product, $excludedAttribute);

        if(is_array($changes) && !empty($changes)){
            
            $this->logger->info('Product changes detected: '. PHP_EOL .
                print_r(['product_id' => (int)$product->getId(),
                'sku' => (string)$product->getSku(),
                'Product Type' => (string)$product->getTypeId(),
                'changes' => $changes,
                ],true)
            );

        }else{
            $this->logger->info(
                sprintf(
                    'Product with SKU %s was saved without changes: ',
                    $product->getSku()
                )
            );
        }
        
    }

    protected function getAdminInformations(){

        $adminUser = $this->adminSession->getUser();

        $adminData = $adminUser ? [
            'admin_id' => (int)$adminUser->getId(),
            'admin_username' => (string)$adminUser->getUserName(),
            'admin_email' => (string)$adminUser->getEmail(),
        ] : [
            'admin' => 'system',
        ];

        return $adminData;

    }

    protected function getChangeInformations($product, $excludedAttribute=[]){

        $changes = [];

        foreach ($product->getOrigData() as $attributeCode => $oldValue) {

            if(in_array($attributeCode, $excludedAttribute, true)){
                continue;
            }
        
            if (!$product->dataHasChangedFor($attributeCode)) {
                continue;
            }

            $attributeType = $this->getAttributeType($attributeCode);

            if($attributeType == 'select'){

                // Retrieve Label Of oldValue 
                $oldLabel = $this->getSelectLabel($product, $attributeCode, $oldValue);

                // Retrieve Label Of NewValue 
                $newValue = $product->getData($attributeCode);
                $newLabel = $this->getSelectLabel($product, $attributeCode, $newValue);

                $changes[$attributeCode] = [
                    'from' => $this->normalizeValue($oldLabel),
                    'to'   => $this->normalizeValue($newLabel),
                ];

            }elseif($attributeType == 'multiselect'){

                // Retrieve Label Of oldValue 
                $oldLabel = $this->getMultiselectLabels($product, $attributeCode, $oldValue);

                // Retrieve Label Of NewValue 
                $newValue = $product->getData($attributeCode);
                $newLabel = $this->getMultiselectLabels($product, $attributeCode, $newValue);

                $changes[$attributeCode] = [
                    'from' => $oldLabel,
                    'to'   => $newLabel,
                ];

            }else{
    
                $newValue = $product->getData($attributeCode);
            
                $changes[$attributeCode] = [
                    'from' => $this->normalizeValue($oldValue),
                    'to'   => $this->normalizeValue($newValue),
                ];

            }

            
        }

        return $changes;
    }

    protected function normalizeValue($value){

        if (is_array($value)) {
            return json_encode($value);
        }

        if (is_object($value)) {
            return '[object]';
        }

        return (string)$value;
    }

    protected function getAttributeType($attributeCode){

        $product = $this->productFactory->create();
        $attribute = $product->getResource()->getAttribute($attributeCode);

        if (!$attribute || !$attribute->getId()) {
            // Not found OR not an EAV attribute (e.g. website_ids)
            return null;
        }
        $inputType = (string) $attribute->getFrontendInput();

        return $inputType;

    }

    protected function getSelectLabel($product, string $attributeCode, $value){

        if ($value === null || $value === '') {
            return '';
        }

        $product = $this->productFactory->create();
        $attribute = $product->getResource()->getAttribute($attributeCode);
    
        if (!$attribute || !$attribute->usesSource()) {
            return (string) $value;
        }
    
        return (string) $attribute->getSource()->getOptionText($value);
    }

    protected function getMultiselectLabels($product, string $attributeCode, $value){

        $product = $this->productFactory->create();
        $attribute = $product->getResource()->getAttribute($attributeCode);
    
        if (!$attribute || !$attribute->usesSource()) {
            return [];
        }
    
        $values = $this->normalizeMultiselect($value);
        $labels = [];

        foreach ($values as $optionId) {
            $datas = (array) $attribute->getSource()->getOptionText($optionId);
            foreach($datas as $key => $data){
                if(empty($data)){
                    continue;
                }
                $labels[] = $data;
            }
        }
    
        sort($labels);

        return $labels;
    }
    
    private function normalizeMultiselect($value){

        if (empty($value)) {
            return [];
        }

        if (is_string($value)) {
            $value = explode(',', $value);
        }

        $value = array_map('trim', $value);
        $value = array_filter($value);
        sort($value);

        return $value;
    }
}