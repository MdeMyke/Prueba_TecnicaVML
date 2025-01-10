
<?php
// Lógica para agregar una nueva tarea
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['task'], $_POST['tab_id'])) {
    $task = $_POST['task'];
    $tab_id = $_POST['tab_id'];

    // Consulta para insertar la tarea en la base de datos
    $sql = "INSERT INTO tasks (task, tab_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $task, $tab_id);
    $stmt->execute();
    $stmt->close();
    // Redirigir con el ID de la pestaña seleccionada
    header("Location: index.php?tab=$tab_id");
    exit();
}

// Lógica para crear una nueva pestaña
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tab_name'])) {
    $tab_name = $_POST['tab_name'];

    // Consulta para insertar la nueva pestaña en la base de datos
    $sql = "INSERT INTO tabs (name) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $tab_name);
    $stmt->execute();
    $stmt->close();
    $new_tab_id = $conn->insert_id;  // ID generado por la inserción

    // Redirigir a la nueva pestaña
    header("Location: index.php?tab=" . $new_tab_id);
    exit();
}

// Lógica para actualizar el estado de la tarea (marcar como completada)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['task_id'], $_POST['completed'], $_POST['tab_id'])) {
    $task_id = $_POST['task_id'];
    $completed = $_POST['completed'];
    $tab_id = $_POST['tab_id'];  // Mantener la pestaña activa

    // Actualizar el estado de la tarea en la base de datos
    $sql = "UPDATE tasks SET completed = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $completed, $task_id);
    $stmt->execute();
    $stmt->close();

    // Redirigir a la misma pestaña activa
    header("Location: index.php?tab=$tab_id");
    exit();
}

// Lógica para eliminar una tarea
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_task_id'])) {
    $delete_task_id = $_POST['delete_task_id'];

    // Consulta para eliminar la tarea de la base de datos
    $sql = "DELETE FROM tasks WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_task_id);
    $stmt->execute();
    $stmt->close();

    // Redirigir a la misma pestaña activa
    header("Location: index.php?tab=" . $_POST['tab_id']);
    exit();
}

// Lógica para editar una tarea
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_task_id'], $_POST['edited_task'], $_POST['tab_id'])) {
    $edit_task_id = $_POST['edit_task_id'];
    $edited_task = $_POST['edited_task'];
    $tab_id = $_POST['tab_id'];  // Obtener el tab_id correctamente

    // Consulta para actualizar la tarea en la base de datos
    $sql = "UPDATE tasks SET task = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $edited_task, $edit_task_id);
    $stmt->execute();
    $stmt->close();

    // Redirigir a la misma pestaña activa
    header("Location: index.php?tab=" . $tab_id);
    exit();
}

// Obtener todas las pestañas
$sql_tabs = "SELECT * FROM tabs";
$result_tabs = $conn->query($sql_tabs);

// Verificar si la consulta fue exitosa
if ($result_tabs === false) {
    die("Error en la consulta de pestañas: " . $conn->error);
}

// Obtener el ID de la pestaña activa desde la URL (si está presente), o usar la pestaña "Todo" por defecto
$active_tab_id = isset($_GET['tab']) ? $_GET['tab'] : 1;  // Por defecto, "Todo" tiene ID = 1

// Obtener el nombre de la pestaña activa
$sql_active_tab_name = "SELECT name FROM tabs WHERE id = ?";
$stmt_active_tab_name = $conn->prepare($sql_active_tab_name);
$stmt_active_tab_name->bind_param("i", $active_tab_id);
$stmt_active_tab_name->execute();
$result_active_tab_name = $stmt_active_tab_name->get_result();

// Verificar si la pestaña activa fue encontrada
if ($result_active_tab_name && $result_active_tab_name->num_rows > 0) {
    $active_tab_name = $result_active_tab_name->fetch_assoc()['name'];
} else {
    $active_tab_name = "Pestaña no encontrada";
}
$stmt_active_tab_name->close();

// Obtener las tareas de la pestaña activa
$sql_tasks = "SELECT * FROM tasks WHERE tab_id = ?";
$stmt_tasks = $conn->prepare($sql_tasks);
$stmt_tasks->bind_param("i", $active_tab_id);
$stmt_tasks->execute();
$result_tasks = $stmt_tasks->get_result();

// Obtener el total de tareas y las tareas completadas para la pestaña activa
$sql_task_counts = "SELECT COUNT(*) AS total_tasks, SUM(completed) AS completed_tasks FROM tasks WHERE tab_id = ?";
$stmt_task_counts = $conn->prepare($sql_task_counts);
$stmt_task_counts->bind_param("i", $active_tab_id);
$stmt_task_counts->execute();
$result_task_counts = $stmt_task_counts->get_result();
$task_counts = $result_task_counts->fetch_assoc();
$stmt_task_counts->close();

// Calcular el porcentaje de tareas completadas
$total_tasks = $task_counts['total_tasks'];
$completed_tasks = $task_counts['completed_tasks'];
$progress_percentage = ($total_tasks > 0) ? ($completed_tasks / $total_tasks) * 100 : 0;

// Obtener todas las pestañas para el modal (para evitar que el cursor de la consulta se desplace)
$sql_tabs_modal = "SELECT * FROM tabs";
$result_tabs_modal = $conn->query($sql_tabs_modal);

// Verificar si la consulta fue exitosa
if ($result_tabs_modal === false) {
    die("Error en la consulta de pestañas del modal: " . $conn->error);
}

// Lógica para eliminar una pestaña
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tab_id_to_delete'])) {
    $tab_id_to_delete = $_POST['tab_id_to_delete'];

    // Consulta para eliminar todas las tareas asociadas a la pestaña
    $sql_delete_tasks = "DELETE FROM tasks WHERE tab_id = ?";
    $stmt_delete_tasks = $conn->prepare($sql_delete_tasks);
    $stmt_delete_tasks->bind_param("i", $tab_id_to_delete);
    $stmt_delete_tasks->execute();
    $stmt_delete_tasks->close();

    // Consulta para eliminar la pestaña
    $sql_delete_tab = "DELETE FROM tabs WHERE id = ?";
    $stmt_delete_tab = $conn->prepare($sql_delete_tab);
    $stmt_delete_tab->bind_param("i", $tab_id_to_delete);
    $stmt_delete_tab->execute();
    $stmt_delete_tab->close();

    // Redirigir a la primera pestaña por defecto (o a la página de inicio)
    header("Location: index.php?tab=1");
    exit();
}


// Lógica para eliminar las tareas completadas
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_completed_tasks_tab_id'])) {
    $tab_id = $_POST['delete_completed_tasks_tab_id'];

    // Consulta para eliminar todas las tareas completadas de la pestaña activa
    $sql_delete_completed_tasks = "DELETE FROM tasks WHERE tab_id = ? AND completed = 1";
    $stmt_delete_completed_tasks = $conn->prepare($sql_delete_completed_tasks);
    $stmt_delete_completed_tasks->bind_param("i", $tab_id);
    $stmt_delete_completed_tasks->execute();
    $stmt_delete_completed_tasks->close();

    // Redirigir a la misma pestaña activa para ver los cambios
    header("Location: index.php?tab=$tab_id");
    exit();
}


?>