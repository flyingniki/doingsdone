DROP DATABASE IF EXISTS doingsdone;

CREATE DATABASE doingsdone
  DEFAULT CHARACTER SET utf8
  DEFAULT COLLATE utf8_general_ci;

USE doingsdone;

/*Сначала создаем cusstomers, так как на ее ключи ссылаются внешние ключи из других таблиц*/
CREATE TABLE users (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `date_add` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `name` VARCHAR(255) NOT NULL,
  `password` CHAR(64) NOT NULL
);

CREATE TABLE projects (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL UNIQUE,
  `user_id` INT NOT NULL,
  /*внешний ключ user_id ссылается на ключ id из таблицы users*/
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
);

CREATE TABLE tasks (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `date_add` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` TINYINT DEFAULT 0,
  `title` VARCHAR(255) NOT NULL,
  `file` VARCHAR(255),
  `date_final` DATETIME,
  `user_id` INT NOT NULL,
  `project_id` INT NOT NULL,
  /*внешний ключ user_id ссылается на ключ id из таблицы users*/
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  /*внешний ключ project_id ссылается на ключ id из таблицы projects*/
  FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`)
);

CREATE INDEX t_title ON tasks (title);
