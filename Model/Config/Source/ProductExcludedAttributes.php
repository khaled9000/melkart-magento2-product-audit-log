<?php

/**
 * @author  Kallel Khaled
 * @created 2025-12-22
 * @package Melkart_ProductAuditLog
 */


 namespace Melkart\ProductAuditLog\Model\Config\Source;

 use Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory;
 use Magento\Framework\Option\ArrayInterface;
 
 class ProductExcludedAttributes implements ArrayInterface
 {
    protected $attributeCollectionFactory;

    protected $excludedAttributes = [
        'updated_at',
        'created_at',
        'media_gallery',
        'quantity_and_stock_status',
    ];

    public function __construct(
        CollectionFactory $attributeCollectionFactory
    ) {
        $this->attributeCollectionFactory = $attributeCollectionFactory;
    }

    public function toOptionArray(): array
    {

        $collection = $this->attributeCollectionFactory->create()
            ->addFieldToFilter('entity_type_id', 4)
            ->addFieldToFilter('attribute_code', ['in' => $this->excludedAttributes]);

        foreach ($collection as $attribute) {
            $options[] = [
                'value' => $attribute->getAttributeCode(),
                'label' => $attribute->getAttributeCode()
            ];
        }
 
        return $options;
    }
 }