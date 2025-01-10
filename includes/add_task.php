<?php
// add_task.php
include '../config/db.php';

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
?>
<!-- task_form.php -->
<form action="add_task.php" method="POST" class="card card-custom">
  <h5 class="card-title">Añadir nueva tarea</h5>
  <div class="input-group">
    <input type="text" name="task" class="task-input" placeholder="Escribe una nueva tarea..." required>
    <input type="hidden" name="tab_id" value="<?php echo $active_tab_id; ?>">
    <button type="submit" class="add-btn">+</button>
  </div>
</form>
