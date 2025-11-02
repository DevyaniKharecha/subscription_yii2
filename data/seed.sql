-- Minimal schema & data for the assessment
-- NOTE: Adjust table options/engine/charset as needed.

-- ----------------------------
-- USERS
-- ----------------------------
CREATE TABLE IF NOT EXISTS `user` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `email` varchar(128) NULL,
  `username` varchar(64) NULL,
  `password` varchar(64) NULL,
  `auth_key` varchar(64) NULL,
  `is_admin` tinyint(1) DEFAULT 0
);

-- ----------------------------
-- PLANS
-- ----------------------------
CREATE TABLE IF NOT EXISTS `plan` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00
);

-- Seed plans
INSERT INTO `plan` (`name`, `price`) VALUES
('Basic', 9.99),
('Pro', 19.99),
('Enterprise', 99.00);

-- ----------------------------
-- USERS (seed demo data)
-- ----------------------------
INSERT INTO `user` (`email`, `password`, `username`, `is_admin`) VALUES
('alice@gmail.com', 'alice', 'alice', 0),
('bob@hotmail.com', 'bob', 'bob', 1),
('charlie@yahoo.com', 'charlie', 'charlie', 0);

-- ----------------------------
-- SUBSCRIPTIONS (requires migration already created)
-- ----------------------------
-- Fields assumed: id, user_id, plan_id, type, status, start_at, end_at, created_at, updated_at

INSERT INTO `subscription` (`user_id`, `plan_id`, `type`, `status`, `start_at`, `end_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'trial', 'active', NOW(), DATE_ADD(NOW(), INTERVAL 7 DAY), NOW(), NOW()),
(2, 2, 'paid', 'active', NOW(), DATE_ADD(NOW(), INTERVAL 1 MONTH), NOW(), NOW()),
(3, 3, 'cancelled', 'cancelled', DATE_SUB(NOW(), INTERVAL 10 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY), NOW(), NOW());

-- ----------------------------
-- NOTES
-- ----------------------------
-- Login credentials:
--   username: alice | password: alice → Regular user
--   username: bob   | password: alice → Admin user
--   username: charlie | password: alice → Regular user
--
-- After import, run migration for subscription if not present:
--   php yii migrate
--
-- Then visit /site/login and /subscription/subscription/index
