// Manejar el cambio de actividad
function updatePageData() {
    const selectActividad = document.getElementById('select-actividad');
    if (selectActividad) {
        const actividadId = selectActividad.value;
        const reportMakingDiv = document.getElementById('reportCreator');
        reportMakingDiv.classList.add('hide');
        const btnEditor = document.getElementById('showReportCreator');
        if (actividadId !== 'none' && Number.isInteger(parseInt(actividadId))) {
            
            const url = '../controller/actionsManager.php?ajaxUpdate=true&activityId=' + actividadId;

            makeAjaxRequest(url, 'POST', null, function(response) {
                // Validar respuesta
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

                    // Actualizar la tabla de reportes
                    updateReportsTable(actividadId, data.numeroReportes);

                    if(!btnEditor){
                        createAddButton();
                    }

                } else {
                    document.getElementById('estadoActividad').innerHTML = '<i>No disponible</i>';
                    document.getElementById('numeroReportes').innerHTML = '<i>0</i>';
                    if (btnEditor) {
                        addBtnDiv.remove();
                    }
                }
            },
            function(error) {
                // Manejo de errores
                console.error('Error en la solicitud:', error);
            });

        } else {
            // Si se selecciona 'none', limpiar o restablecer la información mostrada.
            document.getElementById('estadoActividad').innerHTML = '<i>No disponible</i>';
            document.getElementById('numeroReportes').innerHTML = '<i>0</i>';
            const tableBody = document.getElementById('reportsMade_tbody');
            tableBody.innerHTML = '';
            const noReportRow = document.createElement('tr');
            const noReportCell = document.createElement('td');
            noReportCell.colSpan = 3;
            noReportCell.innerHTML = '<i>Selecciona una actividad</i>';
            noReportRow.appendChild(noReportCell);
            tableBody.appendChild(noReportRow);
        }
    } else {
        console.error('No se encontró el elemento select con el id "select-actividad".');
    }
}

//Boton para abrir el editor de reporte
function createAddButton() {
    const addBtnDiv = document.createElement('div');
    addBtnDiv.classList.add('addBtn');

    addBtnDiv.onclick = function() {
        openAddReport(this);
    };
    
    const anchor = document.createElement('a');
    anchor.title = 'Crear reporte';
    addBtnDiv.setAttribute('tabindex', '0');
    anchor.classList.add('fa', 'fa-plus');
    
    addBtnDiv.id = 'showReportCreator';
    addBtnDiv.appendChild(anchor);
    
    document.querySelector('.main').appendChild(addBtnDiv);

    addBtnDiv.addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            addBtnDiv.click();
        }
    });
}

//Actualiza los datos de la tabla de reportes
function updateReportsTable(actividadId, reportes) {
    const table = document.getElementById('reportsMade');
    if (table) {
        const tableBody = document.getElementById('reportsMade_tbody');
        if (reportes > 0) {
            const url = `../controller/actionsManager.php?getReportInfo=true&actId=${actividadId}`;

            // Ajax para obtener la información de los reportes y mostrarla en la tabla de reportes
            makeAjaxRequest(url, 'POST', null, function(response) {
                if (response && response.success) {
                    const data = response.data;
                    tableBody.innerHTML = '';
                    data.forEach(rowData => {
                        const reportRow = document.createElement('tr');
                        reportRow.setAttribute('av-id', `${rowData.id_avance}`); 
                        const cellNombre = document.createElement('td');
                        cellNombre.innerText = rowData.nombre;
                        reportRow.appendChild(cellNombre);
                        const cellFecha = document.createElement('td');
                        cellFecha.innerText = rowData.fecha_creacion;
                        reportRow.appendChild(cellFecha);
                        const cellActions = document.createElement('td');
                        cellActions.innerHTML = "<i class='fa fa-eye button' title='Ver reporte' onclick='createReportView(this)'></i><i class='fa fa-trash button' title='Eliminar reporte'></i>";
                        reportRow.appendChild(cellActions);
                        tableBody.appendChild(reportRow);
                    });
                    addButtonEvents();
                } else {
                    console.error('Respuesta inválida o no exitosa:', response);
                    alert('No se pudieron cargar los reportes de la actividad.');
                }
            }, function(error) {
                console.error('Error en la solicitud:', error);
            });
        } else {
            tableBody.innerHTML = '';
            const noReportRow = document.createElement('tr');
            const noReportCell = document.createElement('td');
            noReportCell.colSpan = 3;
            noReportCell.innerHTML = '<i>Sin reportes realizados...</i>';
            noReportRow.appendChild(noReportCell);
            tableBody.appendChild(noReportRow);
        }
    }
}

//Cargar la informacion de una actividad tras insertar un reporte
document.addEventListener('DOMContentLoaded', function() {
    const updateData = document.getElementById('updateDataNow');
    if (updateData) {
        console.log('The div exists');
        const selectActividad = document.getElementById('select-actividad');
        selectActividad.value = updateData.value;
        updatePageData();
        updateData.remove();
    }
});

