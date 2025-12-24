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

    const XML_ENABLE_PRODUCT_TRACE = 'product_audit_log/product_logger_options/enable_product_trace';
    const XML_ENABLE_ADMIN_TRACE = 'product_audit_log/product_logger_options/enable_admin_trace';
    const XML_PRODUCT_EXCLUDED_ATTRIBUTES = 'product_audit_log/product_logger_options/product_excluded_attributes';
    const XML_LOGGING_WEBSITE_IDS = 'product_audit_log/product_logger_options/product_website_ids';

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

    public function isEnabledProductTrace(){
        return $this->scopeConfig->getValue(
            self::XML_ENABLE_PRODUCT_TRACE,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function isEnabledAdminTrace(){
        return $this->scopeConfig->getValue(
            self::XML_ENABLE_ADMIN_TRACE,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function productExcludedAttribute(){
        return $this->scopeConfig->getValue(
            self::XML_PRODUCT_EXCLUDED_ATTRIBUTES,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function loggingWebsiteIds(){
        return $this->scopeConfig->getValue(
            self::XML_LOGGING_WEBSITE_IDS,
            ScopeInterface::SCOPE_STORE
        );
    }
}
