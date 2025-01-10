<?php
include '../config/database.php';
include '../includes/header.php';


// Agregar una nueva pestaña
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_tab'])) {
    $new_tab = htmlspecialchars($_POST['new_tab']);
    if (!empty($new_tab)) {
        // Insertar la nueva pestaña en la base de datos
        $stmt = $pdo->prepare("INSERT INTO tabs (name) VALUES (:name)");
        $stmt->execute(['name' => $new_tab]);
    }
}

// Agregar una nueva tarea
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_task']) && isset($_POST['current_tab'])) {
    $current_tab = $_POST['current_tab'];
    $new_task = htmlspecialchars($_POST['new_task']);
    if (!empty($new_task)) {
        // Obtener el ID de la pestaña
        $stmt = $pdo->prepare("SELECT id FROM tabs WHERE name = :name");
        $stmt->execute(['name' => $current_tab]);
        $tab_id = $stmt->fetchColumn();

        // Insertar la tarea en la base de datos
        $stmt = $pdo->prepare("INSERT INTO tasks (task_name, tab_id) VALUES (:task_name, :tab_id)");
        $stmt->execute(['task_name' => $new_task, 'tab_id' => $tab_id]);
    }
}

// Editar una tarea
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_task']) && isset($_POST['current_tab']) && isset($_POST['edited_task'])) {
    $current_tab = $_POST['current_tab'];
    $old_task = $_POST['edit_task'];
    $new_task = htmlspecialchars($_POST['edited_task']);
    
    // Actualizar la tarea en la base de datos
    $stmt = $pdo->prepare("UPDATE tasks SET task_name = :new_task WHERE task_name = :old_task AND tab_id = (SELECT id FROM tabs WHERE name = :tab_name)");
    $stmt->execute(['new_task' => $new_task, 'old_task' => $old_task, 'tab_name' => $current_tab]);
}

// Eliminar una tarea
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_task']) && isset($_POST['current_tab'])) {
    $current_tab = $_POST['current_tab'];
    $task_to_delete = $_POST['delete_task'];

    // Eliminar la tarea de la base de datos
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE task_name = :task_name AND tab_id = (SELECT id FROM tabs WHERE name = :tab_name)");
    $stmt->execute(['task_name' => $task_to_delete, 'tab_name' => $current_tab]);
}

// Mover una tarea completada
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complete_task']) && isset($_POST['current_tab'])) {
    $current_tab = $_POST['current_tab'];
    $completed_task = $_POST['complete_task'];

    // Marcar la tarea como completada
    $stmt = $pdo->prepare("UPDATE tasks SET completed = 1 WHERE task_name = :task_name AND tab_id = (SELECT id FROM tabs WHERE name = :tab_name)");
    $stmt->execute(['task_name' => $completed_task, 'tab_name' => $current_tab]);
}

// Destachar una tarea completada
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['uncomplete_task']) && isset($_POST['current_tab'])) {
    $current_tab = $_POST['current_tab'];
    $uncompleted_task = $_POST['uncomplete_task'];

    // Marcar la tarea como no completada
    $stmt = $pdo->prepare("UPDATE tasks SET completed = 0 WHERE task_name = :task_name AND tab_id = (SELECT id FROM tabs WHERE name = :tab_name)");
    $stmt->execute(['task_name' => $uncompleted_task, 'tab_name' => $current_tab]);
}

// Eliminar todas las tareas completadas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_completed']) && isset($_POST['current_tab'])) {
    $current_tab = $_POST['current_tab'];

    // Eliminar las tareas completadas
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE completed = 1 AND tab_id = (SELECT id FROM tabs WHERE name = :tab_name)");
    $stmt->execute(['tab_name' => $current_tab]);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/main.js" defer></script>
