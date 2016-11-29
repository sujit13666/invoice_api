<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2016-11-18 13:59:02 --> 404 Page Not Found: api/Invoices/create_invoice
ERROR - 2016-11-18 14:00:19 --> Query error: Unknown column 'item_id' in 'field list' - Invalid query: INSERT INTO `invoices` (`user_id`, `client_id`, `invoice_number`, `invoice_date`, `due_date`, `item_id`, `subtotal`, `vat`, `total`, `paid`, `comment`, `is_paid`, `card_acceptable`, `attached_image`, `rid`, `created_on`, `updated_on`) VALUES ('123', '123', 1, '2016-11-18', '2016-12-18', '123', '123', '123', 246, '123', 'i am in live', '1', '1', '123_14794740193d2e41ca5b.png', '1f6f719ce86f28700d074780bcf9e069009220bc', 1479474019, 1479474019)
ERROR - 2016-11-18 14:00:19 --> Could not find the language line "record_creation_unsuccessful"
ERROR - 2016-11-18 14:01:31 --> Query error: Table 'ci_invoicing.invoice_items' doesn't exist - Invalid query: INSERT INTO `invoice_items` (`invoice_id`, `item_id`, `qty`, `rid`, `created_on`, `updated_on`) VALUES ('123', '123', '23', 'efac55c602f68a2fd9f7444adbae2621d3f9c08f', 1479474091, 1479474091)
