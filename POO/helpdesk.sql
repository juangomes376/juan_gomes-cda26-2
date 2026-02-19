CREATE DATABASE IF NOT EXISTS `helpdesk`;

-- Cria um usuário dedicado para desenvolvimento: usuário=`helpdesk`, senha=`helpdesk`
-- (Ajuste host / senha em produção; senha em texto claro apenas para ambiente local/seed)
CREATE USER IF NOT EXISTS 'helpdesk'@'localhost' IDENTIFIED BY 'helpdesk';
GRANT ALL PRIVILEGES ON `helpdesk`.* TO 'helpdesk'@'localhost';
FLUSH PRIVILEGES;

SET NAMES utf8mb4;
SET time_zone = '+00:00';
USE `helpdesk`;
DROP TABLE IF EXISTS ticket_tags;
DROP TABLE IF EXISTS comments;
DROP TABLE IF EXISTS tags;
DROP TABLE IF EXISTS tickets;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS roles;

CREATE TABLE roles (
  id TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
  label VARCHAR(32) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_roles_label (label)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  username VARCHAR(64) NOT NULL,
  email VARCHAR(255) NOT NULL,
  password VARCHAR(255) DEFAULT NULL,
  role_id TINYINT UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_username (username),
  UNIQUE KEY uq_users_email (email),
  KEY idx_users_role (role_id),
  CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE tickets (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INT UNSIGNED NOT NULL,
  title VARCHAR(150) NOT NULL,
  description TEXT NOT NULL,
  status ENUM('open','in_progress','closed') NOT NULL DEFAULT 'open',
  priority ENUM('low','normal','high','urgent') NOT NULL DEFAULT 'normal',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_tickets_user (user_id),
  KEY idx_tickets_status (status),
  KEY idx_tickets_priority (priority),
  CONSTRAINT fk_tickets_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE tags (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  label VARCHAR(50) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_tags_label (label)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE ticket_tags (
  ticket_id INT UNSIGNED NOT NULL,
  tag_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (ticket_id, tag_id),
  KEY idx_ticket_tags_tag (tag_id),
  CONSTRAINT fk_tt_ticket FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
  CONSTRAINT fk_tt_tag FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE comments (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  ticket_id INT UNSIGNED NOT NULL,
  author VARCHAR(100) NOT NULL,
  body TEXT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_comments_ticket (ticket_id),
  CONSTRAINT fk_comments_ticket FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed data
INSERT INTO roles (label) VALUES
  ('client'),
  ('developer'),
  ('manager');

INSERT INTO users (username, email, password, role_id) VALUES
  ('alice', 'alice.7382@example.test', NULL, 1),
  ('bob',   'bob.2910@example.test', NULL, 1),
  ('carol', 'carol.1465@example.test', NULL, 2),
  ('dave',  'dave.5508@example.test', NULL, 2),
  ('eve',   'eve.9023@example.test', NULL, 3);

INSERT INTO tickets (user_id, title, description, status, priority, created_at) VALUES
  (1, 'Impossible de se connecter à la prod', 'Connexion SSH refusée depuis ce matin sur le serveur principal.', 'open', 'urgent', '2026-02-17 08:30:00'),
  (2, 'Erreur 500 sur la page de paiement', 'Les clients obtiennent une erreur 500 après la soumission du formulaire CB.', 'in_progress', 'high', '2026-02-15 14:10:00'),
  (1, 'Ajouter un captcha au formulaire de contact', 'Demande de sécurité pour limiter les spams.', 'open', 'normal', '2026-02-10 09:00:00'),
  (3, 'Refacto module PDF', 'Le module de génération PDF prend trop de mémoire, besoin de refactoring.', 'in_progress', 'high', '2026-02-05 11:45:00'),
  (4, 'Mise à jour dépendances Composer', 'Mettre à jour les dépendances et vérifier la compatibilité PHP 8.3.', 'closed', 'normal', '2026-01-30 16:20:00'),
  (2, 'Logo flou sur la page d’accueil', 'Le logo apparaît flou sur mobile retina.', 'closed', 'low', '2026-01-25 10:00:00');

INSERT INTO tags (label) VALUES
  ('infra'),
  ('backend'),
  ('frontend'),
  ('paiement'),
  ('sécurité'),
  ('performance'),
  ('low'),
  ('normal'),
  ('high'),
  ('urgent'),
  ('maintenance');

INSERT INTO ticket_tags (ticket_id, tag_id) VALUES
  (1, 10),
  (1, 2),
  (2, 1),
  (2, 3),
  (2, 5),
  (3, 6),
  (4, 7),
  (4, 3),
  (4, 8),
  (5, 8),
  (6, 4);

INSERT INTO comments (ticket_id, author, body, created_at) VALUES
  (1, 'carol', 'Vérifié : le port SSH est bien ouvert, suspicion de blocage Fail2ban.', '2026-02-17 09:10:00'),
  (1, 'eve', 'Merci d’ajouter les logs /var/log/auth.log sur l’issue.', '2026-02-17 09:20:00'),
  (2, 'dave', 'Trace trouvée : NullPointerException dans PaymentService.php:142.', '2026-02-15 15:00:00'),
  (2, 'carol', 'Patch en cours, on teste la route /payments/confirm.', '2026-02-15 18:30:00'),
  (3, 'alice', 'Peut-on prioriser ? les spams augmentent.', '2026-02-12 08:05:00'),
  (4, 'carol', 'Refacto démarrée, extraction du renderer vers un service dédié.', '2026-02-06 10:00:00'),
  (5, 'dave', 'Mises à jour effectuées, tests unitaires OK.', '2026-02-01 09:30:00'),
  (6, 'bob', 'Logo optimisé et exporté en 2x, merci de vérifier.', '2026-01-26 11:00:00');

