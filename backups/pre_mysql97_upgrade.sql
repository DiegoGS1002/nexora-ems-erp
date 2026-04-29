-- MySQL dump 10.13  Distrib 8.0.45, for Linux (x86_64)
--
-- Host: localhost    Database: nexora
-- ------------------------------------------------------
-- Server version	8.0.45

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `accounts_payable`
--

DROP TABLE IF EXISTS `accounts_payable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `accounts_payable` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `description_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `supplier_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `chart_of_account_id` bigint unsigned DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `due_date_at` date DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `paid_amount` decimal(15,2) DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `observation` text COLLATE utf8mb4_unicode_ci,
  `attachment_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_recurring` tinyint(1) NOT NULL DEFAULT '0',
  `recurrence_day` tinyint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `value` decimal(15,2) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `accounts_payable_supplier_id_foreign` (`supplier_id`),
  KEY `accounts_payable_chart_of_account_id_foreign` (`chart_of_account_id`),
  CONSTRAINT `accounts_payable_chart_of_account_id_foreign` FOREIGN KEY (`chart_of_account_id`) REFERENCES `plans_of_accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `accounts_payable_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accounts_payable`
--

LOCK TABLES `accounts_payable` WRITE;
/*!40000 ALTER TABLE `accounts_payable` DISABLE KEYS */;
/*!40000 ALTER TABLE `accounts_payable` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `accounts_receivable`
--

DROP TABLE IF EXISTS `accounts_receivable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `accounts_receivable` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `description_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `chart_of_account_id` bigint unsigned DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `received_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `due_date_at` date NOT NULL,
  `received_at` date DEFAULT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `installment_number` int NOT NULL DEFAULT '1',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `observation` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `accounts_receivable_client_id_foreign` (`client_id`),
  KEY `accounts_receivable_chart_of_account_id_foreign` (`chart_of_account_id`),
  CONSTRAINT `accounts_receivable_chart_of_account_id_foreign` FOREIGN KEY (`chart_of_account_id`) REFERENCES `plans_of_accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `accounts_receivable_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accounts_receivable`
--

LOCK TABLES `accounts_receivable` WRITE;
/*!40000 ALTER TABLE `accounts_receivable` DISABLE KEYS */;
/*!40000 ALTER TABLE `accounts_receivable` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `baccarat_accounts`
--

DROP TABLE IF EXISTS `baccarat_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `baccarat_accounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agency` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('corrente','poupanca','caixa_interno','digital') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'corrente',
  `balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `predicted_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `color` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `chart_of_account_id` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_reconciled` tinyint(1) NOT NULL DEFAULT '0',
  `last_reconciled_at` date DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `baccarat_accounts_chart_of_account_id_foreign` (`chart_of_account_id`),
  CONSTRAINT `baccarat_accounts_chart_of_account_id_foreign` FOREIGN KEY (`chart_of_account_id`) REFERENCES `plans_of_accounts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `baccarat_accounts`
--

LOCK TABLES `baccarat_accounts` WRITE;
/*!40000 ALTER TABLE `baccarat_accounts` DISABLE KEYS */;
/*!40000 ALTER TABLE `baccarat_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('nexora-cache-ai_history_11_suporte','a:2:{i:0;a:3:{s:4:\"role\";s:4:\"user\";s:7:\"content\";s:35:\"Como cadastrar produtos no sistema?\";s:9:\"timestamp\";s:5:\"21:19\";}i:1;a:3:{s:4:\"role\";s:5:\"model\";s:7:\"content\";s:69:\"Desculpe, ocorreu um erro ao processar sua pergunta. Tente novamente.\";s:9:\"timestamp\";s:5:\"21:19\";}}',1776471579),('nexora-cache-ai-chat:11','i:1;',1776385237),('nexora-cache-ai-chat:11:timer','i:1776385237;',1776385237);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carriers`
--

DROP TABLE IF EXISTS `carriers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carriers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `trade_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cnpj` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ie` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carriers`
--

LOCK TABLES `carriers` WRITE;
/*!40000 ALTER TABLE `carriers` DISABLE KEYS */;
/*!40000 ALTER TABLE `carriers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cash_flows`
--

DROP TABLE IF EXISTS `cash_flows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cash_flows` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cash_flows`
--

LOCK TABLES `cash_flows` WRITE;
/*!40000 ALTER TABLE `cash_flows` DISABLE KEYS */;
/*!40000 ALTER TABLE `cash_flows` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clients` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_pessoa` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PJ',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `social_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `taxNumber` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `inscricao_estadual` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `credit_limit` decimal(15,2) DEFAULT NULL,
  `payment_condition_default` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `situation` enum('active','inactive','defaulter') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `price_table_id` bigint unsigned DEFAULT NULL,
  `discount_limit` decimal(5,2) DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_zip_code` varchar(9) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_street` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_complement` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_district` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_state` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clients_taxnumber_unique` (`taxNumber`),
  UNIQUE KEY `clients_email_unique` (`email`),
  KEY `clients_price_table_id_foreign` (`price_table_id`),
  CONSTRAINT `clients_price_table_id_foreign` FOREIGN KEY (`price_table_id`) REFERENCES `price_tables` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clients`
--

LOCK TABLES `clients` WRITE;
/*!40000 ALTER TABLE `clients` DISABLE KEYS */;
INSERT INTO `clients` VALUES ('668d9c02-9bfa-406f-b9d8-e9956d1dcb63','PJ','Hotel Mar Azul','Mar Azul Hospedagem LTDA','23123456000104',NULL,NULL,NULL,'active',NULL,NULL,'suprimentos@hotelmarazul.com.br','48993456789','Rua das Palmeiras, 77 - Centro - Florianopolis/SC',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),('8633c247-5b7a-4255-b28a-f5bc3367d1c9','PJ','Lanchonete Ponto Certo','Ponto Certo Alimentos LTDA','56123456000105',NULL,NULL,NULL,'active',NULL,NULL,'financeiro@pontocerto.com.br','62994567890','Avenida Independencia, 990 - Setor Central - Goiania/GO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),('92511a7a-19f4-4ba5-ba4e-cd21ea83d84e','PJ','Restaurante Sabor da Casa','Sabor da Casa Refeicoes LTDA','78123456000103',NULL,NULL,NULL,'active',NULL,NULL,'pedidos@sabordacasa.com.br','31992345678','Rua Goias, 800 - Centro - Belo Horizonte/MG',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),('ae5ec605-b67f-4034-ba81-f6bf2d8828e2','PJ','Mercado Bom Preco','Bom Preco Comercio de Alimentos LTDA','12123456000101',NULL,NULL,NULL,'active',NULL,NULL,'compras@bompreco.com.br','11987654321','Rua das Acacias, 120 - Centro - Sao Paulo/SP',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),('f3535548-b045-49ad-8086-aa33ebe34437','PJ','Padaria Sol Nascente','Padaria Sol Nascente LTDA','45123456000102',NULL,NULL,NULL,'active',NULL,NULL,'contato@solnascente.com.br','21991234567','Avenida Brasil, 455 - Tijuca - Rio de Janeiro/RJ',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22');
/*!40000 ALTER TABLE `clients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `companies`
--

DROP TABLE IF EXISTS `companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `companies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `social_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cnpj` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `inscricao_estadual` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `inscricao_municipal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_zip_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_street` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_complement` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_district` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_state` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `segment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `companies_cnpj_unique` (`cnpj`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `companies`
--

LOCK TABLES `companies` WRITE;
/*!40000 ALTER TABLE `companies` DISABLE KEYS */;
INSERT INTO `companies` VALUES (1,'Nexora Alimentos','Nexora Comercio de Alimentos LTDA','12.345.678/0001-90','123.456.789.000','987654321','contato@nexora-alimentos.com.br','(11) 3456-7890','https://www.nexora-alimentos.com.br','01310-100','Avenida Paulista','1000','Andar 5','Bela Vista','São Paulo','SP',NULL,'Alimentos e Bebidas',1,'Empresa principal do grupo Nexora.','2026-04-14 22:51:50','2026-04-14 22:51:50'),(2,'Nexora Logística','Nexora Transportes e Logística LTDA','23.456.789/0001-12','234.567.890.000','876543210','operacoes@nexora-logistica.com.br','(21) 2345-6789','https://www.nexora-logistica.com.br','20040-020','Rua da Assembléia','77','Sala 302','Centro','Rio de Janeiro','RJ',NULL,'Transporte e Logística',1,'Responsável pela distribuição e frota.','2026-04-14 22:51:50','2026-04-14 22:51:50'),(3,'Nexora Indústria','Nexora Industria e Manufatura SA','34.567.890/0001-34','345.678.901.000','765432109','producao@nexora-industria.com.br','(31) 3333-4444','https://www.nexora-industria.com.br','30130-110','Rua dos Caetés','500',NULL,'Centro','Belo Horizonte','MG',NULL,'Indústria e Manufatura',1,'Unidade fabril do grupo Nexora.','2026-04-14 22:51:50','2026-04-14 22:51:50'),(4,'Nexora Varejo','Nexora Comercio Varejista EIRELI','45.678.901/0001-56','456.789.012.000','654321098','vendas@nexora-varejo.com.br','(41) 3210-9876',NULL,'80410-001','Rua XV de Novembro','1200','Loja 5','Centro','Curitiba','PR',NULL,'Varejo',1,'Ponto de venda ao consumidor final.','2026-04-14 22:51:50','2026-04-14 22:51:50');
/*!40000 ALTER TABLE `companies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `configurations`
--

DROP TABLE IF EXISTS `configurations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `configurations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `configurations`
--

LOCK TABLES `configurations` WRITE;
/*!40000 ALTER TABLE `configurations` DISABLE KEYS */;
/*!40000 ALTER TABLE `configurations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cotacao_items`
--

DROP TABLE IF EXISTS `cotacao_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cotacao_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cotacao_id` bigint unsigned NOT NULL,
  `product_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'UN',
  `quantity` decimal(12,3) NOT NULL DEFAULT '1.000',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cotacao_items_cotacao_id_foreign` (`cotacao_id`),
  KEY `cotacao_items_product_id_foreign` (`product_id`),
  CONSTRAINT `cotacao_items_cotacao_id_foreign` FOREIGN KEY (`cotacao_id`) REFERENCES `cotacoes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cotacao_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cotacao_items`
--

LOCK TABLES `cotacao_items` WRITE;
/*!40000 ALTER TABLE `cotacao_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `cotacao_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cotacao_respostas`
--

DROP TABLE IF EXISTS `cotacao_respostas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cotacao_respostas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cotacao_id` bigint unsigned NOT NULL,
  `cotacao_item_id` bigint unsigned NOT NULL,
  `supplier_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit_price` decimal(14,2) NOT NULL DEFAULT '0.00',
  `delivery_days` int DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `selected` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cotacao_respostas_cotacao_id_foreign` (`cotacao_id`),
  KEY `cotacao_respostas_cotacao_item_id_foreign` (`cotacao_item_id`),
  KEY `cotacao_respostas_supplier_id_foreign` (`supplier_id`),
  CONSTRAINT `cotacao_respostas_cotacao_id_foreign` FOREIGN KEY (`cotacao_id`) REFERENCES `cotacoes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cotacao_respostas_cotacao_item_id_foreign` FOREIGN KEY (`cotacao_item_id`) REFERENCES `cotacao_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cotacao_respostas_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cotacao_respostas`
--

LOCK TABLES `cotacao_respostas` WRITE;
/*!40000 ALTER TABLE `cotacao_respostas` DISABLE KEYS */;
/*!40000 ALTER TABLE `cotacao_respostas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cotacoes`
--

DROP TABLE IF EXISTS `cotacoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cotacoes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'rascunho',
  `deadline_date` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `purchase_order_id` bigint unsigned DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cotacoes_number_unique` (`number`),
  KEY `cotacoes_purchase_order_id_foreign` (`purchase_order_id`),
  KEY `cotacoes_created_by_foreign` (`created_by`),
  CONSTRAINT `cotacoes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cotacoes_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cotacoes`
--

LOCK TABLES `cotacoes` WRITE;
/*!40000 ALTER TABLE `cotacoes` DISABLE KEYS */;
/*!40000 ALTER TABLE `cotacoes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delivery_time_windows`
--

DROP TABLE IF EXISTS `delivery_time_windows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `delivery_time_windows` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `capacity` int unsigned NOT NULL DEFAULT '10',
  `region` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_time_windows`
--

LOCK TABLES `delivery_time_windows` WRITE;
/*!40000 ALTER TABLE `delivery_time_windows` DISABLE KEYS */;
/*!40000 ALTER TABLE `delivery_time_windows` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `driver_managements`
--

DROP TABLE IF EXISTS `driver_managements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `driver_managements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `driver_managements`
--

LOCK TABLES `driver_managements` WRITE;
/*!40000 ALTER TABLE `driver_managements` DISABLE KEYS */;
/*!40000 ALTER TABLE `driver_managements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_managements`
--

DROP TABLE IF EXISTS `employee_managements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee_managements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_managements`
--

LOCK TABLES `employee_managements` WRITE;
/*!40000 ALTER TABLE `employee_managements` DISABLE KEYS */;
/*!40000 ALTER TABLE `employee_managements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employees` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `social_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `identification_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rg` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rg_issuer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rg_date` date DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `gender` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `marital_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nationality` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'Brasileiro(a)',
  `birthplace` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `internal_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observations` text COLLATE utf8mb4_unicode_ci,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_secondary` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `zip_code` varchar(9) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `street` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `complement` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `neighborhood` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'Brasil',
  `emergency_contact_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact_relationship` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `access_profile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `admission_date` date DEFAULT NULL,
  `work_schedule` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `allow_system_access` tinyint(1) NOT NULL DEFAULT '0',
  `department` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employees_identification_number_unique` (`identification_number`),
  UNIQUE KEY `employees_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES ('009a1384-5d1d-41af-a01a-5211830ceebd','Geralda Maria Campos',NULL,'05104215300',NULL,NULL,NULL,'1975-02-20','Feminino','Solteiro(a)','Brasileiro(a)',NULL,'Auxiliar de Limpeza',1412.00,NULL,NULL,'geralda.campos@supermercadobh.com.br','31991000051',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2018-09-01','TURNOA',0,'Conservação','2026-04-11 17:57:25','2026-04-11 17:57:25'),('040b200c-0e2f-4da2-98b9-6fae3fb799f2','Cleber Augusto Moura',NULL,'04803915000',NULL,NULL,NULL,'1980-07-31','Masculino','Solteiro(a)','Brasileiro(a)',NULL,'Vigilante/Segurança',2200.00,NULL,NULL,'cleber.moura@supermercadobh.com.br','31991000048',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2016-08-01','TURNOA',0,'Segurança','2026-04-11 17:57:25','2026-04-11 17:57:25'),('04b0ca55-2c2a-4865-8da0-a411eabc616c','Wellington José Barbosa',NULL,'03202313400',NULL,NULL,NULL,'1996-07-21','Masculino','Solteiro(a)','Brasileiro(a)',NULL,'Repositor(a)',1700.00,NULL,NULL,'wellington.barbosa@supermercadobh.com.br','31991000032',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2021-08-15','TURNOC',0,'Estoque','2026-04-11 17:57:25','2026-04-11 17:57:25'),('08aa2340-0016-4350-81a2-7bf6585a4a80','Rodrigo Teixeira Carvalho',NULL,'01800912000',NULL,NULL,NULL,'1993-04-05','Masculino','Solteiro(a)','Brasileiro(a)',NULL,'Analista Administrativo',3600.00,NULL,NULL,'rodrigo.carvalho@supermercadobh.com.br','31991000018',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2021-03-15','ADM',0,'Administrativo','2026-04-11 17:57:25','2026-04-11 17:57:25'),('0d08912c-1d3f-4c64-91c4-eb9280e390d8','Eduardo Costa Drummond',NULL,'00300400500',NULL,NULL,NULL,'1979-11-08','Masculino','Solteiro(a)','Brasileiro(a)',NULL,'Diretor Financeiro',16200.00,NULL,NULL,'eduardo.drummond@supermercadobh.com.br','31991000003',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2013-01-01','ADM',0,'Diretoria','2026-04-11 17:57:24','2026-04-11 17:57:24'),('1169275b-d47c-43e5-ad89-bbd3f6f288f9','José Carlos Barbosa',NULL,'01400511600',NULL,NULL,NULL,'1984-11-02','Masculino','Solteiro(a)','Brasileiro(a)',NULL,'Supervisor de Açougue',4800.00,NULL,NULL,'jose.barbosa@supermercadobh.com.br','31991000014',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2016-06-01','TURNOA',0,'Açougue','2026-04-11 17:57:25','2026-04-11 17:57:25'),('135ccc36-5608-47ac-9e02-e2c3b6a981da','Carlos Henrique Lima',NULL,'00500600700',NULL,NULL,NULL,'1983-09-28','Masculino','Solteiro(a)','Brasileiro(a)',NULL,'Gerente Administrativo',8500.00,NULL,NULL,'carlos.lima@supermercadobh.com.br','31991000005',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2016-05-01','ADM',0,'Administrativo','2026-04-11 17:57:25','2026-04-11 17:57:25'),('13a25382-565e-4da4-af7f-c63d423d7d18','Paulo Roberto Mendes',NULL,'05404515600',NULL,NULL,NULL,'1979-10-24','Masculino','Solteiro(a)','Brasileiro(a)',NULL,'Técnico de Manutenção',2800.00,NULL,NULL,'paulo.mendes@supermercadobh.com.br','31991000054',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2017-11-01','ADM',0,'Manutenção','2026-04-11 17:57:25','2026-04-11 17:57:25'),('13eac7ba-8683-4790-bec7-b5d1d8e3bae4','Luana Sousa Andrade',NULL,'01901012100',NULL,NULL,NULL,'1996-01-30','Feminino','Solteiro(a)','Brasileiro(a)',NULL,'Auxiliar Administrativo',2200.00,NULL,NULL,'luana.andrade@supermercadobh.com.br','31991000019',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2022-08-01','ADM',0,'Administrativo','2026-04-11 17:57:25','2026-04-11 17:57:25'),('1a9cb80e-4042-420c-8b33-223b67c53a8e','Renata Borges Araújo',NULL,'00400500600',NULL,NULL,NULL,'1980-04-12','Feminino','Solteiro(a)','Brasileiro(a)',NULL,'Gerente de Loja',9800.00,NULL,NULL,'renata.araujo@supermercadobh.com.br','31991000004',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2015-02-01','ADM',0,'Operações','2026-04-11 17:57:24','2026-04-11 17:57:24'),('1db870e0-c68c-4c22-94b4-c4da6b267c44','Daniel Luís Souza',NULL,'02101212300',NULL,NULL,NULL,'1999-07-08','Masculino','Solteiro(a)','Brasileiro(a)',NULL,'Operador(a) de Caixa',1800.00,NULL,NULL,'daniel.souza@supermercadobh.com.br','31991000021',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2022-01-10','TURNOB',0,'Checkout','2026-04-11 17:57:25','2026-04-11 17:57:25'),('1f36c247-1de1-4bdc-99ce-ac9770bcf1f6','Thiago Roberto Pinto',NULL,'01100211300',NULL,NULL,NULL,'1987-10-14','Masculino','Solteiro(a)','Brasileiro(a)',NULL,'Supervisor de Estoque',4600.00,NULL,NULL,'thiago.pinto@supermercadobh.com.br','31991000011',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2018-07-01','TURNOB',0,'Estoque','2026-04-11 17:57:25','2026-04-11 17:57:25'),('294b23b3-9044-454e-b7ac-dad71f68000f','Marcos Paulo Oliveira',NULL,'02902013100',NULL,NULL,NULL,'1999-04-29','Masculino','Solteiro(a)','Brasileiro(a)',NULL,'Operador(a) de Caixa',1800.00,NULL,NULL,'marcos.oliveira@supermercadobh.com.br','31991000029',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2023-09-10','TURNOA',0,'Checkout','2026-04-11 17:57:25','2026-04-11 17:57:25'),('2a25dbdd-5460-44f1-965a-7f4508c84b41','Débora Cristina Campos',NULL,'01000111200',NULL,NULL,NULL,'1990-05-20','Feminino','Solteiro(a)','Brasileiro(a)',NULL,'Supervisor de Checkout',4800.00,NULL,NULL,'debora.campos@supermercadobh.com.br','31991000010',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2019-01-15','TURNOA',0,'Operações','2026-04-11 17:57:25','2026-04-11 17:57:25'),('2b674743-e636-4e64-a73f-c24c82332b1f','Sebastião Morais Cunha',NULL,'03002113200',NULL,NULL,NULL,'1990-01-17','Masculino','Solteiro(a)','Brasileiro(a)',NULL,'Repositor(a)',1700.00,NULL,NULL,'sebastiao.cunha@supermercadobh.com.br','31991000030',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2019-05-01','TURNOA',0,'Estoque','2026-04-11 17:57:25','2026-04-11 17:57:25'),('30edbf86-e553-4e91-ba7b-45486dc415ce','Nilza Aparecida Correia',NULL,'03102213300',NULL,NULL,NULL,'1993-10-08','Feminino','Solteiro(a)','Brasileiro(a)',NULL,'Repositor(a)',1700.00,NULL,NULL,'nilza.correia@supermercadobh.com.br','31991000031',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2020-02-10','TURNOB',0,'Estoque','2026-04-11 17:57:25','2026-04-11 17:57:25'),('31659f66-0ca5-4298-a985-a342cf288f62','Patricia Oliveira Nunes',NULL,'00600700800',NULL,NULL,NULL,'1986-01-15','Feminino','Solteiro(a)','Brasileiro(a)',NULL,'Gerente Financeiro',8200.00,NULL,NULL,'patricia.nunes@supermercadobh.com.br','31991000006',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2017-03-01','ADM',0,'Financeiro','2026-04-11 17:57:25','2026-04-11 17:57:25'),('3cd593e5-a891-435f-aae1-e021807d0c6f','Welington da Cruz Santos',NULL,'04303414500',NULL,NULL,NULL,'1979-09-12','Masculino','Solteiro(a)','Brasileiro(a)',NULL,'Açougueiro(a)',2800.00,NULL,NULL,'welington.santos@supermercadobh.com.br','31991000043',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2015-07-01','TURNOA',0,'Açougue','2026-04-11 17:57:25','2026-04-11 17:57:25'),('3ffe9ca6-e714-49e7-8803-eb8b045f0f65','Antônio Carlos Rezende',NULL,'03903014100',NULL,NULL,NULL,'1981-04-05','Masculino','Solteiro(a)','Brasileiro(a)',NULL,'Padeiro(a)',2400.00,NULL,NULL,'antonio.rezende@supermercadobh.com.br','31991000039',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2017-01-10','MADRUGADA',0,'Padaria','2026-04-11 17:57:25','2026-04-11 17:57:25'),('41100142-cce8-40ea-8045-5d07a631690f','Igor Henrique Teles',NULL,'04904015100',NULL,NULL,NULL,'1988-11-06','Masculino','Solteiro(a)','Brasileiro(a)',NULL,'Vigilante/Segurança',2200.00,NULL,NULL,'igor.teles@supermercadobh.com.br','31991000049',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2019-03-15','TURNOB',0,'Segurança','2026-04-11 17:57:25','2026-04-11 17:57:25'),('4294f329-3094-4d80-b315-89f80e30ed9f','Felipe Nunes Ribeiro',NULL,'02701812900',NULL,NULL,NULL,'2000-08-19','Masculino','Solteiro(a)','Brasileiro(a)',NULL,'Operador(a) de Caixa',1800.00,NULL,NULL,'felipe.ribeiro@supermercadobh.com.br','31991000027',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2023-04-15','TURNOB',0,'Checkout','2026-04-11 17:57:25','2026-04-11 17:57:25'),('4889d258-74ff-4e90-8941-eb3ddf37e11d','Vitor Gonçalves Ramos',NULL,'03802914000',NULL,NULL,NULL,'2002-02-10','Masculino','Solteiro(a)','Brasileiro(a)',NULL,'Auxiliar de Estoque',1600.00,NULL,NULL,'vitor.ramos@supermercadobh.com.br','31991000038',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2024-01-15','TURNOC',0,'Estoque','2026-04-11 17:57:25','2026-04-11 17:57:25'),('4df530f6-76d0-4b63-9dea-6ef18a582caf','Jéssica Aparecida Souza',NULL,'05004115200',NULL,NULL,NULL,'1995-03-27','Feminino','Solteiro(a)','Brasileiro(a)',NULL,'Vigilante/Segurança',2200.00,NULL,NULL,'jessica.souza@supermercadobh.com.br','31991000050',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2021-05-01','TURNOC',0,'Segurança','2026-04-11 17:57:25','2026-04-11 17:57:25'),('522e429a-0de2-4333-a5b7-d6936889050d','Marcelino Alves Ribeiro',NULL,'05704815900',NULL,NULL,NULL,'1995-06-28','Masculino','Solteiro(a)','Brasileiro(a)',NULL,'Auxiliar de Entrega',1700.00,NULL,NULL,'marcelino.ribeiro@supermercadobh.com.br','31991000057',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2023-05-15','ADM',0,'Logística','2026-04-11 17:57:25','2026-04-11 17:57:25'),('55957dc7-9de9-4dfe-bec8-273d45060c88','Cíntia Pereira Araújo',NULL,'03702813900',NULL,NULL,NULL,'1999-05-27','Feminino','Solteiro(a)','Brasileiro(a)',NULL,'Auxiliar de Estoque',1600.00,NULL,NULL,'cintia.araujo@supermercadobh.com.br','31991000037',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2023-07-01','TURNOB',0,'Estoque','2026-04-11 17:57:25','2026-04-11 17:57:25'),('561e3f47-10f1-45d0-8207-b4f0c113132f','Douglas Machado Freitas',NULL,'03602713800',NULL,NULL,NULL,'2000-11-15','Masculino','Solteiro(a)','Brasileiro(a)',NULL,'Repositor(a)',1700.00,NULL,NULL,'douglas.freitas@supermercadobh.com.br','31991000036',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2023-03-01','TURNOA',0,'Estoque','2026-04-11 17:57:25','2026-04-11 17:57:25'),('5e55c075-177c-4aa3-ac96-e0f76a573b0b','Camila Duarte Vieira',NULL,'01500611700',NULL,NULL,NULL,'1992-02-18','Feminino','Solteiro(a)','Brasileiro(a)',NULL,'Analista de RH',3800.00,NULL,NULL,'camila.vieira@supermercadobh.com.br','31991000015',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2020-03-01','ADM',0,'RH','2026-04-11 17:57:25','2026-04-11 17:57:25'),('5fff037c-fa2e-41ab-881f-3ba008783ad8','Leonardo Dias Ferreira',NULL,'05604715800',NULL,NULL,NULL,'1990-09-15','Masculino','Solteiro(a)','Brasileiro(a)',NULL,'Motorista/Entregador',2400.00,NULL,NULL,'leonardo.ferreira@supermercadobh.com.br','31991000056',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2020-10-01','ADM',0,'Logística','2026-04-11 17:57:25','2026-04-11 17:57:25'),('6174738d-b77e-4828-991a-1ca7ce8c9a24','Cristiano Lima Faria',NULL,'04503614700',NULL,NULL,NULL,'1991-12-19','Masculino','Solteiro(a)','Brasileiro(a)',NULL,'Auxiliar de Açougue',1750.00,NULL,NULL,'cristiano.faria@supermercadobh.com.br','31991000045',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2021-09-01','TURNOA',0,'Açougue','2026-04-11 17:57:25','2026-04-11 17:57:25'),('6cad0075-30c0-42c0-b21b-02f327cd8d58','Irene Nogueira Pereira',NULL,'04603714800',NULL,NULL,NULL,'1987-08-14','Feminino','Solteiro(a)','Brasileiro(a)',NULL,'Atendente de Hortifruti',1750.00,NULL,NULL,'irene.pereira@supermercadobh.com.br','31991000046',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2020-11-01','TURNOA',0,'Hortifruti','2026-04-11 17:57:25','2026-04-11 17:57:25'),('77864cab-00fd-4e56-bb10-37f2aa79877b','Maria do Carmo Leal',NULL,'04003114200',NULL,NULL,NULL,'1985-10-18','Feminino','Solteiro(a)','Brasileiro(a)',NULL,'Padeiro(a)',2400.00,NULL,NULL,'maria.leal@supermercadobh.com.br','31991000040',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2018-06-01','MADRUGADA',0,'Padaria','2026-04-11 17:57:25','2026-04-11 17:57:25'),('79e7fd11-80f2-45a9-96a5-31506b7cc89a','Valério Santos Gomes',NULL,'01200311400',NULL,NULL,NULL,'1985-03-07','Masculino','Solteiro(a)','Brasileiro(a)',NULL,'Supervisor de Segurança',4500.00,NULL,NULL,'valerio.gomes@supermercadobh.com.br','31991000012',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2017-09-01','TURNOC',0,'Segurança','2026-04-11 17:57:25','2026-04-11 17:57:25'),('7e8deae3-0e36-4c49-a2e3-360c187cf5f0','Ana Paula Ferreira',NULL,'02001112200',NULL,NULL,NULL,'1998-03-14','Feminino','Solteiro(a)','Brasileiro(a)',NULL,'Operador(a) de Caixa',1800.00,NULL,NULL,'ana.ferreira@supermercadobh.com.br','31991000020',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2021-11-01','TURNOA',0,'Checkout','2026-04-11 17:57:25','2026-04-11 17:57:25'),('85eb7523-3499-4ea9-b996-6ed515a97844','André Luiz Pereira',NULL,'00700800900',NULL,NULL,NULL,'1978-06-30','Masculino','Solteiro(a)','Brasileiro(a)',NULL,'Gerente de Compras',8800.00,NULL,NULL,'andre.pereira@supermercadobh.com.br','31991000007',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2014-08-01','ADM',0,'Compras','2026-04-11 17:57:25','2026-04-11 17:57:25'),('8668154b-2ce6-4183-a3f3-3c43511e46e6','Marcos Antônio Silveira',NULL,'00100200300',NULL,NULL,NULL,'1972-03-15','Masculino','Solteiro(a)','Brasileiro(a)',NULL,'Diretor Geral',18500.00,NULL,NULL,'marcos.silveira@supermercadobh.com.br','31991000001',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2010-03-01','ADM',0,'Diretoria','2026-04-11 17:57:24','2026-04-11 17:57:24'),('8b519a8e-e6ab-4148-8161-96774200f2f0','Isabela Nascimento Reis',NULL,'01700811900',NULL,NULL,NULL,'1994-09-23','Feminino','Solteiro(a)','Brasileiro(a)',NULL,'Analista de TI',4200.00,NULL,NULL,'isabela.reis@supermercadobh.com.br','31991000017',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2021-06-01','ADM',0,'TI','2026-04-11 17:57:25','2026-04-11 17:57:25'),('8e482bea-2958-4f5e-8010-20ae6856a866','Evandro Costa Machado',NULL,'05504615700',NULL,NULL,NULL,'1984-01-07','Masculino','Solteiro(a)','Brasileiro(a)',NULL,'Motorista/Entregador',2400.00,NULL,NULL,'evandro.machado@supermercadobh.com.br','31991000055',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2018-05-01','ADM',0,'Logística','2026-04-11 17:57:25','2026-04-11 17:57:25'),('9856c42d-0c60-43a2-8560-0a5318178ca7','Rogério Mendes Coelho',NULL,'04403514600',NULL,NULL,NULL,'1983-05-03','Masculino','Solteiro(a)','Brasileiro(a)',NULL,'Açougueiro(a)',2800.00,NULL,NULL,'rogerio.coelho@supermercadobh.com.br','31991000044',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2017-02-01','TURNOB',0,'Açougue','2026-04-11 17:57:25','2026-04-11 17:57:25'),('994be2d7-d5f5-486f-ac26-e6459c5fbb83','Tatiany Borges Freitas',NULL,'05804916000',NULL,NULL,NULL,'1993-12-03','Feminino','Solteiro(a)','Brasileiro(a)',NULL,'Promotor(a) de Vendas',1900.00,NULL,NULL,'tatiany.freitas@supermercadobh.com.br','31991000058',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2022-02-01','TURNOA',0,'Marketing','2026-04-11 17:57:25','2026-04-11 17:57:25'),('b0590869-2204-4708-9bf4-5d523b38e7d7','Priscila Andrade Torres',NULL,'06005116200',NULL,NULL,NULL,'1994-07-22','Feminino','Solteiro(a)','Brasileiro(a)',NULL,'Operador(a) de SAC',2100.00,NULL,NULL,'priscila.torres@supermercadobh.com.br','31991000060',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2021-04-01','ADM',0,'Atendimento','2026-04-11 17:57:25','2026-04-11 17:57:25'),('b8df2a31-3339-4b2a-b8df-8a9f3fdfe093','Edson Carlos Vieira',NULL,'03402513600',NULL,NULL,NULL,'1988-12-26','Masculino','Solteiro(a)','Brasileiro(a)',NULL,'Repositor(a)',1700.00,NULL,NULL,'edson.vieira@supermercadobh.com.br','31991000034',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2018-03-01','TURNOB',0,'Estoque','2026-04-11 17:57:25','2026-04-11 17:57:25'),('b958bd2a-facc-496e-8a06-7e25d5a594ce','Ricardo Alves Moreira',NULL,'00900011100',NULL,NULL,NULL,'1988-08-18','Masculino','Solteiro(a)','Brasileiro(a)',NULL,'Gerente de TI',9200.00,NULL,NULL,'ricardo.moreira@supermercadobh.com.br','31991000009',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2018-04-01','ADM',0,'TI','2026-04-11 17:57:25','2026-04-11 17:57:25'),('be45269c-860c-416f-8062-d289f28c2178','Gustavo Henrique Alves',NULL,'02301412500',NULL,NULL,NULL,'2000-05-16','Masculino','Solteiro(a)','Brasileiro(a)',NULL,'Operador(a) de Caixa',1800.00,NULL,NULL,'gustavo.alves@supermercadobh.com.br','31991000023',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2022-06-01','TURNOA',0,'Checkout','2026-04-11 17:57:25','2026-04-11 17:57:25'),('bec16dd1-cf7c-4a7e-a203-0d95e1bec22f','Francisco das Chagas Lopes',NULL,'05204315400',NULL,NULL,NULL,'1977-08-13','Masculino','Solteiro(a)','Brasileiro(a)',NULL,'Auxiliar de Limpeza',1412.00,NULL,NULL,'francisco.lopes@supermercadobh.com.br','31991000052',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2019-01-10','TURNOB',0,'Conservação','2026-04-11 17:57:25','2026-04-11 17:57:25'),('beca2d10-fc74-4b1c-b2a0-eae79c5d9ab5','Sônia Maria Figueiredo',NULL,'01300411500',NULL,NULL,NULL,'1982-07-25','Feminino','Solteiro(a)','Brasileiro(a)',NULL,'Supervisor de Padaria',4700.00,NULL,NULL,'sonia.figueiredo@supermercadobh.com.br','31991000013',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2016-03-01','TURNOA',0,'Padaria','2026-04-11 17:57:25','2026-04-11 17:57:25'),('bfa5624a-d54a-4e1b-8166-88d09080f2e2','Juliana Moreira Lima',NULL,'02401512600',NULL,NULL,NULL,'1998-09-03','Feminino','Solteiro(a)','Brasileiro(a)',NULL,'Operador(a) de Caixa',1800.00,NULL,NULL,'juliana.lima@supermercadobh.com.br','31991000024',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2022-07-20','TURNOB',0,'Checkout','2026-04-11 17:57:25','2026-04-11 17:57:25'),('c2e8964f-2e1c-4962-b170-5e108a674d2a','Henrique Soares Dias',NULL,'04203314400',NULL,NULL,NULL,'1993-02-14','Masculino','Solteiro(a)','Brasileiro(a)',NULL,'Auxiliar de Padaria',1700.00,NULL,NULL,'henrique.dias@supermercadobh.com.br','31991000042',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2022-05-01','TURNOA',0,'Padaria','2026-04-11 17:57:25','2026-04-11 17:57:25'),('c2e95635-654d-4507-9fe3-018f0e7938e9','Mariana Cristina Leite',NULL,'02201312400',NULL,NULL,NULL,'1997-11-22','Feminino','Solteiro(a)','Brasileiro(a)',NULL,'Operador(a) de Caixa',1800.00,NULL,NULL,'mariana.leite@supermercadobh.com.br','31991000022',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2022-03-15','TURNOC',0,'Checkout','2026-04-11 17:57:25','2026-04-11 17:57:25'),('c90d3b77-892b-4917-bd85-08b05c4ed34f','Luciana Ferreira Matos',NULL,'00200300400',NULL,NULL,NULL,'1975-07-22','Feminino','Solteiro(a)','Brasileiro(a)',NULL,'Diretor Comercial',15800.00,NULL,NULL,'luciana.matos@supermercadobh.com.br','31991000002',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2012-06-01','ADM',0,'Diretoria','2026-04-11 17:57:24','2026-04-11 17:57:24'),('cc8127a9-91c1-44cc-90d1-8232bf4e0cd3','Simone Cristina Martins',NULL,'03302413500',NULL,NULL,NULL,'1994-03-13','Feminino','Solteiro(a)','Brasileiro(a)',NULL,'Repositor(a)',1700.00,NULL,NULL,'simone.martins@supermercadobh.com.br','31991000033',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2021-10-01','TURNOA',0,'Estoque','2026-04-11 17:57:25','2026-04-11 17:57:25'),('d8b9e99d-4e0e-4f0e-8893-77784c5e4755','Bruna Tavares Lopes',NULL,'02601712800',NULL,NULL,NULL,'1999-12-11','Feminino','Solteiro(a)','Brasileiro(a)',NULL,'Operador(a) de Caixa',1800.00,NULL,NULL,'bruna.lopes@supermercadobh.com.br','31991000026',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2023-02-01','TURNOA',0,'Checkout','2026-04-11 17:57:25','2026-04-11 17:57:25'),('e09e1434-7774-4d89-9591-ddae50efad26','Jonas Ferreira da Silva',NULL,'04703814900',NULL,NULL,NULL,'1992-04-22','Masculino','Solteiro(a)','Brasileiro(a)',NULL,'Atendente de Hortifruti',1750.00,NULL,NULL,'jonas.silva@supermercadobh.com.br','31991000047',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2022-03-01','TURNOB',0,'Hortifruti','2026-04-11 17:57:25','2026-04-11 17:57:25'),('e296d459-e091-4206-a005-34575cfb7893','Rafael Costa Mendes',NULL,'02501612700',NULL,NULL,NULL,'2001-02-27','Masculino','Solteiro(a)','Brasileiro(a)',NULL,'Operador(a) de Caixa',1800.00,NULL,NULL,'rafael.mendes@supermercadobh.com.br','31991000025',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2023-01-05','TURNOC',0,'Checkout','2026-04-11 17:57:25','2026-04-11 17:57:25'),('e32b0e87-e782-486d-a8ba-2bb13d40c637','Neuza Santos Carvalho',NULL,'05304415500',NULL,NULL,NULL,'1982-05-09','Feminino','Solteiro(a)','Brasileiro(a)',NULL,'Auxiliar de Limpeza',1412.00,NULL,NULL,'neuza.carvalho@supermercadobh.com.br','31991000053',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2020-06-01','TURNOC',0,'Conservação','2026-04-11 17:57:25','2026-04-11 17:57:25'),('e396c27e-f382-42e1-baa4-5e0e0db16e65','Bruno César Magalhães',NULL,'01600711800',NULL,NULL,NULL,'1991-06-10','Masculino','Solteiro(a)','Brasileiro(a)',NULL,'Analista Financeiro',3900.00,NULL,NULL,'bruno.magalhaes@supermercadobh.com.br','31991000016',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2020-01-15','ADM',0,'Financeiro','2026-04-11 17:57:25','2026-04-11 17:57:25'),('e48014f4-b088-451e-8ffa-ec9e65874e4c','Fernanda Rocha Santos',NULL,'00800901000',NULL,NULL,NULL,'1985-12-05','Feminino','Solteiro(a)','Brasileiro(a)',NULL,'Gerente de Estoque',7900.00,NULL,NULL,'fernanda.santos@supermercadobh.com.br','31991000008',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2015-11-01','ADM',0,'Estoque','2026-04-11 17:57:25','2026-04-11 17:57:25'),('e560742d-124d-4063-9bec-e7c3c2ccb518','Larissa Fonseca Gomes',NULL,'02801913000',NULL,NULL,NULL,'2001-06-04','Feminino','Solteiro(a)','Brasileiro(a)',NULL,'Operador(a) de Caixa',1800.00,NULL,NULL,'larissa.gomes@supermercadobh.com.br','31991000028',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2023-06-01','TURNOC',0,'Checkout','2026-04-11 17:57:25','2026-04-11 17:57:25'),('ea918f39-3809-446e-8778-accefb3d79c2','Josefa Cristina Batista',NULL,'04103214300',NULL,NULL,NULL,'1989-06-30','Feminino','Solteiro(a)','Brasileiro(a)',NULL,'Confeiteiro(a)',2600.00,NULL,NULL,'josefa.batista@supermercadobh.com.br','31991000041',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2019-04-01','TURNOA',0,'Padaria','2026-04-11 17:57:25','2026-04-11 17:57:25'),('fbec0947-986a-4f07-88b0-3c370408a294','Roberto Assis Lima',NULL,'05905016100',NULL,NULL,NULL,'1996-04-17','Masculino','Solteiro(a)','Brasileiro(a)',NULL,'Promotor(a) de Vendas',1900.00,NULL,NULL,'roberto.lima@supermercadobh.com.br','31991000059',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2023-08-01','TURNOB',0,'Marketing','2026-04-11 17:57:25','2026-04-11 17:57:25'),('fd41d20e-ee01-4e1d-8f51-9c1a93232796','Tatiana Rocha Pires',NULL,'03502613700',NULL,NULL,NULL,'1997-08-09','Feminino','Solteiro(a)','Brasileiro(a)',NULL,'Repositor(a)',1700.00,NULL,NULL,'tatiana.pires@supermercadobh.com.br','31991000035',NULL,'Belo Horizonte, MG','30000000','Rua não informada',NULL,NULL,NULL,'Belo Horizonte','MG','Brasil',NULL,NULL,NULL,NULL,NULL,1,'2022-09-20','TURNOC',0,'Estoque','2026-04-11 17:57:25','2026-04-11 17:57:25');
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `entrances`
--

DROP TABLE IF EXISTS `entrances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `entrances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `entrances`
--

LOCK TABLES `entrances` WRITE;
/*!40000 ALTER TABLE `entrances` DISABLE KEYS */;
/*!40000 ALTER TABLE `entrances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exits`
--

DROP TABLE IF EXISTS `exits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exits` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exits`
--

LOCK TABLES `exits` WRITE;
/*!40000 ALTER TABLE `exits` DISABLE KEYS */;
/*!40000 ALTER TABLE `exits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `financial_reports`
--

DROP TABLE IF EXISTS `financial_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `financial_reports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `financial_reports`
--

LOCK TABLES `financial_reports` WRITE;
/*!40000 ALTER TABLE `financial_reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `financial_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fiscal_notes`
--

DROP TABLE IF EXISTS `fiscal_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fiscal_notes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `client_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_number` varchar(9) COLLATE utf8mb4_unicode_ci NOT NULL,
  `series` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `access_key` varchar(44) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('nfe','nfce') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'nfe',
  `environment` enum('production','homologation') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'homologation',
  `status` enum('draft','sent','authorized','rejected','cancelled','denied') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `protocol` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sefaz_message` text COLLATE utf8mb4_unicode_ci,
  `authorized_at` timestamp NULL DEFAULT NULL,
  `cancel_protocol` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cancel_reason` text COLLATE utf8mb4_unicode_ci,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `xml_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `xml_cancel_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `emitted_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fiscal_notes_access_key_unique` (`access_key`),
  KEY `fiscal_notes_client_id_foreign` (`client_id`),
  KEY `fiscal_notes_emitted_by_foreign` (`emitted_by`),
  KEY `fiscal_notes_status_created_at_index` (`status`,`created_at`),
  KEY `fiscal_notes_environment_type_index` (`environment`,`type`),
  CONSTRAINT `fiscal_notes_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fiscal_notes_emitted_by_foreign` FOREIGN KEY (`emitted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fiscal_notes`
--

LOCK TABLES `fiscal_notes` WRITE;
/*!40000 ALTER TABLE `fiscal_notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `fiscal_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grupo_tributarios`
--

DROP TABLE IF EXISTS `grupo_tributarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `grupo_tributarios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nome` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` text COLLATE utf8mb4_unicode_ci,
  `regime_tributario` enum('simples_nacional','lucro_presumido','lucro_real','todos') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'todos',
  `ncm` varchar(8) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo_operacao_saida_id` bigint unsigned DEFAULT NULL,
  `tipo_operacao_entrada_id` bigint unsigned DEFAULT NULL,
  `icms_cst` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icms_modalidade_bc` tinyint DEFAULT NULL,
  `icms_aliquota` decimal(5,2) DEFAULT NULL,
  `icms_reducao_bc` decimal(5,2) DEFAULT NULL,
  `ipi_cst` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ipi_modalidade` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ipi_aliquota` decimal(5,2) DEFAULT NULL,
  `pis_cst` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pis_aliquota` decimal(5,2) DEFAULT NULL,
  `cofins_cst` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cofins_aliquota` decimal(5,2) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `grupo_tributarios_codigo_unique` (`codigo`),
  KEY `grupo_tributarios_tipo_operacao_saida_id_foreign` (`tipo_operacao_saida_id`),
  KEY `grupo_tributarios_tipo_operacao_entrada_id_foreign` (`tipo_operacao_entrada_id`),
  KEY `grupo_tributarios_regime_tributario_is_active_index` (`regime_tributario`,`is_active`),
  CONSTRAINT `grupo_tributarios_tipo_operacao_entrada_id_foreign` FOREIGN KEY (`tipo_operacao_entrada_id`) REFERENCES `tipo_operacoes_fiscais` (`id`) ON DELETE SET NULL,
  CONSTRAINT `grupo_tributarios_tipo_operacao_saida_id_foreign` FOREIGN KEY (`tipo_operacao_saida_id`) REFERENCES `tipo_operacoes_fiscais` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grupo_tributarios`
--

LOCK TABLES `grupo_tributarios` WRITE;
/*!40000 ALTER TABLE `grupo_tributarios` DISABLE KEYS */;
INSERT INTO `grupo_tributarios` VALUES (1,'GT-MERC-SN','Mercadoria para Revenda – Simples Nacional','Grupo tributário padrão para empresas optantes pelo Simples Nacional que revendem mercadorias.','simples_nacional',NULL,9,6,'400',NULL,0.00,NULL,NULL,NULL,NULL,'07',0.00,'07',0.00,1,NULL,'2026-04-11 17:57:25','2026-04-11 17:57:25'),(2,'GT-MERC-LP','Mercadoria para Revenda – Lucro Presumido','Grupo tributário para empresas no regime de Lucro Presumido.','lucro_presumido',NULL,1,6,'00',3,12.00,NULL,NULL,NULL,NULL,'01',0.65,'01',3.00,1,NULL,'2026-04-11 17:57:25','2026-04-11 17:57:25'),(3,'GT-ST','Mercadoria com Substituição Tributária','Para produtos sujeitos à ST (ex: bebidas, combustíveis, autopeças).','todos',NULL,3,6,'60',NULL,0.00,NULL,NULL,NULL,NULL,'01',0.65,'01',3.00,1,NULL,'2026-04-11 17:57:25','2026-04-11 17:57:25'),(4,'GT-SERVICO','Prestação de Serviços','Grupo para serviços gerais (não sujeitos a IPI).','todos',NULL,1,NULL,'41',NULL,0.00,NULL,'53',NULL,0.00,'01',0.65,'01',3.00,1,NULL,'2026-04-11 17:57:25','2026-04-11 17:57:25'),(5,'GT-ISENTO','Mercadoria Isenta','Para produtos com isenção fiscal (ex: livros, alguns alimentos).','todos',NULL,1,6,'40',NULL,0.00,NULL,NULL,NULL,NULL,'07',0.00,'07',0.00,1,NULL,'2026-04-11 17:57:25','2026-04-11 17:57:25');
/*!40000 ALTER TABLE `grupo_tributarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mensagens_suporte`
--

DROP TABLE IF EXISTS `mensagens_suporte`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mensagens_suporte` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ticket_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `conteudo` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_suporte` tinyint(1) NOT NULL DEFAULT '0',
  `is_ia` tinyint(1) NOT NULL DEFAULT '0',
  `lida` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mensagens_suporte_ticket_id_foreign` (`ticket_id`),
  KEY `mensagens_suporte_user_id_foreign` (`user_id`),
  CONSTRAINT `mensagens_suporte_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `tickets_suporte` (`id`) ON DELETE CASCADE,
  CONSTRAINT `mensagens_suporte_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mensagens_suporte`
--

LOCK TABLES `mensagens_suporte` WRITE;
/*!40000 ALTER TABLE `mensagens_suporte` DISABLE KEYS */;
INSERT INTO `mensagens_suporte` VALUES ('02de5ba2-ef99-42c4-914d-0316d498f53a','4350b4e1-e78b-42f5-931d-dc6b9baf07c4',1,'🕒 **Ticket fechado automaticamente por inatividade**\n\nEste ticket foi encerrado devido à falta de atividade nos últimos 30 minutos.\n\n**Precisa de mais ajuda?**\n- Você pode abrir um novo ticket a qualquer momento\n- Para suporte urgente: **WhatsApp (32) 98450-2345**\n\nObrigado por utilizar o Nexora ERP! 🚀',1,1,1,'2026-04-15 21:45:40','2026-04-16 12:29:34'),('19a71ad2-b091-494d-8ede-a919fdc5b4ca','ae5584fc-1967-41d6-9c74-2eb7253ffbcd',1,'🕒 **Ticket fechado automaticamente por inatividade**\n\nEste ticket foi encerrado devido à falta de atividade nos últimos 30 minutos.\n\n**Precisa de mais ajuda?**\n- Você pode abrir um novo ticket a qualquer momento\n- Para suporte urgente: **WhatsApp (32) 98450-2345**\n\nObrigado por utilizar o Nexora ERP! 🚀',1,1,1,'2026-04-15 21:45:40','2026-04-16 21:29:50'),('2adb3291-aa20-4a0b-a646-ec57c944a46f','4350b4e1-e78b-42f5-931d-dc6b9baf07c4',11,'Erro ao gerar nota fiscal de venda',0,0,1,'2026-04-15 21:31:11','2026-04-16 12:29:02'),('2e0ee821-96e8-4ef1-b227-567aca58c01a','79f7fd43-0943-495d-9aee-fb4a6eed328a',1,'🕒 **Ticket fechado automaticamente por inatividade**\n\nEste ticket foi encerrado devido à falta de atividade nos últimos 30 minutos.\n\n**Precisa de mais ajuda?**\n- Você pode abrir um novo ticket a qualquer momento\n- Para suporte urgente: **WhatsApp (32) 98450-2345**\n\nObrigado por utilizar o Nexora ERP! 🚀',1,1,1,'2026-04-15 21:45:40','2026-04-16 21:29:51'),('378aa4ea-f850-465d-aa93-e7d747c60d81','4350b4e1-e78b-42f5-931d-dc6b9baf07c4',1,'⚠️ *Modo Simplificado* — Minha IA está temporariamente indisponível devido a limitações da API.\n\n**Como emitir Nota Fiscal Eletrônica:**\n\n1. Acesse **Fiscal > NF-e > Nova Nota**\n2. Selecione o cliente e tipo de operação (CFOP)\n3. Adicione os produtos\n4. Verifique os impostos (ICMS, IPI, PIS/COFINS)\n5. Clique em **Gerar NF-e**\n\n**Problemas comuns:**\n- ❌ Certificado digital vencido → Atualize em **Fiscal > Configurações**\n- ❌ Cliente sem CPF/CNPJ → Complete o cadastro\n- ❌ Produto sem NCM → Configure em **Cadastro > Produtos**\n\n---\n\n🤖 Para respostas mais detalhadas com consulta aos seus dados reais, aguarde alguns minutos ou **entre em contato via WhatsApp: (32) 98450-2345**',1,1,1,'2026-04-15 21:36:32','2026-04-15 21:36:32'),('39354165-4134-4634-833a-4031e72754cd','4350b4e1-e78b-42f5-931d-dc6b9baf07c4',11,'Erro ao gerar nota fiscal de venda\n',0,0,1,'2026-04-15 21:36:30','2026-04-16 12:29:02'),('50c69e5b-ff66-4bca-b12a-660a951ff251','9786f037-6c51-4655-8be1-83658a0ba8ba',1,'⚠️ *Modo Simplificado* — Minha IA está temporariamente indisponível devido a limitações da API.\n\n**Como emitir Nota Fiscal Eletrônica:**\n\n1. Acesse **Fiscal > NF-e > Nova Nota**\n2. Selecione o cliente e tipo de operação (CFOP)\n3. Adicione os produtos\n4. Verifique os impostos (ICMS, IPI, PIS/COFINS)\n5. Clique em **Gerar NF-e**\n\n**Problemas comuns:**\n- ❌ Certificado digital vencido → Atualize em **Fiscal > Configurações**\n- ❌ Cliente sem CPF/CNPJ → Complete o cadastro\n- ❌ Produto sem NCM → Configure em **Cadastro > Produtos**\n\n---\n\n🤖 Para respostas mais detalhadas com consulta aos seus dados reais, aguarde alguns minutos ou **entre em contato via WhatsApp: (32) 98450-2345**',1,1,1,'2026-04-16 21:31:30','2026-04-16 21:31:30'),('531b26a3-4564-4178-8ab9-5dbb905f1d28','79f7fd43-0943-495d-9aee-fb4a6eed328a',11,'ERRO AO FATURAR NOTA FISCAL',0,0,0,'2026-04-15 20:58:02','2026-04-15 20:58:02'),('58bee831-2b78-4277-9658-eaca4935f393','79f7fd43-0943-495d-9aee-fb4a6eed328a',1,'Desculpe, estou temporariamente indisponível devido ao alto volume de requisições. Por favor, aguarde alguns minutos e tente novamente, ou entre em contato via WhatsApp: **(32) 98450-2345**',1,1,1,'2026-04-15 21:31:44','2026-04-15 21:31:44'),('7544e784-0c79-4d60-9cdd-f5f84d9eac00','79f7fd43-0943-495d-9aee-fb4a6eed328a',11,'Finalizar',0,0,0,'2026-04-15 21:31:43','2026-04-15 21:31:43'),('8387237d-4622-4e62-91e8-b032545c3dd4','2163781b-3c22-4a6c-9dec-60846ea8d802',11,'Erro ao gerar nota fiscal de venda',0,0,0,'2026-04-15 21:27:43','2026-04-15 21:27:43'),('85d7f2d3-8cd2-437a-af97-3df9ebd6d974','9786f037-6c51-4655-8be1-83658a0ba8ba',11,'tirando uma nota fiscal de venda apareceu a seguinte rejeição na nota fiscal: A Rejeição 778: Informado NCM inexistente [item8]\n',0,0,1,'2026-04-16 21:31:28','2026-04-20 18:45:49'),('aa852f48-3fdd-4dbb-b29d-14e4bc90ace5','2163781b-3c22-4a6c-9dec-60846ea8d802',1,'🕒 **Ticket fechado automaticamente por inatividade**\n\nEste ticket foi encerrado devido à falta de atividade nos últimos 30 minutos.\n\n**Precisa de mais ajuda?**\n- Você pode abrir um novo ticket a qualquer momento\n- Para suporte urgente: **WhatsApp (32) 98450-2345**\n\nObrigado por utilizar o Nexora ERP! 🚀',1,1,1,'2026-04-15 21:45:40','2026-04-16 21:29:49'),('b8d82125-8cd0-4638-b2f5-c2b9c736cfc2','ae5584fc-1967-41d6-9c74-2eb7253ffbcd',11,'Está dando erro ao tentar emitir uma nota ficar de um pedido de venda',0,0,0,'2026-04-15 21:10:44','2026-04-15 21:10:44'),('ba53b0e1-789d-4534-82ec-0a30f3682a2a','4350b4e1-e78b-42f5-931d-dc6b9baf07c4',1,'Desculpe, estou temporariamente indisponível devido ao alto volume de requisições. Por favor, aguarde alguns minutos e tente novamente, ou entre em contato via WhatsApp: **(32) 98450-2345**',1,1,1,'2026-04-15 21:31:12','2026-04-15 21:31:13');
/*!40000 ALTER TABLE `mensagens_suporte` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=104 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'000000_create_users_table',1),(2,'000001_create_cache_table',1),(3,'000002_create_jobs_table',1),(4,'2026_02_28_163928_create_employees_table',1),(5,'2026_02_28_164205_create_vehicles_table',1),(6,'2026_03_01_170000_create_roles_table',1),(7,'2026_03_01_170001_create_production_orders_table',1),(8,'2026_03_01_170002_create_stocks_table',1),(9,'2026_03_01_170003_create_requests_table',1),(10,'2026_03_01_170004_create_visits_table',1),(11,'2026_03_01_170005_create_sales_reports_table',1),(12,'2026_03_01_170006_create_entrances_table',1),(13,'2026_03_01_170007_create_exits_table',1),(14,'2026_03_01_170008_create_plans_of_accounts_table',1),(15,'2026_03_01_170009_create_baccarat_accounts_table',1),(16,'2026_03_01_170010_create_accounts_payable_table',1),(17,'2026_03_01_170011_create_accounts_receivable_table',1),(18,'2026_03_01_170012_create_cash_flows_table',1),(19,'2026_03_01_170013_create_financial_reports_table',1),(20,'2026_03_01_170014_create_working_days_table',1),(21,'2026_03_01_170015_create_stitch_beats_table',1),(22,'2026_03_01_170016_create_payrolls_table',1),(23,'2026_03_01_170017_create_employee_managements_table',1),(24,'2026_03_01_170018_create_rh_reports_table',1),(25,'2026_03_01_170019_create_route_managements_table',1),(26,'2026_03_01_170020_create_routings_table',1),(27,'2026_03_01_170021_create_scheduling_of_deliveries_table',1),(28,'2026_03_01_170022_create_monitoring_of_deliveries_table',1),(29,'2026_03_01_170023_create_driver_managements_table',1),(30,'2026_03_01_170024_create_romaneios_table',1),(31,'2026_03_01_170025_create_vehicle_trackings_table',1),(32,'2026_03_01_170026_create_vehicle_maintenances_table',1),(33,'2026_03_01_170027_create_transport_reports_table',1),(34,'2026_03_01_173425_create_suppliers_table',1),(35,'2026_03_01_173444_create_products_table',1),(36,'2026_03_01_181020_create_clients_table',1),(37,'2026_03_01_204610_create_configurations_table',1),(38,'2026_03_01_204629_create_profiles_table',1),(39,'2026_03_01_210301_create_product_suppliers_table',1),(40,'2026_04_03_000001_add_admin_and_last_login_columns_to_users_table',1),(41,'2026_04_03_150423_add_status_and_license_to_users_table',1),(42,'2026_04_03_152259_add_modules_to_users_table',1),(43,'2026_04_03_200000_create_settings_table',1),(44,'2026_04_04_150756_add_address_fields_to_clients_table',1),(45,'2026_04_04_160513_add_modules_column_to_users_table',1),(46,'2026_04_04_170017_add_extended_fields_to_products_table',1),(47,'2026_04_04_171847_make_ean_and_description_nullable_in_products',1),(48,'2026_04_05_000001_add_extended_fields_to_employees_table',1),(49,'2026_04_05_181331_update_roles_table_add_fields',1),(50,'2026_04_05_184330_add_fleet_columns_to_vehicles_table',1),(51,'2026_04_05_194039_create_tickets_suporte_table',1),(52,'2026_04_05_194057_create_mensagens_suporte_table',1),(53,'2026_04_05_200001_create_system_logs_table',1),(54,'2026_04_06_000001_add_cost_price_and_stock_min_to_products_table',1),(55,'2026_04_06_000002_make_suppliers_name_nullable',1),(56,'2026_04_06_100001_add_profile_fields_to_users_table',1),(57,'2026_04_06_210826_create_notifications_table',1),(58,'2026_04_06_213909_add_hierarchy_fields_to_plans_of_accounts_table',1),(59,'2026_04_07_000001_update_baccarat_accounts_add_bank_fields',1),(60,'2026_04_08_000001_create_product_categories_table',1),(61,'2026_04_08_000002_create_units_of_measure_table',1),(62,'2026_04_08_000003_add_category_unit_fk_to_products_table',1),(63,'2026_04_08_100001_update_accounts_payable_add_full_fields',1),(64,'2026_04_08_211653_update_accounts_receivable_add_full_fields',1),(65,'2026_04_09_000001_update_payrolls_add_full_fields',1),(66,'2026_04_09_000002_create_payroll_items_table',1),(67,'2026_04_09_222259_create_stock_movements_table',1),(68,'2026_04_10_121747_create_work_shifts_table',1),(69,'2026_04_10_121751_create_time_records_table',1),(70,'2026_04_10_200000_create_fiscal_notes_table',1),(71,'2026_04_10_220001_update_production_orders_add_full_fields',1),(72,'2026_04_10_220002_create_production_items_table',1),(73,'2026_04_10_230001_create_production_order_products_table',1),(74,'2026_04_10_235001_create_sales_orders_table',1),(75,'2026_04_10_235002_create_sales_order_items_table',1),(76,'2026_04_10_240001_create_tipo_operacoes_fiscais_table',1),(77,'2026_04_10_240002_create_grupo_tributarios_table',1),(78,'2026_04_10_240003_add_fiscal_fields_to_products_table',1),(79,'2026_04_11_000001_update_sales_orders_add_full_fields',1),(80,'2026_04_11_000002_create_sales_order_addresses_table',1),(81,'2026_04_11_000003_create_sales_order_payments_table',1),(82,'2026_04_11_000004_create_sales_order_installments_table',1),(83,'2026_04_11_000005_update_sales_order_items_add_full_fields',1),(84,'2026_04_11_000006_create_sales_order_logs_table',1),(85,'2026_04_11_000007_create_sales_order_attachments_table',1),(86,'2026_04_11_000008_create_price_tables_table',1),(87,'2026_04_11_000009_create_price_table_items_table',1),(88,'2026_04_11_000010_create_carriers_table',1),(89,'2026_04_11_000011_update_clients_add_sales_fields',1),(90,'2026_04_11_100001_create_purchase_orders_table',1),(91,'2026_04_11_100002_create_purchase_order_items_table',1),(92,'2026_04_11_200001_create_cotacoes_table',1),(93,'2026_04_11_200002_create_cotacao_items_table',1),(94,'2026_04_11_200003_create_cotacao_respostas_table',1),(95,'2026_04_11_300001_create_purchase_requisitions_table',1),(96,'2026_04_11_300002_create_purchase_requisition_items_table',1),(97,'2026_04_11_300003_make_purchase_orders_supplier_nullable',1),(98,'2026_04_11_400001_create_delivery_time_windows_table',1),(99,'2026_04_11_400002_update_scheduling_of_deliveries_table',1),(100,'2026_04_14_000001_create_companies_table',2),(101,'2026_04_14_000002_add_company_id_to_users_table',2),(103,'2026_04_15_204439_add_is_ia_to_mensagens_suporte_table',3);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `monitoring_of_deliveries`
--

DROP TABLE IF EXISTS `monitoring_of_deliveries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `monitoring_of_deliveries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `monitoring_of_deliveries`
--

LOCK TABLES `monitoring_of_deliveries` WRITE;
/*!40000 ALTER TABLE `monitoring_of_deliveries` DISABLE KEYS */;
/*!40000 ALTER TABLE `monitoring_of_deliveries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint unsigned NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_items`
--

DROP TABLE IF EXISTS `payroll_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payroll_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `payroll_id` bigint unsigned NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('earning','deduction') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'earning',
  `amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payroll_items_payroll_id_foreign` (`payroll_id`),
  CONSTRAINT `payroll_items_payroll_id_foreign` FOREIGN KEY (`payroll_id`) REFERENCES `payrolls` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_items`
--

LOCK TABLES `payroll_items` WRITE;
/*!40000 ALTER TABLE `payroll_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `payroll_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payrolls`
--

DROP TABLE IF EXISTS `payrolls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payrolls` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_month` date DEFAULT NULL,
  `base_salary` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_earnings` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_deductions` decimal(15,2) NOT NULL DEFAULT '0.00',
  `net_salary` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `payment_date` date DEFAULT NULL,
  `observations` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payrolls_employee_id_foreign` (`employee_id`),
  CONSTRAINT `payrolls_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payrolls`
--

LOCK TABLES `payrolls` WRITE;
/*!40000 ALTER TABLE `payrolls` DISABLE KEYS */;
INSERT INTO `payrolls` VALUES (1,'009a1384-5d1d-41af-a01a-5211830ceebd','2026-04-01',1412.00,0.00,0.00,1412.00,'draft',NULL,NULL,'2026-04-13 21:34:47','2026-04-13 21:34:47'),(2,'040b200c-0e2f-4da2-98b9-6fae3fb799f2','2026-04-01',2200.00,0.00,0.00,2200.00,'draft',NULL,NULL,'2026-04-13 21:34:47','2026-04-13 21:34:47'),(3,'04b0ca55-2c2a-4865-8da0-a411eabc616c','2026-04-01',1700.00,0.00,0.00,1700.00,'draft',NULL,NULL,'2026-04-13 21:34:47','2026-04-13 21:34:47'),(4,'08aa2340-0016-4350-81a2-7bf6585a4a80','2026-04-01',3600.00,0.00,0.00,3600.00,'draft',NULL,NULL,'2026-04-13 21:34:47','2026-04-13 21:34:47'),(5,'0d08912c-1d3f-4c64-91c4-eb9280e390d8','2026-04-01',16200.00,0.00,0.00,16200.00,'draft',NULL,NULL,'2026-04-13 21:34:47','2026-04-13 21:34:47'),(6,'1169275b-d47c-43e5-ad89-bbd3f6f288f9','2026-04-01',4800.00,0.00,0.00,4800.00,'draft',NULL,NULL,'2026-04-13 21:34:47','2026-04-13 21:34:47'),(7,'135ccc36-5608-47ac-9e02-e2c3b6a981da','2026-04-01',8500.00,0.00,0.00,8500.00,'draft',NULL,NULL,'2026-04-13 21:34:47','2026-04-13 21:34:47'),(8,'13a25382-565e-4da4-af7f-c63d423d7d18','2026-04-01',2800.00,0.00,0.00,2800.00,'draft',NULL,NULL,'2026-04-13 21:34:47','2026-04-13 21:34:47'),(9,'13eac7ba-8683-4790-bec7-b5d1d8e3bae4','2026-04-01',2200.00,0.00,0.00,2200.00,'draft',NULL,NULL,'2026-04-13 21:34:47','2026-04-13 21:34:47'),(10,'1a9cb80e-4042-420c-8b33-223b67c53a8e','2026-04-01',9800.00,0.00,0.00,9800.00,'draft',NULL,NULL,'2026-04-13 21:34:47','2026-04-13 21:34:47'),(11,'1db870e0-c68c-4c22-94b4-c4da6b267c44','2026-04-01',1800.00,0.00,0.00,1800.00,'draft',NULL,NULL,'2026-04-13 21:34:47','2026-04-13 21:34:47'),(12,'1f36c247-1de1-4bdc-99ce-ac9770bcf1f6','2026-04-01',4600.00,0.00,0.00,4600.00,'draft',NULL,NULL,'2026-04-13 21:34:48','2026-04-13 21:34:48'),(13,'294b23b3-9044-454e-b7ac-dad71f68000f','2026-04-01',1800.00,0.00,0.00,1800.00,'draft',NULL,NULL,'2026-04-13 21:34:48','2026-04-13 21:34:48'),(14,'2a25dbdd-5460-44f1-965a-7f4508c84b41','2026-04-01',4800.00,0.00,0.00,4800.00,'draft',NULL,NULL,'2026-04-13 21:34:48','2026-04-13 21:34:48'),(15,'2b674743-e636-4e64-a73f-c24c82332b1f','2026-04-01',1700.00,0.00,0.00,1700.00,'draft',NULL,NULL,'2026-04-13 21:34:48','2026-04-13 21:34:48'),(16,'30edbf86-e553-4e91-ba7b-45486dc415ce','2026-04-01',1700.00,0.00,0.00,1700.00,'draft',NULL,NULL,'2026-04-13 21:34:48','2026-04-13 21:34:48'),(17,'31659f66-0ca5-4298-a985-a342cf288f62','2026-04-01',8200.00,0.00,0.00,8200.00,'draft',NULL,NULL,'2026-04-13 21:34:48','2026-04-13 21:34:48'),(18,'3cd593e5-a891-435f-aae1-e021807d0c6f','2026-04-01',2800.00,0.00,0.00,2800.00,'draft',NULL,NULL,'2026-04-13 21:34:48','2026-04-13 21:34:48'),(19,'3ffe9ca6-e714-49e7-8803-eb8b045f0f65','2026-04-01',2400.00,0.00,0.00,2400.00,'draft',NULL,NULL,'2026-04-13 21:34:48','2026-04-13 21:34:48'),(20,'41100142-cce8-40ea-8045-5d07a631690f','2026-04-01',2200.00,0.00,0.00,2200.00,'draft',NULL,NULL,'2026-04-13 21:34:48','2026-04-13 21:34:48'),(21,'4294f329-3094-4d80-b315-89f80e30ed9f','2026-04-01',1800.00,0.00,0.00,1800.00,'draft',NULL,NULL,'2026-04-13 21:34:48','2026-04-13 21:34:48'),(22,'4889d258-74ff-4e90-8941-eb3ddf37e11d','2026-04-01',1600.00,0.00,0.00,1600.00,'draft',NULL,NULL,'2026-04-13 21:34:48','2026-04-13 21:34:48'),(23,'4df530f6-76d0-4b63-9dea-6ef18a582caf','2026-04-01',2200.00,0.00,0.00,2200.00,'draft',NULL,NULL,'2026-04-13 21:34:48','2026-04-13 21:34:48'),(24,'522e429a-0de2-4333-a5b7-d6936889050d','2026-04-01',1700.00,0.00,0.00,1700.00,'draft',NULL,NULL,'2026-04-13 21:34:48','2026-04-13 21:34:48'),(25,'55957dc7-9de9-4dfe-bec8-273d45060c88','2026-04-01',1600.00,0.00,0.00,1600.00,'draft',NULL,NULL,'2026-04-13 21:34:48','2026-04-13 21:34:48'),(26,'561e3f47-10f1-45d0-8207-b4f0c113132f','2026-04-01',1700.00,0.00,0.00,1700.00,'draft',NULL,NULL,'2026-04-13 21:34:48','2026-04-13 21:34:48'),(27,'5e55c075-177c-4aa3-ac96-e0f76a573b0b','2026-04-01',3800.00,0.00,0.00,3800.00,'draft',NULL,NULL,'2026-04-13 21:34:48','2026-04-13 21:34:48'),(28,'5fff037c-fa2e-41ab-881f-3ba008783ad8','2026-04-01',2400.00,0.00,0.00,2400.00,'draft',NULL,NULL,'2026-04-13 21:34:48','2026-04-13 21:34:48'),(29,'6174738d-b77e-4828-991a-1ca7ce8c9a24','2026-04-01',1750.00,0.00,0.00,1750.00,'draft',NULL,NULL,'2026-04-13 21:34:48','2026-04-13 21:34:48'),(30,'6cad0075-30c0-42c0-b21b-02f327cd8d58','2026-04-01',1750.00,0.00,0.00,1750.00,'draft',NULL,NULL,'2026-04-13 21:34:48','2026-04-13 21:34:48'),(31,'77864cab-00fd-4e56-bb10-37f2aa79877b','2026-04-01',2400.00,0.00,0.00,2400.00,'draft',NULL,NULL,'2026-04-13 21:34:48','2026-04-13 21:34:48'),(32,'79e7fd11-80f2-45a9-96a5-31506b7cc89a','2026-04-01',4500.00,0.00,0.00,4500.00,'draft',NULL,NULL,'2026-04-13 21:34:48','2026-04-13 21:34:48'),(33,'7e8deae3-0e36-4c49-a2e3-360c187cf5f0','2026-04-01',1800.00,0.00,0.00,1800.00,'paid','2026-04-13',NULL,'2026-04-13 21:34:48','2026-04-13 21:35:13'),(34,'85eb7523-3499-4ea9-b996-6ed515a97844','2026-04-01',8800.00,0.00,0.00,8800.00,'paid','2026-04-13',NULL,'2026-04-13 21:34:48','2026-04-13 22:07:16'),(35,'8668154b-2ce6-4183-a3f3-3c43511e46e6','2026-04-01',18500.00,0.00,0.00,18500.00,'draft',NULL,NULL,'2026-04-13 21:34:48','2026-04-13 21:34:48'),(36,'8b519a8e-e6ab-4148-8161-96774200f2f0','2026-04-01',4200.00,0.00,0.00,4200.00,'draft',NULL,NULL,'2026-04-13 21:34:48','2026-04-13 21:34:48'),(37,'8e482bea-2958-4f5e-8010-20ae6856a866','2026-04-01',2400.00,0.00,0.00,2400.00,'draft',NULL,NULL,'2026-04-13 21:34:48','2026-04-13 21:34:48'),(38,'9856c42d-0c60-43a2-8560-0a5318178ca7','2026-04-01',2800.00,0.00,0.00,2800.00,'draft',NULL,NULL,'2026-04-13 21:34:48','2026-04-13 21:34:48'),(39,'994be2d7-d5f5-486f-ac26-e6459c5fbb83','2026-04-01',1900.00,0.00,0.00,1900.00,'draft',NULL,NULL,'2026-04-13 21:34:48','2026-04-13 21:34:48'),(40,'b0590869-2204-4708-9bf4-5d523b38e7d7','2026-04-01',2100.00,0.00,0.00,2100.00,'draft',NULL,NULL,'2026-04-13 21:34:48','2026-04-13 21:34:48'),(41,'b8df2a31-3339-4b2a-b8df-8a9f3fdfe093','2026-04-01',1700.00,0.00,0.00,1700.00,'draft',NULL,NULL,'2026-04-13 21:34:48','2026-04-13 21:34:48'),(42,'b958bd2a-facc-496e-8a06-7e25d5a594ce','2026-04-01',9200.00,0.00,0.00,9200.00,'draft',NULL,NULL,'2026-04-13 21:34:48','2026-04-13 21:34:48'),(43,'be45269c-860c-416f-8062-d289f28c2178','2026-04-01',1800.00,0.00,0.00,1800.00,'draft',NULL,NULL,'2026-04-13 21:34:48','2026-04-13 21:34:48'),(44,'bec16dd1-cf7c-4a7e-a203-0d95e1bec22f','2026-04-01',1412.00,0.00,0.00,1412.00,'draft',NULL,NULL,'2026-04-13 21:34:48','2026-04-13 21:34:48'),(45,'beca2d10-fc74-4b1c-b2a0-eae79c5d9ab5','2026-04-01',4700.00,0.00,0.00,4700.00,'draft',NULL,NULL,'2026-04-13 21:34:48','2026-04-13 21:34:48'),(46,'bfa5624a-d54a-4e1b-8166-88d09080f2e2','2026-04-01',1800.00,0.00,0.00,1800.00,'draft',NULL,NULL,'2026-04-13 21:34:48','2026-04-13 21:34:48'),(47,'c2e8964f-2e1c-4962-b170-5e108a674d2a','2026-04-01',1700.00,0.00,0.00,1700.00,'draft',NULL,NULL,'2026-04-13 21:34:48','2026-04-13 21:34:48'),(48,'c2e95635-654d-4507-9fe3-018f0e7938e9','2026-04-01',1800.00,0.00,0.00,1800.00,'draft',NULL,NULL,'2026-04-13 21:34:48','2026-04-13 21:34:48'),(49,'c90d3b77-892b-4917-bd85-08b05c4ed34f','2026-04-01',15800.00,0.00,0.00,15800.00,'draft',NULL,NULL,'2026-04-13 21:34:48','2026-04-13 21:34:48'),(50,'cc8127a9-91c1-44cc-90d1-8232bf4e0cd3','2026-04-01',1700.00,0.00,0.00,1700.00,'draft',NULL,NULL,'2026-04-13 21:34:48','2026-04-13 21:34:48'),(51,'d8b9e99d-4e0e-4f0e-8893-77784c5e4755','2026-04-01',1800.00,0.00,0.00,1800.00,'draft',NULL,NULL,'2026-04-13 21:34:48','2026-04-13 21:34:48'),(52,'e09e1434-7774-4d89-9591-ddae50efad26','2026-04-01',1750.00,0.00,0.00,1750.00,'draft',NULL,NULL,'2026-04-13 21:34:48','2026-04-13 21:34:48'),(53,'e296d459-e091-4206-a005-34575cfb7893','2026-04-01',1800.00,0.00,0.00,1800.00,'draft',NULL,NULL,'2026-04-13 21:34:48','2026-04-13 21:34:48'),(54,'e32b0e87-e782-486d-a8ba-2bb13d40c637','2026-04-01',1412.00,0.00,0.00,1412.00,'draft',NULL,NULL,'2026-04-13 21:34:48','2026-04-13 21:34:48'),(55,'e396c27e-f382-42e1-baa4-5e0e0db16e65','2026-04-01',3900.00,0.00,0.00,3900.00,'draft',NULL,NULL,'2026-04-13 21:34:48','2026-04-13 21:34:48'),(56,'e48014f4-b088-451e-8ffa-ec9e65874e4c','2026-04-01',7900.00,0.00,0.00,7900.00,'draft',NULL,NULL,'2026-04-13 21:34:48','2026-04-13 21:34:48'),(57,'e560742d-124d-4063-9bec-e7c3c2ccb518','2026-04-01',1800.00,0.00,0.00,1800.00,'draft',NULL,NULL,'2026-04-13 21:34:49','2026-04-13 21:34:49'),(58,'ea918f39-3809-446e-8778-accefb3d79c2','2026-04-01',2600.00,0.00,0.00,2600.00,'draft',NULL,NULL,'2026-04-13 21:34:49','2026-04-13 21:34:49'),(59,'fbec0947-986a-4f07-88b0-3c370408a294','2026-04-01',1900.00,0.00,0.00,1900.00,'draft',NULL,NULL,'2026-04-13 21:34:49','2026-04-13 21:34:49'),(60,'fd41d20e-ee01-4e1d-8f51-9c1a93232796','2026-04-01',1700.00,0.00,0.00,1700.00,'draft',NULL,NULL,'2026-04-13 21:34:49','2026-04-13 21:34:49');
/*!40000 ALTER TABLE `payrolls` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plans_of_accounts`
--

DROP TABLE IF EXISTS `plans_of_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `plans_of_accounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint unsigned DEFAULT NULL,
  `code` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('receita','despesa','ativo','passivo') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_selectable` tinyint(1) NOT NULL DEFAULT '1',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `plans_of_accounts_parent_id_foreign` (`parent_id`),
  CONSTRAINT `plans_of_accounts_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `plans_of_accounts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plans_of_accounts`
--

LOCK TABLES `plans_of_accounts` WRITE;
/*!40000 ALTER TABLE `plans_of_accounts` DISABLE KEYS */;
INSERT INTO `plans_of_accounts` VALUES (1,NULL,'1','receita',0,1,'Receitas',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(2,1,'1.1','receita',0,1,'Receitas Operacionais',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(3,2,'1.1.001','receita',1,1,'Venda de Produtos',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(4,2,'1.1.002','receita',1,1,'Prestação de Serviços',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(5,2,'1.1.003','receita',1,1,'Comissões Recebidas',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(6,1,'1.2','receita',0,1,'Receitas Financeiras',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(7,6,'1.2.001','receita',1,1,'Juros Recebidos',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(8,6,'1.2.002','receita',1,1,'Rendimentos Financeiros',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(9,1,'1.3','receita',0,1,'Outras Receitas',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(10,9,'1.3.001','receita',1,1,'Receitas Diversas',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(11,9,'1.3.002','receita',1,1,'Recuperação de Despesas',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(12,NULL,'2','despesa',0,1,'Custos',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(13,12,'2.1','despesa',0,1,'Custo de Mercadorias Vendidas',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(14,13,'2.1.001','despesa',1,1,'CMV — Mercadorias',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(15,13,'2.1.002','despesa',1,1,'CMV — Matéria-Prima',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(16,12,'2.2','despesa',0,1,'Custo dos Serviços Prestados',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(17,16,'2.2.001','despesa',1,1,'Mão de Obra Direta',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(18,16,'2.2.002','despesa',1,1,'Materiais de Produção',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(19,NULL,'3','despesa',0,1,'Despesas Operacionais',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(20,19,'3.1','despesa',0,1,'Despesas Administrativas',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(21,20,'3.1.001','despesa',1,1,'Aluguel',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(22,20,'3.1.002','despesa',1,1,'Energia Elétrica',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(23,20,'3.1.003','despesa',1,1,'Água e Saneamento',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(24,20,'3.1.004','despesa',1,1,'Telefone / Internet',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(25,20,'3.1.005','despesa',1,1,'Material de Escritório',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(26,20,'3.1.006','despesa',1,1,'Manutenção e Reparos',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(27,19,'3.2','despesa',0,1,'Despesas com Pessoal',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(28,27,'3.2.001','despesa',1,1,'Salários e Ordenados',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(29,27,'3.2.002','despesa',1,1,'Encargos Sociais (FGTS / INSS)',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(30,27,'3.2.003','despesa',1,1,'Vale-Transporte',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(31,27,'3.2.004','despesa',1,1,'Vale-Refeição',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(32,27,'3.2.005','despesa',1,1,'Plano de Saúde',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(33,27,'3.2.006','despesa',1,1,'13º Salário',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(34,27,'3.2.007','despesa',1,1,'Férias',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(35,19,'3.3','despesa',0,1,'Despesas Financeiras',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(36,35,'3.3.001','despesa',1,1,'Juros e Multas',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(37,35,'3.3.002','despesa',1,1,'Tarifas Bancárias',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(38,35,'3.3.003','despesa',1,1,'IOF',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(39,19,'3.4','despesa',0,1,'Despesas com Veículos e Frota',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(40,39,'3.4.001','despesa',1,1,'Combustível',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(41,39,'3.4.002','despesa',1,1,'Manutenção de Veículos',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(42,39,'3.4.003','despesa',1,1,'Seguro de Frota',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(43,39,'3.4.004','despesa',1,1,'IPVA e Licenciamento',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(44,NULL,'4','despesa',0,1,'Impostos e Tributos',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(45,44,'4.1','despesa',0,1,'Impostos sobre Faturamento',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(46,45,'4.1.001','despesa',1,1,'ICMS',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(47,45,'4.1.002','despesa',1,1,'PIS',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(48,45,'4.1.003','despesa',1,1,'COFINS',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(49,45,'4.1.004','despesa',1,1,'ISS',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(50,45,'4.1.005','despesa',1,1,'IPI',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(51,44,'4.2','despesa',0,1,'Impostos sobre o Lucro',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(52,51,'4.2.001','despesa',1,1,'IRPJ',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(53,51,'4.2.002','despesa',1,1,'CSLL',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(54,NULL,'5','ativo',0,1,'Ativo',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(55,54,'5.1','ativo',0,1,'Ativo Circulante',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(56,55,'5.1.001','ativo',1,1,'Caixa',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(57,55,'5.1.002','ativo',1,1,'Bancos',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(58,55,'5.1.003','ativo',1,1,'Contas a Receber',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(59,55,'5.1.004','ativo',1,1,'Estoque',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(60,54,'5.2','ativo',0,1,'Ativo Não Circulante',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(61,60,'5.2.001','ativo',1,1,'Imóveis',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(62,60,'5.2.002','ativo',1,1,'Veículos',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(63,60,'5.2.003','ativo',1,1,'Maquinário',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(64,NULL,'6','passivo',0,1,'Passivo',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(65,64,'6.1','passivo',0,1,'Passivo Circulante',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(66,65,'6.1.001','passivo',1,1,'Contas a Pagar',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(67,65,'6.1.002','passivo',1,1,'Salários a Pagar',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(68,65,'6.1.003','passivo',1,1,'Impostos a Recolher',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(69,64,'6.2','passivo',0,1,'Passivo Não Circulante',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(70,69,'6.2.001','passivo',1,1,'Empréstimos e Financiamentos',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22'),(71,69,'6.2.002','passivo',1,1,'Debêntures',NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22');
/*!40000 ALTER TABLE `plans_of_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `price_table_items`
--

DROP TABLE IF EXISTS `price_table_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `price_table_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `price_table_id` bigint unsigned NOT NULL,
  `product_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `minimum_price` decimal(15,2) DEFAULT NULL,
  `promotional_price` decimal(15,2) DEFAULT NULL,
  `promotional_valid_from` date DEFAULT NULL,
  `promotional_valid_until` date DEFAULT NULL,
  `quantity_from` decimal(15,3) DEFAULT NULL,
  `quantity_to` decimal(15,3) DEFAULT NULL,
  `quantity_price` decimal(15,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `price_table_items_price_table_id_product_id_quantity_from_unique` (`price_table_id`,`product_id`,`quantity_from`),
  KEY `price_table_items_product_id_foreign` (`product_id`),
  CONSTRAINT `price_table_items_price_table_id_foreign` FOREIGN KEY (`price_table_id`) REFERENCES `price_tables` (`id`) ON DELETE CASCADE,
  CONSTRAINT `price_table_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `price_table_items`
--

LOCK TABLES `price_table_items` WRITE;
/*!40000 ALTER TABLE `price_table_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `price_table_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `price_tables`
--

DROP TABLE IF EXISTS `price_tables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `price_tables` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `valid_from` date DEFAULT NULL,
  `valid_until` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `price_tables_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `price_tables`
--

LOCK TABLES `price_tables` WRITE;
/*!40000 ALTER TABLE `price_tables` DISABLE KEYS */;
/*!40000 ALTER TABLE `price_tables` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_categories`
--

DROP TABLE IF EXISTS `product_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_categories` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `color` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#6366F1',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_categories_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_categories`
--

LOCK TABLES `product_categories` WRITE;
/*!40000 ALTER TABLE `product_categories` DISABLE KEYS */;
INSERT INTO `product_categories` VALUES ('0cebc537-30b2-4ba1-8447-7a12ce023303','Hortifruti','hortifruti','Frutas, legumes, verduras e temperos frescos','#10B981',1,'2026-04-11 17:57:21','2026-04-11 17:57:21',NULL),('0f8ce34d-e338-45ac-9ec7-7f0ac75f4140','Bebê e Criança','bebe-crianca','Fraldas, papinhas, leite infantil e produtos para bebês','#FBBF24',1,'2026-04-11 17:57:22','2026-04-11 17:57:22',NULL),('180adce5-9814-4351-99a4-1bff104b0d08','Açougue','acougue','Cortes especiais e processados do açougue interno','#DC2626',1,'2026-04-11 17:57:22','2026-04-11 17:57:22',NULL),('297d7768-c12d-4581-a661-e35762bbf9b4','Bazar e Utilidades','bazar-utilidades','Utensílios domésticos, pilhas, descartáveis e papelaria','#F97316',1,'2026-04-11 17:57:22','2026-04-11 17:57:22',NULL),('4f8c85f2-4400-455d-b93e-350703f0a060','Congelados e Refrigerados','congelados-refrigerados','Refeições prontas congeladas, sorvetes, pizzas e massas frescas','#6366F1',1,'2026-04-11 17:57:22','2026-04-11 17:57:22',NULL),('6c10ef54-c8c9-4080-8b15-5132ef592c7a','Padaria (Produção Própria)','padaria-producao-propria','Produtos fabricados na padaria interna do supermercado','#D97706',1,'2026-04-11 17:57:22','2026-04-11 17:57:22',NULL),('7940f1d2-310f-408f-9d15-0e46577a9269','Carnes e Peixes','carnes-e-peixes','Carnes bovinas, suínas, aves, peixes e frutos do mar','#EF4444',1,'2026-04-11 17:57:21','2026-04-11 17:57:21',NULL),('96a196e4-e61e-46ef-8e0b-65e8a8a433cd','Higiene Pessoal','higiene-pessoal','Shampoo, sabonete, creme dental, desodorante e cuidados pessoais','#EC4899',1,'2026-04-11 17:57:22','2026-04-11 17:57:22',NULL),('b7bca2ae-8caa-4f31-9aa3-9b7683022d67','Bebidas','bebidas','Água, sucos, refrigerantes, cervejas, vinhos e destilados','#06B6D4',1,'2026-04-11 17:57:22','2026-04-11 17:57:22',NULL),('d6c50329-d925-49b0-bee1-a7e598910681','Pet Shop','pet-shop','Ração, petiscos, acessórios e higiene para animais de estimação','#A78BFA',1,'2026-04-11 17:57:22','2026-04-11 17:57:22',NULL),('e9546890-3cd3-49e4-9586-f0099dc34dbd','Mercearia Seca','mercearia-seca','Arroz, feijão, macarrão, farinhas, óleos e temperos','#8B5CF6',1,'2026-04-11 17:57:22','2026-04-11 17:57:22',NULL),('eaf59113-cb76-4c6e-868c-21a424bc76c4','Limpeza e Conservação','limpeza-conservacao','Detergentes, desinfetantes, alvejantes e produtos de limpeza geral','#14B8A6',1,'2026-04-11 17:57:22','2026-04-11 17:57:22',NULL),('efd1a890-6cb4-452d-aa69-61c722c81454','Padaria e Confeitaria','padaria-confeitaria','Pães, bolos, doces, salgados e produtos de confeitaria','#F59E0B',1,'2026-04-11 17:57:22','2026-04-11 17:57:22',NULL),('f26ea04e-0384-42a2-85a3-5ca92dd79177','Laticínios e Frios','laticinios-frios','Leite, queijos, iogurtes, manteiga, frios e embutidos','#3B82F6',1,'2026-04-11 17:57:22','2026-04-11 17:57:22',NULL);
/*!40000 ALTER TABLE `product_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_supplier`
--

DROP TABLE IF EXISTS `product_supplier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_supplier` (
  `product_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `product_supplier_product_id_supplier_id_unique` (`product_id`,`supplier_id`),
  KEY `product_supplier_supplier_id_foreign` (`supplier_id`),
  CONSTRAINT `product_supplier_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_supplier_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_supplier`
--

LOCK TABLES `product_supplier` WRITE;
/*!40000 ALTER TABLE `product_supplier` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_supplier` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `production_items`
--

DROP TABLE IF EXISTS `production_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `production_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `production_order_id` bigint unsigned NOT NULL,
  `component_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `required_qty` decimal(15,3) NOT NULL,
  `consumed_qty` decimal(15,3) NOT NULL DEFAULT '0.000',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `production_items_production_order_id_foreign` (`production_order_id`),
  KEY `production_items_component_id_foreign` (`component_id`),
  CONSTRAINT `production_items_component_id_foreign` FOREIGN KEY (`component_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `production_items_production_order_id_foreign` FOREIGN KEY (`production_order_id`) REFERENCES `production_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `production_items`
--

LOCK TABLES `production_items` WRITE;
/*!40000 ALTER TABLE `production_items` DISABLE KEYS */;
INSERT INTO `production_items` VALUES (1,1,'0346bcba-e13d-4238-a71d-3cc088a2bc56',520.000,510.000,'2026-04-14 22:53:19','2026-04-14 22:53:19'),(2,1,'03aee4fe-e5f9-48f5-b5e2-ae64010f879c',10.000,10.000,'2026-04-14 22:53:19','2026-04-14 22:53:19'),(3,2,'04541d4d-6596-424c-a9fe-4902708d08cb',800.000,600.000,'2026-04-14 22:53:19','2026-04-14 22:53:19'),(4,2,'07928bf6-6acd-4b51-bfa7-3ccd891a6b39',50.000,40.000,'2026-04-14 22:53:19','2026-04-14 22:53:19'),(5,2,'081c19b2-f7ba-48cf-afe4-766973ae243b',100.000,80.000,'2026-04-14 22:53:19','2026-04-14 22:53:19'),(6,3,'08c5de51-5762-43ff-8d98-1b18ab475703',200.000,0.000,'2026-04-14 22:53:19','2026-04-14 22:53:19'),(7,3,'0da05818-ae14-4846-91a4-e9046e50f090',200.000,0.000,'2026-04-14 22:53:19','2026-04-14 22:53:19'),(8,3,'1036ecdd-24af-40b1-8318-64f0bb94ad05',200.000,0.000,'2026-04-14 22:53:19','2026-04-14 22:53:19'),(9,3,'12aa060f-9a39-4dd5-bd6f-f2bec366f925',200.000,0.000,'2026-04-14 22:53:19','2026-04-14 22:53:19'),(10,4,'15aac3d5-03b4-4e53-94be-079cb16e9fb9',2000.000,0.000,'2026-04-14 22:53:19','2026-04-14 22:53:19'),(11,4,'17bf6c49-0ac1-423f-ab5c-03546019d347',500.000,0.000,'2026-04-14 22:53:19','2026-04-14 22:53:19'),(12,5,'1aae08d8-cd77-4199-ba04-ea5cda4fd374',6000.000,5950.000,'2026-04-14 22:53:19','2026-04-14 22:53:19'),(13,5,'1af5ba27-dcf7-495a-8264-2ad2ffbce15c',120.000,115.000,'2026-04-14 22:53:19','2026-04-14 22:53:19'),(14,6,'0346bcba-e13d-4238-a71d-3cc088a2bc56',520.000,510.000,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(15,6,'03aee4fe-e5f9-48f5-b5e2-ae64010f879c',10.000,10.000,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(16,7,'04541d4d-6596-424c-a9fe-4902708d08cb',800.000,600.000,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(17,7,'07928bf6-6acd-4b51-bfa7-3ccd891a6b39',50.000,40.000,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(18,7,'081c19b2-f7ba-48cf-afe4-766973ae243b',100.000,80.000,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(19,8,'08c5de51-5762-43ff-8d98-1b18ab475703',200.000,0.000,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(20,8,'0da05818-ae14-4846-91a4-e9046e50f090',200.000,0.000,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(21,8,'1036ecdd-24af-40b1-8318-64f0bb94ad05',200.000,0.000,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(22,8,'12aa060f-9a39-4dd5-bd6f-f2bec366f925',200.000,0.000,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(23,9,'15aac3d5-03b4-4e53-94be-079cb16e9fb9',2000.000,0.000,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(24,9,'17bf6c49-0ac1-423f-ab5c-03546019d347',500.000,0.000,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(25,10,'1aae08d8-cd77-4199-ba04-ea5cda4fd374',6000.000,5950.000,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(26,10,'1af5ba27-dcf7-495a-8264-2ad2ffbce15c',120.000,115.000,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(27,11,'0346bcba-e13d-4238-a71d-3cc088a2bc56',520.000,510.000,'2026-04-14 22:55:42','2026-04-14 22:55:42'),(28,11,'03aee4fe-e5f9-48f5-b5e2-ae64010f879c',10.000,10.000,'2026-04-14 22:55:42','2026-04-14 22:55:42'),(29,12,'04541d4d-6596-424c-a9fe-4902708d08cb',800.000,600.000,'2026-04-14 22:55:42','2026-04-14 22:55:42'),(30,12,'07928bf6-6acd-4b51-bfa7-3ccd891a6b39',50.000,40.000,'2026-04-14 22:55:42','2026-04-14 22:55:42'),(31,12,'081c19b2-f7ba-48cf-afe4-766973ae243b',100.000,80.000,'2026-04-14 22:55:42','2026-04-14 22:55:42'),(32,13,'08c5de51-5762-43ff-8d98-1b18ab475703',200.000,0.000,'2026-04-14 22:55:42','2026-04-14 22:55:42'),(33,13,'0da05818-ae14-4846-91a4-e9046e50f090',200.000,0.000,'2026-04-14 22:55:42','2026-04-14 22:55:42'),(34,13,'1036ecdd-24af-40b1-8318-64f0bb94ad05',200.000,0.000,'2026-04-14 22:55:42','2026-04-14 22:55:42'),(35,13,'12aa060f-9a39-4dd5-bd6f-f2bec366f925',200.000,0.000,'2026-04-14 22:55:42','2026-04-14 22:55:42'),(36,14,'15aac3d5-03b4-4e53-94be-079cb16e9fb9',2000.000,0.000,'2026-04-14 22:55:42','2026-04-14 22:55:42'),(37,14,'17bf6c49-0ac1-423f-ab5c-03546019d347',500.000,0.000,'2026-04-14 22:55:42','2026-04-14 22:55:42'),(38,15,'1aae08d8-cd77-4199-ba04-ea5cda4fd374',6000.000,5950.000,'2026-04-14 22:55:42','2026-04-14 22:55:42'),(39,15,'1af5ba27-dcf7-495a-8264-2ad2ffbce15c',120.000,115.000,'2026-04-14 22:55:42','2026-04-14 22:55:42');
/*!40000 ALTER TABLE `production_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `production_order_products`
--

DROP TABLE IF EXISTS `production_order_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `production_order_products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `production_order_id` bigint unsigned NOT NULL,
  `product_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_quantity` decimal(15,3) NOT NULL,
  `produced_quantity` decimal(15,3) NOT NULL DEFAULT '0.000',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `production_order_products_production_order_id_foreign` (`production_order_id`),
  KEY `production_order_products_product_id_foreign` (`product_id`),
  CONSTRAINT `production_order_products_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `production_order_products_production_order_id_foreign` FOREIGN KEY (`production_order_id`) REFERENCES `production_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `production_order_products`
--

LOCK TABLES `production_order_products` WRITE;
/*!40000 ALTER TABLE `production_order_products` DISABLE KEYS */;
INSERT INTO `production_order_products` VALUES (1,1,'009bef2f-284d-4863-8d2d-490201e44be5',300.000,300.000,'2026-04-14 22:53:19','2026-04-14 22:53:19'),(2,1,'0346bcba-e13d-4238-a71d-3cc088a2bc56',200.000,200.000,'2026-04-14 22:53:19','2026-04-14 22:53:19'),(3,2,'03aee4fe-e5f9-48f5-b5e2-ae64010f879c',600.000,450.000,'2026-04-14 22:53:19','2026-04-14 22:53:19'),(4,2,'04541d4d-6596-424c-a9fe-4902708d08cb',400.000,300.000,'2026-04-14 22:53:19','2026-04-14 22:53:19'),(5,3,'081c19b2-f7ba-48cf-afe4-766973ae243b',200.000,0.000,'2026-04-14 22:53:19','2026-04-14 22:53:19'),(6,4,'14f01d52-3314-47ad-af2f-d79577a276bd',1200.000,0.000,'2026-04-14 22:53:19','2026-04-14 22:53:19'),(7,4,'15aac3d5-03b4-4e53-94be-079cb16e9fb9',800.000,0.000,'2026-04-14 22:53:19','2026-04-14 22:53:19'),(8,5,'1a7509ff-db38-4add-aa4c-ada8cee9e77b',1800.000,1800.000,'2026-04-14 22:53:19','2026-04-14 22:53:19'),(9,5,'1aae08d8-cd77-4199-ba04-ea5cda4fd374',1200.000,1200.000,'2026-04-14 22:53:19','2026-04-14 22:53:19'),(10,6,'009bef2f-284d-4863-8d2d-490201e44be5',300.000,300.000,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(11,6,'0346bcba-e13d-4238-a71d-3cc088a2bc56',200.000,200.000,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(12,7,'03aee4fe-e5f9-48f5-b5e2-ae64010f879c',600.000,450.000,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(13,7,'04541d4d-6596-424c-a9fe-4902708d08cb',400.000,300.000,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(14,8,'081c19b2-f7ba-48cf-afe4-766973ae243b',200.000,0.000,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(15,9,'14f01d52-3314-47ad-af2f-d79577a276bd',1200.000,0.000,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(16,9,'15aac3d5-03b4-4e53-94be-079cb16e9fb9',800.000,0.000,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(17,10,'1a7509ff-db38-4add-aa4c-ada8cee9e77b',1800.000,1800.000,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(18,10,'1aae08d8-cd77-4199-ba04-ea5cda4fd374',1200.000,1200.000,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(19,11,'009bef2f-284d-4863-8d2d-490201e44be5',300.000,300.000,'2026-04-14 22:55:42','2026-04-14 22:55:42'),(20,11,'0346bcba-e13d-4238-a71d-3cc088a2bc56',200.000,200.000,'2026-04-14 22:55:42','2026-04-14 22:55:42'),(21,12,'03aee4fe-e5f9-48f5-b5e2-ae64010f879c',600.000,450.000,'2026-04-14 22:55:42','2026-04-14 22:55:42'),(22,12,'04541d4d-6596-424c-a9fe-4902708d08cb',400.000,300.000,'2026-04-14 22:55:42','2026-04-14 22:55:42'),(23,13,'081c19b2-f7ba-48cf-afe4-766973ae243b',200.000,0.000,'2026-04-14 22:55:42','2026-04-14 22:55:42'),(24,14,'14f01d52-3314-47ad-af2f-d79577a276bd',1200.000,0.000,'2026-04-14 22:55:42','2026-04-14 22:55:42'),(25,14,'15aac3d5-03b4-4e53-94be-079cb16e9fb9',800.000,0.000,'2026-04-14 22:55:42','2026-04-14 22:55:42'),(26,15,'1a7509ff-db38-4add-aa4c-ada8cee9e77b',1800.000,1800.000,'2026-04-14 22:55:42','2026-04-14 22:55:42'),(27,15,'1aae08d8-cd77-4199-ba04-ea5cda4fd374',1200.000,1200.000,'2026-04-14 22:55:42','2026-04-14 22:55:42');
/*!40000 ALTER TABLE `production_order_products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `production_orders`
--

DROP TABLE IF EXISTS `production_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `production_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `product_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `target_quantity` decimal(15,3) NOT NULL DEFAULT '1.000',
  `produced_quantity` decimal(15,3) NOT NULL DEFAULT '0.000',
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'planned',
  `estimated_cost` decimal(15,2) NOT NULL DEFAULT '0.00',
  `lot_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `user_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `production_orders_product_id_foreign` (`product_id`),
  KEY `production_orders_user_id_foreign` (`user_id`),
  CONSTRAINT `production_orders_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `production_orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `production_orders`
--

LOCK TABLES `production_orders` WRITE;
/*!40000 ALTER TABLE `production_orders` DISABLE KEYS */;
INSERT INTO `production_orders` VALUES (1,'OP-2026-001 | Produção Lote Arroz Branco','Produção do lote mensal de Arroz Branco Tipo 1 para reabastecimento do estoque.','009bef2f-284d-4863-8d2d-490201e44be5',500.000,500.000,'2026-03-15 22:53:19','2026-03-23 22:53:19','completed',4875.00,'LOTE-ARR-2026-04-001','Lote concluído dentro do prazo e sem perdas.',1,'2026-04-14 22:53:19','2026-04-14 22:53:19'),(2,'OP-2026-002 | Produção Molho de Tomate','Produção da linha de molho de tomate para atendimento dos pedidos de venda pendentes.','03aee4fe-e5f9-48f5-b5e2-ae64010f879c',1000.000,750.000,'2026-04-09 22:53:19','2026-04-17 22:53:19','in_progress',2200.00,'LOTE-MLH-2026-04-002','Em andamento. 75% concluído.',1,'2026-04-14 22:53:19','2026-04-14 22:53:19'),(3,'OP-2026-003 | Produção Kit Higiene Pessoal','Montagem e embalagem de kits de higiene pessoal para o canal de varejo.','081c19b2-f7ba-48cf-afe4-766973ae243b',200.000,0.000,'2026-04-16 22:53:19','2026-04-24 22:53:19','planned',8600.00,'LOTE-KIT-2026-04-003','Aguardando liberação do estoque de insumos.',1,'2026-04-14 22:53:19','2026-04-14 22:53:19'),(4,'OP-2026-004 | Produção Bebidas Energéticas','Produção e envase de bebidas energéticas para pedido de grande cliente.','14f01d52-3314-47ad-af2f-d79577a276bd',2000.000,0.000,'2026-04-06 22:53:19','2026-04-19 22:53:19','paused',13000.00,'LOTE-BEB-2026-04-004','Pausada por falta de matéria-prima (aguardando entrega do fornecedor).',1,'2026-04-14 22:53:19','2026-04-14 22:53:19'),(5,'OP-2026-005 | Produção Lote Laticínios','Produção e pasteurização de linha de laticínios para distribuição regional.','1a7509ff-db38-4add-aa4c-ada8cee9e77b',3000.000,3000.000,'2026-02-13 22:53:19','2026-02-25 22:53:19','completed',9600.00,'LOTE-LAT-2026-02-005','Concluído com aprovação do controle de qualidade.',1,'2026-04-14 22:53:19','2026-04-14 22:53:19'),(6,'OP-2026-001 | Produção Lote Arroz Branco','Produção do lote mensal de Arroz Branco Tipo 1 para reabastecimento do estoque.','009bef2f-284d-4863-8d2d-490201e44be5',500.000,500.000,'2026-03-15 22:53:55','2026-03-23 22:53:55','completed',4875.00,'LOTE-ARR-2026-04-001','Lote concluído dentro do prazo e sem perdas.',1,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(7,'OP-2026-002 | Produção Molho de Tomate','Produção da linha de molho de tomate para atendimento dos pedidos de venda pendentes.','03aee4fe-e5f9-48f5-b5e2-ae64010f879c',1000.000,750.000,'2026-04-09 22:53:55','2026-04-17 22:53:55','in_progress',2200.00,'LOTE-MLH-2026-04-002','Em andamento. 75% concluído.',1,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(8,'OP-2026-003 | Produção Kit Higiene Pessoal','Montagem e embalagem de kits de higiene pessoal para o canal de varejo.','081c19b2-f7ba-48cf-afe4-766973ae243b',200.000,0.000,'2026-04-16 22:53:55','2026-04-24 22:53:55','planned',8600.00,'LOTE-KIT-2026-04-003','Aguardando liberação do estoque de insumos.',1,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(9,'OP-2026-004 | Produção Bebidas Energéticas','Produção e envase de bebidas energéticas para pedido de grande cliente.','14f01d52-3314-47ad-af2f-d79577a276bd',2000.000,0.000,'2026-04-06 22:53:55','2026-04-19 22:53:55','paused',13000.00,'LOTE-BEB-2026-04-004','Pausada por falta de matéria-prima (aguardando entrega do fornecedor).',1,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(10,'OP-2026-005 | Produção Lote Laticínios','Produção e pasteurização de linha de laticínios para distribuição regional.','1a7509ff-db38-4add-aa4c-ada8cee9e77b',3000.000,3000.000,'2026-02-13 22:53:55','2026-02-25 22:53:55','completed',9600.00,'LOTE-LAT-2026-02-005','Concluído com aprovação do controle de qualidade.',1,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(11,'OP-2026-001 | Produção Lote Arroz Branco','Produção do lote mensal de Arroz Branco Tipo 1 para reabastecimento do estoque.','009bef2f-284d-4863-8d2d-490201e44be5',500.000,500.000,'2026-03-15 22:55:42','2026-03-23 22:55:42','completed',4875.00,'LOTE-ARR-2026-04-001','Lote concluído dentro do prazo e sem perdas.',1,'2026-04-14 22:55:42','2026-04-14 22:55:42'),(12,'OP-2026-002 | Produção Molho de Tomate','Produção da linha de molho de tomate para atendimento dos pedidos de venda pendentes.','03aee4fe-e5f9-48f5-b5e2-ae64010f879c',1000.000,750.000,'2026-04-09 22:55:42','2026-04-17 22:55:42','in_progress',2200.00,'LOTE-MLH-2026-04-002','Em andamento. 75% concluído.',1,'2026-04-14 22:55:42','2026-04-14 22:55:42'),(13,'OP-2026-003 | Produção Kit Higiene Pessoal','Montagem e embalagem de kits de higiene pessoal para o canal de varejo.','081c19b2-f7ba-48cf-afe4-766973ae243b',200.000,0.000,'2026-04-16 22:55:42','2026-04-24 22:55:42','planned',8600.00,'LOTE-KIT-2026-04-003','Aguardando liberação do estoque de insumos.',1,'2026-04-14 22:55:42','2026-04-14 22:55:42'),(14,'OP-2026-004 | Produção Bebidas Energéticas','Produção e envase de bebidas energéticas para pedido de grande cliente.','14f01d52-3314-47ad-af2f-d79577a276bd',2000.000,0.000,'2026-04-06 22:55:42','2026-04-19 22:55:42','paused',13000.00,'LOTE-BEB-2026-04-004','Pausada por falta de matéria-prima (aguardando entrega do fornecedor).',1,'2026-04-14 22:55:42','2026-04-14 22:55:42'),(15,'OP-2026-005 | Produção Lote Laticínios','Produção e pasteurização de linha de laticínios para distribuição regional.','1a7509ff-db38-4add-aa4c-ada8cee9e77b',3000.000,3000.000,'2026-02-13 22:55:42','2026-02-25 22:55:42','completed',9600.00,'LOTE-LAT-2026-02-005','Concluído com aprovação do controle de qualidade.',1,'2026-04-14 22:55:42','2026-04-14 22:55:42');
/*!40000 ALTER TABLE `production_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ean` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ncm` varchar(8) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cfop_saida` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cfop_entrada` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grupo_tributario_id` bigint unsigned DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `short_description` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `brand` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'produto_fisico',
  `nature` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'mercadoria_revenda',
  `product_line` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `weight_net` decimal(8,3) DEFAULT NULL,
  `weight_gross` decimal(8,3) DEFAULT NULL,
  `height` decimal(8,2) DEFAULT NULL,
  `width` decimal(8,2) DEFAULT NULL,
  `depth` decimal(8,2) DEFAULT NULL,
  `full_description` longtext COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `highlights` json DEFAULT NULL,
  `tags` json DEFAULT NULL,
  `unit_of_measure` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unidade',
  `unit_of_measure_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sale_price` decimal(8,2) DEFAULT NULL,
  `cost_price` decimal(8,2) DEFAULT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `stock_min` int NOT NULL DEFAULT '0',
  `expiration_date` date DEFAULT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_category_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_name_unique` (`name`),
  UNIQUE KEY `products_ean_unique` (`ean`),
  UNIQUE KEY `products_product_code_unique` (`product_code`),
  KEY `products_product_category_id_foreign` (`product_category_id`),
  KEY `products_unit_of_measure_id_foreign` (`unit_of_measure_id`),
  KEY `products_grupo_tributario_id_foreign` (`grupo_tributario_id`),
  CONSTRAINT `products_grupo_tributario_id_foreign` FOREIGN KEY (`grupo_tributario_id`) REFERENCES `grupo_tributarios` (`id`) ON DELETE SET NULL,
  CONSTRAINT `products_product_category_id_foreign` FOREIGN KEY (`product_category_id`) REFERENCES `product_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `products_unit_of_measure_id_foreign` FOREIGN KEY (`unit_of_measure_id`) REFERENCES `units_of_measure` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES ('009bef2f-284d-4863-8d2d-490201e44be5',NULL,'Escova Dental Oral-B Pro Saúde','7500435080324',NULL,NULL,NULL,NULL,'Escova Dental Oral-B Pro Saúde',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,7.99,5.10,500,60,NULL,'higiene-pessoal','96a196e4-e61e-46ef-8e0b-65e8a8a433cd',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('0346bcba-e13d-4238-a71d-3cc088a2bc56',NULL,'Condicionador Dove Hidratação 400ml','7891150057012',NULL,NULL,NULL,NULL,'Condicionador Dove Hidratação 400ml',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,17.49,11.20,300,35,NULL,'higiene-pessoal','96a196e4-e61e-46ef-8e0b-65e8a8a433cd',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('03aee4fe-e5f9-48f5-b5e2-ae64010f879c',NULL,'Farinha de Mandioca Yoki 500g','7896336402100',NULL,NULL,NULL,NULL,'Farinha de Mandioca Yoki 500g',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'PCT',NULL,4.29,2.70,350,50,NULL,'mercearia-seca','e9546890-3cd3-49e4-9586-f0099dc34dbd',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('04541d4d-6596-424c-a9fe-4902708d08cb',NULL,'Lenço Umedecido Huggies 50 unid.','7896007541020',NULL,NULL,NULL,NULL,'Lenço Umedecido Huggies 50 unid.',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'PCT',NULL,12.99,8.30,200,25,NULL,'bebe-crianca','0f8ce34d-e338-45ac-9ec7-7f0ac75f4140',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('07928bf6-6acd-4b51-bfa7-3ccd891a6b39',NULL,'Brócolis (maço)','2000000000019',NULL,NULL,NULL,NULL,'Brócolis (maço)',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,4.99,2.80,100,15,'2026-04-15','hortifruti','0cebc537-30b2-4ba1-8447-7a12ce023303',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('081c19b2-f7ba-48cf-afe4-766973ae243b',NULL,'Camarão Pequeno Congelado 300g','7896010000038',NULL,NULL,NULL,NULL,'Camarão Pequeno Congelado 300g',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'PCT',NULL,19.90,12.80,120,15,'2026-07-10','carnes-e-peixes','7940f1d2-310f-408f-9d15-0e46577a9269',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('08c5de51-5762-43ff-8d98-1b18ab475703',NULL,'Areia Higiênica Pipicat 4kg','7898413690034',NULL,NULL,NULL,NULL,'Areia Higiênica Pipicat 4kg',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'SC',NULL,22.90,14.80,80,10,NULL,'pet-shop','d6c50329-d925-49b0-bee1-a7e598910681',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('0da05818-ae14-4846-91a4-e9046e50f090',NULL,'Mel Puro Baldoni 280g','7896000566016',NULL,NULL,NULL,NULL,'Mel Puro Baldoni 280g',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,14.99,9.60,150,20,NULL,'mercearia-seca','e9546890-3cd3-49e4-9586-f0099dc34dbd',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('1036ecdd-24af-40b1-8318-64f0bb94ad05',NULL,'Arroz Integral Urbano 1kg','7896005302019',NULL,NULL,NULL,NULL,'Arroz Integral Urbano 1kg',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'PCT',NULL,8.49,5.80,220,30,NULL,'mercearia-seca','e9546890-3cd3-49e4-9586-f0099dc34dbd',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('12aa060f-9a39-4dd5-bd6f-f2bec366f925',NULL,'Sabonete em Barra 90g','7891000100105',NULL,NULL,NULL,NULL,'Sabonete em Barra 90g',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,1.99,0.00,500,0,'2027-10-11','outro',NULL,NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('14f01d52-3314-47ad-af2f-d79577a276bd',NULL,'Macarrão Parafuso Renata 500g','7896022203052',NULL,NULL,NULL,NULL,'Macarrão Parafuso Renata 500g',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'PCT',NULL,4.49,2.80,550,80,NULL,'mercearia-seca','e9546890-3cd3-49e4-9586-f0099dc34dbd',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('15aac3d5-03b4-4e53-94be-079cb16e9fb9',NULL,'Detergente Neutro 500ml','7891000100103',NULL,NULL,NULL,NULL,'Detergente Neutro 500ml',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,2.99,0.00,350,0,NULL,'outro',NULL,NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22',NULL),('17bf6c49-0ac1-423f-ab5c-03546019d347',NULL,'Molho de Tomate Heinz 340g','7896102503133',NULL,NULL,NULL,NULL,'Molho de Tomate Heinz 340g',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,3.49,2.20,750,100,NULL,'mercearia-seca','e9546890-3cd3-49e4-9586-f0099dc34dbd',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('1a7509ff-db38-4add-aa4c-ada8cee9e77b',NULL,'Petisco Pedigree Dentastix 77g','7896029040256',NULL,NULL,NULL,NULL,'Petisco Pedigree Dentastix 77g',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'PCT',NULL,6.99,4.30,200,25,NULL,'pet-shop','d6c50329-d925-49b0-bee1-a7e598910681',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('1aae08d8-cd77-4199-ba04-ea5cda4fd374',NULL,'Feijão Carioca Camil 1kg','7896006706060',NULL,NULL,NULL,NULL,'Feijão Carioca Camil 1kg',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'PCT',NULL,9.99,6.70,450,60,NULL,'mercearia-seca','e9546890-3cd3-49e4-9586-f0099dc34dbd',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('1af5ba27-dcf7-495a-8264-2ad2ffbce15c',NULL,'Prato Descartável Cristal 10un','7896025900010',NULL,NULL,NULL,NULL,'Prato Descartável Cristal 10un',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'PCT',NULL,4.99,3.10,600,80,NULL,'bazar-utilidades','297d7768-c12d-4581-a661-e35762bbf9b4',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('1f467287-9e5b-4764-b898-e7689fcc84bb',NULL,'Chocolate em Pó Nestlé 200g','7891000095005',NULL,NULL,NULL,NULL,'Chocolate em Pó Nestlé 200g',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,6.99,4.40,280,35,NULL,'mercearia-seca','e9546890-3cd3-49e4-9586-f0099dc34dbd',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('20439a67-8432-4338-b45f-004a27d3950f',NULL,'Detergente Ypê Neutro 500ml','7896098900027',NULL,NULL,NULL,NULL,'Detergente Ypê Neutro 500ml',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,2.49,1.40,1200,150,NULL,'limpeza-conservacao','eaf59113-cb76-4c6e-868c-21a424bc76c4',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('20d5dffb-fa97-4c3d-87df-d97945d95791',NULL,'Amaciante Downy Brisa do Mar 1L','7500435108539',NULL,NULL,NULL,NULL,'Amaciante Downy Brisa do Mar 1L',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,12.99,8.30,350,40,NULL,'limpeza-conservacao','eaf59113-cb76-4c6e-868c-21a424bc76c4',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('21fc0fc1-08e4-4ace-9571-1384ca390ffc',NULL,'Aveia em Flocos Quaker 200g','7891000100010',NULL,NULL,NULL,NULL,'Aveia em Flocos Quaker 200g',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'PCT',NULL,5.99,3.80,300,40,NULL,'mercearia-seca','e9546890-3cd3-49e4-9586-f0099dc34dbd',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('223e06a4-e68b-44f4-b431-8dcbfd11b785',NULL,'Arroz Tipo 1 Branco Camil 5kg','7896006702055',NULL,NULL,NULL,NULL,'Arroz Tipo 1 Branco Camil 5kg',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'PCT',NULL,27.90,19.50,380,50,NULL,'mercearia-seca','e9546890-3cd3-49e4-9586-f0099dc34dbd',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('24d6503b-6225-4cc1-bf62-92424016b122',NULL,'Papinha Nestlé Maçã 115g','7891000300091',NULL,NULL,NULL,NULL,'Papinha Nestlé Maçã 115g',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,3.99,2.40,300,40,'2026-07-10','bebe-crianca','0f8ce34d-e338-45ac-9ec7-7f0ac75f4140',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('24e2f59c-009c-4c6c-99df-70d1bd12401b',NULL,'Ração Pedigree Adulto 3kg','7896029040058',NULL,NULL,NULL,NULL,'Ração Pedigree Adulto 3kg',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'SC',NULL,39.90,26.00,120,15,NULL,'pet-shop','d6c50329-d925-49b0-bee1-a7e598910681',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('2853e37b-de1a-4a38-97ae-72d0f29ab6f5',NULL,'Refrigerante Coca-Cola Lata 350ml','7894900011517',NULL,NULL,NULL,NULL,'Refrigerante Coca-Cola Lata 350ml',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,3.49,2.10,1800,200,NULL,'bebidas','b7bca2ae-8caa-4f31-9aa3-9b7683022d67',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('2997884b-64da-4513-9e65-761e5ba76fe8',NULL,'Açúcar Cristal União 1kg','7891910000197',NULL,NULL,NULL,NULL,'Açúcar Cristal União 1kg',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'PCT',NULL,4.69,3.00,700,100,NULL,'mercearia-seca','e9546890-3cd3-49e4-9586-f0099dc34dbd',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('2adbd69c-222e-4621-abfb-aac215dcf1d5',NULL,'Pilha Duracell AA 2 unid.','5000394004023',NULL,NULL,NULL,NULL,'Pilha Duracell AA 2 unid.',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'PCT',NULL,9.99,6.40,400,50,NULL,'bazar-utilidades','297d7768-c12d-4581-a661-e35762bbf9b4',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('2d5f88c1-c26a-4917-929c-1de36d0b330d',NULL,'Maçã Gala (kg)','2000000000012',NULL,NULL,NULL,NULL,'Maçã Gala (kg)',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'KG',NULL,7.99,4.90,150,20,'2026-04-25','hortifruti','0cebc537-30b2-4ba1-8447-7a12ce023303',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('2d94de48-f73a-47fc-8e9b-d498a1855192',NULL,'Azeite de Oliva Gallo 500ml','5601252066022',NULL,NULL,NULL,NULL,'Azeite de Oliva Gallo 500ml',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,32.90,22.00,180,20,NULL,'mercearia-seca','e9546890-3cd3-49e4-9586-f0099dc34dbd',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('2f3d75d5-25ad-49b3-9759-89eb618fe599',NULL,'Limpador WD-40 300ml','0078590001007',NULL,NULL,NULL,NULL,'Limpador WD-40 300ml',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,19.90,12.80,150,20,NULL,'limpeza-conservacao','eaf59113-cb76-4c6e-868c-21a424bc76c4',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('30d77347-d439-464f-80d7-3238a7968275',NULL,'Biscoito Cream Cracker Piraquê 400g','7896024700109',NULL,NULL,NULL,NULL,'Biscoito Cream Cracker Piraquê 400g',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'PCT',NULL,4.79,3.00,480,60,NULL,'mercearia-seca','e9546890-3cd3-49e4-9586-f0099dc34dbd',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('31957221-1c0c-4dbc-b006-a78915bf3cd0',NULL,'Limão Taiti (kg)','2000000000021',NULL,NULL,NULL,NULL,'Limão Taiti (kg)',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'KG',NULL,5.99,3.20,180,25,'2026-04-25','hortifruti','0cebc537-30b2-4ba1-8447-7a12ce023303',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('329b55c5-0103-42bd-930f-002a01eae17b',NULL,'Feijão Preto Kicaldo 1kg','7896193501024',NULL,NULL,NULL,NULL,'Feijão Preto Kicaldo 1kg',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'PCT',NULL,10.49,7.10,300,40,NULL,'mercearia-seca','e9546890-3cd3-49e4-9586-f0099dc34dbd',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('354d4b41-7619-4fb3-ad86-de182e192049',NULL,'Costela Bovina (kg)','2001000000005',NULL,NULL,NULL,NULL,'Costela Bovina (kg)',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'KG',NULL,39.90,26.00,120,15,'2026-04-14','carnes-e-peixes','7940f1d2-310f-408f-9d15-0e46577a9269',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('35a6810d-ff0c-445f-ae19-36b8d6403f48',NULL,'Fralda Pampers Confort Sec G 28un','7500435143225',NULL,NULL,NULL,NULL,'Fralda Pampers Confort Sec G 28un',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'PCT',NULL,44.90,29.00,150,20,NULL,'higiene-pessoal','96a196e4-e61e-46ef-8e0b-65e8a8a433cd',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('36b319d9-050a-434b-a47f-97909458196f',NULL,'Frango Inteiro Sadia Congelado (kg)','2001000000001',NULL,NULL,NULL,NULL,'Frango Inteiro Sadia Congelado (kg)',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'KG',NULL,10.99,7.20,350,50,'2026-06-10','carnes-e-peixes','7940f1d2-310f-408f-9d15-0e46577a9269',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('3761bb1c-f9c0-420f-bb22-54679b43335e',NULL,'Coxa e Sobrecoxa de Frango BRF (kg)','2001000000007',NULL,NULL,NULL,NULL,'Coxa e Sobrecoxa de Frango BRF (kg)',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'KG',NULL,12.99,8.40,300,40,'2026-04-16','carnes-e-peixes','7940f1d2-310f-408f-9d15-0e46577a9269',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('39239e5b-7063-4e8f-ac3f-4ed3401e783d',NULL,'Pizza Congelada Sadia Mozzarella 460g','7896085071017',NULL,NULL,NULL,NULL,'Pizza Congelada Sadia Mozzarella 460g',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,18.99,12.20,200,25,'2026-08-09','congelados-refrigerados','4f8c85f2-4400-455d-b93e-350703f0a060',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('3ef3278f-6573-4111-a4ff-651ada3e4e25',NULL,'Água Mineral Crystal 500ml','7891149101706',NULL,NULL,NULL,NULL,'Água Mineral Crystal 500ml',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,1.99,0.90,2400,300,NULL,'bebidas','b7bca2ae-8caa-4f31-9aa3-9b7683022d67',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('3fb1664b-1d80-4c96-98c2-f8ed3e932976',NULL,'Abacate Hass (unidade)','2000000000020',NULL,NULL,NULL,NULL,'Abacate Hass (unidade)',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,3.49,1.90,150,20,'2026-04-18','hortifruti','0cebc537-30b2-4ba1-8447-7a12ce023303',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('401b7b87-8cd0-4fbe-a187-05a0d2d665b8',NULL,'Esponja Scotch-Brite Dupla Face','7891040012116',NULL,NULL,NULL,NULL,'Esponja Scotch-Brite Dupla Face',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,2.99,1.70,800,100,NULL,'limpeza-conservacao','eaf59113-cb76-4c6e-868c-21a424bc76c4',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('43e1bb24-a94b-464f-bf84-a6c654d1b6ab',NULL,'Geleia de Morango Queensberry 320g','7896429300028',NULL,NULL,NULL,NULL,'Geleia de Morango Queensberry 320g',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,8.99,5.80,200,25,NULL,'mercearia-seca','e9546890-3cd3-49e4-9586-f0099dc34dbd',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('447938b0-3274-4908-8afd-0e3c52195ed9',NULL,'Batata Inglesa (kg)','2000000000016',NULL,NULL,NULL,NULL,'Batata Inglesa (kg)',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'KG',NULL,4.49,2.50,300,40,'2026-04-26','hortifruti','0cebc537-30b2-4ba1-8447-7a12ce023303',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('474e8bf7-a820-45d4-a831-4eea7066a736',NULL,'Suco Del Valle Laranja 1L','7894900171212',NULL,NULL,NULL,NULL,'Suco Del Valle Laranja 1L',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,6.49,4.00,500,60,NULL,'bebidas','b7bca2ae-8caa-4f31-9aa3-9b7683022d67',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('4cab7a86-f034-49c3-87d3-8f9771b00cdd',NULL,'Água de Coco Boa Fruta 1L','7896693800019',NULL,NULL,NULL,NULL,'Água de Coco Boa Fruta 1L',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,7.99,4.90,250,30,NULL,'bebidas','b7bca2ae-8caa-4f31-9aa3-9b7683022d67',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('4ce2f14e-fde0-4358-bd11-c44dd3b3ad89',NULL,'Leite Condensado Moça 395g','7891000009306',NULL,NULL,NULL,NULL,'Leite Condensado Moça 395g',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,5.99,3.80,700,90,'2026-10-08','laticinios-frios','f26ea04e-0384-42a2-85a3-5ca92dd79177',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('5405cd76-fafa-46ea-a910-3aa1f97db4a9',NULL,'Aparelho de Barbear Gillette Prestobarba','7500435107037',NULL,NULL,NULL,NULL,'Aparelho de Barbear Gillette Prestobarba',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,3.49,2.10,600,80,NULL,'higiene-pessoal','96a196e4-e61e-46ef-8e0b-65e8a8a433cd',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('57c841cf-9325-42bc-9c7d-19fed97cd3bf',NULL,'Ervilha em Lata Bonduelle 170g','3083680000041',NULL,NULL,NULL,NULL,'Ervilha em Lata Bonduelle 170g',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,3.99,2.40,400,50,NULL,'mercearia-seca','e9546890-3cd3-49e4-9586-f0099dc34dbd',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('57f233ca-530a-4fa5-870a-757f2aa53166',NULL,'Macarrão Espaguete Barilla 500g','8076800195057',NULL,NULL,NULL,NULL,'Macarrão Espaguete Barilla 500g',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'PCT',NULL,5.99,3.90,600,80,NULL,'mercearia-seca','e9546890-3cd3-49e4-9586-f0099dc34dbd',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('5af7f0a0-bcbc-48ef-bd6d-617b7d58e00a',NULL,'Alcatra Bovina (kg)','2001000000006',NULL,NULL,NULL,NULL,'Alcatra Bovina (kg)',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'KG',NULL,44.90,29.00,100,12,'2026-04-14','carnes-e-peixes','7940f1d2-310f-408f-9d15-0e46577a9269',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('5c97231e-2a6f-4078-b55b-7b48ab1b507b',NULL,'Biscoito Passatempo Nestlé 130g','7891000053409',NULL,NULL,NULL,NULL,'Biscoito Passatempo Nestlé 130g',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'PCT',NULL,3.99,2.40,500,60,NULL,'mercearia-seca','e9546890-3cd3-49e4-9586-f0099dc34dbd',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('65e32f64-25e8-452c-baf1-9dbc6e98fb40',NULL,'Vinho Tinto Seco Miolo 750ml','7898190600113',NULL,NULL,NULL,NULL,'Vinho Tinto Seco Miolo 750ml',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,38.90,24.00,150,20,NULL,'bebidas','b7bca2ae-8caa-4f31-9aa3-9b7683022d67',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('6649d502-b18b-4b42-830a-0557e4679a73',NULL,'Manteiga com Sal Aviação 200g','7891007301128',NULL,NULL,NULL,NULL,'Manteiga com Sal Aviação 200g',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,9.99,6.40,400,50,'2026-06-10','laticinios-frios','f26ea04e-0384-42a2-85a3-5ca92dd79177',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('66ca2235-6259-45c6-b909-8a31200a653f',NULL,'Queijo Prato Fatiado Polenghi 150g','7896214502015',NULL,NULL,NULL,NULL,'Queijo Prato Fatiado Polenghi 150g',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,12.49,7.80,280,35,'2026-05-01','laticinios-frios','f26ea04e-0384-42a2-85a3-5ca92dd79177',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('67a18917-b47b-4c91-98bd-2fd0fcf3cba5',NULL,'Refrigerante Sprite 2L','7894900720015',NULL,NULL,NULL,NULL,'Refrigerante Sprite 2L',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,7.99,4.90,600,80,NULL,'bebidas','b7bca2ae-8caa-4f31-9aa3-9b7683022d67',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('681435dc-e6ed-4d14-acb9-16e22cb23cc8',NULL,'Presunto Fatiado Sadia 200g','7896085038027',NULL,NULL,NULL,NULL,'Presunto Fatiado Sadia 200g',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,10.49,6.80,300,40,'2026-05-11','laticinios-frios','f26ea04e-0384-42a2-85a3-5ca92dd79177',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('686f01e5-c259-47ca-8360-105072970524',NULL,'Protetor Solar Neutrogena FPS50 120ml','3574660417500',NULL,NULL,NULL,NULL,'Protetor Solar Neutrogena FPS50 120ml',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,29.90,19.00,200,25,NULL,'higiene-pessoal','96a196e4-e61e-46ef-8e0b-65e8a8a433cd',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('68d33a78-a115-4e49-9dc3-472e39836966',NULL,'Uva Itália (kg)','2000000000024',NULL,NULL,NULL,NULL,'Uva Itália (kg)',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'KG',NULL,12.99,8.00,80,10,'2026-04-18','hortifruti','0cebc537-30b2-4ba1-8447-7a12ce023303',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('6a3c85f9-a0fa-4ca7-a373-bb6fae1cfce8',NULL,'Caldo de Carne Maggi 57g (6 cubos)','7891000310212',NULL,NULL,NULL,NULL,'Caldo de Carne Maggi 57g (6 cubos)',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'CX',NULL,2.59,1.50,800,100,NULL,'mercearia-seca','e9546890-3cd3-49e4-9586-f0099dc34dbd',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('6ae05b90-a6cb-4d1a-9183-ad0403deacc1',NULL,'Cerveja Stella Artois 600ml','7891991221007',NULL,NULL,NULL,NULL,'Cerveja Stella Artois 600ml',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,9.99,6.50,800,80,NULL,'bebidas','b7bca2ae-8caa-4f31-9aa3-9b7683022d67',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('6bcaf1dc-98db-4d0d-8e6c-e0ebe3166bf2',NULL,'Refrigerante Coca-Cola 2L','7894900700015',NULL,NULL,NULL,NULL,'Refrigerante Coca-Cola 2L',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,8.99,5.60,800,100,NULL,'bebidas','b7bca2ae-8caa-4f31-9aa3-9b7683022d67',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('6d53a624-7034-445a-b410-75de99dfc3ae',NULL,'Sabão em Barra Omo 5 unid.','7891150063036',NULL,NULL,NULL,NULL,'Sabão em Barra Omo 5 unid.',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'PCT',NULL,12.99,8.30,300,40,NULL,'limpeza-conservacao','eaf59113-cb76-4c6e-868c-21a424bc76c4',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('6f798d7b-eca4-4962-abcb-fa76c3b6992f',NULL,'Leite sem Lactose Itambé 1L','7896051130265',NULL,NULL,NULL,NULL,'Leite sem Lactose Itambé 1L',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,6.49,4.10,800,100,'2026-05-11','laticinios-frios','f26ea04e-0384-42a2-85a3-5ca92dd79177',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('7197b558-59d9-4271-938c-d66a1c29bab2',NULL,'Lasanha Bolonhesa Perdigão 600g','7896085061018',NULL,NULL,NULL,NULL,'Lasanha Bolonhesa Perdigão 600g',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,17.49,11.20,180,20,'2026-08-09','congelados-refrigerados','4f8c85f2-4400-455d-b93e-350703f0a060',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('7354c166-cb3c-4b90-bfa7-bb78119d8cdf',NULL,'Filme PVC para Alimentos 30mx30cm','7896005303054',NULL,NULL,NULL,NULL,'Filme PVC para Alimentos 30mx30cm',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'RL',NULL,7.99,5.00,300,40,NULL,'bazar-utilidades','297d7768-c12d-4581-a661-e35762bbf9b4',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('7886a9f3-ccab-4b20-b5c3-1580936113e8',NULL,'Óleo de Soja Soya 900ml','7891107101621',NULL,NULL,NULL,NULL,'Óleo de Soja Soya 900ml',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,6.99,4.70,480,60,NULL,'mercearia-seca','e9546890-3cd3-49e4-9586-f0099dc34dbd',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('79fae5ee-2d5b-4236-aa09-582d25541243',NULL,'Linguiça Calabresa Defumada Sadia 500g','7896085034012',NULL,NULL,NULL,NULL,'Linguiça Calabresa Defumada Sadia 500g',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'PCT',NULL,14.99,9.70,250,30,'2026-05-11','carnes-e-peixes','7940f1d2-310f-408f-9d15-0e46577a9269',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('7a061bc3-6358-4034-9c59-6f3623bd5a8b',NULL,'Açúcar Refinado Guarani 1kg','7896065200015',NULL,NULL,NULL,NULL,'Açúcar Refinado Guarani 1kg',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'PCT',NULL,4.89,3.10,600,80,NULL,'mercearia-seca','e9546890-3cd3-49e4-9586-f0099dc34dbd',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('7a56fd6c-8983-48fa-b8ba-d8a4e5f94a98',NULL,'Creme Dental Colgate Total 12 90g','7509546060019',NULL,NULL,NULL,NULL,'Creme Dental Colgate Total 12 90g',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,6.49,4.10,600,80,NULL,'higiene-pessoal','96a196e4-e61e-46ef-8e0b-65e8a8a433cd',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('7c84135c-3fc2-4814-86b1-a73d27e28472',NULL,'Cerveja Skol Lata 350ml','7891991055034',NULL,NULL,NULL,NULL,'Cerveja Skol Lata 350ml',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,3.29,1.90,2400,300,NULL,'bebidas','b7bca2ae-8caa-4f31-9aa3-9b7683022d67',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('80918119-b82e-4910-bb23-b37be21a3f82',NULL,'Shampoo Clear Men Anticaspa 400ml','7891150048712',NULL,NULL,NULL,NULL,'Shampoo Clear Men Anticaspa 400ml',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,17.49,11.20,280,35,NULL,'higiene-pessoal','96a196e4-e61e-46ef-8e0b-65e8a8a433cd',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('83b19598-3534-4e19-99c9-8acbc01eb9bf',NULL,'Chá Preto Leão 10 Sachês','7896021002108',NULL,NULL,NULL,NULL,'Chá Preto Leão 10 Sachês',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'CX',NULL,4.99,3.00,200,30,NULL,'mercearia-seca','e9546890-3cd3-49e4-9586-f0099dc34dbd',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('83f42bf6-8750-4ad5-9e70-879104ed2a5a',NULL,'Picanha Bovina (kg)','2001000000004',NULL,NULL,NULL,NULL,'Picanha Bovina (kg)',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'KG',NULL,79.90,54.00,80,10,'2026-04-14','carnes-e-peixes','7940f1d2-310f-408f-9d15-0e46577a9269',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('846e3db7-f95f-47bb-9b1e-86366460e616',NULL,'Nuggets de Frango Sadia 300g','7896085072014',NULL,NULL,NULL,NULL,'Nuggets de Frango Sadia 300g',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'PCT',NULL,14.99,9.70,250,30,'2026-07-10','congelados-refrigerados','4f8c85f2-4400-455d-b93e-350703f0a060',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('84c36673-d1ef-448f-8fee-d2859e446e2c',NULL,'Cenoura (kg)','2000000000017',NULL,NULL,NULL,NULL,'Cenoura (kg)',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'KG',NULL,3.99,2.10,200,25,'2026-04-21','hortifruti','0cebc537-30b2-4ba1-8447-7a12ce023303',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('8640fb7c-4d49-457b-b41c-00e475c7df8b',NULL,'Limpador Multiuso Mr. Músculo 500ml','7891035128504',NULL,NULL,NULL,NULL,'Limpador Multiuso Mr. Músculo 500ml',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,6.49,4.10,400,50,NULL,'limpeza-conservacao','eaf59113-cb76-4c6e-868c-21a424bc76c4',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('86aa716b-d78c-4ba4-afe1-9dc36a8a45dd',NULL,'Filé de Tilápia Congelado 800g','7896010000021',NULL,NULL,NULL,NULL,'Filé de Tilápia Congelado 800g',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'PCT',NULL,27.90,18.00,150,20,'2026-07-10','carnes-e-peixes','7940f1d2-310f-408f-9d15-0e46577a9269',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('86e788b9-1eee-490e-85e6-45018195f44e',NULL,'Amido de Milho Maizena 200g','7891000056348',NULL,NULL,NULL,NULL,'Amido de Milho Maizena 200g',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'PCT',NULL,3.79,2.30,350,45,NULL,'mercearia-seca','e9546890-3cd3-49e4-9586-f0099dc34dbd',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('884078b2-83fc-49b7-b9ee-bb44cccbecdc',NULL,'Shampoo Pantene Restauração 400ml','7500435125284',NULL,NULL,NULL,NULL,'Shampoo Pantene Restauração 400ml',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,18.99,12.20,350,40,NULL,'higiene-pessoal','96a196e4-e61e-46ef-8e0b-65e8a8a433cd',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('884d303b-398b-4b49-84fe-83787de99a6c',NULL,'Vela de Aniversário 8 unid.','7898039320015',NULL,NULL,NULL,NULL,'Vela de Aniversário 8 unid.',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'PCT',NULL,4.49,2.70,400,50,NULL,'bazar-utilidades','297d7768-c12d-4581-a661-e35762bbf9b4',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('8b3919b1-1061-4390-b438-73a735a38d75',NULL,'Extrato de Tomate Elefante 340g','7891083001115',NULL,NULL,NULL,NULL,'Extrato de Tomate Elefante 340g',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,2.89,1.70,600,80,NULL,'mercearia-seca','e9546890-3cd3-49e4-9586-f0099dc34dbd',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('8b715035-7511-4199-9ec5-7b1d86c669fd',NULL,'Café 3 Corações Extra Forte 500g','7896224400017',NULL,NULL,NULL,NULL,'Café 3 Corações Extra Forte 500g',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'PCT',NULL,16.49,11.20,300,40,NULL,'mercearia-seca','e9546890-3cd3-49e4-9586-f0099dc34dbd',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('8bbeff03-cdc7-4e87-9790-a0dee39239e4',NULL,'Batata Frita McCain 1,05kg','7896019400014',NULL,NULL,NULL,NULL,'Batata Frita McCain 1,05kg',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'PCT',NULL,19.90,13.00,150,20,'2026-10-08','congelados-refrigerados','4f8c85f2-4400-455d-b93e-350703f0a060',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('8c022496-89d1-4968-b849-66e6a37c7205',NULL,'Desodorante Rexona Men Aerosol 150ml','7891150023018',NULL,NULL,NULL,NULL,'Desodorante Rexona Men Aerosol 150ml',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,12.99,8.30,400,50,NULL,'higiene-pessoal','96a196e4-e61e-46ef-8e0b-65e8a8a433cd',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('8eeb183f-133c-4e08-b30e-93da2c4a3687',NULL,'Sabonete Dove Hidratação Profunda 90g','7891150034113',NULL,NULL,NULL,NULL,'Sabonete Dove Hidratação Profunda 90g',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,3.99,2.40,800,100,NULL,'higiene-pessoal','96a196e4-e61e-46ef-8e0b-65e8a8a433cd',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('8f9b79ba-1a8f-4e7f-abe0-558a95c754a7',NULL,'Leite Integral Itambé 1L','7896051130241',NULL,NULL,NULL,NULL,'Leite Integral Itambé 1L',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,4.89,3.20,1800,200,'2026-05-11','laticinios-frios','f26ea04e-0384-42a2-85a3-5ca92dd79177',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('92756e28-f12f-4e47-95bc-86f9e82fe866',NULL,'Desinfetante Pinho Sol Lavanda 500ml','7891035110011',NULL,NULL,NULL,NULL,'Desinfetante Pinho Sol Lavanda 500ml',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,4.49,2.80,600,80,NULL,'limpeza-conservacao','eaf59113-cb76-4c6e-868c-21a424bc76c4',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('97cf1975-27ec-4b7c-b51b-336d5dd03b8f',NULL,'Laranja Lima (kg)','2000000000013',NULL,NULL,NULL,NULL,'Laranja Lima (kg)',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'KG',NULL,3.99,2.10,250,30,'2026-04-21','hortifruti','0cebc537-30b2-4ba1-8447-7a12ce023303',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('9c9febdb-f3f5-45e6-9ffa-1b2eb5fdeb26',NULL,'Copo Descartável 200ml 50un','7896098210104',NULL,NULL,NULL,NULL,'Copo Descartável 200ml 50un',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'PCT',NULL,3.99,2.40,800,100,NULL,'bazar-utilidades','297d7768-c12d-4581-a661-e35762bbf9b4',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('a1827ed4-d7e2-47db-b10c-1080fbd18ffe',NULL,'Lava-Roupas OMO Multiação 3kg','7891150014619',NULL,NULL,NULL,NULL,'Lava-Roupas OMO Multiação 3kg',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,37.90,24.00,250,30,NULL,'limpeza-conservacao','eaf59113-cb76-4c6e-868c-21a424bc76c4',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('a28350f0-f739-43e9-9a20-c47bfeaea739',NULL,'Tempero Completo Knorr 500g','7891150060295',NULL,NULL,NULL,NULL,'Tempero Completo Knorr 500g',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,9.49,6.20,280,30,NULL,'mercearia-seca','e9546890-3cd3-49e4-9586-f0099dc34dbd',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('a358144d-ba3f-4c8e-ae66-08695b97fc84',NULL,'Água Mineral Bonafont 1,5L','7891149102505',NULL,NULL,NULL,NULL,'Água Mineral Bonafont 1,5L',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,3.49,1.80,1200,150,NULL,'bebidas','b7bca2ae-8caa-4f31-9aa3-9b7683022d67',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('a527bedd-1d55-47a2-940d-235d15bd8c72',NULL,'Biscoito Recheado Oreo 144g','7622300489052',NULL,NULL,NULL,NULL,'Biscoito Recheado Oreo 144g',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'PCT',NULL,5.49,3.30,550,70,NULL,'mercearia-seca','e9546890-3cd3-49e4-9586-f0099dc34dbd',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('a6196a26-528c-4dbb-803c-3bc48cd7d212',NULL,'Cerveja Brahma Lata 350ml','7891991010484',NULL,NULL,NULL,NULL,'Cerveja Brahma Lata 350ml',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,3.49,2.10,2400,300,NULL,'bebidas','b7bca2ae-8caa-4f31-9aa3-9b7683022d67',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('a89ef62a-0109-480e-bfbd-3d83d6b2f523',NULL,'Leite Desnatado Itambé 1L','7896051130258',NULL,NULL,NULL,NULL,'Leite Desnatado Itambé 1L',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,4.99,3.30,1200,150,'2026-05-11','laticinios-frios','f26ea04e-0384-42a2-85a3-5ca92dd79177',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('ad1325f8-9daf-4484-84cd-deb69034731e',NULL,'Café Pilão Torrado e Moído 500g','7896229900539',NULL,NULL,NULL,NULL,'Café Pilão Torrado e Moído 500g',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'PCT',NULL,15.99,10.80,350,50,NULL,'mercearia-seca','e9546890-3cd3-49e4-9586-f0099dc34dbd',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('aea97261-ac0d-4ce1-8473-75aa4e5d58c0',NULL,'Maionese Hellmanns 500g','7891150062039',NULL,NULL,NULL,NULL,'Maionese Hellmanns 500g',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,8.99,5.90,420,50,NULL,'mercearia-seca','e9546890-3cd3-49e4-9586-f0099dc34dbd',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('b137f600-bf4d-499f-b26a-786f8cf94c62',NULL,'Energético Monster Energy 473ml','7898381500046',NULL,NULL,NULL,NULL,'Energético Monster Energy 473ml',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,9.99,6.50,400,50,NULL,'bebidas','b7bca2ae-8caa-4f31-9aa3-9b7683022d67',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('b56a1392-b276-4772-a5db-62a6051290be',NULL,'Leite em Pó Nan Nestlé 400g','7891000306048',NULL,NULL,NULL,NULL,'Leite em Pó Nan Nestlé 400g',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,34.90,22.00,150,20,'2026-10-08','bebe-crianca','0f8ce34d-e338-45ac-9ec7-7f0ac75f4140',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('b8fb1bb7-8a5f-43d1-8d06-b71804accf8c',NULL,'Sal Refinado Cisne 1kg','7896110002053',NULL,NULL,NULL,NULL,'Sal Refinado Cisne 1kg',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'PCT',NULL,2.49,1.40,500,60,NULL,'mercearia-seca','e9546890-3cd3-49e4-9586-f0099dc34dbd',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('ba72b257-5787-4aa0-a605-efebb57296f2',NULL,'Leite de Coco Sococo 200ml','7896004400148',NULL,NULL,NULL,NULL,'Leite de Coco Sococo 200ml',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,4.99,3.10,300,40,NULL,'bebidas','b7bca2ae-8caa-4f31-9aa3-9b7683022d67',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('baccc7db-84f8-42e3-aa0e-5627372a1ed1',NULL,'Salame Italiano Rezende 200g','7896085001113',NULL,NULL,NULL,NULL,'Salame Italiano Rezende 200g',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,12.99,8.50,200,25,'2026-05-26','laticinios-frios','f26ea04e-0384-42a2-85a3-5ca92dd79177',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('bb20f3ea-fc01-428e-812c-9f0e5708e2fd',NULL,'Sorvete Kibon Flocos Pote 2L','7891151051014',NULL,NULL,NULL,NULL,'Sorvete Kibon Flocos Pote 2L',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,24.90,16.00,120,15,'2026-10-08','congelados-refrigerados','4f8c85f2-4400-455d-b93e-350703f0a060',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('beb3f8eb-99d3-4ff8-b3ff-d0658216eebd',NULL,'Ketchup Heinz 397g','7896102506455',NULL,NULL,NULL,NULL,'Ketchup Heinz 397g',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,6.99,4.50,350,40,NULL,'mercearia-seca','e9546890-3cd3-49e4-9586-f0099dc34dbd',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('bed3e01f-6311-4b21-81e3-f1d0b8be5c37',NULL,'Salsicha Viena Perdigão 500g','7896085051019',NULL,NULL,NULL,NULL,'Salsicha Viena Perdigão 500g',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'PCT',NULL,11.49,7.40,300,40,'2026-05-11','carnes-e-peixes','7940f1d2-310f-408f-9d15-0e46577a9269',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('c0480a35-63b0-4ca9-868d-10714a2df0c6',NULL,'Mozzarella Fatiada Polenghi 150g','7896214500011',NULL,NULL,NULL,NULL,'Mozzarella Fatiada Polenghi 150g',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,13.99,9.00,250,30,'2026-05-01','laticinios-frios','f26ea04e-0384-42a2-85a3-5ca92dd79177',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('c0c4cf31-9d89-42bb-9170-ff1cbc7ae0b1',NULL,'Fermento em Pó Royal 100g','7891000093308',NULL,NULL,NULL,NULL,'Fermento em Pó Royal 100g',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,3.49,2.10,300,40,NULL,'mercearia-seca','e9546890-3cd3-49e4-9586-f0099dc34dbd',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('c14d0aab-cadd-45e9-9030-b93942cab435',NULL,'Feijao Carioca 1kg','7891000100102',NULL,NULL,NULL,NULL,'Feijao Carioca 1kg',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'KG',NULL,8.50,0.00,200,0,'2026-12-11','alimentos',NULL,NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22',NULL),('c23e5d2c-7829-4094-82dc-6a859d1449bb',NULL,'Milho Verde em Lata Yoki 200g','7896336400021',NULL,NULL,NULL,NULL,'Milho Verde em Lata Yoki 200g',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,3.29,2.00,450,60,NULL,'mercearia-seca','e9546890-3cd3-49e4-9586-f0099dc34dbd',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('c2ac81f7-2e07-4172-94fd-ab92237675a8',NULL,'Papel Higienico Folha Dupla 12un','7891000100104',NULL,NULL,NULL,NULL,'Papel Higienico Folha Dupla 12un',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'CX',NULL,19.90,0.00,90,0,NULL,'outro',NULL,NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22',NULL),('c34b459a-2a76-4fe8-8e02-49e3cd944003',NULL,'Absorvente Always Ultrafino 8un','7500435073500',NULL,NULL,NULL,NULL,'Absorvente Always Ultrafino 8un',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'PCT',NULL,7.99,5.10,400,50,NULL,'higiene-pessoal','96a196e4-e61e-46ef-8e0b-65e8a8a433cd',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('c65bcdb0-32a1-4815-b6d4-0e135f5731b1',NULL,'Iogurte Grego Danone 100g','7891025114121',NULL,NULL,NULL,NULL,'Iogurte Grego Danone 100g',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,4.49,2.80,500,60,'2026-05-01','laticinios-frios','f26ea04e-0384-42a2-85a3-5ca92dd79177',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('c7f2b9ee-847f-4611-bacf-0dd1ae5b8e69',NULL,'Banana Prata (kg)','2000000000011',NULL,NULL,NULL,NULL,'Banana Prata (kg)',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'KG',NULL,4.99,2.80,200,30,'2026-04-18','hortifruti','0cebc537-30b2-4ba1-8447-7a12ce023303',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('cafd9982-ddc1-4531-ab71-a41d7b45b226',NULL,'Achocolatado Nescau 400g','7891000024705',NULL,NULL,NULL,NULL,'Achocolatado Nescau 400g',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,7.49,4.80,400,50,NULL,'mercearia-seca','e9546890-3cd3-49e4-9586-f0099dc34dbd',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('d1f67e7f-eb86-478f-b788-bd57b7e87ea2',NULL,'Arroz Branco Tipo 1 5kg','7891000100101',NULL,NULL,NULL,NULL,'Arroz Branco Tipo 1 5kg',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'KG',NULL,27.90,0.00,120,0,'2027-02-11','alimentos',NULL,NULL,'2026-04-11 17:57:22','2026-04-11 17:57:22',NULL),('d349cac7-05a9-4755-8d3c-8d4cd08aa05d',NULL,'Cebola (kg)','2000000000015',NULL,NULL,NULL,NULL,'Cebola (kg)',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'KG',NULL,3.49,1.90,220,30,'2026-05-01','hortifruti','0cebc537-30b2-4ba1-8447-7a12ce023303',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('d46c68a3-3a15-462b-b745-2205480837e2',NULL,'Peito de Frango Filé BRF (kg)','2001000000002',NULL,NULL,NULL,NULL,'Peito de Frango Filé BRF (kg)',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'KG',NULL,17.99,11.80,280,40,'2026-04-16','carnes-e-peixes','7940f1d2-310f-408f-9d15-0e46577a9269',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('dab94847-83e5-4098-9676-f795cac227b7',NULL,'Palito de Dente 100 unid.','7896000100107',NULL,NULL,NULL,NULL,'Palito de Dente 100 unid.',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'PCT',NULL,2.49,1.40,500,60,NULL,'bazar-utilidades','297d7768-c12d-4581-a661-e35762bbf9b4',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('dc8edec7-2bc4-42dc-b834-ebe293810189',NULL,'Queijo Minas Frescal Itambé 400g','7896051140028',NULL,NULL,NULL,NULL,'Queijo Minas Frescal Itambé 400g',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,14.99,9.50,300,40,'2026-04-26','laticinios-frios','f26ea04e-0384-42a2-85a3-5ca92dd79177',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('df930593-2e19-4a39-92b1-07d4c2f4d2ed',NULL,'Carne Bovina Patinho Moído (kg)','2001000000003',NULL,NULL,NULL,NULL,'Carne Bovina Patinho Moído (kg)',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'KG',NULL,34.99,23.00,200,30,'2026-04-14','carnes-e-peixes','7940f1d2-310f-408f-9d15-0e46577a9269',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('e1912767-f60e-4ea6-98c5-a390cb6610d8',NULL,'Iogurte Natural Activia 170g','7891025110512',NULL,NULL,NULL,NULL,'Iogurte Natural Activia 170g',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,3.99,2.50,600,80,'2026-05-01','laticinios-frios','f26ea04e-0384-42a2-85a3-5ca92dd79177',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('e20ce07c-161d-4af4-aa9d-ac86bf4c5fdc',NULL,'Saco de Lixo 100L Vonder 10un','7896060200010',NULL,NULL,NULL,NULL,'Saco de Lixo 100L Vonder 10un',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'PCT',NULL,7.99,4.90,400,50,NULL,'limpeza-conservacao','eaf59113-cb76-4c6e-868c-21a424bc76c4',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('e286f5d2-8b03-460a-b44c-fb34f934dcc8',NULL,'Papel Higiênico Neve Premium 4 Rolos','7896027203020',NULL,NULL,NULL,NULL,'Papel Higiênico Neve Premium 4 Rolos',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'PCT',NULL,5.99,3.70,700,90,NULL,'higiene-pessoal','96a196e4-e61e-46ef-8e0b-65e8a8a433cd',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('e3651837-c7b1-4782-a5cc-96b403b1db29',NULL,'Vinagre de Álcool Castelo 750ml','7897793000024',NULL,NULL,NULL,NULL,'Vinagre de Álcool Castelo 750ml',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,3.29,1.90,320,40,NULL,'mercearia-seca','e9546890-3cd3-49e4-9586-f0099dc34dbd',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('e6ea4383-5791-4ce4-a82b-e2e65e392b5b',NULL,'Hambúrguer Sadia Blend 672g','7896085033015',NULL,NULL,NULL,NULL,'Hambúrguer Sadia Blend 672g',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'PCT',NULL,24.99,16.50,200,25,'2026-06-10','congelados-refrigerados','4f8c85f2-4400-455d-b93e-350703f0a060',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('e93f38e0-f2cd-4a7c-9918-b00d5b71d386',NULL,'Alface Americana (unidade)','2000000000018',NULL,NULL,NULL,NULL,'Alface Americana (unidade)',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,2.99,1.50,120,20,'2026-04-16','hortifruti','0cebc537-30b2-4ba1-8447-7a12ce023303',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('e9ce612e-9c44-4da8-851b-3190903b4e3d',NULL,'Tomate Italiano (kg)','2000000000014',NULL,NULL,NULL,NULL,'Tomate Italiano (kg)',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'KG',NULL,5.99,3.50,180,25,'2026-04-16','hortifruti','0cebc537-30b2-4ba1-8447-7a12ce023303',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('ea136103-6967-40fa-927a-545dc61b05fa',NULL,'Papel Alumínio 7,5m Reynolds','7896005303047',NULL,NULL,NULL,NULL,'Papel Alumínio 7,5m Reynolds',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'RL',NULL,8.99,5.70,300,40,NULL,'bazar-utilidades','297d7768-c12d-4581-a661-e35762bbf9b4',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('ef888923-b98e-4261-be69-7d7326600a2c',NULL,'Alho (100g)','2000000000022',NULL,NULL,NULL,NULL,'Alho (100g)',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'PCT',NULL,3.99,2.10,300,40,'2026-05-11','hortifruti','0cebc537-30b2-4ba1-8447-7a12ce023303',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('f481f2cb-829a-47eb-bb61-2d8ba9342ab2',NULL,'Mamão Formosa (unidade)','2000000000023',NULL,NULL,NULL,NULL,'Mamão Formosa (unidade)',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,6.49,3.80,100,15,'2026-04-18','hortifruti','0cebc537-30b2-4ba1-8447-7a12ce023303',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('f6de1b2b-b779-4805-8c1e-82581cebf94e',NULL,'Creme de Leite Nestlé 200g','7891000251034',NULL,NULL,NULL,NULL,'Creme de Leite Nestlé 200g',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,3.99,2.50,800,100,'2026-07-10','laticinios-frios','f26ea04e-0384-42a2-85a3-5ca92dd79177',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('f728e732-f7ae-4b3f-88f6-23166dd8461c',NULL,'Refrigerante Guaraná Antarctica 2L','7891991010507',NULL,NULL,NULL,NULL,'Refrigerante Guaraná Antarctica 2L',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,7.99,4.90,700,80,NULL,'bebidas','b7bca2ae-8caa-4f31-9aa3-9b7683022d67',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('fab74287-914d-487f-aaa1-50e4bf11b26c',NULL,'Alvejante Água Sanitária Qboa 2L','7891035312508',NULL,NULL,NULL,NULL,'Alvejante Água Sanitária Qboa 2L',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,5.99,3.70,500,60,NULL,'limpeza-conservacao','eaf59113-cb76-4c6e-868c-21a424bc76c4',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('fbab11c5-9cc0-418e-b807-c2f191b73471',NULL,'Farinha de Trigo Dona Benta 1kg','7891080400102',NULL,NULL,NULL,NULL,'Farinha de Trigo Dona Benta 1kg',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'PCT',NULL,4.99,3.20,400,50,NULL,'mercearia-seca','e9546890-3cd3-49e4-9586-f0099dc34dbd',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('fda9687c-dbb1-41b9-a786-d448b52158e3',NULL,'Ração Purina Cat Chow Adulto 3kg','7891000302230',NULL,NULL,NULL,NULL,'Ração Purina Cat Chow Adulto 3kg',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'SC',NULL,49.90,32.00,100,15,NULL,'pet-shop','d6c50329-d925-49b0-bee1-a7e598910681',NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24',NULL),('fdc3615a-ccd3-4dfc-bd27-d41f6d857938',NULL,'Suco Ades Caju 1L','7891000250501',NULL,NULL,NULL,NULL,'Suco Ades Caju 1L',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,5.99,3.70,400,50,NULL,'bebidas','b7bca2ae-8caa-4f31-9aa3-9b7683022d67',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL),('fdc9deca-f098-4585-b98c-f5ef8c10d08a',NULL,'Cerveja Heineken Long Neck 330ml','8712000023968',NULL,NULL,NULL,NULL,'Cerveja Heineken Long Neck 330ml',NULL,NULL,'produto_fisico','mercadoria_revenda',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,'UN',NULL,5.99,3.80,1200,120,NULL,'bebidas','b7bca2ae-8caa-4f31-9aa3-9b7683022d67',NULL,'2026-04-11 17:57:23','2026-04-11 17:57:23',NULL);
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profiles`
--

DROP TABLE IF EXISTS `profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `profiles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profiles`
--

LOCK TABLES `profiles` WRITE;
/*!40000 ALTER TABLE `profiles` DISABLE KEYS */;
/*!40000 ALTER TABLE `profiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_order_items`
--

DROP TABLE IF EXISTS `purchase_order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `purchase_order_id` bigint unsigned NOT NULL,
  `product_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'UN',
  `quantity` decimal(12,3) NOT NULL DEFAULT '1.000',
  `unit_price` decimal(14,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `total` decimal(14,2) NOT NULL DEFAULT '0.00',
  `cost_center` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_order_items_purchase_order_id_foreign` (`purchase_order_id`),
  KEY `purchase_order_items_product_id_foreign` (`product_id`),
  CONSTRAINT `purchase_order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchase_order_items_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_order_items`
--

LOCK TABLES `purchase_order_items` WRITE;
/*!40000 ALTER TABLE `purchase_order_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase_order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_orders`
--

DROP TABLE IF EXISTS `purchase_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `buyer_id` bigint unsigned DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'rascunho',
  `origin` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual',
  `order_date` datetime NOT NULL,
  `expected_delivery_date` date DEFAULT NULL,
  `received_at` date DEFAULT NULL,
  `payment_condition` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `freight_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `carrier_id` bigint unsigned DEFAULT NULL,
  `delivery_address` text COLLATE utf8mb4_unicode_ci,
  `subtotal` decimal(14,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `shipping_amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `other_expenses` decimal(14,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `notes_supplier` text COLLATE utf8mb4_unicode_ci,
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchase_orders_order_number_unique` (`order_number`),
  KEY `purchase_orders_supplier_id_foreign` (`supplier_id`),
  KEY `purchase_orders_buyer_id_foreign` (`buyer_id`),
  KEY `purchase_orders_carrier_id_foreign` (`carrier_id`),
  KEY `purchase_orders_approved_by_foreign` (`approved_by`),
  KEY `purchase_orders_created_by_foreign` (`created_by`),
  CONSTRAINT `purchase_orders_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchase_orders_buyer_id_foreign` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchase_orders_carrier_id_foreign` FOREIGN KEY (`carrier_id`) REFERENCES `carriers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchase_orders_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchase_orders_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_orders`
--

LOCK TABLES `purchase_orders` WRITE;
/*!40000 ALTER TABLE `purchase_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_requisition_items`
--

DROP TABLE IF EXISTS `purchase_requisition_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_requisition_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `purchase_requisition_id` bigint unsigned NOT NULL,
  `product_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'UN',
  `quantity` decimal(12,3) NOT NULL DEFAULT '1.000',
  `estimated_price` decimal(14,2) NOT NULL DEFAULT '0.00',
  `cost_center` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_requisition_items_purchase_requisition_id_foreign` (`purchase_requisition_id`),
  KEY `purchase_requisition_items_product_id_foreign` (`product_id`),
  CONSTRAINT `purchase_requisition_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchase_requisition_items_purchase_requisition_id_foreign` FOREIGN KEY (`purchase_requisition_id`) REFERENCES `purchase_requisitions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_requisition_items`
--

LOCK TABLES `purchase_requisition_items` WRITE;
/*!40000 ALTER TABLE `purchase_requisition_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase_requisition_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_requisitions`
--

DROP TABLE IF EXISTS `purchase_requisitions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_requisitions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'rascunho',
  `priority` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal',
  `requester_id` bigint unsigned DEFAULT NULL,
  `department` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `needed_by` date DEFAULT NULL,
  `justification` text COLLATE utf8mb4_unicode_ci,
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `purchase_order_id` bigint unsigned DEFAULT NULL,
  `cotacao_id` bigint unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchase_requisitions_number_unique` (`number`),
  KEY `purchase_requisitions_requester_id_foreign` (`requester_id`),
  KEY `purchase_requisitions_approved_by_foreign` (`approved_by`),
  KEY `purchase_requisitions_purchase_order_id_foreign` (`purchase_order_id`),
  KEY `purchase_requisitions_cotacao_id_foreign` (`cotacao_id`),
  KEY `purchase_requisitions_created_by_foreign` (`created_by`),
  CONSTRAINT `purchase_requisitions_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchase_requisitions_cotacao_id_foreign` FOREIGN KEY (`cotacao_id`) REFERENCES `cotacoes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchase_requisitions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchase_requisitions_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchase_requisitions_requester_id_foreign` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_requisitions`
--

LOCK TABLES `purchase_requisitions` WRITE;
/*!40000 ALTER TABLE `purchase_requisitions` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase_requisitions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `requests`
--

DROP TABLE IF EXISTS `requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `requests`
--

LOCK TABLES `requests` WRITE;
/*!40000 ALTER TABLE `requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rh_reports`
--

DROP TABLE IF EXISTS `rh_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rh_reports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rh_reports`
--

LOCK TABLES `rh_reports` WRITE;
/*!40000 ALTER TABLE `rh_reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `rh_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_role_id` bigint unsigned DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `allow_assignment` tinyint(1) NOT NULL DEFAULT '1',
  `permissions` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_code_unique` (`code`),
  KEY `roles_parent_role_id_foreign` (`parent_role_id`),
  CONSTRAINT `roles_parent_role_id_foreign` FOREIGN KEY (`parent_role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Diretor Geral','Diretoria','DIR-001',NULL,'Responsável geral pela gestão estratégica do supermercado',1,1,NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24'),(2,'Diretor Comercial','Diretoria','DIR-002',NULL,'Responsável pelas estratégias comerciais e de vendas',1,1,NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24'),(3,'Diretor Financeiro','Diretoria','DIR-003',NULL,'Responsável pela gestão financeira e contábil',1,1,NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24'),(4,'Gerente de Loja','Operações','GER-001',NULL,'Gerencia todas as operações diárias da loja',1,1,NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24'),(5,'Gerente Administrativo','Administrativo','GER-002',NULL,'Gerencia os processos administrativos e de RH',1,1,NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24'),(6,'Gerente Financeiro','Financeiro','GER-003',NULL,'Gestão de contas a pagar, receber e fluxo de caixa',1,1,NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24'),(7,'Gerente de Compras','Compras','GER-004',NULL,'Responsável pelas negociações com fornecedores e compras',1,1,NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24'),(8,'Gerente de Estoque','Estoque','GER-005',NULL,'Controla e gerencia o estoque de produtos',1,1,NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24'),(9,'Gerente de TI','TI','GER-006',NULL,'Gerencia os sistemas e infraestrutura de TI',1,1,NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24'),(10,'Supervisor de Checkout','Operações','SUP-001',NULL,'Supervisiona os operadores de caixa e fluxo de clientes',1,1,NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24'),(11,'Supervisor de Estoque','Estoque','SUP-002',NULL,'Supervisiona repositores e controle de estoque',1,1,NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24'),(12,'Supervisor de Segurança','Segurança','SUP-003',NULL,'Coordena a equipe de vigilância e prevenção de perdas',1,1,NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24'),(13,'Supervisor de Padaria','Padaria','SUP-004',NULL,'Supervisiona a produção e qualidade da padaria',1,1,NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24'),(14,'Supervisor de Açougue','Açougue','SUP-005',NULL,'Supervisiona os açougueiros e controle de carnes',1,1,NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24'),(15,'Analista Administrativo','Administrativo','ADM-001',NULL,'Suporte administrativo geral',1,1,NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24'),(16,'Analista de RH','RH','ADM-002',NULL,'Recrutamento, seleção e gestão de pessoal',1,1,NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24'),(17,'Analista Financeiro','Financeiro','ADM-003',NULL,'Análise e controle financeiro',1,1,NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24'),(18,'Auxiliar Administrativo','Administrativo','ADM-004',NULL,'Suporte às tarefas administrativas',1,1,NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24'),(19,'Analista de TI','TI','ADM-005',NULL,'Suporte técnico e manutenção de sistemas',1,1,NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24'),(20,'Operador(a) de Caixa','Checkout','CXA-001',NULL,'Operação dos caixas e atendimento ao cliente no checkout',1,1,NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24'),(21,'Operador(a) de SAC','Atendimento','CXA-002',NULL,'Atendimento ao cliente, trocas e devoluções',1,1,NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24'),(22,'Repositor(a)','Estoque','EST-001',NULL,'Reposição de produtos nas gôndolas e controle de validade',1,1,NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24'),(23,'Conferente de Estoque','Estoque','EST-002',NULL,'Conferência de notas fiscais e recebimento de mercadorias',1,1,NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24'),(24,'Auxiliar de Estoque','Estoque','EST-003',NULL,'Suporte nas atividades de estoque e movimentação de produtos',1,1,NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24'),(25,'Padeiro(a)','Padaria','PAD-001',NULL,'Produção de pães, bolos e produtos de confeitaria',1,1,NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24'),(26,'Confeiteiro(a)','Padaria','PAD-002',NULL,'Produção de bolos, tortas e produtos decorados',1,1,NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24'),(27,'Auxiliar de Padaria','Padaria','PAD-003',NULL,'Suporte nas atividades de padaria e confeitaria',1,1,NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24'),(28,'Açougueiro(a)','Açougue','ACO-001',NULL,'Corte, embalagem e exposição de carnes',1,1,NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24'),(29,'Auxiliar de Açougue','Açougue','ACO-002',NULL,'Suporte nas atividades do açougue',1,1,NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24'),(30,'Atendente de Hortifruti','Hortifruti','HOR-001',NULL,'Organização e atendimento na seção de hortifruti',1,1,NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24'),(31,'Vigilante/Segurança','Segurança','SEG-001',NULL,'Vigilância patrimonial e prevenção de perdas',1,1,NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24'),(32,'Auxiliar de Limpeza','Conservação','LMP-001',NULL,'Limpeza e conservação das instalações',1,1,NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24'),(33,'Técnico de Manutenção','Manutenção','MNT-001',NULL,'Manutenção preventiva e corretiva de equipamentos',1,1,NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24'),(34,'Motorista/Entregador','Logística','LOG-001',NULL,'Transporte e entrega de mercadorias',1,1,NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24'),(35,'Auxiliar de Entrega','Logística','LOG-002',NULL,'Suporte nas operações de entrega',1,1,NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24'),(36,'Promotor(a) de Vendas','Marketing','MKT-001',NULL,'Ações promocionais e demonstração de produtos',1,1,NULL,'2026-04-11 17:57:24','2026-04-11 17:57:24');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `romaneios`
--

DROP TABLE IF EXISTS `romaneios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `romaneios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `romaneios`
--

LOCK TABLES `romaneios` WRITE;
/*!40000 ALTER TABLE `romaneios` DISABLE KEYS */;
/*!40000 ALTER TABLE `romaneios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `route_managements`
--

DROP TABLE IF EXISTS `route_managements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `route_managements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `route_managements`
--

LOCK TABLES `route_managements` WRITE;
/*!40000 ALTER TABLE `route_managements` DISABLE KEYS */;
/*!40000 ALTER TABLE `route_managements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `routings`
--

DROP TABLE IF EXISTS `routings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `routings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `routings`
--

LOCK TABLES `routings` WRITE;
/*!40000 ALTER TABLE `routings` DISABLE KEYS */;
/*!40000 ALTER TABLE `routings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales_order_addresses`
--

DROP TABLE IF EXISTS `sales_order_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales_order_addresses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sales_order_id` bigint unsigned NOT NULL,
  `type` enum('billing','delivery','collection') COLLATE utf8mb4_unicode_ci NOT NULL,
  `zip_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `street` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `complement` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `district` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Brasil',
  `ibge_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sales_order_addresses_sales_order_id_type_index` (`sales_order_id`,`type`),
  CONSTRAINT `sales_order_addresses_sales_order_id_foreign` FOREIGN KEY (`sales_order_id`) REFERENCES `sales_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales_order_addresses`
--

LOCK TABLES `sales_order_addresses` WRITE;
/*!40000 ALTER TABLE `sales_order_addresses` DISABLE KEYS */;
/*!40000 ALTER TABLE `sales_order_addresses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales_order_attachments`
--

DROP TABLE IF EXISTS `sales_order_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales_order_attachments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sales_order_id` bigint unsigned NOT NULL,
  `uploaded_by` bigint unsigned DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_size` bigint DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sales_order_attachments_sales_order_id_foreign` (`sales_order_id`),
  KEY `sales_order_attachments_uploaded_by_foreign` (`uploaded_by`),
  CONSTRAINT `sales_order_attachments_sales_order_id_foreign` FOREIGN KEY (`sales_order_id`) REFERENCES `sales_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sales_order_attachments_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales_order_attachments`
--

LOCK TABLES `sales_order_attachments` WRITE;
/*!40000 ALTER TABLE `sales_order_attachments` DISABLE KEYS */;
/*!40000 ALTER TABLE `sales_order_attachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales_order_installments`
--

DROP TABLE IF EXISTS `sales_order_installments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales_order_installments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sales_order_payment_id` bigint unsigned NOT NULL,
  `sales_order_id` bigint unsigned NOT NULL,
  `installment_number` int NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `due_date` date NOT NULL,
  `payment_date` date DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sales_order_installments_sales_order_payment_id_foreign` (`sales_order_payment_id`),
  KEY `sales_order_installments_sales_order_id_installment_number_index` (`sales_order_id`,`installment_number`),
  CONSTRAINT `sales_order_installments_sales_order_id_foreign` FOREIGN KEY (`sales_order_id`) REFERENCES `sales_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sales_order_installments_sales_order_payment_id_foreign` FOREIGN KEY (`sales_order_payment_id`) REFERENCES `sales_order_payments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales_order_installments`
--

LOCK TABLES `sales_order_installments` WRITE;
/*!40000 ALTER TABLE `sales_order_installments` DISABLE KEYS */;
/*!40000 ALTER TABLE `sales_order_installments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales_order_items`
--

DROP TABLE IF EXISTS `sales_order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales_order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sales_order_id` bigint unsigned NOT NULL,
  `product_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `serial_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stock_location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ean` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` decimal(15,3) NOT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `discount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `discount_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `addition` decimal(15,2) NOT NULL DEFAULT '0.00',
  `addition_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `cfop` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ncm` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cst` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `csosn` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `origin` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icms_base` decimal(15,2) NOT NULL DEFAULT '0.00',
  `icms_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `icms_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `icms_st_base` decimal(15,2) NOT NULL DEFAULT '0.00',
  `icms_st_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `icms_st_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `ipi_base` decimal(15,2) NOT NULL DEFAULT '0.00',
  `ipi_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `ipi_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `pis_base` decimal(15,2) NOT NULL DEFAULT '0.00',
  `pis_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `pis_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `cofins_base` decimal(15,2) NOT NULL DEFAULT '0.00',
  `cofins_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `cofins_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `fcp_base` decimal(15,2) NOT NULL DEFAULT '0.00',
  `fcp_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `fcp_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(15,2) NOT NULL,
  `total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sales_order_items_sales_order_id_foreign` (`sales_order_id`),
  KEY `sales_order_items_product_id_foreign` (`product_id`),
  CONSTRAINT `sales_order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sales_order_items_sales_order_id_foreign` FOREIGN KEY (`sales_order_id`) REFERENCES `sales_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales_order_items`
--

LOCK TABLES `sales_order_items` WRITE;
/*!40000 ALTER TABLE `sales_order_items` DISABLE KEYS */;
INSERT INTO `sales_order_items` VALUES (1,1,'009bef2f-284d-4863-8d2d-490201e44be5',NULL,NULL,NULL,NULL,NULL,'7500435080324','Escova Dental Oral-B Pro Saúde','UN',10.000,7.99,0.00,0.00,0.00,0.00,0.00,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,79.90,79.90,NULL,'2026-04-14 22:53:13','2026-04-14 22:53:13'),(2,1,'0346bcba-e13d-4238-a71d-3cc088a2bc56',NULL,NULL,NULL,NULL,NULL,'7891150057012','Condicionador Dove Hidratação 400ml','UN',5.000,17.49,0.00,0.00,0.00,0.00,0.00,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,87.45,87.45,NULL,'2026-04-14 22:53:14','2026-04-14 22:53:14'),(3,1,'03aee4fe-e5f9-48f5-b5e2-ae64010f879c',NULL,NULL,NULL,NULL,NULL,'7896336402100','Farinha de Mandioca Yoki 500g','PCT',20.000,4.29,0.00,0.00,0.00,0.00,0.00,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,85.80,85.80,NULL,'2026-04-14 22:53:14','2026-04-14 22:53:14'),(4,2,'04541d4d-6596-424c-a9fe-4902708d08cb',NULL,NULL,NULL,NULL,NULL,'7896007541020','Lenço Umedecido Huggies 50 unid.','PCT',8.000,12.99,0.00,0.00,0.00,0.00,0.00,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,103.92,103.92,NULL,'2026-04-14 22:53:14','2026-04-14 22:53:14'),(5,2,'07928bf6-6acd-4b51-bfa7-3ccd891a6b39',NULL,NULL,NULL,NULL,NULL,'2000000000019','Brócolis (maço)','UN',15.000,4.99,0.00,0.00,0.00,0.00,0.00,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,74.85,74.85,NULL,'2026-04-14 22:53:14','2026-04-14 22:53:14'),(6,3,'081c19b2-f7ba-48cf-afe4-766973ae243b',NULL,NULL,NULL,NULL,NULL,'7896010000038','Camarão Pequeno Congelado 300g','PCT',50.000,19.90,0.00,0.00,0.00,0.00,0.00,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,995.00,995.00,NULL,'2026-04-14 22:53:14','2026-04-14 22:53:14'),(7,3,'08c5de51-5762-43ff-8d98-1b18ab475703',NULL,NULL,NULL,NULL,NULL,'7898413690034','Areia Higiênica Pipicat 4kg','SC',30.000,22.90,0.00,0.00,0.00,0.00,0.00,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,687.00,687.00,NULL,'2026-04-14 22:53:14','2026-04-14 22:53:14'),(8,3,'0da05818-ae14-4846-91a4-e9046e50f090',NULL,NULL,NULL,NULL,NULL,'7896000566016','Mel Puro Baldoni 280g','UN',12.000,14.99,0.00,0.00,0.00,0.00,0.00,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,179.88,179.88,NULL,'2026-04-14 22:53:14','2026-04-14 22:53:14'),(9,4,'1036ecdd-24af-40b1-8318-64f0bb94ad05',NULL,NULL,NULL,NULL,NULL,'7896005302019','Arroz Integral Urbano 1kg','PCT',6.000,8.49,0.00,0.00,0.00,0.00,0.00,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,50.94,50.94,NULL,'2026-04-14 22:53:14','2026-04-14 22:53:14'),(10,4,'12aa060f-9a39-4dd5-bd6f-f2bec366f925',NULL,NULL,NULL,NULL,NULL,'7891000100105','Sabonete em Barra 90g','UN',24.000,1.99,0.00,0.00,0.00,0.00,0.00,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,47.76,47.76,NULL,'2026-04-14 22:53:14','2026-04-14 22:53:14'),(11,5,'14f01d52-3314-47ad-af2f-d79577a276bd',NULL,NULL,NULL,NULL,NULL,'7896022203052','Macarrão Parafuso Renata 500g','PCT',100.000,4.49,0.00,0.00,0.00,0.00,0.00,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,449.00,449.00,NULL,'2026-04-14 22:53:14','2026-04-14 22:53:14'),(12,5,'15aac3d5-03b4-4e53-94be-079cb16e9fb9',NULL,NULL,NULL,NULL,NULL,'7891000100103','Detergente Neutro 500ml','UN',50.000,2.99,0.00,0.00,0.00,0.00,0.00,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,149.50,149.50,NULL,'2026-04-14 22:53:14','2026-04-14 22:53:14'),(13,5,'17bf6c49-0ac1-423f-ab5c-03546019d347',NULL,NULL,NULL,NULL,NULL,'7896102503133','Molho de Tomate Heinz 340g','UN',25.000,3.49,0.00,0.00,0.00,0.00,0.00,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,87.25,87.25,NULL,'2026-04-14 22:53:14','2026-04-14 22:53:14'),(14,6,'009bef2f-284d-4863-8d2d-490201e44be5',NULL,NULL,NULL,NULL,NULL,'7500435080324','Escova Dental Oral-B Pro Saúde','UN',3.000,7.99,0.00,0.00,0.00,0.00,0.00,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,23.97,23.97,NULL,'2026-04-14 22:53:14','2026-04-14 22:53:14'),(15,7,'009bef2f-284d-4863-8d2d-490201e44be5',NULL,NULL,NULL,NULL,NULL,'7500435080324','Escova Dental Oral-B Pro Saúde','UN',10.000,7.99,0.00,0.00,0.00,0.00,0.00,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,79.90,79.90,NULL,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(16,7,'0346bcba-e13d-4238-a71d-3cc088a2bc56',NULL,NULL,NULL,NULL,NULL,'7891150057012','Condicionador Dove Hidratação 400ml','UN',5.000,17.49,0.00,0.00,0.00,0.00,0.00,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,87.45,87.45,NULL,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(17,7,'03aee4fe-e5f9-48f5-b5e2-ae64010f879c',NULL,NULL,NULL,NULL,NULL,'7896336402100','Farinha de Mandioca Yoki 500g','PCT',20.000,4.29,0.00,0.00,0.00,0.00,0.00,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,85.80,85.80,NULL,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(18,8,'04541d4d-6596-424c-a9fe-4902708d08cb',NULL,NULL,NULL,NULL,NULL,'7896007541020','Lenço Umedecido Huggies 50 unid.','PCT',8.000,12.99,0.00,0.00,0.00,0.00,0.00,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,103.92,103.92,NULL,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(19,8,'07928bf6-6acd-4b51-bfa7-3ccd891a6b39',NULL,NULL,NULL,NULL,NULL,'2000000000019','Brócolis (maço)','UN',15.000,4.99,0.00,0.00,0.00,0.00,0.00,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,74.85,74.85,NULL,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(20,9,'081c19b2-f7ba-48cf-afe4-766973ae243b',NULL,NULL,NULL,NULL,NULL,'7896010000038','Camarão Pequeno Congelado 300g','PCT',50.000,19.90,0.00,0.00,0.00,0.00,0.00,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,995.00,995.00,NULL,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(21,9,'08c5de51-5762-43ff-8d98-1b18ab475703',NULL,NULL,NULL,NULL,NULL,'7898413690034','Areia Higiênica Pipicat 4kg','SC',30.000,22.90,0.00,0.00,0.00,0.00,0.00,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,687.00,687.00,NULL,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(22,9,'0da05818-ae14-4846-91a4-e9046e50f090',NULL,NULL,NULL,NULL,NULL,'7896000566016','Mel Puro Baldoni 280g','UN',12.000,14.99,0.00,0.00,0.00,0.00,0.00,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,179.88,179.88,NULL,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(23,10,'1036ecdd-24af-40b1-8318-64f0bb94ad05',NULL,NULL,NULL,NULL,NULL,'7896005302019','Arroz Integral Urbano 1kg','PCT',6.000,8.49,0.00,0.00,0.00,0.00,0.00,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,50.94,50.94,NULL,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(24,10,'12aa060f-9a39-4dd5-bd6f-f2bec366f925',NULL,NULL,NULL,NULL,NULL,'7891000100105','Sabonete em Barra 90g','UN',24.000,1.99,0.00,0.00,0.00,0.00,0.00,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,47.76,47.76,NULL,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(25,11,'14f01d52-3314-47ad-af2f-d79577a276bd',NULL,NULL,NULL,NULL,NULL,'7896022203052','Macarrão Parafuso Renata 500g','PCT',100.000,4.49,0.00,0.00,0.00,0.00,0.00,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,449.00,449.00,NULL,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(26,11,'15aac3d5-03b4-4e53-94be-079cb16e9fb9',NULL,NULL,NULL,NULL,NULL,'7891000100103','Detergente Neutro 500ml','UN',50.000,2.99,0.00,0.00,0.00,0.00,0.00,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,149.50,149.50,NULL,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(27,11,'17bf6c49-0ac1-423f-ab5c-03546019d347',NULL,NULL,NULL,NULL,NULL,'7896102503133','Molho de Tomate Heinz 340g','UN',25.000,3.49,0.00,0.00,0.00,0.00,0.00,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,87.25,87.25,NULL,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(28,12,'009bef2f-284d-4863-8d2d-490201e44be5',NULL,NULL,NULL,NULL,NULL,'7500435080324','Escova Dental Oral-B Pro Saúde','UN',3.000,7.99,0.00,0.00,0.00,0.00,0.00,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,23.97,23.97,NULL,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(29,13,'009bef2f-284d-4863-8d2d-490201e44be5',NULL,NULL,NULL,NULL,NULL,'7500435080324','Escova Dental Oral-B Pro Saúde','UN',10.000,7.99,0.00,0.00,0.00,0.00,0.00,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,79.90,79.90,NULL,'2026-04-14 22:55:42','2026-04-14 22:55:42'),(30,13,'0346bcba-e13d-4238-a71d-3cc088a2bc56',NULL,NULL,NULL,NULL,NULL,'7891150057012','Condicionador Dove Hidratação 400ml','UN',5.000,17.49,0.00,0.00,0.00,0.00,0.00,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,87.45,87.45,NULL,'2026-04-14 22:55:42','2026-04-14 22:55:42'),(31,13,'03aee4fe-e5f9-48f5-b5e2-ae64010f879c',NULL,NULL,NULL,NULL,NULL,'7896336402100','Farinha de Mandioca Yoki 500g','PCT',20.000,4.29,0.00,0.00,0.00,0.00,0.00,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,85.80,85.80,NULL,'2026-04-14 22:55:42','2026-04-14 22:55:42'),(32,14,'04541d4d-6596-424c-a9fe-4902708d08cb',NULL,NULL,NULL,NULL,NULL,'7896007541020','Lenço Umedecido Huggies 50 unid.','PCT',8.000,12.99,0.00,0.00,0.00,0.00,0.00,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,103.92,103.92,NULL,'2026-04-14 22:55:42','2026-04-14 22:55:42'),(33,14,'07928bf6-6acd-4b51-bfa7-3ccd891a6b39',NULL,NULL,NULL,NULL,NULL,'2000000000019','Brócolis (maço)','UN',15.000,4.99,0.00,0.00,0.00,0.00,0.00,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,74.85,74.85,NULL,'2026-04-14 22:55:42','2026-04-14 22:55:42'),(34,15,'081c19b2-f7ba-48cf-afe4-766973ae243b',NULL,NULL,NULL,NULL,NULL,'7896010000038','Camarão Pequeno Congelado 300g','PCT',50.000,19.90,0.00,0.00,0.00,0.00,0.00,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,995.00,995.00,NULL,'2026-04-14 22:55:42','2026-04-14 22:55:42'),(35,15,'08c5de51-5762-43ff-8d98-1b18ab475703',NULL,NULL,NULL,NULL,NULL,'7898413690034','Areia Higiênica Pipicat 4kg','SC',30.000,22.90,0.00,0.00,0.00,0.00,0.00,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,687.00,687.00,NULL,'2026-04-14 22:55:42','2026-04-14 22:55:42'),(36,15,'0da05818-ae14-4846-91a4-e9046e50f090',NULL,NULL,NULL,NULL,NULL,'7896000566016','Mel Puro Baldoni 280g','UN',12.000,14.99,0.00,0.00,0.00,0.00,0.00,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,179.88,179.88,NULL,'2026-04-14 22:55:42','2026-04-14 22:55:42'),(37,16,'1036ecdd-24af-40b1-8318-64f0bb94ad05',NULL,NULL,NULL,NULL,NULL,'7896005302019','Arroz Integral Urbano 1kg','PCT',6.000,8.49,0.00,0.00,0.00,0.00,0.00,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,50.94,50.94,NULL,'2026-04-14 22:55:42','2026-04-14 22:55:42'),(38,16,'12aa060f-9a39-4dd5-bd6f-f2bec366f925',NULL,NULL,NULL,NULL,NULL,'7891000100105','Sabonete em Barra 90g','UN',24.000,1.99,0.00,0.00,0.00,0.00,0.00,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,47.76,47.76,NULL,'2026-04-14 22:55:42','2026-04-14 22:55:42'),(39,17,'14f01d52-3314-47ad-af2f-d79577a276bd',NULL,NULL,NULL,NULL,NULL,'7896022203052','Macarrão Parafuso Renata 500g','PCT',100.000,4.49,0.00,0.00,0.00,0.00,0.00,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,449.00,449.00,NULL,'2026-04-14 22:55:42','2026-04-14 22:55:42'),(40,17,'15aac3d5-03b4-4e53-94be-079cb16e9fb9',NULL,NULL,NULL,NULL,NULL,'7891000100103','Detergente Neutro 500ml','UN',50.000,2.99,0.00,0.00,0.00,0.00,0.00,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,149.50,149.50,NULL,'2026-04-14 22:55:42','2026-04-14 22:55:42'),(41,17,'17bf6c49-0ac1-423f-ab5c-03546019d347',NULL,NULL,NULL,NULL,NULL,'7896102503133','Molho de Tomate Heinz 340g','UN',25.000,3.49,0.00,0.00,0.00,0.00,0.00,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,87.25,87.25,NULL,'2026-04-14 22:55:42','2026-04-14 22:55:42'),(42,18,'009bef2f-284d-4863-8d2d-490201e44be5',NULL,NULL,NULL,NULL,NULL,'7500435080324','Escova Dental Oral-B Pro Saúde','UN',3.000,7.99,0.00,0.00,0.00,0.00,0.00,NULL,NULL,NULL,NULL,NULL,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,23.97,23.97,NULL,'2026-04-14 22:55:42','2026-04-14 22:55:42');
/*!40000 ALTER TABLE `sales_order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales_order_logs`
--

DROP TABLE IF EXISTS `sales_order_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales_order_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sales_order_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `old_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `new_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `changes` json DEFAULT NULL,
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sales_order_logs_user_id_foreign` (`user_id`),
  KEY `sales_order_logs_sales_order_id_created_at_index` (`sales_order_id`,`created_at`),
  CONSTRAINT `sales_order_logs_sales_order_id_foreign` FOREIGN KEY (`sales_order_id`) REFERENCES `sales_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sales_order_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales_order_logs`
--

LOCK TABLES `sales_order_logs` WRITE;
/*!40000 ALTER TABLE `sales_order_logs` DISABLE KEYS */;
INSERT INTO `sales_order_logs` VALUES (1,1,NULL,'created',NULL,NULL,'Pedido criado','[]','127.0.0.1','Symfony','2026-04-14 22:53:13'),(2,2,NULL,'created',NULL,NULL,'Pedido criado','[]','127.0.0.1','Symfony','2026-04-14 22:53:14'),(3,3,NULL,'created',NULL,NULL,'Pedido criado','[]','127.0.0.1','Symfony','2026-04-14 22:53:14'),(4,4,NULL,'created',NULL,NULL,'Pedido criado','[]','127.0.0.1','Symfony','2026-04-14 22:53:14'),(5,5,NULL,'created',NULL,NULL,'Pedido criado','[]','127.0.0.1','Symfony','2026-04-14 22:53:14'),(6,6,NULL,'created',NULL,NULL,'Pedido criado','[]','127.0.0.1','Symfony','2026-04-14 22:53:14');
/*!40000 ALTER TABLE `sales_order_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales_order_payments`
--

DROP TABLE IF EXISTS `sales_order_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales_order_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sales_order_id` bigint unsigned NOT NULL,
  `payment_condition` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `installments` int NOT NULL DEFAULT '1',
  `total_amount` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sales_order_payments_sales_order_id_foreign` (`sales_order_id`),
  CONSTRAINT `sales_order_payments_sales_order_id_foreign` FOREIGN KEY (`sales_order_id`) REFERENCES `sales_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales_order_payments`
--

LOCK TABLES `sales_order_payments` WRITE;
/*!40000 ALTER TABLE `sales_order_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `sales_order_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales_orders`
--

DROP TABLE IF EXISTS `sales_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_date` timestamp NULL DEFAULT NULL,
  `client_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_cpf_cnpj` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_ie` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_credit_limit` decimal(15,2) DEFAULT NULL,
  `client_situation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_contact_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_contact_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `seller_id` bigint unsigned DEFAULT NULL,
  `is_fiscal` tinyint(1) NOT NULL DEFAULT '0',
  `operation_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sales_channel` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `origin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_branch` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `separation_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `separator_user_id` bigint unsigned DEFAULT NULL,
  `separation_date` timestamp NULL DEFAULT NULL,
  `conference_date` timestamp NULL DEFAULT NULL,
  `fiscal_note_id` bigint unsigned DEFAULT NULL,
  `nfe_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nfe_series` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nfe_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nfe_protocol` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nfe_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nfe_issued_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approval_reason` text COLLATE utf8mb4_unicode_ci,
  `needs_approval` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `gross_total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `additions_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `shipping_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `insurance_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `other_expenses` decimal(15,2) NOT NULL DEFAULT '0.00',
  `net_total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `icms_base` decimal(15,2) NOT NULL DEFAULT '0.00',
  `icms_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `icms_st_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `ipi_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `pis_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `cofins_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `fcp_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `payment_condition` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price_table_id` bigint unsigned DEFAULT NULL,
  `minimum_margin` decimal(5,2) DEFAULT NULL,
  `internal_notes` text COLLATE utf8mb4_unicode_ci,
  `customer_notes` text COLLATE utf8mb4_unicode_ci,
  `fiscal_notes_obs` text COLLATE utf8mb4_unicode_ci,
  `delivery_date` date DEFAULT NULL,
  `expected_delivery_date` timestamp NULL DEFAULT NULL,
  `carrier_id` bigint unsigned DEFAULT NULL,
  `freight_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gross_weight` decimal(10,3) DEFAULT NULL,
  `net_weight` decimal(10,3) DEFAULT NULL,
  `volumes` int DEFAULT NULL,
  `tracking_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sales_orders_order_number_unique` (`order_number`),
  KEY `sales_orders_client_id_foreign` (`client_id`),
  KEY `sales_orders_user_id_foreign` (`user_id`),
  KEY `sales_orders_seller_id_foreign` (`seller_id`),
  KEY `sales_orders_separator_user_id_foreign` (`separator_user_id`),
  KEY `sales_orders_approved_by_foreign` (`approved_by`),
  KEY `sales_orders_created_by_foreign` (`created_by`),
  KEY `sales_orders_updated_by_foreign` (`updated_by`),
  CONSTRAINT `sales_orders_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sales_orders_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sales_orders_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sales_orders_seller_id_foreign` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sales_orders_separator_user_id_foreign` FOREIGN KEY (`separator_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sales_orders_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sales_orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales_orders`
--

LOCK TABLES `sales_orders` WRITE;
/*!40000 ALTER TABLE `sales_orders` DISABLE KEYS */;
INSERT INTO `sales_orders` VALUES (1,'PV-000001','2026-03-25 22:53:13','668d9c02-9bfa-406f-b9d8-e9956d1dcb63','23123456000104',NULL,'PJ',NULL,'active',NULL,NULL,1,1,0,'venda_normal','balcao','manual',NULL,'approved',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,253.15,253.15,0.00,0.00,80.00,0.00,0.00,253.15,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,333.15,'30/60 dias',NULL,NULL,'Cliente prioritário. Entrega confirmada.',NULL,NULL,'2026-04-04',NULL,NULL,'cif',NULL,NULL,NULL,NULL,NULL,'2026-04-14 22:53:13','2026-04-14 22:53:14'),(2,'PV-000002','2026-03-10 22:53:13','8633c247-5b7a-4255-b28a-f5bc3367d1c9','56123456000105',NULL,'PJ',NULL,'active',NULL,NULL,2,2,0,'venda_normal','online','ecommerce',NULL,'delivered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,178.77,178.77,0.00,0.00,0.00,0.00,0.00,178.77,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,178.77,'À vista',NULL,NULL,'Pedido de e-commerce, pagamento antecipado.',NULL,NULL,'2026-03-23',NULL,NULL,'fob',NULL,NULL,NULL,NULL,NULL,'2026-04-14 22:53:14','2026-04-14 22:53:14'),(3,'PV-000003','2026-04-09 22:53:13','92511a7a-19f4-4ba5-ba4e-cd21ea83d84e','78123456000103',NULL,'PJ',NULL,'active',NULL,NULL,3,3,0,'venda_normal','representante','forca_vendas',NULL,'aberto',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,1861.88,1861.88,0.00,0.00,150.00,0.00,0.00,1861.88,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,2011.88,'28 dias',NULL,NULL,'Pedido via representante regional.',NULL,NULL,'2026-04-24',NULL,NULL,'cif',NULL,NULL,NULL,NULL,NULL,'2026-04-14 22:53:14','2026-04-14 22:53:14'),(4,'PV-000004','2026-04-11 22:53:13','ae5ec605-b67f-4034-ba81-f6bf2d8828e2','12123456000101',NULL,'PJ',NULL,'active',NULL,NULL,1,1,0,'venda_normal','televendas','manual',NULL,'em_separacao',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,98.70,98.70,0.00,0.00,60.00,0.00,0.00,98.70,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,158.70,'30 dias',NULL,NULL,'Separação iniciada em 13/04.',NULL,NULL,'2026-04-21',NULL,NULL,'cif',NULL,NULL,NULL,NULL,NULL,'2026-04-14 22:53:14','2026-04-14 22:53:14'),(5,'PV-000005','2026-04-13 22:53:13','f3535548-b045-49ad-8086-aa33ebe34437','45123456000102',NULL,'PJ',NULL,'active',NULL,NULL,2,2,0,'consignacao','balcao','manual',NULL,'draft',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,685.75,685.75,0.00,0.00,200.00,0.00,0.00,685.75,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,885.75,'60 dias',NULL,NULL,'Rascunho aguardando aprovação do gerente.',NULL,NULL,'2026-04-29',NULL,NULL,'terceiros',NULL,NULL,NULL,NULL,NULL,'2026-04-14 22:53:14','2026-04-14 22:53:14'),(6,'PV-000006','2026-02-28 22:53:13','668d9c02-9bfa-406f-b9d8-e9956d1dcb63','23123456000104',NULL,'PJ',NULL,'active',NULL,NULL,3,3,0,'venda_normal','online','ecommerce',NULL,'cancelled',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,23.97,23.97,0.00,0.00,0.00,0.00,0.00,23.97,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,23.97,'À vista',NULL,NULL,'Cancelado a pedido do cliente por duplicidade.',NULL,NULL,'2026-03-15',NULL,NULL,'fob',NULL,NULL,NULL,NULL,NULL,'2026-04-14 22:53:14','2026-04-14 22:53:14'),(7,'PV-000007','2026-03-25 22:53:55','668d9c02-9bfa-406f-b9d8-e9956d1dcb63','23123456000104',NULL,'PJ',NULL,'active',NULL,NULL,1,1,0,'venda_normal','balcao','manual',NULL,'approved',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,253.15,253.15,0.00,0.00,80.00,0.00,0.00,253.15,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,333.15,'30/60 dias',NULL,NULL,'Cliente prioritário. Entrega confirmada.',NULL,NULL,'2026-04-04',NULL,NULL,'cif',NULL,NULL,NULL,NULL,NULL,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(8,'PV-000008','2026-03-10 22:53:55','8633c247-5b7a-4255-b28a-f5bc3367d1c9','56123456000105',NULL,'PJ',NULL,'active',NULL,NULL,2,2,0,'venda_normal','online','ecommerce',NULL,'delivered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,178.77,178.77,0.00,0.00,0.00,0.00,0.00,178.77,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,178.77,'À vista',NULL,NULL,'Pedido de e-commerce, pagamento antecipado.',NULL,NULL,'2026-03-23',NULL,NULL,'fob',NULL,NULL,NULL,NULL,NULL,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(9,'PV-000009','2026-04-09 22:53:55','92511a7a-19f4-4ba5-ba4e-cd21ea83d84e','78123456000103',NULL,'PJ',NULL,'active',NULL,NULL,3,3,0,'venda_normal','representante','forca_vendas',NULL,'aberto',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,1861.88,1861.88,0.00,0.00,150.00,0.00,0.00,1861.88,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,2011.88,'28 dias',NULL,NULL,'Pedido via representante regional.',NULL,NULL,'2026-04-24',NULL,NULL,'cif',NULL,NULL,NULL,NULL,NULL,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(10,'PV-000010','2026-04-11 22:53:55','ae5ec605-b67f-4034-ba81-f6bf2d8828e2','12123456000101',NULL,'PJ',NULL,'active',NULL,NULL,1,1,0,'venda_normal','televendas','manual',NULL,'em_separacao',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,98.70,98.70,0.00,0.00,60.00,0.00,0.00,98.70,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,158.70,'30 dias',NULL,NULL,'Separação iniciada em 13/04.',NULL,NULL,'2026-04-21',NULL,NULL,'cif',NULL,NULL,NULL,NULL,NULL,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(11,'PV-000011','2026-04-13 22:53:55','f3535548-b045-49ad-8086-aa33ebe34437','45123456000102',NULL,'PJ',NULL,'active',NULL,NULL,2,2,0,'consignacao','balcao','manual',NULL,'draft',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,685.75,685.75,0.00,0.00,200.00,0.00,0.00,685.75,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,885.75,'60 dias',NULL,NULL,'Rascunho aguardando aprovação do gerente.',NULL,NULL,'2026-04-29',NULL,NULL,'terceiros',NULL,NULL,NULL,NULL,NULL,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(12,'PV-000012','2026-02-28 22:53:55','668d9c02-9bfa-406f-b9d8-e9956d1dcb63','23123456000104',NULL,'PJ',NULL,'active',NULL,NULL,3,3,0,'venda_normal','online','ecommerce',NULL,'cancelled',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,23.97,23.97,0.00,0.00,0.00,0.00,0.00,23.97,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,23.97,'À vista',NULL,NULL,'Cancelado a pedido do cliente por duplicidade.',NULL,NULL,'2026-03-15',NULL,NULL,'fob',NULL,NULL,NULL,NULL,NULL,'2026-04-14 22:53:55','2026-04-14 22:53:55'),(13,'PV-000013','2026-03-25 22:55:42','668d9c02-9bfa-406f-b9d8-e9956d1dcb63','23123456000104',NULL,'PJ',NULL,'active',NULL,NULL,1,1,0,'venda_normal','balcao','manual',NULL,'approved',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,253.15,253.15,0.00,0.00,80.00,0.00,0.00,253.15,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,333.15,'30/60 dias',NULL,NULL,'Cliente prioritário. Entrega confirmada.',NULL,NULL,'2026-04-04',NULL,NULL,'cif',NULL,NULL,NULL,NULL,NULL,'2026-04-14 22:55:42','2026-04-14 22:55:42'),(14,'PV-000014','2026-03-10 22:55:42','8633c247-5b7a-4255-b28a-f5bc3367d1c9','56123456000105',NULL,'PJ',NULL,'active',NULL,NULL,2,2,0,'venda_normal','online','ecommerce',NULL,'delivered',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,178.77,178.77,0.00,0.00,0.00,0.00,0.00,178.77,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,178.77,'À vista',NULL,NULL,'Pedido de e-commerce, pagamento antecipado.',NULL,NULL,'2026-03-23',NULL,NULL,'fob',NULL,NULL,NULL,NULL,NULL,'2026-04-14 22:55:42','2026-04-14 22:55:42'),(15,'PV-000015','2026-04-09 22:55:42','92511a7a-19f4-4ba5-ba4e-cd21ea83d84e','78123456000103',NULL,'PJ',NULL,'active',NULL,NULL,3,3,0,'venda_normal','representante','forca_vendas',NULL,'aberto',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,1861.88,1861.88,0.00,0.00,150.00,0.00,0.00,1861.88,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,2011.88,'28 dias',NULL,NULL,'Pedido via representante regional.',NULL,NULL,'2026-04-24',NULL,NULL,'cif',NULL,NULL,NULL,NULL,NULL,'2026-04-14 22:55:42','2026-04-14 22:55:42'),(16,'PV-000016','2026-04-11 22:55:42','ae5ec605-b67f-4034-ba81-f6bf2d8828e2','12123456000101',NULL,'PJ',NULL,'active',NULL,NULL,1,1,0,'venda_normal','televendas','manual',NULL,'em_separacao',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,98.70,98.70,0.00,0.00,60.00,0.00,0.00,98.70,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,158.70,'30 dias',NULL,NULL,'Separação iniciada em 13/04.',NULL,NULL,'2026-04-21',NULL,NULL,'cif',NULL,NULL,NULL,NULL,NULL,'2026-04-14 22:55:42','2026-04-14 22:55:42'),(17,'PV-000017','2026-04-13 22:55:42','f3535548-b045-49ad-8086-aa33ebe34437','45123456000102',NULL,'PJ',NULL,'active',NULL,NULL,2,2,0,'consignacao','balcao','manual',NULL,'draft',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,685.75,685.75,0.00,0.00,200.00,0.00,0.00,685.75,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,885.75,'60 dias',NULL,NULL,'Rascunho aguardando aprovação do gerente.',NULL,NULL,'2026-04-29',NULL,NULL,'terceiros',NULL,NULL,NULL,NULL,NULL,'2026-04-14 22:55:42','2026-04-14 22:55:42'),(18,'PV-000018','2026-02-28 22:55:42','668d9c02-9bfa-406f-b9d8-e9956d1dcb63','23123456000104',NULL,'PJ',NULL,'active',NULL,NULL,3,3,0,'venda_normal','online','ecommerce',NULL,'cancelled',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,23.97,23.97,0.00,0.00,0.00,0.00,0.00,23.97,0.00,0.00,0.00,0.00,0.00,0.00,0.00,0.00,23.97,'À vista',NULL,NULL,'Cancelado a pedido do cliente por duplicidade.',NULL,NULL,'2026-03-15',NULL,NULL,'fob',NULL,NULL,NULL,NULL,NULL,'2026-04-14 22:55:42','2026-04-14 22:55:42');
/*!40000 ALTER TABLE `sales_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales_reports`
--

DROP TABLE IF EXISTS `sales_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales_reports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales_reports`
--

LOCK TABLES `sales_reports` WRITE;
/*!40000 ALTER TABLE `sales_reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `sales_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `scheduling_of_deliveries`
--

DROP TABLE IF EXISTS `scheduling_of_deliveries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `scheduling_of_deliveries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `schedule_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_id` bigint unsigned DEFAULT NULL,
  `client_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `delivery_address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `delivery_date` date NOT NULL,
  `time_window_id` bigint unsigned DEFAULT NULL,
  `vehicle_id` bigint unsigned DEFAULT NULL,
  `driver_id` bigint unsigned DEFAULT NULL,
  `driver_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `weight_kg` decimal(10,3) DEFAULT NULL,
  `volume_m3` decimal(10,3) DEFAULT NULL,
  `priority` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendente',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `receiver_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receiver_document` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivered_at` timestamp NULL DEFAULT NULL,
  `rescheduled_from_id` bigint unsigned DEFAULT NULL,
  `reschedule_reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `scheduling_of_deliveries_schedule_number_unique` (`schedule_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `scheduling_of_deliveries`
--

LOCK TABLES `scheduling_of_deliveries` WRITE;
/*!40000 ALTER TABLE `scheduling_of_deliveries` DISABLE KEYS */;
/*!40000 ALTER TABLE `scheduling_of_deliveries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('GuwtmRTvU3wGsoLfYqiccQSf0L1gYyrTwHqux17f',NULL,'172.20.0.1','curl/8.5.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiTENpUTJUZ3RnenZRZnpnY29OWWpYZExsUGRjbHpRblk1MW1FNGREZCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyMToiaHR0cDovL2xvY2FsaG9zdDo4MDAwIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1777390196),('kz9ybxZduq2btXhOwUQ8y2UdWj7m796V3AQih83n',1,'172.20.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiVkFRSUZhU2ZZTTlibWl1azlTakJVTWh4WmRnV3ljYjNubFJNVDF6SyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzQ6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC91c2Vycy8zL2VkaXQiO3M6NToicm91dGUiO3M6MTA6InVzZXJzLmVkaXQiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=',1776386153),('MMxpqsVlwWQeZby5Kct6UXeIRgT5PFvXxij8PnJo',1,'172.20.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiY3lHeUE5bnVuY1h0SVZibVdOdXZMWGZWMDB3ajlFb0I2eDI3a3Z4byI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMCI7czo1OiJyb3V0ZSI7czo0OiJob21lIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9',1776992173),('pdnMPemy0A4ZfmFAKWsNvk31q1HyMCuT19kMKhZD',1,'172.20.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUzZqczJoSEIyT09KVUtTdUxWTk5TOFB4TG9lNmN2cmx3aWMxTThhRiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMCI7czo1OiJyb3V0ZSI7czo0OiJob21lIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9',1776722887),('uffig7Qts3VqFqrnrXJWAZMH8d4tCVlxDoy0mVeH',NULL,'172.20.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTFJuNmd0RXAwQThkY1lWb0FXUFRRNnJpWEJjM1JwTUhOZ1lKdzd5MiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyMToiaHR0cDovL2xvY2FsaG9zdDo4MDAwIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMCI7czo1OiJyb3V0ZSI7czo0OiJob21lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1777390265);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `group` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'system_name','Nexora ERP','general','2026-04-11 17:57:21','2026-04-11 17:57:21'),(2,'system_slogan','Gestão Inteligente para Empresas Modernas','general','2026-04-11 17:57:21','2026-04-11 17:57:21'),(3,'timezone','America/Sao_Paulo','general','2026-04-11 17:57:21','2026-04-11 17:57:21'),(4,'language','pt_BR','general','2026-04-11 17:57:21','2026-04-11 17:57:21'),(5,'date_format','d/m/Y','general','2026-04-11 17:57:21','2026-04-11 17:57:21'),(6,'time_format','24h','general','2026-04-11 17:57:21','2026-04-11 17:57:21'),(7,'company_name','','company','2026-04-11 17:57:21','2026-04-11 17:57:21'),(8,'company_fantasy','','company','2026-04-11 17:57:21','2026-04-11 17:57:21'),(9,'company_cnpj','','company','2026-04-11 17:57:21','2026-04-11 17:57:21'),(10,'company_ie','','company','2026-04-11 17:57:21','2026-04-11 17:57:21'),(11,'company_address','','company','2026-04-11 17:57:21','2026-04-11 17:57:21'),(12,'company_number','','company','2026-04-11 17:57:21','2026-04-11 17:57:21'),(13,'company_city','','company','2026-04-11 17:57:21','2026-04-11 17:57:21'),(14,'company_state','SP','company','2026-04-11 17:57:21','2026-04-11 17:57:21'),(15,'company_zipcode','','company','2026-04-11 17:57:21','2026-04-11 17:57:21'),(16,'company_email','','company','2026-04-11 17:57:21','2026-04-11 17:57:21'),(17,'company_phone','','company','2026-04-11 17:57:21','2026-04-11 17:57:21'),(18,'currency','BRL','financial','2026-04-11 17:57:21','2026-04-11 17:57:21'),(19,'decimal_separator',',','financial','2026-04-11 17:57:21','2026-04-11 17:57:21'),(20,'thousand_separator','.','financial','2026-04-11 17:57:21','2026-04-11 17:57:21'),(21,'default_tax','0','financial','2026-04-11 17:57:21','2026-04-11 17:57:21'),(22,'notify_low_stock','1','notifications','2026-04-11 17:57:21','2026-04-11 17:57:21'),(23,'notify_welcome_email','1','notifications','2026-04-11 17:57:21','2026-04-11 17:57:21'),(24,'notify_browser','1','notifications','2026-04-11 17:57:21','2026-04-11 17:57:21'),(25,'whatsapp_api_key','','notifications','2026-04-11 17:57:21','2026-04-11 17:57:21'),(26,'theme','light','appearance','2026-04-11 17:57:21','2026-04-11 17:57:21'),(27,'primary_color','blue','appearance','2026-04-11 17:57:21','2026-04-11 17:57:21'),(28,'ui_density','comfortable','appearance','2026-04-11 17:57:21','2026-04-11 17:57:21'),(29,'sidebar_default','expanded','appearance','2026-04-11 17:57:21','2026-04-11 17:57:21'),(30,'session_timeout','120','security','2026-04-11 17:57:21','2026-04-11 17:57:21'),(31,'password_strength','1','security','2026-04-11 17:57:21','2026-04-11 17:57:21'),(32,'maintenance_mode','0','security','2026-04-11 17:57:21','2026-04-11 17:57:21'),(33,'activity_log','1','security','2026-04-11 17:57:21','2026-04-11 17:57:21'),(34,'allow_sale_no_stock','nao','stock','2026-04-11 17:57:21','2026-04-11 17:57:21'),(35,'stock_reserve_moment','nota','stock','2026-04-11 17:57:21','2026-04-11 17:57:21'),(36,'critical_stock_percent','10','stock','2026-04-11 17:57:21','2026-04-11 17:57:21'),(37,'default_cfop','5102','fiscal','2026-04-11 17:57:21','2026-04-11 17:57:21'),(38,'auto_emit_nfe','0','fiscal','2026-04-11 17:57:21','2026-04-11 17:57:21'),(39,'emission_environment','homologacao','fiscal','2026-04-11 17:57:21','2026-04-11 17:57:21'),(40,'realtime_tax_calc','0','fiscal','2026-04-11 17:57:21','2026-04-11 17:57:21'),(41,'allow_negative_margin','0','sales','2026-04-11 17:57:21','2026-04-11 17:57:21'),(42,'max_discount_percent','5','sales','2026-04-11 17:57:21','2026-04-11 17:57:21'),(43,'active_price_table','varejo','sales','2026-04-11 17:57:21','2026-04-11 17:57:21'),(44,'quote_validity_days','7','sales','2026-04-11 17:57:21','2026-04-11 17:57:21'),(45,'sale_type','hibrido','sales','2026-04-11 17:57:21','2026-04-11 17:57:21'),(46,'require_cpf_on_note','0','sales','2026-04-11 17:57:21','2026-04-11 17:57:21');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stitch_beats`
--

DROP TABLE IF EXISTS `stitch_beats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stitch_beats` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stitch_beats`
--

LOCK TABLES `stitch_beats` WRITE;
/*!40000 ALTER TABLE `stitch_beats` DISABLE KEYS */;
/*!40000 ALTER TABLE `stitch_beats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_movements`
--

DROP TABLE IF EXISTS `stock_movements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_movements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `quantity` decimal(15,3) NOT NULL,
  `type` enum('input','output','adjustment','transfer') COLLATE utf8mb4_unicode_ci NOT NULL,
  `origin` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit_cost` decimal(15,2) DEFAULT NULL,
  `observation` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_movements_product_id_foreign` (`product_id`),
  KEY `stock_movements_user_id_foreign` (`user_id`),
  CONSTRAINT `stock_movements_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_movements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_movements`
--

LOCK TABLES `stock_movements` WRITE;
/*!40000 ALTER TABLE `stock_movements` DISABLE KEYS */;
/*!40000 ALTER TABLE `stock_movements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stocks`
--

DROP TABLE IF EXISTS `stocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stocks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stocks`
--

LOCK TABLES `stocks` WRITE;
/*!40000 ALTER TABLE `stocks` DISABLE KEYS */;
/*!40000 ALTER TABLE `stocks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suppliers` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `social_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `taxNumber` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address_zip_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address_street` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address_complement` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_district` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address_city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address_state` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
INSERT INTO `suppliers` VALUES ('40802797-d363-4639-accf-7f13a67491ff','Fornecedor Beta LTDA','Beta Atacadista','98765432000155','vendas@beta.com','20040002','Avenida Central','456','Sem complemento','Centro','Rio de Janeiro','RJ','21988882222','2026-04-11 17:57:22','2026-04-11 17:57:22',NULL),('7f42b0ac-29d5-44f6-af13-dd0568a6322a','Fornecedor Gama LTDA','Gama Comercial','45123789000110','gama@gama.com','30140071','Rua Minas Gerais','789','Loja B','Funcionários','Belo Horizonte','MG','31977773333','2026-04-11 17:57:22','2026-04-11 17:57:22',NULL),('b9549045-f4ca-434d-827f-5e5500adaab9','Fornecedor Alpha LTDA','Alpha Distribuidora','12345678000190','contato@alpha.com','01001000','Rua das Flores','123','Sala 01','Centro','São Paulo','SP','11999991111','2026-04-11 17:57:22','2026-04-11 17:57:22',NULL);
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_logs`
--

DROP TABLE IF EXISTS `system_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `level` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'success',
  `action` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `module` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Sistema',
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `user_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `context` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `system_logs_level_created_at_index` (`level`,`created_at`),
  KEY `system_logs_module_created_at_index` (`module`,`created_at`),
  KEY `system_logs_user_id_index` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_logs`
--

LOCK TABLES `system_logs` WRITE;
/*!40000 ALTER TABLE `system_logs` DISABLE KEYS */;
INSERT INTO `system_logs` VALUES (1,'success','EDITAR_USER','Segurança','Usuário \"Administrador\" foi atualizado (ID: 1).','172.20.0.1',1,'Administrador','admin@nexora.local',NULL,'2026-04-23 21:54:30','2026-04-23 21:54:30'),(2,'success','EDITAR_USER','Segurança','Usuário \"Administrador\" foi atualizado (ID: 1).','172.20.0.1',1,'Administrador','admin@nexora.local',NULL,'2026-04-23 21:56:12','2026-04-23 21:56:12'),(3,'success','LOGIN','Segurança','Usuário realizou login no sistema.','172.20.0.1',1,'Administrador','admin@nexora.local','{\"ip\": \"172.20.0.1\"}','2026-04-23 21:56:12','2026-04-23 21:56:12');
/*!40000 ALTER TABLE `system_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tickets_suporte`
--

DROP TABLE IF EXISTS `tickets_suporte`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tickets_suporte` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `assunto` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aberto',
  `prioridade` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'media',
  `categoria` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fechado_em` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tickets_suporte_user_id_foreign` (`user_id`),
  CONSTRAINT `tickets_suporte_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tickets_suporte`
--

LOCK TABLES `tickets_suporte` WRITE;
/*!40000 ALTER TABLE `tickets_suporte` DISABLE KEYS */;
INSERT INTO `tickets_suporte` VALUES ('2163781b-3c22-4a6c-9dec-60846ea8d802',11,'Erro nota fiscal','fechado','media','Vendas','2026-04-15 21:45:40','2026-04-15 21:27:43','2026-04-15 21:45:40'),('4350b4e1-e78b-42f5-931d-dc6b9baf07c4',11,'Erro nota fiscal','fechado','media','Vendas','2026-04-15 21:45:40','2026-04-15 21:31:11','2026-04-15 21:45:40'),('79f7fd43-0943-495d-9aee-fb4a6eed328a',11,'NOTA FISCAL','fechado','media','FISCAL','2026-04-15 21:45:40','2026-04-15 20:58:02','2026-04-15 21:45:40'),('9786f037-6c51-4655-8be1-83658a0ba8ba',11,'rejeição na nota fiscal','em_andamento','media','vendas e fiscal',NULL,'2026-04-16 21:31:28','2026-04-16 21:31:28'),('ae5584fc-1967-41d6-9c74-2eb7253ffbcd',11,'Erro ao emitir nota fiscal','fechado','urgente','Venda','2026-04-15 21:45:40','2026-04-15 21:10:44','2026-04-15 21:45:40');
/*!40000 ALTER TABLE `tickets_suporte` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `time_records`
--

DROP TABLE IF EXISTS `time_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `time_records` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `work_shift_id` bigint unsigned DEFAULT NULL,
  `date` date NOT NULL,
  `clock_in` datetime DEFAULT NULL,
  `break_start` datetime DEFAULT NULL,
  `break_end` datetime DEFAULT NULL,
  `clock_out` datetime DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'absent',
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `observation` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `time_records_employee_id_foreign` (`employee_id`),
  KEY `time_records_work_shift_id_foreign` (`work_shift_id`),
  CONSTRAINT `time_records_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `time_records_work_shift_id_foreign` FOREIGN KEY (`work_shift_id`) REFERENCES `work_shifts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `time_records`
--

LOCK TABLES `time_records` WRITE;
/*!40000 ALTER TABLE `time_records` DISABLE KEYS */;
/*!40000 ALTER TABLE `time_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_operacoes_fiscais`
--

DROP TABLE IF EXISTS `tipo_operacoes_fiscais`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipo_operacoes_fiscais` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `natureza_operacao` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo_movimento` enum('entrada','saida') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'saida',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `cfop` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icms_cst` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icms_modalidade_bc` tinyint DEFAULT NULL,
  `icms_aliquota` decimal(5,2) DEFAULT NULL,
  `icms_reducao_bc` decimal(5,2) DEFAULT NULL,
  `ipi_cst` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ipi_modalidade` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ipi_aliquota` decimal(5,2) DEFAULT NULL,
  `pis_cst` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pis_aliquota` decimal(5,2) DEFAULT NULL,
  `cofins_cst` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cofins_aliquota` decimal(5,2) DEFAULT NULL,
  `observacoes` text COLLATE utf8mb4_unicode_ci,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tipo_operacoes_fiscais_codigo_unique` (`codigo`),
  KEY `tipo_operacoes_fiscais_tipo_movimento_is_active_index` (`tipo_movimento`,`is_active`),
  KEY `tipo_operacoes_fiscais_cfop_index` (`cfop`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_operacoes_fiscais`
--

LOCK TABLES `tipo_operacoes_fiscais` WRITE;
/*!40000 ALTER TABLE `tipo_operacoes_fiscais` DISABLE KEYS */;
INSERT INTO `tipo_operacoes_fiscais` VALUES (1,'VENDA-EST','Venda de Mercadoria – Estadual','Venda de Mercadoria','saida',1,'5102','00',3,12.00,NULL,NULL,NULL,NULL,'01',0.65,'01',3.00,NULL,NULL,'2026-04-11 17:57:25','2026-04-11 17:57:25'),(2,'VENDA-INT','Venda de Mercadoria – Interestadual','Venda de Mercadoria','saida',1,'6102','00',3,7.00,NULL,NULL,NULL,NULL,'01',0.65,'01',3.00,NULL,NULL,'2026-04-11 17:57:25','2026-04-11 17:57:25'),(3,'VENDA-ST-EST','Venda de Mercadoria com ST – Estadual','Venda de Mercadoria c/ ST','saida',1,'5405','60',NULL,0.00,NULL,NULL,NULL,NULL,'01',0.65,'01',3.00,NULL,NULL,'2026-04-11 17:57:25','2026-04-11 17:57:25'),(4,'VENDA-ST-INT','Venda de Mercadoria com ST – Interestadual','Venda de Mercadoria c/ ST','saida',1,'6404','10',3,12.00,NULL,NULL,NULL,NULL,'01',0.65,'01',3.00,NULL,NULL,'2026-04-11 17:57:25','2026-04-11 17:57:25'),(5,'DEV-COMPRA-EST','Devolução de Compra – Estadual','Devolução de Compra','saida',1,'5202','00',3,12.00,NULL,NULL,NULL,NULL,'01',0.65,'01',3.00,NULL,NULL,'2026-04-11 17:57:25','2026-04-11 17:57:25'),(6,'COMPRA-EST','Compra de Mercadoria – Estadual','Compra de Mercadoria','entrada',1,'1102','00',3,12.00,NULL,NULL,NULL,NULL,'50',0.65,'50',3.00,NULL,NULL,'2026-04-11 17:57:25','2026-04-11 17:57:25'),(7,'COMPRA-INT','Compra de Mercadoria – Interestadual','Compra de Mercadoria','entrada',1,'2102','00',3,12.00,NULL,NULL,NULL,NULL,'50',0.65,'50',3.00,NULL,NULL,'2026-04-11 17:57:25','2026-04-11 17:57:25'),(8,'DEV-VENDA-EST','Devolução de Venda – Estadual','Devolução de Venda','entrada',1,'1202','00',3,12.00,NULL,NULL,NULL,NULL,'70',0.00,'70',0.00,NULL,NULL,'2026-04-11 17:57:25','2026-04-11 17:57:25'),(9,'VENDA-SN-EST','Venda de Mercadoria – Simples Nacional Estadual','Venda de Mercadoria','saida',1,'5102','400',NULL,0.00,NULL,NULL,NULL,NULL,'07',0.00,'07',0.00,'Para empresas optantes pelo Simples Nacional.',NULL,'2026-04-11 17:57:25','2026-04-11 17:57:25'),(10,'REMESSA','Remessa para Industrialização / Beneficiamento','Remessa p/ Industrialização','saida',1,'5949','41',NULL,0.00,NULL,NULL,NULL,NULL,'08',0.00,'08',0.00,NULL,NULL,'2026-04-11 17:57:25','2026-04-11 17:57:25');
/*!40000 ALTER TABLE `tipo_operacoes_fiscais` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transport_reports`
--

DROP TABLE IF EXISTS `transport_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transport_reports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transport_reports`
--

LOCK TABLES `transport_reports` WRITE;
/*!40000 ALTER TABLE `transport_reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `transport_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `units_of_measure`
--

DROP TABLE IF EXISTS `units_of_measure`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `units_of_measure` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abbreviation` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `units_of_measure_abbreviation_unique` (`abbreviation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `units_of_measure`
--

LOCK TABLES `units_of_measure` WRITE;
/*!40000 ALTER TABLE `units_of_measure` DISABLE KEYS */;
INSERT INTO `units_of_measure` VALUES ('062ed3df-02a4-459c-a51f-91ac96bba439','Rolo','RL','Produto vendido em rolo (ex: plástico, tecido)',1,'2026-04-11 17:57:21','2026-04-11 17:57:21',NULL),('284be9c2-6ff9-40fc-8004-252e7cdff49b','Pacote','PCT','Produto vendido em pacote',1,'2026-04-11 17:57:21','2026-04-11 17:57:21',NULL),('37eb6bd2-8f7e-47ba-925e-3fca82f11e12','Unidade','UN','Unidade simples de contagem',1,'2026-04-11 17:57:21','2026-04-11 17:57:21',NULL),('40b7166a-75a3-4be5-ad95-82e80e6865e2','Metro','MT','Medida de comprimento em metros',1,'2026-04-11 17:57:21','2026-04-11 17:57:21',NULL),('41a5e852-6f82-4d5e-862c-eddde94eb590','Saco','SC','Produto vendido em saco',1,'2026-04-11 17:57:21','2026-04-11 17:57:21',NULL),('439b42cb-3cad-4395-b54d-d429cafbe9b7','Litro','LT','Medida de volume em litros',1,'2026-04-11 17:57:21','2026-04-11 17:57:21',NULL),('4716df2f-1fdf-4cf0-88ea-fd4273736fb0','Grama','G','Medida de massa em gramas',1,'2026-04-11 17:57:21','2026-04-11 17:57:21',NULL),('5716f442-9562-4701-a2cd-d5c92cb3f3b7','Mililitro','ML','Medida de volume em mililitros',1,'2026-04-11 17:57:21','2026-04-11 17:57:21',NULL),('5e51e099-90de-480e-ba65-acebccf93401','Hora','H','Unidade de tempo para serviços',1,'2026-04-11 17:57:21','2026-04-11 17:57:21',NULL),('7b63f2e3-c95e-423a-87f5-9fde839c99a4','Caixa','CX','Agrupamento em caixa',1,'2026-04-11 17:57:21','2026-04-11 17:57:21',NULL),('9c174c75-d975-4ba6-99c5-62a5c7afaccd','Metro Quadrado','M2','Medida de área em metros quadrados',1,'2026-04-11 17:57:21','2026-04-11 17:57:21',NULL),('9de2aed3-66d7-44eb-a978-86cf201e6ec4','Dúzia','DZ','Conjunto de 12 unidades',1,'2026-04-11 17:57:21','2026-04-11 17:57:21',NULL),('a4290c51-43b9-43d1-96bd-39adc9081738','Par','PR','Par de itens (ex: sapatos, meias)',1,'2026-04-11 17:57:21','2026-04-11 17:57:21',NULL),('b1e3c706-e759-4dde-8312-02e675d40be2','Quilograma','KG','Medida de massa em quilogramas',1,'2026-04-11 17:57:21','2026-04-11 17:57:21',NULL),('b37eb12d-071d-4f1a-82a3-2ba4d1962e59','Centímetro','CM','Medida de comprimento em centímetros',1,'2026-04-11 17:57:21','2026-04-11 17:57:21',NULL),('b46c7929-be0e-4e9d-8bf0-398f227b055b','Cento','CT','Conjunto de 100 unidades',1,'2026-04-11 17:57:21','2026-04-11 17:57:21',NULL),('d672d8a6-8000-4b2d-825c-d8318d4a3e87','Tambor','TB','Produto vendido em tambor',1,'2026-04-11 17:57:21','2026-04-11 17:57:21',NULL),('f6001e57-9091-42c4-ad9f-ad432d01c33d','Peça','PC','Peça avulsa de produto',1,'2026-04-11 17:57:21','2026-04-11 17:57:21',NULL);
/*!40000 ALTER TABLE `units_of_measure` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `job_title` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `has_license` tinyint(1) NOT NULL DEFAULT '0',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `modules` json DEFAULT NULL,
  `company_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_company_id_foreign` (`company_id`),
  CONSTRAINT `users_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Administrador','admin@nexora.local',NULL,NULL,NULL,NULL,NULL,NULL,'$2y$12$4LxAF/tErzJOa9v8mGfmGuXvGpNESXJlkU9WUwia4BrFWmzSQ8nSm',1,1,1,'6tgJVWjiRA51axWpQHW00k7Byuiy40xnHk1ZyhuI3olkUHKxn9LCFGAGow30','2026-04-23 21:56:12','2026-04-11 17:57:21','2026-04-23 21:56:12',NULL,NULL),(2,'Carlos Eduardo Mendes','carlos.mendes@nexora-alimentos.com.br',NULL,'(11) 91234-5678','Gerente Comercial','Comercial',NULL,NULL,'$2y$12$Eqq9JsoFUyK.LxC/UM.AwuEuc3LgvXE5ixz74W226SMrrRNKM8KyG',0,1,1,NULL,NULL,'2026-04-14 22:51:58','2026-04-15 20:13:03','[\"dashboard\", \"cadastro\", \"producao\", \"estoque\", \"vendas\", \"compras\"]',1),(3,'Ana Paula Ferreira','ana.ferreira@nexora-alimentos.com.br',NULL,'(11) 92345-6789','Analista Financeiro','Financeiro',NULL,NULL,'$2y$12$n1n8Mz3uPxx5F0VuiwkYkuwmlYXuSqT2tppF4mObOjdum/q4OHn8K',0,1,1,NULL,NULL,'2026-04-14 22:51:58','2026-04-15 20:12:25','[\"dashboard\", \"cadastro\", \"producao\", \"estoque\", \"vendas\", \"compras\", \"fiscal\", \"financeiro\"]',1),(4,'Roberto Silva Santos','roberto.santos@nexora-alimentos.com.br',NULL,'(11) 93456-7890','Vendedor Externo','Vendas',NULL,NULL,'$2y$12$uOI8hEY5.nv/bEON.RRs3utHrOYvsSdKrMxpiwH5WgRk/SCwIwG5O',0,1,1,NULL,NULL,'2026-04-14 22:51:58','2026-04-14 22:51:58','[\"vendas\", \"pedidos\"]',1),(5,'Fernanda Lima Costa','fernanda.costa@nexora-logistica.com.br',NULL,'(21) 91234-0001','Coordenadora de Frota','Operações',NULL,NULL,'$2y$12$IEpG5BGdIkfsLrc7sCADQu2RdQdB6mWcZ3Th9RvGzu/VxwgJO2XFO',0,1,1,NULL,NULL,'2026-04-14 22:51:58','2026-04-14 22:51:58','[\"logistica\", \"veiculos\", \"rotas\"]',2),(6,'Marcos Vinicius Pereira','marcos.pereira@nexora-logistica.com.br',NULL,'(21) 99876-5432','Motorista Sênior','Operações',NULL,NULL,'$2y$12$oxqST5xJdIMmgjn5tiy2TuVfXheBeGUR6LU3gGYnVRIXKPBHdhtva',0,1,1,NULL,NULL,'2026-04-14 22:51:58','2026-04-14 22:51:58','[\"rotas\", \"entregas\"]',2),(7,'João Batista Oliveira','joao.oliveira@nexora-industria.com.br',NULL,'(31) 91111-2222','Supervisor de Produção','Produção',NULL,NULL,'$2y$12$03BaQqNJ7od85p0EFVVGJu6uWklTEAaWFkpZ4TG01JG06nclXTmWe',0,1,1,NULL,NULL,'2026-04-14 22:51:58','2026-04-14 22:51:58','[\"producao\", \"ordens_producao\", \"estoque\"]',3),(8,'Patrícia Souza Ramos','patricia.ramos@nexora-industria.com.br',NULL,'(31) 92222-3333','Analista de Qualidade','Qualidade',NULL,NULL,'$2y$12$eKCaI9WEbifjF210n4NyOu7SvwCf8ir9PbGxP1zSm//bCmRrmcPF6',0,1,1,NULL,NULL,'2026-04-14 22:51:58','2026-04-14 22:51:58','[\"producao\", \"qualidade\"]',3),(9,'Lucas Henrique Alves','lucas.alves@nexora-varejo.com.br',NULL,'(41) 93333-4444','Gerente de Loja','Varejo',NULL,NULL,'$2y$12$cfh23Xrn6cHcAjXniQmN8u6n/GO3qB8r9Owg5T2j97b3dxCjA1PwS',0,1,1,NULL,NULL,'2026-04-14 22:51:58','2026-04-14 22:51:58','[\"vendas\", \"estoque\", \"caixa\"]',4),(10,'Camila Torres Martins','camila.martins@nexora-varejo.com.br',NULL,'(41) 94444-5555','Assistente de Vendas','Vendas',NULL,NULL,'$2y$12$AmmQ1XeRLi4yhyjwAXZIyu.MHVnJOO2ObbxoispURIOgqYDyZYvi.',0,1,1,NULL,NULL,'2026-04-14 22:51:58','2026-04-14 22:51:58','[\"vendas\", \"pedidos\"]',4),(11,'Carlos Eduardo Mendes','teste@nexora.local',NULL,'(11) 91234-5678','Gerente Comercial','Comercial',NULL,NULL,'$2y$12$CQS5xObpHcrGp6KyLk9TuuAp8.Vzd8VPGL9v9mGwbLcpd6uLB/qH6',0,1,1,'WaJtRU82ABc4BDuwl7AlMyARUR2WYj5hJJerF9Kv3Jiid84eNZhkgbeQkAcP','2026-04-16 12:29:17','2026-04-14 22:55:42','2026-04-16 12:29:17','[\"dashboard\", \"cadastro\", \"producao\", \"estoque\", \"vendas\", \"compras\"]',1);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehicle_maintenances`
--

DROP TABLE IF EXISTS `vehicle_maintenances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehicle_maintenances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehicle_maintenances`
--

LOCK TABLES `vehicle_maintenances` WRITE;
/*!40000 ALTER TABLE `vehicle_maintenances` DISABLE KEYS */;
/*!40000 ALTER TABLE `vehicle_maintenances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehicle_trackings`
--

DROP TABLE IF EXISTS `vehicle_trackings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehicle_trackings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehicle_trackings`
--

LOCK TABLES `vehicle_trackings` WRITE;
/*!40000 ALTER TABLE `vehicle_trackings` DISABLE KEYS */;
/*!40000 ALTER TABLE `vehicle_trackings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vehicles`
--

DROP TABLE IF EXISTS `vehicles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vehicles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plate` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `renavam` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `chassis` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vehicle_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `species` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manufacturing_year` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model_year` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `brand` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fuel_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `year` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `power_hp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `displacement_cc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doors` tinyint unsigned DEFAULT NULL,
  `passenger_capacity` smallint unsigned DEFAULT NULL,
  `transmission_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `traction_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gross_weight` decimal(10,2) DEFAULT NULL,
  `net_weight` decimal(10,2) DEFAULT NULL,
  `cargo_capacity` decimal(10,2) DEFAULT NULL,
  `department` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `responsible_driver` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cost_center` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `acquisition_date` date DEFAULT NULL,
  `acquisition_value` decimal(15,2) DEFAULT NULL,
  `photos` json DEFAULT NULL,
  `observations` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vehicles_plate_unique` (`plate`),
  UNIQUE KEY `vehicles_renavam_unique` (`renavam`),
  UNIQUE KEY `vehicles_chassis_unique` (`chassis`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vehicles`
--

LOCK TABLES `vehicles` WRITE;
/*!40000 ALTER TABLE `vehicles` DISABLE KEYS */;
INSERT INTO `vehicles` VALUES (1,'Caminhão Baú 01 - Distribuição SP','ABC-1A23','12345678901','9BWZZZ377VT004251','caminhao','frota','carga','2020','2021','Mercedes-Benz','Atego 1719','Branco','diesel','2021','190','6374',2,2,'Manual','4x2',16000.00,6200.00,9800.00,'Logística','Marcos Vinicius Pereira','CC-LOG-001',NULL,'Garagem Central - Rio de Janeiro/RJ',NULL,1,'2021-03-15',185000.00,NULL,'Veículo principal de distribuição da rota SP.','2026-04-14 22:53:08','2026-04-14 22:53:08'),(2,'Caminhão Baú 02 - Distribuição RJ','DEF-2B34','23456789012','9BWZZZ377VT004252','caminhao','frota','carga','2019','2020','Volvo','FMX 460','Cinza','diesel','2020','460','12800',2,2,'Automático','6x4',25000.00,9500.00,15500.00,'Logística','Marcos Vinicius Pereira','CC-LOG-002',NULL,'Garagem Central - Rio de Janeiro/RJ',NULL,1,'2020-07-20',320000.00,NULL,'Rota RJ - MG.','2026-04-14 22:53:08','2026-04-14 22:53:08'),(3,'Van de Entrega 01','GHI-3C45','34567890123','9BD178TS0L3241001','van_furgao','frota','misto','2022','2022','Fiat','Ducato Cargo','Branco','diesel','2022','148','2287',3,3,'Manual','4x2',3500.00,1900.00,1600.00,'Logística',NULL,'CC-LOG-003',NULL,'Filial - São Paulo/SP',NULL,1,'2022-01-10',135000.00,NULL,'Entregas locais na região metropolitana.','2026-04-14 22:53:08','2026-04-14 22:53:08'),(4,'Carro de Representação - Diretoria','JKL-4D56','45678901234','9BWZZZ377VT004254','passeio','oficial','passageiro','2023','2023','Toyota','Corolla XEi','Prata','flex','2023','177','2000',4,5,'CVT','4x2',1755.00,1340.00,NULL,'Diretoria','Carlos Eduardo Mendes','CC-DIR-001',NULL,'Sede - São Paulo/SP',NULL,1,'2023-05-05',148990.00,NULL,'Veículo para uso da diretoria e reuniões externas.','2026-04-14 22:53:08','2026-04-14 22:53:08'),(5,'Pickup Comercial - Compras','MNO-5E67','56789012345','9BM255060N0123456','pickupe','frota','misto','2021','2022','Chevrolet','S10 LTZ','Preto','diesel','2022','200','2800',4,5,'Automático','4x4',2650.00,1900.00,750.00,'Compras','Roberto Silva Santos','CC-COM-001',NULL,'Filial - Belo Horizonte/MG',NULL,1,'2022-08-18',195000.00,NULL,'Utilizada para visitas a fornecedores.','2026-04-14 22:53:08','2026-04-14 22:53:08'),(6,'Utilitário - Manutenção','PQR-6F78','67890123456','9BWZZZ377VT004256','utilitario','frota','misto','2018','2019','Volkswagen','Saveiro Robust','Vermelho','flex','2019','104','1600',2,2,'Manual','4x2',1530.00,1000.00,530.00,'Manutenção',NULL,'CC-MAN-001',NULL,'Garagem - Curitiba/PR',NULL,1,'2019-02-28',62000.00,NULL,'Suporte às equipes de manutenção interna.','2026-04-14 22:53:08','2026-04-14 22:53:08');
/*!40000 ALTER TABLE `vehicles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `visits`
--

DROP TABLE IF EXISTS `visits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `visits` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `visits`
--

LOCK TABLES `visits` WRITE;
/*!40000 ALTER TABLE `visits` DISABLE KEYS */;
/*!40000 ALTER TABLE `visits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `work_shifts`
--

DROP TABLE IF EXISTS `work_shifts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `work_shifts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `break_duration` int NOT NULL DEFAULT '60',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `work_shifts`
--

LOCK TABLES `work_shifts` WRITE;
/*!40000 ALTER TABLE `work_shifts` DISABLE KEYS */;
/*!40000 ALTER TABLE `work_shifts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `working_days`
--

DROP TABLE IF EXISTS `working_days`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `working_days` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `working_days`
--

LOCK TABLES `working_days` WRITE;
/*!40000 ALTER TABLE `working_days` DISABLE KEYS */;
/*!40000 ALTER TABLE `working_days` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'nexora'
--

--
-- Dumping routines for database 'nexora'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-28 15:39:50
