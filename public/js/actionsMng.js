// Manejar el cambio de actividad
document.addEventListener('DOMContentLoaded', function() {
    const selectActividad = document.getElementById('select-actividad'); // Asegúrate de que este ID coincida con el HTML.

    if (selectActividad) { // Verifica que selectActividad no sea null
        selectActividad.addEventListener('change', function() {
            const actividadId = selectActividad.value;
    
            // Verifica que una actividad válida haya sido seleccionada y que actividadId sea un entero válido
            if (actividadId !== 'none' && Number.isInteger(parseInt(actividadId))) {

                const url = '../controller/actionsManager.php?ajaxUpdate=true&activityId=' + actividadId;
                makeAjaxRequest(url, 'POST', null, function(response) {
                    // Verificar respuesta
                    if (response && response.success) {
                        const data = response.data;

                        const estados = {
                            1: 'Pendiente',
                            2: 'En proceso',
                            3: 'Retrasado',
                            4: 'Finalizado'
                        };
            
                        const updateFieldValue = (fieldId, value) => {
                            const field = document.getElementById(fieldId);
                            if (field) {
                                field.textContent = value || '';
                            }
                        };
                        updateFieldValue('estadoActividad', `${estados[data.estadoActual]}`);
                        updateFieldValue('numeroReportes', `${data.numeroReportes}`);
                        
                        updateReportsTable(actividadId, data.numeroReportes);

                    } else {
                        document.getElementById('estadoActividad').innerHTML = '<i>No disponible</i>';
                        document.getElementById('numeroReportes').innerHTML = '<i>0</i>';
                    }
                },
                function(error) {
                    // Manejo de errores
                    console.error('Error en la solicitud:', error);
                });



            } else {
                // Si se selecciona 'none', limpiar o restablecer la información mostrada.
                document.getElementById('estadoActividad').innerHTML = '<i>Pendiente</i>';
                document.getElementById('numeroReportes').innerHTML = '<i>0</i>';
            }
        });    
    } else {
        console.error('No se encontró el elemento select con el id "select-actividad".');
    }
    
    function updateReportsTable(actividadId, reportes){
        const table = document.getElementById('reportsMade');
        if(table){
            if(reportes > 0){
                alert('Existen reportes');
            }else{
                alert('No hay reportes registrados.');
            }
        }
    }
});


// AREA DE CREACIÓN DE REPORTES

const reportInputArea = document.getElementById('reportInputArea');
const imageUploader = document.getElementById('imageUploader');

// Función para crear el botón de eliminación dentro del contenedor del elemento
function createRemoveButton(targetContainer) {
    const removeBtn = document.createElement('i');
    removeBtn.classList.add('fa', 'fa-close', 'remove-btn');
    removeBtn.title = 'Eliminar';

    removeBtn.addEventListener('click', function () {
        targetContainer.remove(); // Eliminar el contenedor completo
    });
    return removeBtn;
}

// Función para ajustar el tamaño de los input/textarea según el contenido
function autoResize(element) {
    element.style.height = 'auto'; // Reiniciar el height
    element.style.height = element.scrollHeight + 'px'; // Ajustar al nuevo height
}

// Agregar título (h2)
document.getElementById('addTitle').addEventListener('click', function () {
    const container = document.createElement('div');
    container.classList.add('input-container');

    const titleInput = document.createElement('textarea'); // Cambié de input a textarea para mejor adaptación
    titleInput.placeholder = 'Escribe el título aquí...';
    titleInput.classList.add('input-title');
    titleInput.rows = 1;
    titleInput.addEventListener('input', function () {
        autoResize(titleInput);
    });

    container.appendChild(titleInput);
    container.appendChild(createRemoveButton(container));
    reportInputArea.appendChild(container);
});

// Agregar subtítulo (h3)
document.getElementById('addSubtitle').addEventListener('click', function () {
    const container = document.createElement('div');
    container.classList.add('input-container');

    const subtitleInput = document.createElement('textarea'); // Cambié de input a textarea para mejor adaptación
    subtitleInput.placeholder = 'Escribe el subtítulo aquí...';
    subtitleInput.classList.add('input-subtitle');
    subtitleInput.rows = 1;
    subtitleInput.addEventListener('input', function () {
        autoResize(subtitleInput);
    });

    container.appendChild(subtitleInput);
    container.appendChild(createRemoveButton(container));
    reportInputArea.appendChild(container);
});

// Agregar texto (textarea)
document.getElementById('addText').addEventListener('click', function () {
    const container = document.createElement('div');
    container.classList.add('input-container');

    const textArea = document.createElement('textarea');
    textArea.placeholder = 'Escribe el texto aquí...';
    textArea.classList.add('input-text');
    textArea.rows = 1;
    textArea.addEventListener('input', function () {
        autoResize(textArea);
    });

    container.appendChild(textArea);
    container.appendChild(createRemoveButton(container));
    reportInputArea.appendChild(container);
});

// Abrir seleccionador de archivos para agregar imagen
document.getElementById('addImage').addEventListener('click', function () {
    imageUploader.click();
});

// Mostrar imagen seleccionada en el reporte
imageUploader.addEventListener('change', function (event) {
    const file = event.target.files[0];

    // Validar que el archivo no exceda los 5 MB
    if (file && file.size <= 5 * 1024 * 1024) { // 5 MB en bytes
        const reader = new FileReader();
        reader.onload = function (e) {
            const container = document.createElement('div');
            container.classList.add('input-container');

            const img = document.createElement('img');
            img.src = e.target.result;
            img.classList.add('report-image');

            container.appendChild(img);
            container.appendChild(createRemoveButton(container));
            reportInputArea.appendChild(container);
        };
        reader.readAsDataURL(file);
    } else {
        alert('La imagen debe ser menor a 5 MB y en formato PNG, JPG, JPEG o WEBP.');
    }
});