//Mostrar el editor de reportes.
function openAddReport(element){
    const reportMakingDiv = document.getElementById('reportCreator');
    reportMakingDiv.classList.remove('hide');
    const btnTarget = document.getElementById('createReport');
    btnTarget.scrollIntoView({ behavior: 'smooth' });
    element.remove();

    addButtonEvents();
}

function closeAddReport(){
    const reportMakingArea = document.getElementById('reportInputArea');
    if (!reportInputArea || reportInputArea.children.length === 0) {
        reportMakingArea.parentElement.classList.add('hide');
        createAddButton();
    }else{
        createConfirmationDialog('Advertencia','Se eliminará el avance del reporte en curso.\n\n¿Continuar?', function() {
            reportMakingArea.innerHTML = "";
            reportMakingArea.parentElement.classList.add('hide');
            createAddButton();
        });
    }
}

// Crear div para nombrar el reporte
function createSaveReport() {
    const nombrarReporte = document.createElement('div');
    nombrarReporte.id = 'saveReportNombrar';
    nombrarReporte.classList.add('nombrarReporte', 'hidden');

    const nombrarReporteContent = document.createElement('div');
    nombrarReporteContent.classList.add('nombrarReporte-content');

    const title = document.createElement('h3');
    title.textContent = 'Guardar reporte';
    nombrarReporteContent.appendChild(title);

    const label = document.createElement('label');
    label.setAttribute('for', 'reportName');
    label.textContent = 'Nombre del archivo:';
    nombrarReporteContent.appendChild(label);

    const input = document.createElement('input');
    input.type = 'text';
    input.id = 'reportName';
    input.classList.add('input-text');
    input.maxLength = 255;
    nombrarReporteContent.appendChild(input);

    const buttonContainer = document.createElement('div');
    buttonContainer.classList.add('nombrarReporte-buttons');

    const saveButton = document.createElement('button');
    saveButton.id = 'saveReportBtn';
    saveButton.textContent = 'Guardar';
    buttonContainer.appendChild(saveButton);

    const cancelButton = document.createElement('button');
    cancelButton.id = 'cancelBtn';
    cancelButton.textContent = 'Cancelar';
    buttonContainer.appendChild(cancelButton);

    nombrarReporteContent.appendChild(buttonContainer);
    nombrarReporte.appendChild(nombrarReporteContent);

    document.body.appendChild(nombrarReporte);

    saveButton.addEventListener('click', function() {
        const reportName = input.value.trim();
        if (reportName === '') {
            alert('Por favor, ingresa un nombre para el reporte.');
        } else {
            guardarReporte(reportName);
            nombrarReporte.remove();
        }
    });

    cancelButton.addEventListener('click', function() {
        nombrarReporte.remove();
    });
}

//Validar el contenido del reporte a guardar
function saveNewReport() {
    const reportContainer = document.querySelector('.report-input-area'); // Selecciona el contenedor del reporte

    if (reportContainer && reportContainer.children.length === 0) {
        reportContainer.classList.add('highlight-error');

        setTimeout(function() {
            reportContainer.classList.remove('highlight-error');
        }, 1000);
    } else {
        createSaveReport();
    }
}

//AJAX para guardar el reporte
function guardarReporte(reportName) {
    const reportContainer = document.querySelector('.report-input-area');
    const reportElements = reportContainer.children;
    if(!reportName){
        return;
    }
    let contenido = [];
    let imagenes = [];

    // Recorremos todos los elementos de la sección del reporte
    Array.from(reportElements).forEach(container => {
        // Obtener todos los textarea e img dentro del contenedor
        const inputElements = container.querySelectorAll('textarea, img');
        inputElements.forEach(inputElement => {
            if (inputElement.tagName === 'TEXTAREA') {
                let type = '';

                if (inputElement.classList.contains('input-title')) {
                    type = 'h2';
                } else if (inputElement.classList.contains('input-subtitle')) {
                    type = 'h3';
                } else if (inputElement.classList.contains('input-text')) {
                    type = 'p';
                }

                // Añadir el contenido del textarea al array
                if (type) {
                    contenido.push({ type: type, value: inputElement.value.trim() });
                }
            } else if (inputElement.tagName === 'IMG') {
                imagenes.push(inputElement.src); // Guardar la imagen (base64)
            }
        });
    });

    // Si no hay contenido, cancelar el guardado
    if (contenido.length === 0) {
        alert('No has agregado contenido al reporte.');
        return;
    }

    const actividadId = document.getElementById('select-actividad').value;

    // Crear el formulario dinámicamente
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '../controller/actionsManager.php?saveNewReport=true';
    form.enctype = 'multipart/form-data';

    // Agregar el contenido JSON al formulario
    const contenidoInput = document.createElement('input');
    contenidoInput.type = 'hidden';
    contenidoInput.name = 'contenido';
    contenidoInput.value = JSON.stringify(contenido);
    form.appendChild(contenidoInput);

    // Agregar el contenido JSON al formulario
    const nombreR = document.createElement('input');
    nombreR.type = 'hidden';
    nombreR.name = 'nombreReporte';
    nombreR.value = reportName;
    form.appendChild(nombreR);

    // Agregar el ID de la actividad
    const actividadInput = document.createElement('input');
    actividadInput.type = 'hidden';
    actividadInput.name = 'id_actividad';
    actividadInput.value = actividadId;
    form.appendChild(actividadInput);

    // Agregar las imágenes (si existen)
    imagenes.forEach((imagenSrc, index) => {
        const imagenInput = document.createElement('input');
        imagenInput.type = 'hidden';
        imagenInput.name = `imagen_${index}`;
        imagenInput.value = imagenSrc;
        form.appendChild(imagenInput);
    });

    // Añadir el formulario al DOM y enviarlo
    document.body.appendChild(form);
    form.submit();
}


