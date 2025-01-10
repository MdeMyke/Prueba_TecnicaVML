<?php
include '../config/db.php';
include '../config/database_functions.php';
?>
<header>
<?php include '../includes/header.php'; ?>

</header>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>To Do List</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
  <div class="container mt-5">
    <!-- Card para envolver todo el contenido -->
    <div class="card card-custom">
      <div class="card-body">
        <h1 class="text-center">To-Do List</h1>
        <!-- Subtítulo debajo del título -->
        <h3 class="text-center mb-4">VML HOLDING</h3>

        <!-- Fecha actual en la esquina superior derecha -->
        <div class="current-date" id="currentDate"></div>

        <!-- Mostrar las pestañas -->
        <div class="tabs">
          <?php while ($tab = $result_tabs->fetch_assoc()): ?>
            <button class="tab-btn <?php echo ($tab['id'] == $active_tab_id) ? 'active' : ''; ?>" 
                    onclick="window.location.href='index.php?tab=<?php echo $tab['id']; ?>'">
              <?php echo $tab['name']; ?>
            </button>
          <?php endwhile; ?>

          <!-- Icono para abrir el modal de nueva pestaña -->
          <button class="tab-btn1" id="new-tab-btn" data-bs-toggle="modal" data-bs-target="#newTabModal">
            <i class="fas fa-folder"></i> <!-- Icono de carpeta -->
          </button>

          <!-- Botón para abrir el modal de eliminar pestaña -->
          <button class="tab-btn1" id="edit-tab-btn" data-bs-toggle="modal" data-bs-target="#deleteTabModal">
            <i class="fas fa-pencil-alt"></i>
          </button>
        </div>

        <!-- Barra de progreso de tareas completadas -->
        <div class="progress mb-3">
          <div class="progress-bar" role="progressbar" style="width: <?php echo $progress_percentage; ?>%;" aria-valuenow="<?php echo $progress_percentage; ?>" aria-valuemin="0" aria-valuemax="100">
            <?php echo round($progress_percentage); ?>% Tareas Completadas
          </div>
        </div>

        <!-- Formulario para añadir nueva tarea -->
        <form action="index.php" method="POST" class="card card-custom-add">
          <h5 class="card-title">Añadir nueva tarea</h5>
          <div class="input-group">
            <input type="text" name="task" class="task-input" placeholder="Escribe una nueva tarea..." required>
            <input type="date" name="due_date" class="task-input" required>  <!-- Nuevo campo de fecha límite -->
            <input type="hidden" name="tab_id" value="<?php echo $active_tab_id; ?>">
            <button type="submit" class="add-btn">+</button>
          </div>
        </form>

        <!-- Filtros de tareas -->
        <div class="d-flex justify-content-between align-items-center mb-3">
          <!-- Texto Filtrar Por -->
          <label for="taskFilter" class="mr-3">Filtrar Por:</label>

          <!-- Casillas de selección para Filtrar -->
          <div class="btn-group" role="group" aria-label="Filtros de tareas">
            <button type="button" class="btn btn-outline-secondary" onclick="window.location.href='index.php?tab=<?php echo $active_tab_id; ?>&filter=completed'">
              <input type="radio" name="filter" value="completed" class="mr-2">Completadas
            </button>
            <button type="button" class="btn btn-outline-secondary" onclick="window.location.href='index.php?tab=<?php echo $active_tab_id; ?>&filter=pending'">
              <input type="radio" name="filter" value="pending" class="mr-2">Pendientes
            </button>
          </div>
        </div>

        <!-- Lista de tareas -->
        <div id="task-list-container">
          <h5> Tus tareas en "<?php echo htmlspecialchars($active_tab_name); ?>"</h5>
          <ul class="task-list">
            <?php while ($task = $result_tasks->fetch_assoc()): ?>
              <li class="task-item <?php echo ($task['completed'] == 1) ? 'completed' : ''; ?>">
                <form action="index.php" method="POST" style="display:inline;">
                  <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                  <input type="hidden" name="completed" value="<?php echo ($task['completed'] == 1) ? 0 : 1; ?>">
                  <input type="hidden" name="tab_id" value="<?php echo $active_tab_id; ?>">
                  <input type="checkbox" class="checkbox" onchange="this.form.submit()" <?php echo ($task['completed'] == 1) ? 'checked' : ''; ?>>
                </form>
                <?php echo $task['task']; ?>
                <small class="text-muted"><?php echo date("d/m/Y H:i", strtotime($task['created_at'])); ?></small>

                <!-- Mostrar la fecha límite si está disponible -->
                <?php if ($task['due_date']): ?>
                  <small class="text-muted"> | Fecha límite: <?php echo date("d/m/Y", strtotime($task['due_date'])); ?></small>
                <?php endif; ?>

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
                    <input type="hidden" name="tab_id" value="<?php echo $active_tab_id; ?>">
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
                <h5 class="modal-title" id="newTabModalLabel">Crear nueva pestaña de tareas</h5>
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

        <!-- Modal para eliminar pestaña -->
        <div class="modal fade" id="deleteTabModal" tabindex="-1" aria-labelledby="deleteTabModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="deleteTabModalLabel">Eliminar Pestaña de tareas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <form action="index.php" method="POST">
                  <label for="tab-to-delete">Selecciona una pestaña para eliminar:</label>
                  <select name="tab_id_to_delete" class="form-control" required>
                    <?php while ($tab = $result_tabs_modal->fetch_assoc()): ?>
                      <option value="<?php echo $tab['id']; ?>"><?php echo $tab['name']; ?></option>
                    <?php endwhile; ?>
                  </select>
                  <button type="submit" class="btn btn-danger mt-3">Eliminar Pestaña</button>
                </form>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal de confirmación para eliminar tareas completadas -->
        <div class="modal fade" id="deleteCompletedTasksModal" tabindex="-1" aria-labelledby="deleteCompletedTasksModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="deleteCompletedTasksModalLabel">Eliminar tareas completadas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar todas las tareas completadas en esta pestaña?</p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="index.php" method="POST">
                  <input type="hidden" name="delete_completed_tasks_tab_id" value="<?php echo $active_tab_id; ?>">
                  <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
              </div>
            </div>
          </div>
        </div>

        <!-- Icono de la caneca dentro de la card -->
        <span class="delete-icon" data-bs-toggle="modal" data-bs-target="#deleteCompletedTasksModal">
          <i class="fas fa-trash-alt"></i>
        </span>

      </div>
    </div>
  </div>

  <footer>
  <?php include '../includes/footer.php'; ?>

  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

  <script src="../assets/js/script.js"></script>
</body>

</html>


<!--
Este código es para gestionar tareas utilizando pestañas:
1. Pestañas que representan diferentes listas de tareas.
2. Formularios para agregar tareas con fecha de vencimiento.
3. Un sistema de filtros para ver solo tareas completadas o pendientes.
4. Funciones para editar y eliminar tareas, así como para agregar y eliminar pestañas.
5. Modal para confirmar la eliminación de pestañas y tareas completadas.

La lógica de base de datos, como consultas para obtener las pestañas y tareas, se maneja en el archivo de configuración de base de datos y funciones (`db.php`, `database_functions.php`).
-->
