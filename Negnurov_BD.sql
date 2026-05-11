-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Май 03 2026 г., 10:51
-- Версия сервера: 8.0.30
-- Версия PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `Negnurov_BD`
--

-- --------------------------------------------------------

--
-- Структура таблицы `Exercises`
--

CREATE TABLE `Exercises` (
  `Exercise_id` int NOT NULL,
  `Exercise_name` varchar(150) NOT NULL,
  `Description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `Gif_path` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `Primary_group_id` int NOT NULL,
  `Secondary_group_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Exercises`
--

INSERT INTO `Exercises` (`Exercise_id`, `Exercise_name`, `Description`, `Gif_path`, `Primary_group_id`, `Secondary_group_id`) VALUES
(5, 'Тяга верхнего блока', 'Тяговое упражнение для широчайших мышц спины.', NULL, 1, 21),
(104, 'Жим штанги лежа', 'Базовое упражнение для развития силы и массы грудных мышц.', NULL, 18, 22),
(105, 'Жим гантелей на наклонной скамье', 'Смещает акцент на верх грудных мышц и стабилизаторы.', NULL, 18, 20),
(106, 'Отжимания', 'Базовое упражнение с собственным весом для груди, трицепса и плеч.', NULL, 18, 22),
(107, 'Разведение гантелей лежа', 'Изолирующее упражнение для растяжения и сокращения грудных мышц.', NULL, 18, 20),
(108, 'Сведение рук в кроссовере', 'Позволяет контролируемо проработать грудь в полной амплитуде.', NULL, 18, NULL),
(109, 'Жим в тренажере на грудь', 'Упражнение для безопасной силовой работы и контроля траектории.', NULL, 18, 22),
(110, 'Подтягивания широким хватом', 'Базовое упражнение для широчайших и силы спины.', NULL, 1, 21),
(111, 'Тяга штанги в наклоне', 'Развивает толщину спины и силу тяговых движений.', NULL, 1, 21),
(112, 'Тяга горизонтального блока', 'Позволяет проработать среднюю часть спины и контроль лопаток.', NULL, 1, 21),
(113, 'Тяга гантели к поясу', 'Односторонняя тяга для акцента на широчайшие и ромбовидные.', NULL, 1, 21),
(114, 'Гиперэкстензия', 'Укрепляет разгибатели спины, ягодицы и заднюю поверхность бедра.', NULL, 1, 24),
(115, 'Приседания со штангой', 'Базовое силовое движение для ног и ягодиц.', NULL, 19, 24),
(116, 'Жим ногами', 'Позволяет безопасно увеличить нагрузку на ноги в тренажере.', NULL, 19, 24),
(117, 'Выпады с гантелями', 'Развивают силу ног, баланс и стабилизацию корпуса.', NULL, 19, 24),
(118, 'Румынская тяга', 'Сильный акцент на заднюю поверхность бедра и ягодицы.', NULL, 19, 24),
(119, 'Разгибание ног в тренажере', 'Изолирующее упражнение на квадрицепсы.', NULL, 19, NULL),
(120, 'Сгибание ног лежа', 'Изолирующее упражнение для бицепса бедра.', NULL, 19, NULL),
(121, 'Подъем на носки стоя', 'Классическое упражнение для икроножных мышц.', NULL, 19, NULL),
(122, 'Жим гантелей сидя', 'Базовое упражнение для объема и силы плеч.', NULL, 20, 22),
(123, 'Армейский жим', 'Силовой жим над головой для плеч и трицепса.', NULL, 20, 22),
(124, 'Разведения гантелей в стороны', 'Изолирует средний пучок дельтовидных мышц.', NULL, 20, NULL),
(125, 'Разведения гантелей в наклоне', 'Акцент на заднюю дельту и верх спины.', NULL, 20, 1),
(126, 'Тяга штанги к подбородку', 'Развивает средние дельты и верх трапеций.', NULL, 20, 1),
(127, 'Жим Арнольда', 'Увеличивает нагрузку на передние и средние дельты.', NULL, 20, 22),
(128, 'Подъем штанги на бицепс', 'Классическое базовое движение для бицепса.', NULL, 21, NULL),
(129, 'Подъем гантелей на бицепс', 'Позволяет отдельно проработать каждую руку.', NULL, 21, NULL),
(130, 'Молотки', 'Смещают акцент на плечелучевую мышцу и брахиалис.', NULL, 21, NULL),
(131, 'Сгибание рук на скамье Скотта', 'Изолирует бицепс и уменьшает читинг.', NULL, 21, NULL),
(132, 'Сгибание рук в кроссовере', 'Постоянное натяжение помогает лучше почувствовать мышцу.', NULL, 21, NULL),
(133, 'Концентрированный подъем на бицепс', 'Подходит для детальной проработки и контроля амплитуды.', NULL, 21, NULL),
(134, 'Французский жим', 'Классическое упражнение для длинной головки трицепса.', NULL, 22, NULL),
(135, 'Разгибание рук на верхнем блоке', 'Изолирующее упражнение для трицепса в тренажере.', NULL, 22, NULL),
(136, 'Отжимания на брусьях', 'Силовое движение для трицепса и нижней части груди.', NULL, 22, 18),
(137, 'Разгибание руки с гантелью из-за головы', 'Позволяет проработать трицепс по одной руке.', NULL, 22, NULL),
(138, 'Жим лежа узким хватом', 'Усиленный акцент на трицепс при жимовом движении.', NULL, 22, 18),
(139, 'Обратные отжимания от скамьи', 'Простое упражнение с собственным весом на трицепс.', NULL, 22, NULL),
(140, 'Скручивания', 'Базовое упражнение на прямую мышцу живота.', NULL, 23, NULL),
(141, 'Подъем ног в висе', 'Развивает нижнюю часть пресса и силу хвата.', NULL, 23, NULL),
(142, 'Планка', 'Статическое упражнение на мышцы кора и стабилизацию.', NULL, 23, NULL),
(143, 'Русский твист', 'Работает на косые мышцы живота и контроль корпуса.', NULL, 23, NULL),
(144, 'Велосипед', 'Динамическое упражнение на прямую и косые мышцы живота.', NULL, 23, NULL),
(145, 'Скручивания на блоке', 'Позволяют дозировать нагрузку на пресс с отягощением.', NULL, 23, NULL),
(146, 'Ягодичный мост', 'Одно из лучших упражнений для силы и объема ягодиц.', NULL, 24, 19),
(147, 'Болгарские выпады', 'Сильно нагружают ягодицы и квадрицепсы, улучшают баланс.', NULL, 24, 19),
(148, 'Отведение ноги в кроссовере', 'Изоляция ягодичных мышц в полной амплитуде.', NULL, 24, NULL),
(149, 'Тяга на скамье', 'Развивает заднюю цепь, ягодицы и разгибатели спины.', NULL, 24, 1),
(150, 'Шаги на платформу', 'Функциональное упражнение на ягодицы и ноги.', NULL, 24, 19),
(151, 'Махи ногой назад', 'Изолированная работа на ягодицы без осевой нагрузки.', NULL, 24, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `MuscleGroups`
--

CREATE TABLE `MuscleGroups` (
  `Group_id` int NOT NULL,
  `Group_name` varchar(100) NOT NULL,
  `Description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `MuscleGroups`
--

INSERT INTO `MuscleGroups` (`Group_id`, `Group_name`, `Description`) VALUES
(1, 'Спина', 'Тяговые упражнения для широчайших, трапеций и разгибателей спины.'),
(18, 'Грудь', 'Упражнения для грудных мышц и верхней части корпуса.'),
(19, 'Ноги', 'Базовые и изолирующие упражнения для квадрицепсов, бицепса бедра и икр.'),
(20, 'Плечи', 'Упражнения для передних, средних и задних дельт.'),
(21, 'Бицепс', 'Изолирующие движения на двуглавую мышцу плеча.'),
(22, 'Трицепс', 'Упражнения на разгибатели рук и силу жима.'),
(23, 'Пресс', 'Упражнения на мышцы кора и стабилизацию корпуса.'),
(24, 'Ягодицы', 'Упражнения для ягодичных мышц и задней цепи.');

-- --------------------------------------------------------

--
-- Структура таблицы `TrainingPlans`
--

CREATE TABLE `TrainingPlans` (
  `Plan_id` int NOT NULL,
  `User_id` int NOT NULL,
  `Plan_name` varchar(100) NOT NULL,
  `Created_at` datetime NOT NULL,
  `Updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `TrainingPlans`
--

INSERT INTO `TrainingPlans` (`Plan_id`, `User_id`, `Plan_name`, `Created_at`, `Updated_at`) VALUES
(1, 1, 'понедельник', '2026-02-28 07:55:59', '2026-02-28 07:55:59'),
(11, 3, 'План User1', '2026-04-24 16:10:02', '2026-04-24 16:10:02'),
(12, 3, 'Split v1', '2026-05-03 10:34:36', '2026-05-03 10:51:23');

-- --------------------------------------------------------

--
-- Структура таблицы `Users`
--

CREATE TABLE `Users` (
  `User_id` int NOT NULL,
  `Mail` varchar(255) NOT NULL,
  `Password_hash` varchar(255) NOT NULL,
  `User_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Users`
--

INSERT INTO `Users` (`User_id`, `Mail`, `Password_hash`, `User_name`) VALUES
(1, 'admin@mail.ru', '123', 'Admin'),
(3, 'user1@mail.ru', '$2y$10$arSKvKqmQiD0hy7e0emDv.qSZ4jjSDC7NuaVaPz5jzvJi.5cJ0nTO', 'User1');

-- --------------------------------------------------------

--
-- Структура таблицы `Workouts`
--

CREATE TABLE `Workouts` (
  `Workout_id` int NOT NULL,
  `Plan_id` int NOT NULL,
  `Day_id` int NOT NULL,
  `Created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Workouts`
--

INSERT INTO `Workouts` (`Workout_id`, `Plan_id`, `Day_id`, `Created_at`) VALUES
(1, 1, 1, '2026-02-28 08:26:40'),
(11, 12, 2, '2026-05-03 10:51:18'),
(12, 12, 4, '2026-05-03 10:51:23');

-- --------------------------------------------------------

--
-- Структура таблицы `WorkoutsExercises`
--

CREATE TABLE `WorkoutsExercises` (
  `Workout_exercise_id` int NOT NULL,
  `Workout_id` int NOT NULL,
  `Exercise_id` int NOT NULL,
  `Order_in_workout` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `WorkoutsExercises`
--

INSERT INTO `WorkoutsExercises` (`Workout_exercise_id`, `Workout_id`, `Exercise_id`, `Order_in_workout`) VALUES
(2, 1, 5, 1),
(10, 11, 147, 1),
(11, 12, 114, 1);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `Exercises`
--
ALTER TABLE `Exercises`
  ADD PRIMARY KEY (`Exercise_id`),
  ADD UNIQUE KEY `uq_exercises_name` (`Exercise_name`),
  ADD KEY `Primary_group_id` (`Primary_group_id`),
  ADD KEY `Secondary_group_id` (`Secondary_group_id`);

--
-- Индексы таблицы `MuscleGroups`
--
ALTER TABLE `MuscleGroups`
  ADD PRIMARY KEY (`Group_id`),
  ADD UNIQUE KEY `uq_musclegroups_group_name` (`Group_name`);

--
-- Индексы таблицы `TrainingPlans`
--
ALTER TABLE `TrainingPlans`
  ADD PRIMARY KEY (`Plan_id`),
  ADD KEY `User_id` (`User_id`);

--
-- Индексы таблицы `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`User_id`),
  ADD UNIQUE KEY `uq_users_mail` (`Mail`),
  ADD UNIQUE KEY `uq_users_user_name` (`User_name`);

--
-- Индексы таблицы `Workouts`
--
ALTER TABLE `Workouts`
  ADD PRIMARY KEY (`Workout_id`),
  ADD UNIQUE KEY `uq_workouts_plan_day` (`Plan_id`,`Day_id`),
  ADD KEY `Plan_id` (`Plan_id`),
  ADD KEY `Day_id` (`Day_id`);

--
-- Индексы таблицы `WorkoutsExercises`
--
ALTER TABLE `WorkoutsExercises`
  ADD PRIMARY KEY (`Workout_exercise_id`),
  ADD KEY `Exercise_id` (`Exercise_id`),
  ADD KEY `idx_workoutsexercises_workout_order` (`Workout_id`,`Order_in_workout`),
  ADD KEY `idx_workoutsexercises_workout_exercise` (`Workout_id`,`Exercise_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `Exercises`
--
ALTER TABLE `Exercises`
  MODIFY `Exercise_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=155;

--
-- AUTO_INCREMENT для таблицы `MuscleGroups`
--
ALTER TABLE `MuscleGroups`
  MODIFY `Group_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT для таблицы `TrainingPlans`
--
ALTER TABLE `TrainingPlans`
  MODIFY `Plan_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT для таблицы `Users`
--
ALTER TABLE `Users`
  MODIFY `User_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `Workouts`
--
ALTER TABLE `Workouts`
  MODIFY `Workout_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT для таблицы `WorkoutsExercises`
--
ALTER TABLE `WorkoutsExercises`
  MODIFY `Workout_exercise_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `Exercises`
--
ALTER TABLE `Exercises`
  ADD CONSTRAINT `exercises_ibfk_1` FOREIGN KEY (`Primary_group_id`) REFERENCES `MuscleGroups` (`Group_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Ограничения внешнего ключа таблицы `TrainingPlans`
--
ALTER TABLE `TrainingPlans`
  ADD CONSTRAINT `trainingplans_ibfk_1` FOREIGN KEY (`User_id`) REFERENCES `Users` (`User_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Ограничения внешнего ключа таблицы `Workouts`
--
ALTER TABLE `Workouts`
  ADD CONSTRAINT `workouts_ibfk_1` FOREIGN KEY (`Plan_id`) REFERENCES `TrainingPlans` (`Plan_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Ограничения внешнего ключа таблицы `WorkoutsExercises`
--
ALTER TABLE `WorkoutsExercises`
  ADD CONSTRAINT `workoutsexercises_ibfk_1` FOREIGN KEY (`Workout_id`) REFERENCES `Workouts` (`Workout_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `workoutsexercises_ibfk_2` FOREIGN KEY (`Exercise_id`) REFERENCES `Exercises` (`Exercise_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
