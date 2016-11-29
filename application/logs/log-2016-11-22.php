<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2016-11-22 15:39:50 --> Query error: Unknown column 'clients.name' in 'field list' - Invalid query: SELECT `invoices`.*, `clients`.`name` as `client_name`, CONCAT(first_name, " ", last_name) as full_name, ((SUM(rate * quantity) * tax_rate / 100) + SUM(rate * quantity))  AS total
FROM `invoices`
LEFT JOIN `invoices_lines` ON `invoices_lines`.`invoice_id` = `invoices`.`id`
LEFT JOIN `clients` ON `clients`.`id` = `invoices`.`client_id`
LEFT JOIN `users` ON `users`.`id` = `invoices`.`user_id`
WHERE `invoices`.`user_id` = '3'
GROUP BY `invoices`.`id`
ORDER BY `invoices`.`created_on` desc
 LIMIT 20
ERROR - 2016-11-22 15:39:50 --> Severity: Error --> Call to a member function result() on boolean C:\xamp\htdocs\invoice_api\application\core\MY_Model.php 63
ERROR - 2016-11-22 15:40:35 --> Query error: Unknown column 'clients.name' in 'field list' - Invalid query: SELECT `invoices`.*, `clients`.`name` as `client_name`, CONCAT(first_name, " ", last_name) as full_name, ((SUM(rate * quantity) * tax_rate / 100) + SUM(rate * quantity))  AS total
FROM `invoices`
LEFT JOIN `invoices_lines` ON `invoices_lines`.`invoice_id` = `invoices`.`id`
LEFT JOIN `clients` ON `clients`.`id` = `invoices`.`client_id`
LEFT JOIN `users` ON `users`.`id` = `invoices`.`user_id`
WHERE `invoices`.`user_id` = '3'
GROUP BY `invoices`.`id`
ORDER BY `invoices`.`created_on` desc
 LIMIT 20
ERROR - 2016-11-22 15:40:35 --> Severity: Error --> Call to a member function result() on boolean C:\xamp\htdocs\invoice_api\application\core\MY_Model.php 63
ERROR - 2016-11-22 15:40:47 --> Query error: Unknown column 'clients.name' in 'field list' - Invalid query: SELECT `invoices`.*, `clients`.`name` as `client_name`, CONCAT(first_name, " ", last_name) as full_name, ((SUM(rate * quantity) * tax_rate / 100) + SUM(rate * quantity))  AS total
FROM `invoices`
LEFT JOIN `invoices_lines` ON `invoices_lines`.`invoice_id` = `invoices`.`id`
LEFT JOIN `clients` ON `clients`.`id` = `invoices`.`client_id`
LEFT JOIN `users` ON `users`.`id` = `invoices`.`user_id`
WHERE `invoices`.`user_id` = '3'
GROUP BY `invoices`.`id`
ORDER BY `invoices`.`created_on` desc
 LIMIT 20
ERROR - 2016-11-22 15:40:47 --> Severity: Error --> Call to a member function result() on boolean C:\xamp\htdocs\invoice_api\application\core\MY_Model.php 63
ERROR - 2016-11-22 15:41:20 --> Query error: Unknown column 'clients.name' in 'field list' - Invalid query: SELECT `invoices`.*, `clients`.`name` as `client_name`, CONCAT(first_name, " ", last_name) as full_name, ((SUM(rate * quantity) * tax_rate / 100) + SUM(rate * quantity))  AS total
FROM `invoices`
LEFT JOIN `invoices_lines` ON `invoices_lines`.`invoice_id` = `invoices`.`id`
LEFT JOIN `clients` ON `clients`.`id` = `invoices`.`client_id`
LEFT JOIN `users` ON `users`.`id` = `invoices`.`user_id`
WHERE `invoices`.`user_id` = '3'
GROUP BY `invoices`.`id`
ORDER BY `invoices`.`created_on` desc
 LIMIT 20
ERROR - 2016-11-22 15:41:20 --> Severity: Error --> Call to a member function result() on boolean C:\xamp\htdocs\invoice_api\application\core\MY_Model.php 63
ERROR - 2016-11-22 15:41:40 --> Query error: Unknown column 'clients.name' in 'field list' - Invalid query: SELECT `invoices`.*, `clients`.`name` as `client_name`, CONCAT(first_name, " ", last_name) as full_name, ((SUM(rate * quantity) * tax_rate / 100) + SUM(rate * quantity))  AS total
FROM `invoices`
LEFT JOIN `invoices_lines` ON `invoices_lines`.`invoice_id` = `invoices`.`id`
LEFT JOIN `clients` ON `clients`.`id` = `invoices`.`client_id`
LEFT JOIN `users` ON `users`.`id` = `invoices`.`user_id`
WHERE `invoices`.`user_id` = '3'
GROUP BY `invoices`.`id`
ORDER BY `invoices`.`created_on` desc
 LIMIT 20
ERROR - 2016-11-22 15:41:40 --> Severity: Error --> Call to a member function result() on boolean C:\xamp\htdocs\invoice_api\application\core\MY_Model.php 63
ERROR - 2016-11-22 15:43:45 --> Query error: Unknown column 'clients.name' in 'field list' - Invalid query: SELECT `invoices`.*, `clients`.`name` as `client_name`, CONCAT(first_name, " ", last_name) as full_name, ((SUM(rate * quantity) * tax_rate / 100) + SUM(rate * quantity))  AS total
FROM `invoices`
LEFT JOIN `invoices_lines` ON `invoices_lines`.`invoice_id` = `invoices`.`id`
LEFT JOIN `clients` ON `clients`.`id` = `invoices`.`client_id`
LEFT JOIN `users` ON `users`.`id` = `invoices`.`user_id`
WHERE `invoices`.`user_id` = '3'
GROUP BY `invoices`.`id`
ORDER BY `invoices`.`created_on` desc
 LIMIT 20
ERROR - 2016-11-22 15:43:45 --> Severity: Error --> Call to a member function result() on boolean C:\xamp\htdocs\invoice_api\application\core\MY_Model.php 63