</head>
<body>
    <div class="container">
        <h1>Mis Tareas VML</h1>

        <!-- Sección de pestañas -->
        <div class="tabs">
            <?php
            $stmt = $pdo->query("SELECT * FROM tabs");
            while ($tab = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                <button class="tab-button" onclick="switchTab('<?= $tab['name'] ?>')"><?= $tab['name'] ?></button>
            <?php endwhile; ?>
            <button class="add-tab" onclick="showAddTabForm()">+</button>
        </div>

        <!-- Formulario para agregar pestañas -->
        <form id="add-tab-form" method="POST" style="display: none;">
            <input type="text" name="new_tab" placeholder="Nombre de la pestaña" required>
            <button type="submit">Agregar</button>
        </form>

        <!-- Tareas dinámicas -->
        <div id="task-container">
            <?php
            $stmt = $pdo->query("SELECT * FROM tabs");
            while ($tab = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                <div class="task-list" id="tasks-<?= $tab['name'] ?>" style="display: none;">
                    <h2><?= $tab['name'] ?></h2>

                    <!-- Formulario para agregar tareas -->
                    <form method="POST">
                        <input type="hidden" name="current_tab" value="<?= $tab['name'] ?>">
                        <input type="text" name="new_task" placeholder="Nueva tarea" required>
                        <button type="submit">Agregar tarea</button>
                    </form>

                    <!-- Barra de progreso -->
                    <?php
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM tasks WHERE tab_id = :tab_id");
                    $stmt->execute(['tab_id' => $tab['id']]);
                    $total_tasks = $stmt->fetchColumn();

                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM tasks WHERE tab_id = :tab_id AND completed = 1");
                    $stmt->execute(['tab_id' => $tab['id']]);
                    $completed_tasks = $stmt->fetchColumn();

                    $progress = $total_tasks > 0 ? ($completed_tasks / $total_tasks) * 100 : 0;
                    ?>
                    <div class="progress-bar-container">
                        <div class="progress-bar" style="width: <?= $progress ?>%;"></div>
                    </div>
                    <p><?= round($progress, 1) ?>% de tareas completadas</p>

                    <!-- Tareas pendientes -->
                    <ul>
                        <?php
                        $stmt = $pdo->prepare("SELECT * FROM tasks WHERE tab_id = :tab_id AND completed = 0");
                        $stmt->execute(['tab_id' => $tab['id']]);
                        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($tasks as $task):
                        ?>
                            <li>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="current_tab" value="<?= $tab['name'] ?>">
                                    <input type="hidden" name="complete_task" value="<?= $task['task_name'] ?>">
                                    <input type="checkbox" onchange="this.form.submit()">
                                    <span><?= $task['task_name'] ?></span>
                                </form>
                                <div class="dropdown">
                                    <button>⋮</button>
                                    <div class="dropdown-content">
                                        <!-- Botón para editar -->
                                        <form method="POST" style="margin: 0;">
                                            <input type="hidden" name="current_tab" value="<?= $tab['name'] ?>">
                                            <input type="hidden" name="edit_task" value="<?= $task['task_name'] ?>">
                                            <input type="text" name="edited_task" placeholder="Editar tarea" required>
                                            <button type="submit">Guardar</button>
                                        </form>
                                        <!-- Botón para eliminar -->
                                        <form method="POST" style="margin: 0;">
                                            <input type="hidden" name="current_tab" value="<?= $tab['name'] ?>">
                                            <input type="hidden" name="delete_task" value="<?= $task['task_name'] ?>">
                                            <button type="submit">Eliminar</button>
                                        </form>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <!-- Tareas completadas -->
                    <h3>Tareas Completadas</h3>
                    <ul>
                        <?php
                        $stmt = $pdo->prepare("SELECT * FROM tasks WHERE tab_id = :tab_id AND completed = 1");
                        $stmt->execute(['tab_id' => $tab['id']]);
                        $completed_tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($completed_tasks as $completed_task):
                        ?>
                            <li>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="current_tab" value="<?= $tab['name'] ?>">
                                    <input type="hidden" name="uncomplete_task" value="<?= $completed_task['task_name'] ?>">
                                    <input type="checkbox" checked onchange="this.form.submit()">
                                    <s><?= $completed_task['task_name'] ?></s>
                                </form>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <!-- Botón para eliminar todas las tareas completadas -->
                    <form method="POST">
                        <input type="hidden" name="current_tab" value="<?= $tab['name'] ?>">
                        <button type="submit" name="clear_completed">Eliminar tareas completadas</button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script>
        // Muestra el formulario para agregar pestañas
        function showAddTabForm() {
            document.getElementById('add-tab-form').style.display = 'block';
        }

        // Cambia entre pestañas
        function switchTab(tabName) {
            const taskLists = document.querySelectorAll('.task-list');
            taskLists.forEach(taskList => taskList.style.display = 'none');

            // Guardar la pestaña seleccionada en localStorage
            localStorage.setItem('selectedTab', tabName);

            document.getElementById('tasks-' + tabName).style.display = 'block';
        }

        // Al cargar la página, selecciona la última pestaña visitada
        document.addEventListener('DOMContentLoaded', function() {
            const selectedTab = localStorage.getItem('selectedTab');
            if (selectedTab) {
                switchTab(selectedTab);
            } else {
                const firstTab = document.querySelector('.tab-button');
                if (firstTab) {
                    switchTab(firstTab.innerText);
                }
            }
        });
    </script>
</body>
</html>
