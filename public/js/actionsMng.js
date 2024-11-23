// Manejar el cambio de actividad
function updatePageData() {
    const selectActividad = document.getElementById('select-actividad');
    if (selectActividad) {
        const actividadId = selectActividad.value;
        const btnEditor = document.getElementById('showReportCreator');
        if (actividadId !== 'none' && Number.isInteger(parseInt(actividadId))) {
            
            const url = '../controller/actionsManager.php?ajaxUpdate=true&activityId=' + actividadId;

            makeAjaxRequest(url, 'POST', null, function(response) {
                // Validar respuesta
                if (response && response.success) {
                    const data = response.data;

                    const estados = {
                        1: 'En proceso',
                        2: 'Urgentes',
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

                    if(!btnEditor && data.estadoActual != 4){
                        createAddButton();
                    }else{
                        const existingBtn = document.getElementById('showReportCreator');
                        if(existingBtn){
                            existingBtn.remove();
                        }
                    }

                    const endActBtn = document.querySelector('.finishBtn');
                    if(endActBtn){ endActBtn.remove(); }

                    if(data.numeroReportes > 0){
                        setActStateButtons(actividadId);
                    }else{
                        const existingBtn = document.getElementById('currentActivityState');
                        if(existingBtn){ existingBtn.remove(); }
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
            const existingBtn = document.getElementById('currentActivityState');
            if(existingBtn){ existingBtn.remove(); }
            const adBtn = document.getElementById('showReportCreator');
            if(adBtn){ adBtn.remove(); }
        }
    } else {
        console.error('No se encontró el elemento select con el id "select-actividad".');
    }
}

function setActStateButtons(actividadId){
    const existingBtn = document.getElementById('currentActivityState');
    if(existingBtn){ existingBtn.remove(); }
    const url = '../controller/actionsManager.php?getActState=true&activityId=' + actividadId;
    makeAjaxRequest(url, 'POST', null, function(response) {
        // Validar respuesta
        if (response && response.success) {
            if(response.data.revision === 1){
                const actDiv = document.querySelector('.activityStatusDiv');
                const onRev = document.createElement('button');
                onRev.classList.add('onRevision');
                onRev.textContent = 'Actividad en revisión';
                const spanishDate = formatSpanishDate(response.data.revision_date);
                onRev.setAttribute('title', `En revisión desde el ${spanishDate}`);
                onRev.setAttribute('id', 'currentActivityState');
                actDiv.appendChild(onRev);
            }else if(response.data.revision === 0 && response.data.estadoActual != 4){
                const actDiv = document.querySelector('.activityStatusDiv');
                const finishBtn = document.createElement('button');
                finishBtn.classList.add('finishBtn');
                finishBtn.textContent = 'Finalizar actividad';
                finishBtn.setAttribute('id', 'currentActivityState');
                actDiv.appendChild(finishBtn);
                finishBtn.addEventListener('click', endActivity);
            }
        }
    },
    function(error) {
        // Manejo de errores
        console.error('Error en la solicitud:', error);
    });

}

//Boton para abrir el editor de reporte
function createAddButton() {
    const addBtnDiv = document.createElement('div');
    addBtnDiv.classList.add('addBtn');

    addBtnDiv.onclick = function() {
        createEditorArea();
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
                        cellActions.innerHTML = "<i class='fa fa-eye button' title='Ver reporte' onclick='createReportView(this)'></i><i class='fa fa-trash button' title='Eliminar reporte' onclick='deleteReport(this)'></i>";
                        reportRow.appendChild(cellActions);
                        tableBody.appendChild(reportRow);
                    });
                    addButtonEvents();
                } else {
                    console.error('Respuesta inválida o no exitosa:', response);
                    createAlertDialog('Acción no realizada', 'No se pudieron cargar los reportes de la actividad.', null, 'Aceptar');
                }
            }, function(error) {
                console.error('Error en la solicitud:', error);
                createAlertDialog('Acción no realizada', 'Ocurrió un error al establecer la conexión.\nPor favor, toma captura de pantalla y levanta un ticket de soporte.', null, 'Aceptar');
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

//Abrir info de actividad, tras insertar un nuevo reporte o venir de dashboard a 'crear reporte'
document.addEventListener('DOMContentLoaded', function() {
    const updateData = document.getElementById('updateDataNow');
    if (updateData) {
        console.log('The div exists');
        const selectActividad = document.getElementById('select-actividad');
        selectActividad.value = updateData.value;
        updatePageData();
        updateData.remove();
    }

    const redirectedToReport = localStorage.getItem('openReportEditor');
    if (redirectedToReport) {
        const actId = localStorage.getItem('actId');
        const selectElement = document.getElementById('select-actividad');
        
        if (actId && selectElement) {
            selectElement.value = actId;
            
            const event = new Event('change');
            selectElement.dispatchEvent(event);

            const waitForButton = () => {
                return new Promise((resolve, reject) => {
                    const checkBtn = () => {
                        const reportBtn = document.querySelector('.addBtn');
                        if (reportBtn) {
                            resolve(reportBtn);
                        } else {
                            reject('Report button not found.');
                        }
                    };
                    
                    setTimeout(checkBtn, 100);
                });
            };
        
            waitForButton()
            .then((reportBtn) => {
                reportBtn.click();
                localStorage.removeItem('openReportEditor');
                localStorage.removeItem('actId');
            })
            .catch((error) => {
                waitForButton()
                .then((reportBtn) => {
                    reportBtn.click();
                    localStorage.removeItem('openReportEditor');
                    localStorage.removeItem('actId');
                })
                .catch((error) => {
                    localStorage.removeItem('openReportEditor');
                    localStorage.removeItem('actId');
                    console.error(error);
                });
            });
        }
    }

    const redirectedToAct = localStorage.getItem('openActDetails');
    if(redirectedToAct){
        const actId = localStorage.getItem('actId');
        const selectElement = document.getElementById('select-actividad');
        
        if (actId && selectElement) {
            selectElement.value = actId;
            
            const event = new Event('change');
            selectElement.dispatchEvent(event);
            localStorage.getItem('openActDetails');
            localStorage.removeItem('actId');
        }
    }
    
});

function createReportView(element) {
    const trElement =  element.closest('[av-id]');
    const avId = trElement.getAttribute('av-id');
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
                optionsDiv.innerHTML = `<i class='fa fa-times-rectangle button' title='Cerrar' onclick='closeReportView()'></i><i class='fa fa-download button' onclick='downloadReport(this)' title='Descargar reporte'></i>`;
                
                const opciones = `<div class='file-options'>
                <i class='fa fa-times-rectangle button' title='Cerrar' onclick='closeReportView()'></i>
                <i class='fa fa-download button' onclick='downloadReport(this)' title='Descargar reporte'></i>
                </div>`;
                reportContent.innerHTML = `${opciones}${data.contenido}`;
                reportContent.setAttribute('rpName', data.nombre);
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

function deleteReport(element) {
    const trElement = element.closest('[av-id]');
    const avId = trElement.getAttribute('av-id');

    createConfirmationDialog(
        'Mensaje de confirmación',
        'Estás a punto de eliminar un reporte.\nEsta acción es irreversible.\n\n¿Deseas continuar?', 
        function() {
            createConfirmationDialog(
                'Mensaje de confirmación',
                'Se eliminarán las imágenes anexadas en este reporte de la base de datos.\n\n¿Deseas continuar?', 
                function() {
                    const data = new URLSearchParams({
                        avId: avId 
                    });

                    fetch('../controller/actionsManager.php?deleteReport=true', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: data
                    })
                    .then(response => response.json()) 
                    .then(data => {
                        if (data.success) {
                            trElement.remove();
                            console.log('Reporte eliminado correctamente:', data.message);
                        } else {
                            createAlertDialog('Acción no realizada', 'Error al eliminar el reporte: ' + data.message, null, 'Aceptar');
                        }
                    })
                    .catch(error => {
                        console.error('Error en la solicitud AJAX:', error);
                        createAlertDialog('Acción no realizada', 'Ocurrió un error al establecer la conexión.\nPor favor, toma captura de pantalla y levanta un ticket de soporte.', null, 'Aceptar');
                    });
                },
                function() {
                    console.log('Eliminación del reporte cancelada.');
                }
            );
        },
        function() {
            console.log("Eliminación de reporte cancelada.");
        }
    );
}

// Enviar actividad a revisión para su finalización
function endActivity() {
    const actId = document.getElementById('select-actividad').value;
    createConfirmationDialog(
        'Finalizar actividad',
        'Al confirmar esta acción la actividad seleccionada se enviará a revisión con el representante de proyecto para confirmar su finalización.\n\n¿Deseas continuar?', 
        function() {
            const data = new URLSearchParams({
                actId: actId
            });

            // Realiza la solicitud POST
            fetch('../controller/actionsManager.php?submitActivityRevision=true', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: data
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Actividad enviada a revisión:', data.message);
                    setActStateButtons(actId);
                    createAlertDialog('¡Completado!', 'Actividad enviada correctamente a revisión.', null, 'Aceptar');
                } else {
                    createAlertDialog('Acción no realizada', 'Error al finalizar la actividad: ' + data.message, null, 'Aceptar');
                }
            })
            .catch(error => {
                console.error('Error en la solicitud AJAX:', error);
                createAlertDialog('Acción no realizada', 'Ocurrió un error al establecer la conexión.\nPor favor, toma captura de pantalla y levanta un ticket de soporte.', null, 'Aceptar');
            });
        },
        function() {
            console.log("Finalización de actividad cancelada.");
        }
    );
}


function createEditorArea(){
    const existingContainer = document.querySelector('.editor-container');
    if (existingContainer) {
        existingContainer.remove();
    }

    // Crear el contenedor principal
    const editorContainer = document.createElement('div');
    editorContainer.className = 'editor-container';

    // Crear la barra de herramientas
    const editorToolbar = document.createElement('div');
    editorToolbar.className = 'editorToolbar';

    // Crear botones de la barra de herramientas
    const buttons = [
        { id: 'boldButton', innerHTML: '<b>B</b>' },
        { id: 'italicButton', innerHTML: '<i>I</i>' },
        { id: 'underlineButton', innerHTML: '<u>U</u>' },
        { id: 'orderedListButton', className: 'fa fa-list-ol' },
        { id: 'unorderedListButton', className: 'fa fa-list-ul' },
        { id: 'justifyLeftButton', className: 'fa fa-align-left' },
        { id: 'justifyCenterButton', className: 'fa fa-align-center' },
        { id: 'justifyRightButton', className: 'fa fa-align-right' },
        { id: 'header1Button', innerHTML: 'H1', style: 'font-size:var(--px18);' },
        { id: 'header2Button', innerHTML: 'H2', style: 'font-size:var(--px15);' },
        { id: 'normalTextButton', innerHTML: 'N', style: 'font-size:var(--px13);' },

        { id: 'imageButton', className: 'fa fa-image' }
    ];

    buttons.forEach(btn => {
        const button = document.createElement('button');
        button.id = btn.id;
        if (btn.innerHTML) button.innerHTML = btn.innerHTML;
        if (btn.className) button.className = btn.className;
        if (btn.style) button.style = btn.style;
        editorToolbar.appendChild(button);
    });

    // Crear el input para subir imágenes
    const imageInput = document.createElement('input');
    imageInput.type = 'file';
    imageInput.id = 'imageInput';
    imageInput.accept = 'image/*';
    imageInput.style.display = 'none';
    editorToolbar.appendChild(imageInput);

    // Crear el slider de tamaño de imagen
    const imageSizeSlider = document.createElement('input');
    imageSizeSlider.type = 'range';
    imageSizeSlider.id = 'imageSizeSlider';
    imageSizeSlider.min = '100';
    imageSizeSlider.max = '1000';
    imageSizeSlider.value = '100';
    imageSizeSlider.style.display = 'none';
    editorToolbar.appendChild(imageSizeSlider);

    // Añadir la barra de herramientas al contenedor principal
    editorContainer.appendChild(editorToolbar);

    // Crear el editor de contenido
    const editor = document.createElement('div');
    editor.id = 'editor';
    editor.className = 'editor';
    editor.contentEditable = 'true';
    editorContainer.appendChild(editor);

    // Crear la barra de herramientas inferior con el botón de guardar
    const bottomToolbar = document.createElement('div');
    bottomToolbar.className = 'bottomToolBar';
    const saveButton = document.createElement('button');
    saveButton.id = 'saveButton';
    saveButton.className = 'saveButton';
    saveButton.textContent = 'Guardar';
    bottomToolbar.appendChild(saveButton);
    editorContainer.appendChild(bottomToolbar);

    // Añadir el contenedor principal al cuerpo del documento
    document.querySelector('.main').appendChild(editorContainer);
    document.getElementById('showReportCreator').remove();

    setTimeout(() => {
        addEditorEvents();
    }, 100);
}

function addEditorEvents(){
    document.getElementById("boldButton").addEventListener("click", () => formatText("bold"));
    document.getElementById("italicButton").addEventListener("click", () => formatText("italic"));
    document.getElementById("underlineButton").addEventListener("click", () => formatText("underline"));
    document.getElementById("orderedListButton").addEventListener("click", () => formatText("insertOrderedList"));
    document.getElementById("unorderedListButton").addEventListener("click", () => formatText("insertUnorderedList"));
    document.getElementById("justifyLeftButton").addEventListener("click", () => formatText("justifyLeft"));
    document.getElementById("justifyCenterButton").addEventListener("click", () => formatText("justifyCenter"));
    document.getElementById("justifyRightButton").addEventListener("click", () => formatText("justifyRight"));
    document.getElementById("header1Button").addEventListener("click", () => addHeader("h1"));
    document.getElementById("header2Button").addEventListener("click", () => addHeader("h2"));
    document.getElementById("normalTextButton").addEventListener("click", () => addHeader("p"));

    document.getElementById("imageButton").addEventListener("click", () => {
        document.getElementById("imageInput").click(); // Abre el selector de archivos
    });

    document.getElementById("imageInput").addEventListener("change", handleImageUpload);
    document.getElementById("imageSizeSlider").addEventListener("input", handleImageResize);

    document.addEventListener("click", (event) => {
        if (selectedImage && !selectedImage.contains(event.target) && event.target.id !== "imageSizeSlider") {
            deselectImage();
        }
    });

    document.getElementById("saveButton").addEventListener("click", saveContent);

    const editor = document.getElementById("editor");
    editor.addEventListener("paste", () => {
        setTimeout(() => {
            const editorContent = editor.innerHTML;
            
            if (editorContent.includes("<img")) {
                assignImageEvents();
            }
        }, 150);
    });
}

let selectedImage = null;

function handleImageClick(event) {
    selectImage(event.target);
}

function assignImageEvents() {
    const images = document.querySelectorAll("#editor img");
    images.forEach(img => {
        img.removeEventListener("click", handleImageClick);
        img.addEventListener("click", handleImageClick);
    });
}


function formatText(command) {
    document.execCommand(command, false, null);
}

function addHeader(tag) {
    document.execCommand('formatBlock', false, tag);
}

function handleImageUpload(event) {
    const file = event.target.files[0];
    if (file && file.type.startsWith("image/")) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.createElement("img");
            img.src = e.target.result;
            img.style.maxWidth = "1000px";
            img.style.width = "500px";
            img.style.cursor = "pointer"; 
            img.addEventListener("click", () => selectImage(img));
            document.getElementById("editor").appendChild(img);
        };
        reader.readAsDataURL(file);
    }
}

