<?php
/**
 * @author  Kallel Khaled
 * @created 2025-12-19
 * @package Melkart_ProductAuditLog
 */

namespace Melkart\ProductAuditLog\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const XML_PATH_PRODUCT_AUDIT_LOG_ENABLE = 'product_audit_log/general/enable';

    protected $scopeConfig;

    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
    }

    public function isEnabled(){
        return $this->scopeConfig->getValue(
            self::XML_PATH_PRODUCT_AUDIT_LOG_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
    }
}
