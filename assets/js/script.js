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
  
/*
  Explicación de la Funcionalidad:
Función toggleTaskOptions(taskId):

Esta función se utiliza para mostrar u ocultar el menú de opciones de una tarea específica.
Se genera dinámicamente el ID del menú de opciones de la tarea usando el parámetro taskId. Por ejemplo, si taskId es 1, el ID del menú de opciones será task-options-1.
Se utiliza classList.toggle('show') para agregar o eliminar la clase 'show', lo que hará que el menú de opciones sea visible u oculto, dependiendo de si la clase ya está presente en el elemento.
Mostrar la fecha actual:

Se selecciona un elemento con el ID currentDate donde se mostrará la fecha actual.
Se crea un objeto Date para obtener la fecha y hora actual.
Luego, se formatea la fecha en el formato adecuado para la cultura española ('es-ES') utilizando el método toLocaleDateString(). Este método permite especificar cómo se debe mostrar la fecha: en este caso, se incluyen el día de la semana, el día, el mes y el año.
Finalmente, el contenido de texto del elemento (dateElement) se actualiza con la fecha formateada.
*/