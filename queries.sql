USE doingsdone;

/*Добавляем пользователей*/
INSERT INTO users (`date_add`, `email`, `name`, `password`)
VALUES ('12.01.2022', 'vasya@mail.ru', 'Vasya', 'pass123'),
        ('15.03.2022', 'igor@yandex.ru', 'Igor', 'fsdgjk123'),
        ('12.04.2022', 'jackie@mail.ru', 'Jack', 'jf74f_7f');

/*Добавляем список проектов*/
INSERT INTO projects (`title`, `user_id`)
VALUES ('Входящие', 1),
        ('Учеба', 2),
        ('Работа', 3),
        ('Домашние дела', 3),
        ('Авто', 1);

/*Добавляем список задач*/
INSERT INTO tasks (`date_add`, `status`, `title`, `file`, `date_final`, `user_id`, `project_id`)
VALUES ('21.05.2019', '0', 'Собеседование в IT компании', 'first.ref', '12.04.2022', 3, 3),
        ('15.08.2019', '0', 'Выполнить тестовое задание', 'second.ref', '25.12.2019', 2, 3),
        ('25.05.2019', '1', 'Сделать задание первого раздела', 'third.ref', '21.12.2019', 2, 2),
        ('21.11.2019', '0', 'Встреча с другом', 'fourth.ref', '22.12.2019', 1, 1),
        ('03.12.2019', '0', 'Купить корм для кота', 'fourth.ref', NULL, 3, 4),
        ('12.10.2019', '1', 'Заказать пиццу', 'fifth.ref', NULL, 3, 4);


/*Получаем список из всех проектов для одного пользователя*/
SELECT p.title FROM projects p WHERE p.user_id = 3;

/*Получаем список из всех задач для одного проекта*/
SELECT t.date_add, t.status, t.title, t.file, t.date_final, t.user_id, t.project_id FROM tasks t WHERE t.project_id = 1;

/*Помечаем задачу как выполненную*/
UPDATE tasks SET status = 1 WHERE id = 1;

/*Обновляем название задачи по её идентификатору*/
UPDATE tasks SET title = 'Новое название' WHERE id = 3;

