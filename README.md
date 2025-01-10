# To-Do List

Este es un proyecto de gestión de tareas que permite a los usuarios crear listas de tareas con fechas de vencimiento, marcar tareas como completadas, y gestionar las pestañas (listas de tareas). El sistema proporciona filtros para ver tareas pendientes o completadas y ofrece una interfaz de usuario amigable basada en Bootstrap.

## Características

- Crear y gestionar tareas en diferentes pestañas.
- Añadir tareas con fecha de vencimiento.
- Marcar tareas como completadas o pendientes.
- Filtros para ver tareas completadas o pendientes.
- Editar y eliminar tareas.
- Crear y eliminar pestañas.
- Eliminar todas las tareas completadas de una pestaña.

## Requisitos

- PHP 7.0 o superior.
- Un servidor web (por ejemplo, xampp).
- Base de datos MySQL.
## base de datos
la base de datos la pueden crear de la siguiente manera

Solo deberann copiar y pegar la siguiente informacion el el sql de su preferencia 

CREATE TABLE `tabs` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tabs`
--

INSERT INTO `tabs` (`id`, `name`) VALUES
(37, 'Principal'),
(39, 'Deudas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `task` varchar(255) NOT NULL,
  `tab_id` int(11) NOT NULL,
  `completed` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `due_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tasks`
--

INSERT INTO `tasks` (`id`, `task`, `tab_id`, `completed`, `created_at`, `due_date`) VALUES
(57, 'mañana tarea ', 37, 0, '2025-01-10 10:26:31', '0567-04-23'),
(60, 'hola', 39, 1, '2025-01-10 10:28:01', '4567-03-12');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `tabs`
--
ALTER TABLE `tabs`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tab_id` (`tab_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `tabs`
--
ALTER TABLE `tabs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT de la tabla `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`tab_id`) REFERENCES `tabs` (`id`);
COMMIT;

## Instalación

descomprimir el archivo zimp y ponerlo en la carpeta htdocs de xampp
