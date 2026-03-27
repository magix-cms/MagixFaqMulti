CREATE TABLE IF NOT EXISTS `mc_faqmulti` (
    `id_faqmulti` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `item_type` VARCHAR(50) NOT NULL DEFAULT 'home',
    `item_id` INT UNSIGNED DEFAULT NULL,
    `order_faqmulti` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (`id_faqmulti`),
    KEY `idx_item` (`item_type`, `item_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `mc_faqmulti_content` (
    `id_faqmulti` INT UNSIGNED NOT NULL,
    `id_lang` SMALLINT UNSIGNED NOT NULL,
    `title_faqmulti` VARCHAR(255) NOT NULL,
    `desc_faqmulti` TEXT,
    `published_faqmulti` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (`id_faqmulti`, `id_lang`),
    KEY `id_lang` (`id_lang`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `mc_faqmulti_content`
    ADD CONSTRAINT `fk_faqmulti_id` FOREIGN KEY (`id_faqmulti`) REFERENCES `mc_faqmulti` (`id_faqmulti`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_faqmulti_lang` FOREIGN KEY (`id_lang`) REFERENCES `mc_lang` (`id_lang`) ON DELETE CASCADE ON UPDATE CASCADE;