function selectImage(img) {
    const slider = document.getElementById("imageSizeSlider");
    if(img.style.border==='' || img.style.border==='none'){
        if (selectedImage) {
            selectedImage.style.border = "none";
        }
        selectedImage = img;
        selectedImage.style.border = "2px solid #007bff";

        slider.style.display = "inline-block";
        slider.value = parseInt(selectedImage.style.width);
    }else{
        deselectImage();
    }
}

function deselectImage() {
    if (selectedImage) {
        selectedImage.style.border = "none"; // Quita el borde de selección
        selectedImage = null; // Deselecciona la imagen
    }
    const slider = document.getElementById("imageSizeSlider");
    slider.style.display = "none";
}

function handleImageResize(event) {
    if (selectedImage) {
        const newSize = event.target.value + "px";
        selectedImage.style.width = newSize; // Ajusta el ancho de la imagen
    }
}

function saveContent() {
    const reportContainer = document.getElementById('editor');

    if (reportContainer && reportContainer.children.length === 0) {
        reportContainer.classList.add('highlight-error');

        setTimeout(function() {
            reportContainer.classList.remove('highlight-error');
        }, 1000);
    } else {
        createInputBox('Guardar reporte', 'Nombre del archivo:').then(fileName => {
            saveReport(fileName);
        })
        .catch(error => {
            if (error !== 'Input cancelado') {
                console.error('Error inesperado:', error);
            }
        });
    }
}

