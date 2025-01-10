<?php
include '../config/db.php';

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

// Obtener el ID de la pestaña activa desde la URL (si está presente), o usar la pestaña "Todo" por defecto
$active_tab_id = isset($_GET['tab']) ? $_GET['tab'] : 1;  // Por defecto, "Todo" tiene ID = 1

// Obtener el nombre de la pestaña activa
$sql_active_tab_name = "SELECT name FROM tabs WHERE id = ?";
$stmt_active_tab_name = $conn->prepare($sql_active_tab_name);
$stmt_active_tab_name->bind_param("i", $active_tab_id);
$stmt_active_tab_name->execute();
$result_active_tab_name = $stmt_active_tab_name->get_result();
$active_tab_name = $result_active_tab_name->fetch_assoc()['name'];
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
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>To Do List</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
    .card-custom {
      width: 18rem;
      margin: 20px;
      padding: 20px;
      box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    }
    .task-input {
      border-radius: 20px;
      width: 70%;
      padding: 10px;
      border: 2px solid #ddd;
    }
    .add-btn {
      width: 40px;
      height: 40px;
      background-color: #007bff;
      border-radius: 50%;
      color: white;
      font-size: 24px;
      border: none;
    }
    .task-list {
      list-style-type: none;
      padding-left: 0;
    }
    .task-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 10px;
      margin-bottom: 10px;
      background-color: #f8f9fa;
      border-radius: 5px;
      transition: transform 0.3s ease;
    }
    .task-item.completed {
      text-decoration: line-through;
      background-color: #d1ffd6;
    }
    .task-item .checkbox {
      margin-right: 10px;
    }
    .tabs {
      display: flex;
      gap: 10px;
      margin-bottom: 20px;
    }
    .tab-btn {
      padding: 10px 20px;
      border-radius: 20px;
      border: 1px solid #007bff;
      background-color: #fff;
      cursor: pointer;
    }
    .tab-btn.active {
      background-color: #007bff;
      color: white;
    }
    .menu-icon {
      cursor: pointer;
      margin-left: 10px;
      font-size: 18px;
    }
    .task-options {
  display: none;
  position: fixed; /* Usamos fixed para posicionarlo en relación con la ventana del navegador */
  top: 50%; /* Coloca el menú a la mitad de la pantalla */
  left: 50%; /* Coloca el menú a la mitad de la pantalla */
  transform: translate(-50%, -50%); /* Ajusta para centrarlo completamente */
  background-color: #fff;
  border: 1px solid #ddd;
  padding: 10px;
  border-radius: 5px;
  z-index: 1;
  box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
}

.task-options.show {
  display: block;
}

  </style>
</head>
<body>
  <div class="container mt-5">
    <h1 class="text-center">To-Do List</h1>

    <!-- Mostrar las pestañas -->
    <div class="tabs">
      <?php while ($tab = $result_tabs->fetch_assoc()): ?>
        <button class="tab-btn <?php echo ($tab['id'] == $active_tab_id) ? 'active' : ''; ?>" 
                onclick="window.location.href='index.php?tab=<?php echo $tab['id']; ?>'">
          <?php echo $tab['name']; ?>
        </button>
      <?php endwhile; ?>

      <!-- Icono para abrir el modal de nueva pestaña -->
      <button class="tab-btn" id="new-tab-btn" data-bs-toggle="modal" data-bs-target="#newTabModal">
        <i class="fas fa-folder"></i> <!-- Icono de carpeta -->
      </button>
    </div>

    <!-- Barra de progreso de tareas completadas -->
    <div class="progress mb-3">
      <div class="progress-bar" role="progressbar" style="width: <?php echo $progress_percentage; ?>%;" aria-valuenow="<?php echo $progress_percentage; ?>" aria-valuemin="0" aria-valuemax="100">
        <?php echo round($progress_percentage); ?>% Tareas Completadas
      </div>
    </div>

    <!-- Formulario para agregar tarea -->
    <?php include '../includes/add_task.php'; ?>

    <!-- Lista de tareas -->
    <div id="task-list-container">
      <h5>Tareas en la pestaña "<?php echo htmlspecialchars($active_tab_name); ?>"</h5>
      <ul class="task-list">
        <?php while ($task = $result_tasks->fetch_assoc()): ?>
          <li class="task-item <?php echo ($task['completed'] == 1) ? 'completed' : ''; ?>">
            <form action="index.php" method="POST" style="display:inline;">
              <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
              <input type="hidden" name="completed" value="<?php echo ($task['completed'] == 1) ? 0 : 1; ?>">
              <input type="hidden" name="tab_id" value="<?php echo $active_tab_id; ?>"> <!-- Mantener el tab_id -->
              <input type="checkbox" class="checkbox" onchange="this.form.submit()" <?php echo ($task['completed'] == 1) ? 'checked' : ''; ?>>
            </form>
            <?php echo $task['task']; ?>
            <small class="text-muted"><?php echo date("d/m/Y H:i", strtotime($task['created_at'])); ?></small>

            <!-- Icono de tres puntos para menú de opciones -->
            <span class="menu-icon" onclick="toggleTaskOptions(<?php echo $task['id']; ?>)">...</span>

            <!-- Opciones de tarea -->
            <div class="task-options" id="task-options-<?php echo $task['id']; ?>">
              <form action="index.php" method="POST">
                <input type="hidden" name="tab_id" value="<?php echo $active_tab_id; ?>">
                <input type="hidden" name="edit_task_id" value="<?php echo $task['id']; ?>">
                <input type="text" name="edited_task" value="<?php echo $task['task']; ?>" required>
                <button type="submit" class="btn btn-sm btn-warning mt-2">Editar</button>
              </form>
              <form action="index.php" method="POST">
                <input type="hidden" name="delete_task_id" value="<?php echo $task['id']; ?>">
                <input type="hidden" name="tab_id" value="<?php echo $active_tab_id; ?>"> <!-- Mantener el tab_id -->
                <button type="submit" class="btn btn-sm btn-danger mt-2">Eliminar</button>
              </form>
            </div>
          </li>
        <?php endwhile; ?>
      </ul>
    </div>

    <!-- Modal para crear nueva pestaña -->
    <div class="modal fade" id="newTabModal" tabindex="-1" aria-labelledby="newTabModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="newTabModalLabel">Crear nueva pestaña</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form action="index.php" method="POST">
              <input type="text" name="tab_name" class="task-input" placeholder="Nombre de la nueva pestaña" required>
              <button type="submit" class="btn btn-primary mt-2">Crear Pestaña</button>
            </form>
          </div>
        </div>
      </div>
    </div>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Función para mostrar u ocultar el menú de opciones de la tarea
    function toggleTaskOptions(taskId) {
      var taskOptions = document.getElementById('task-options-' + taskId);
      taskOptions.classList.toggle('show');
    }
  </script>
</body>
</html>