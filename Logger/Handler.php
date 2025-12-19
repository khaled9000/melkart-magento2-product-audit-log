<?php
/**
 * @author  Kallel Khaled
 * @created 2025-12-19
 * @package Melkart_ProductAuditLog
 */

namespace Melkart\ProductAuditLog\Logger;

use Monolog\Logger;
use Magento\Framework\Logger\Handler\Base;


class Handler extends Base
{
    protected $loggerType = Logger::INFO;

    protected $fileName = '/var/log/melkart_product_audit_log.log';
}