function saveReport(reportName) {
    const reportContainer = document.querySelector('.editor');
    const actividadId = document.getElementById('select-actividad').value;

    if (!reportName || !reportContainer) return;

    // Clonar el HTML del contenedor
    const reportHTML = reportContainer.innerHTML;

    // Seleccionar todas las imágenes en el editor
    const images = reportContainer.querySelectorAll('img');
    const base64Images = {};

    let promises = [];
    
    images.forEach((img, index) => {
        // Crear un canvas para convertir la imagen a base64
        let promise = new Promise((resolve, reject) => {
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            canvas.width = img.width;
            canvas.height = img.height;
            context.drawImage(img, 0, 0, canvas.width, canvas.height);
            
            const base64Image = canvas.toDataURL("image/png"); // Cambia el formato si es necesario
            
            // Capturar los estilos del elemento img
            const styles = {
                maxWidth: img.style.maxWidth || '',
                width: img.style.width || '',
                height: img.style.height || '',
                cursor: img.style.cursor || '',
                border: img.style.border || ''
            };
            
            // Almacenar la imagen base64 y sus estilos
            base64Images[`imagen_${index}`] = {
                image: base64Image,
                styles: styles
            };
            
            resolve();
        });
        promises.push(promise);
    });

    Promise.all(promises).then(() => {
        // Crear el formulario dinámicamente
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '../controller/actionsManager.php?saveNewReport2=true';

        // Agregar el nombre del reporte al formulario
        const nameInput = document.createElement('input');
        nameInput.type = 'hidden';
        nameInput.name = 'reportName';
        nameInput.value = reportName;
        form.appendChild(nameInput);

        const actividad = document.createElement('input');
        actividad.type = 'hidden';
        actividad.name = 'id_actividad';
        actividad.value = actividadId;
        form.appendChild(actividad);

        // Agregar el HTML del reporte al formulario
        const htmlInput = document.createElement('input');
        htmlInput.type = 'hidden';
        htmlInput.name = 'reportHTML';
        htmlInput.value = reportHTML;
        form.appendChild(htmlInput);

        // Agregar las imágenes codificadas en base64 al formulario
        Object.keys(base64Images).forEach((key) => {
            const imageInput = document.createElement('input');
            imageInput.type = 'hidden';
            imageInput.name = key;
            imageInput.value = JSON.stringify(base64Images[key]);
            form.appendChild(imageInput);
        });
        // Agregar el formulario al DOM y enviarlo
        document.body.appendChild(form);
        if(htmlInput.value !== ''){
            form.submit();
        }else{
            console.error('No HTML');
        }
    });
}


// Convertir imagen a Base64
function toBase64(img) {
    return new Promise((resolve) => {
        const canvas = document.createElement("canvas");
        canvas.width = img.naturalWidth;
        canvas.height = img.naturalHeight;
        const ctx = canvas.getContext("2d");
        ctx.drawImage(img, 0, 0);
        resolve(canvas.toDataURL("image/png"));
    });
}