// Evento para el botón de guardar
document.getElementById('createReport').addEventListener('click', function () {
    // Validar si hay contenido en el reporte antes de guardar
    if (document.querySelector('.input-container') !== null) {
        guardarReporte();
    }
});

 
const reportInputArea = document.getElementById('reportInputArea');
const imageUploader = document.getElementById('imageUploader');

// Crear el botón de eliminación dentro del contenedor del elemento
function createRemoveButton(targetContainer) {
    const removeBtn = document.createElement('i');
    removeBtn.classList.add('fa', 'fa-close', 'remove-btn');
    removeBtn.title = 'Eliminar';

    removeBtn.addEventListener('click', function () {
        targetContainer.remove(); // Eliminar el elemento
    });
    return removeBtn;
}

function autoResize(element) {
    element.style.height = 'auto';
    element.style.height = element.scrollHeight + 'px';
}

// Agregar título (h2)
document.getElementById('addTitle').addEventListener('click', function () {
    const container = document.createElement('div');
    container.classList.add('input-container');

    const titleInput = document.createElement('textarea');
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

    const subtitleInput = document.createElement('textarea');
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

    if (file && file.size <= 2 * 1024 * 1024) { // 1 MB
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
        alert('La imagen debe ser menor a 2 MB y en formato PNG, JPG, JPEG o WEBP.');
    }
});

function createReportView(element) {
    const trElement =  element.closest('[av-id]');
    const avId = trElement.getAttribute('av-id');
    console.log(`id capturado: ${avId}`);
    // Realizar la solicitud AJAX para obtener la información del reporte
    const url = `../controller/actionsManager.php?getReportData=true&id_avance=${avId}`;
    const formData = new FormData();
    makeAjaxRequest(url, 'POST', formData, function(response) {
        try {
            const data = response;
            if (data && data.success) {
                const data = response.data;
            
                // Crear el contenedor del reporte
                const reportContainer = document.createElement('div');
                reportContainer.classList.add('pdf-view-container');
                const reportContent = document.createElement('div');
                reportContent.classList.add('report-content');
                
                const optionsDiv = document.createElement('div');
                optionsDiv.classList.add('file-options');
                optionsDiv.innerHTML = `<i class='fa fa-times-rectangle button' title='Cerrar' onclick='closeReportView()'></i><i class='fa fa-print button' onclick='prinReport(this)' title='Imprimir reporte'></i>`;
                reportContent.appendChild(optionsDiv);
                // Recorrer el contenido y crear los elementos correspondientes
                let mgFlag = true;
                data.contenido.forEach(item => {
                    const element = document.createElement(item.type);
                    
                    if (item.type === 'p' || item.type === 'h3' || item.type === 'h2') {
                        element.innerHTML = item.value.replace(/\n/g, '<br>');
                    } else {
                        element.textContent = item.value; 
                    }
                    if(item.type === 'h2' && mgFlag == true){
                        element.style = "margin-top:0;";
                    }
                    
                    reportContent.appendChild(element);
                    mgFlag = false;
                });
                
                // Insertar el contenedor en la página
                reportContainer.appendChild(reportContent);
                document.body.appendChild(reportContainer);
                addButtonEvents();
            } else {
                console.error('Error al obtener el reporte:', data.message);
            }
        } catch (error) {
            console.error('Error al analizar la respuesta JSON:', error);
            console.log('Respuesta recibida:', response); // Ver la respuesta exacta que se recibe
        }
    }, function(error) {
        console.error('Error en la solicitud AJAX:', error);
    });

}

function closeReportView(){
    const reportView = document.querySelector('.pdf-view-container');
    if(reportView){
        reportView.remove();
    }
}

function prinReport(element){
    alert('Imprimiendo reporte.');
}