<?php
include '../config/db.php';
include '../config/database_functions.php'
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
      max-width: 500px;
      margin: 20px auto;
      padding: 20px;
      box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    }
    .card-custom-add {
      margin: 20px auto;
      padding: 20px;
      box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    }
    .task-input {
      border-radius: 20px;
      width: 80%;  /* Ocupa todo el ancho disponible */
      height: 50px;
      padding: 15px;
      border: 2px solid #ddd;
      font-size: 16px;
    }
    .add-btn {
      width: 50px;
      height: 50px;
      background-color: #007bff;
      border-radius: 50%;
      color: white;
      font-size: 30px;
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
      flex-wrap: wrap;
    }
    .tab-btn {
      border-radius: 20px;
      border: 1px solid #007bff;
      background-color: #fff;
      cursor: pointer;
      flex: 1;
      margin-right: auto;
    }
    .tab-btn1 {
      border-radius: 20px;
      border: 1px solid #007bff;
      background-color: #fff;
      cursor: pointer;
      margin-left: auto;
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
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
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
    .current-date {
      position: absolute;
      top: 10px;
      right: 20px;
      font-size: 16px;
      color: #555;
    }
    @media (max-width: 768px) {
      .card-custom {
        width: 100%;
        padding: 10px;
      }
      .task-input {
        width: 80%;
        margin-bottom: 10px;
      }
      .add-btn {
        width: 50px;
        height: 50px;
        font-size: 24px;
      }
    }
  </style>
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

        <!-- Formulario para agregar tarea -->
        <form action="index.php" method="POST" class="card card-custom-add">
          <h5 class="card-title">Añadir nueva tarea</h5>
          <div class="input-group">
            <input type="text" name="task" class="task-input" placeholder="Escribe una nueva tarea..." required>
            <input type="hidden" name="tab_id" value="<?php echo $active_tab_id; ?>">
            <button type="submit" class="add-btn">+</button>
          </div>
        </form>

        <!-- Lista de tareas -->
        <div id="task-list-container">
          <h5>Tareas en la pestaña "<?php echo htmlspecialchars($active_tab_name); ?>"</h5>
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

        <!-- Modal para eliminar pestaña -->
        <div class="modal fade" id="deleteTabModal" tabindex="-1" aria-labelledby="deleteTabModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="deleteTabModalLabel">Eliminar Pestaña</h5>
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

    // Mostrar la fecha actual
    const dateElement = document.getElementById('currentDate');
    const currentDate = new Date();
    const formattedDate = currentDate.toLocaleDateString('es-ES', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric',
    });
    dateElement.textContent = formattedDate;
  </script>
</body>
</html>
