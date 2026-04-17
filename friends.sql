CREATE DATABASE IF NOT EXISTS friends
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE friends;

CREATE TABLE IF NOT EXISTS friends (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    surname VARCHAR(64) NOT NULL,
    name VARCHAR(64) NOT NULL,
    patronymic VARCHAR(64) NOT NULL DEFAULT '',
    sex ENUM('М','Ж') NOT NULL DEFAULT 'М',
    birth_date DATE NULL,
    phone VARCHAR(32) NOT NULL DEFAULT '',
    address VARCHAR(255) NOT NULL DEFAULT '',
    email VARCHAR(128) NOT NULL DEFAULT '',
    comment TEXT NULL,
    PRIMARY KEY (id),
    KEY idx_surname_name (surname, name),
    KEY idx_birth_date (birth_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO friends (surname, name, patronymic, sex, birth_date, phone, address, email, comment) VALUES
('Иванов', 'Иван', 'Иванович', 'М', '1998-02-15', '+7-900-111-22-33', 'г. Москва', 'ivanov@example.com', 'Любит спорт'),
('Петрова', 'Анна', 'Сергеевна', 'Ж', '2000-06-20', '+7-900-222-33-44', 'г. Казань', 'petrova@example.com', 'Учится в вузе'),
('Сидоров', 'Павел', 'Олегович', 'М', '1997-11-03', '+7-900-333-44-55', 'г. Новосибирск', 'sidorov@example.com', 'Работает программистом'),
('Кузнецова', 'Мария', 'Андреевна', 'Ж', '2001-09-12', '+7-900-444-55-66', 'г. Санкт-Петербург', 'kuznetsova@example.com', 'Доступна после 18:00'),
('Смирнов', 'Алексей', 'Дмитриевич', 'М', '1995-04-10', '+7-900-555-66-77', 'г. Омск', 'smirnov@example.com', 'Водитель'),
('Васильева', 'Екатерина', 'Игоревна', 'Ж', '1999-08-25', '+7-900-666-77-88', 'г. Самара', 'vasileva@example.com', 'Фрилансер'),
('Морозов', 'Денис', 'Алексеевич', 'М', '1996-01-17', '+7-900-777-88-99', 'г. Уфа', 'morozov@example.com', 'Спортсмен'),
('Новикова', 'Ольга', 'Павловна', 'Ж', '2002-03-30', '+7-900-888-99-00', 'г. Пермь', 'novikova@example.com', 'Студентка'),
('Фёдоров', 'Никита', 'Сергеевич', 'М', '1994-12-05', '+7-900-999-00-11', 'г. Краснодар', 'fedorov@example.com', 'Менеджер'),
('Алексеева', 'Дарья', 'Викторовна', 'Ж', '2001-07-14', '+7-901-111-22-33', 'г. Ростов-на-Дону', 'alekseeva@example.com', 'Работает удаленно'),
('Егоров', 'Максим', 'Ильич', 'М', '1993-10-22', '+7-901-222-33-44', 'г. Воронеж', 'egorov@example.com', 'Инженер');