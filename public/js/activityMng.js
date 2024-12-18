// Mostrar filtros
function FiltersToggle(){
    const filterDiv = document.getElementById('filterDiv');
    const filtros = document.querySelectorAll('.dropDownFilter');

    filterDiv.classList.toggle('closedFilterDiv');
    filterDiv.classList.toggle('openFilterDiv');

    filtros.forEach(filtro => {
        filtro.classList.toggle('hide');
    });
}

// Aplicar filtro seleccionado a la tabla
function FilterResults(selectElement) {
    unpaginate('activity-list-body', 'pagination');

    const filterValue = selectElement.value;
    const tbody = document.getElementById('activity-list-body');
    const rows = tbody.getElementsByTagName('tr');
    let visibleRows = 0;

    // Eliminar el mensaje de "No se encontraron resultados" antes de filtrar
    const noResultsRow = document.getElementById('no-results-row');
    if (noResultsRow) {
        noResultsRow.remove();
    }

    // Filtrar las filas según el valor seleccionado
    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        const estado = row.cells[2].textContent;

        if (filterValue === 'noFilter' || estado === filterValue) {
            row.classList.remove('hide');
            visibleRows++;
        } else {
            row.classList.add('hide');
        }
    }

    // Agregar el mensaje de "No se encontraron resultados" si no hay filas visibles
    if (visibleRows === 0) {
        const newRow = tbody.insertRow();
        newRow.id = 'no-results-row';
        const newCell = newRow.insertCell(0);
        newCell.colSpan = 6; // Ajusta según el número de columnas de tu tabla
        newCell.textContent = "No se encontraron resultados.";
    }

    // Mostrar u ocultar paginación según el filtro
    const pagination = document.getElementById('pagination');
    if (filterValue === 'noFilter') {
        paginateTable('activity-list-body', 7, 'pagination'); 
        pagination.style.display = 'block';
    } else {
        pagination.style.display = 'none';
    }
}

// Abrir formulario de alta de actividad
function openAddForm(){
    const filtros = document.getElementById('filterDiv');
    filtros.scrollIntoView({
        behavior: 'smooth'
    });
    const formDiv = document.getElementById('addActivity-form');
    formDiv.classList.remove('hide');
}

// Confirmación al cerrar la edicion sin guardar
function confirmCancelEdit(){
    createConfirmationDialog('Advertencia','Estás a punto de cerrar el formulario sin guardar los cambios.\n\n¿Continuar?', function() {
        const form = document.getElementById('activity-form');
        const formDiv = document.getElementById('addActivity-form');
        form.reset();
        formDiv.classList.add('hide');
    });
}

// Agregar eventos al cargar la pagina
document.addEventListener("DOMContentLoaded", function() {
    const checkboxes = document.querySelectorAll('.activity-checkbox');
    const allBoxs = document.getElementById('selectAllActivities');
    const actSelectedDiv = document.getElementById('selectedRowsOptions');
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', ActivitiesSelectedOptions);
    });

    allBoxs.addEventListener('change', () => {
        checkboxes.forEach(checkbox => {
            checkbox.checked = allBoxs.checked;
        });
        ActivitiesSelectedOptions();
    });

    function ActivitiesSelectedOptions() {
        const checkedCheckboxes = Array.from(checkboxes).filter(chk => chk.checked);
        if (checkedCheckboxes.length > 0) {
            actSelectedDiv.classList.remove('hide');
        } else {
            actSelectedDiv.classList.add('hide');
        }
    }

    //Cerrar barra al hacer clic fuera del div
    const filterDiv = document.getElementById('filterDiv');
    document.addEventListener('click', function(event) {
        const closeFilterBar = filterDiv.contains(event.target);

        if (!closeFilterBar) {
            if(filterDiv.classList.contains('openFilterDiv')){
                FiltersToggle();
            }
        }
    });

});

// Actualizar la descripción de la actividad seleccionada
function SelectThisRowAndDetails(element, tbodyName){
    const tbody = document.getElementById(`${tbodyName}`);
    const rows = tbody.getElementsByTagName('tr');
    const state = element.classList.contains('rowSelected') || element.classList.contains('revisionSelectedRow');
    const textArea = document.getElementById('descriptionDetails');
    const buttonsDiv = document.querySelector('.buttonsDiv');
    const clName = element.classList.contains('revisionBg') ? 'revisionSelectedRow' : 'rowSelected';
    
    if(buttonsDiv){buttonsDiv.remove();}

    for (let i = 0; i < rows.length; i++) {
        if(rows[i].classList.contains('revisionBg')){rows[i].classList.remove('revisionSelectedRow');}
        else{rows[i].classList.remove('rowSelected');}
        
    }


    if(state===false){ 
        const descripcionCelda = element.querySelector('.thisDescription');
        const descripcionTexto = descripcionCelda ? descripcionCelda.textContent : 'Sin descripción';
        textArea.value = descripcionTexto;
        textArea.classList.remove('italic');
        element.classList.add(clName);

        fetchAndCreateButtons(element);
    }else{
        element.classList.remove(clName);
        textArea.value = '-- Selecciona una actividad --';
        textArea.classList.add('italic');
        
    }
}

function fetchAndCreateButtons(element) {
    const uId = element.getAttribute('u-d');
    const aId = element.getAttribute('a-d');
    const data = new URLSearchParams({
        uId: uId,
        aId: aId
    });
    
    fetch('../controller/activityManager.php?getActivityDetails=true', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: data
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const state = element.classList.contains('rowSelected') || element.classList.contains('revisionBg');
            if(state===true){ 
                if(data.revision === true){
                    createActionButtons(aId, true);
                }else{
                    createActionButtons(aId, false);
                }
            }
        }
    })
    .catch(error => {
         // Manejo de errores
         console.error('Error en la solicitud:', error);
    });
}

function createActionButtons(aId, type){
    //Crear botones de acción
    const buttonsDiv = document.createElement('div');
    buttonsDiv.classList.add('buttonsDiv');
    
    const button = document.createElement('button');
    button.type = 'button';
    if(type === true){
        button.id = 'botonValidar';
        button.classList.add('btn', 'btn-blue');
        button.textContent = 'Validar finalización';
        button.setAttribute('onclick', 'mostrarReporteV(this, true)');
        button.setAttribute('ac-d', aId);
        buttonsDiv.appendChild(button);

        const actDiv = document.querySelector('.activityButtonsDiv');
        actDiv.appendChild(buttonsDiv);
    }else{
        const url = `../controller/activityManager.php?doesItHaveReports=true`;
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                aId: aId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if(data.result){
                    if(document.getElementById('botonReportes')){
                        document.getElementById('botonReportes').remove();
                    }
                    button.id = 'botonReportes';
                    button.classList.add('btn', 'btn-yellow');
                    button.textContent = 'Ver reporte de actividad';
                    button.setAttribute('title', 'Generar reporte general de la actividad.');
                    button.setAttribute('onclick', 'mostrarReporteV(this, false)');
                    button.setAttribute('ac-d', aId);
                    buttonsDiv.appendChild(button);

                    const actDiv = document.querySelector('.activityButtonsDiv');
                    actDiv.appendChild(buttonsDiv);
                }
            }else{
                console.error('Error en la solicitud:', data.message);
            }
        })
        .catch(error => {
             // Manejo de errores
             console.error('Error en la solicitud:', error);
        });
    }
    
}

// Evento de doble clic sobre una actividad
function doubleClickRow(element){
    SelectThisRowAndDetails(element, "activity-list-body");
    if(element.classList.contains('rowSelected')){
        const textArea = document.getElementById('descriptionDetails');
        textArea.scrollIntoView({
            behavior: 'smooth'
        });
    }
}

function mostrarReporteV(element, type) {
    const trElement = element.closest('[ac-d]');
    const acId = trElement.getAttribute('ac-d');

    // Realizar la solicitud para obtener la información del reporte
    const url = `../controller/activityManager.php?getGenReportData=true&id_act=${acId}`;
    
    fetch(url, {
        method: 'POST',
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.exists) {
            const reportData = data.data;

            const reportContainer = document.createElement('div');
            reportContainer.classList.add('pdf-view-container');
            const reportContent = document.createElement('div');
            reportContent.classList.add('report-content');

            const opciones = `<div class='file-options'>
            <i class='fa fa-times-rectangle button' title='Cerrar' onclick='closeReportView()'></i>
            <i class='fa fa-download button' onclick='downloadGenReport(this)' title='Descargar reporte'></i>
            </div>`;
            reportContent.innerHTML = opciones;
            reportData.forEach(report => {
                const reportNameDiv = document.createElement('div');
                reportNameDiv.classList.add('nameAndSpace');
                reportNameDiv.innerHTML = `<div class="spacer">
                                                <div class="spacer-line"></div>
                                                <div class="reportName">${report.nombre}</div>
                                                <div class="spacer-line"></div>
                                            </div>`;
                reportContent.appendChild(reportNameDiv);

                // Insertar el contenido HTML directamente en lugar de intentar parsearlo
                const reportHtmlContent = document.createElement('div');
                reportHtmlContent.innerHTML = report.contenido; // Usar el HTML directamente
                reportContent.appendChild(reportHtmlContent);
            });

            reportContainer.appendChild(reportContent);
            document.body.appendChild(reportContainer);
        } else {
            console.error('No hay reportes para mostrar.');
        }
    })
    .catch(error => {
        console.error('Error en la solicitud AJAX:', error);
    });
}

function downloadGenReport(element) {
    createInputBox('Descargar reporte', 'Nombre del archivo:', null, 'Descargar', 'Cancelar')
        .then(reportName => {
            downloadFunction(reportName, true);
        })
        .catch(error => {
            if(error !== 'Input cancelado'){
                console.error(error);
            }
        });
}

function finishActivity(){
    const btn = document.querySelector('.finishBtn');
    const id = btn.getAttribute('acid');
    const url = `../controller/activityManager.php?finishAct=true&id_act=${id}`;

    createConfirmationDialog('Finalizando actividad','Confirma la finalización de esta actividad.', function() {
    
        makeAjaxRequest(
            url,
            'POST',
            null,
            function (data) { 
                try {
                    const response = data;
                    if (response.success) {
                        location.reload(true);
                    } else {
                        createAlertDialog('Acción no realizada', 'Error capturado:'+response.message, null, 'Aceptar');
                    }
                } catch (error) {
                    console.error('Error de JSON:', error);
                    createAlertDialog('Acción no realizada', 'Error inesperado al procesar la respuesta del servidor.', null, 'Aceptar');
                }
            },
            function (errorMessage) {
                console.error('Error al obtener la información de la actividad:', errorMessage);
                createAlertDialog('Acción no realizada', 'Ocurrió un error al establecer la conexión.\nPor favor, toma captura de pantalla y levanta un ticket de soporte.', null, 'Aceptar');
            }
        );
    });

}

// Actualizar el texto del nombre de encargado de la actividad - addForm
function updateRep(element) {
    const input = document.getElementById('responsableActividad'); 
    if (element.disabled === true) {
        input.value = document.getElementById('myId').value;
    } else {
        input.value = element.value;
    }
}

// Actualiza la descripcion del objetivo seleccionado - addForm
function updateObjectiveDescription(element) {
    const idName = element.id == 'objetivoList' ? 'ObjectiveDescription' : 'editObjectiveDescription';
    const idName2 = element.id == 'objetivoList' ? 'objetivoEnlazado' : 'editObjetivoEnlazado';
    const selectName = element.id == 'objetivoList' ? 'objectiveDescriptionList' : 'editObjectiveDescriptionList';
    const select = document.getElementById(selectName);
    select.value=element.value;
    if(element.selectedIndex == 0){
        var description = "";
        document.getElementById(idName).value = description;
        document.getElementById(idName2).value = "";
    }else{
        var selectedOption = select.options[select.selectedIndex];
        var description = selectedOption.text;
        document.getElementById(idName).value = description;
        document.getElementById(idName2).value = element.value;
    }
}

// Envia el formulario de agregar actividad - addForm
function submitNewActivity() {
    const isValid = validateActivityForm('Fname', 'Fdescription', 'Fdate', 'userRespList', 'objetivoList');
    if (!isValid) {
        return false;
    }
    
    // Código adicional para el envío del formulario nuevo
    const form = document.getElementById('activity-form');
    const actionUrl = `../controller/activityManager.php?addNew=true`;
    form.action = actionUrl;
    form.submit();
    return true;
}

// Envia el formulario de editar actividad - editForm
function submitEditActivity() {
    const isValid = validateActivityForm('editFname', 'editFdescription', 'editFdate', 'editUserRespList', 'editObjetivoList');
    if (!isValid) {
        return false;
    }
    
    // Código adicional para el envío del formulario nuevo
    const n = document.getElementById('editFname');
    let id = n.getAttribute('ac-id');
    id = parseInt(id);
    if(Number.isInteger(id)){
        const form = document.getElementById('edit-activity-form');
        const actionUrl = `../controller/activityManager.php?editActivity=true&editId=${id}`;
        form.action = actionUrl;
        form.submit();
        return true;
    }else{
        createAlertDialog('Acción no realizada', 'El id de la actividad ha sido modificado y es inválido.\nIntenta de nuevo.', null, 'Aceptar');
        return false;
    }
}


// Función común de validación con customValidity
function validateActivityForm(nameId, descriptionId, dateId, userRespId, objectiveId) {
    const nameInput = document.getElementById(nameId);
    const descriptionInput = document.getElementById(descriptionId);
    const dateInput = document.getElementById(dateId);
    const dateContainer = dateInput.parentElement;
    const selectObj = document.getElementById(objectiveId);
    const selectPer = document.getElementById(userRespId).disabled == false ? document.getElementById(userRespId) : false;
    // Validación del nombre de la actividad
    let nameFlag = testRegex(nameInput.id);
    if(nameFlag === false){
        return false;
    }
    nameFlag = testLenght('min', 8, nameInput.id);
    if(nameFlag === false){
        return false;
    }
    nameFlag = testLenght('max', 45, nameInput.id);
    if(nameFlag === false){
        return false;
    }
    nameFlag = testValue('strict', nameInput.id, 'actividad');
    if(nameFlag === false){
        return false;
    }

    let descriptionFlag = testControlledTextInput(descriptionInput.id);
    if(descriptionFlag === false){
        return false;
    }
    descriptionFlag = testLenght('min', 20, descriptionInput.id);
    if(descriptionFlag === false){
        return false;
    }
    descriptionFlag = testLenght('max', 1000, descriptionInput.id);
    if(descriptionFlag === false){
        return false;
    }
    descriptionFlag = testValue('light', descriptionInput.id);
    if(descriptionFlag === false){
        return false;
    }

    const iniDate = new Date(dateContainer.getAttribute('ini-date'));
    const endDate = new Date(dateContainer.getAttribute('end-date'));

    if(dateInput.value != '' && dateInput.value != null){
        const date = new Date(dateInput.value);
        if (!validateDate(date, iniDate)) {
            dateInput.setCustomValidity('La fecha estimada debe ser posterior a la fecha de inicio del proyecto.');
            dateInput.reportValidity();
            return false;
        }
        if (!validateDate(endDate, date)) {
            dateInput.setCustomValidity('La fecha estimada debe ser previa a la fecha de cierre del proyecto.');
            dateInput.reportValidity();
            return false;
        }
    }else{
        dateInput.setCustomValidity('Fecha invalida. Por favor, selecciona una fecha válida para la actividad');
        dateInput.reportValidity();
        return false;
    }

    
    if(selectPer == false || selectPer.value == 'none'){
        selectPer.setCustomValidity('Selecciona un responsable para continuar.');
        selectPer.reportValidity();
        return false;
    }

    if(selectObj.value == 'none'){
        selectObj.setCustomValidity('Selecciona un objetivo para continuar.');
        selectObj.reportValidity();
        return false;
    }

    return true;
}

// Eliminar actividad
function DeleteActivity(id, rep) {
    createConfirmationDialog('Eliminando actividad','¿Estás seguro que deseas eliminar esta actividad?', function() {
        //Actualizar a AJAX cuando tenga tiempo.
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '../controller/activityManager.php';

        const deleteInput = document.createElement('input');
        deleteInput.type = 'hidden';
        deleteInput.name = 'delete';
        deleteInput.value = 'true';
        form.appendChild(deleteInput);

        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        idInput.value = id;
        form.appendChild(idInput);

        const repInput = document.createElement('input');
        repInput.type = 'hidden';
        repInput.name = 'rep';
        repInput.value = rep;
        form.appendChild(repInput);

        // Agregar el formulario al documento y enviarlo
        document.body.appendChild(form);
        form.submit();
    });
}

// agrega el evento para abrir el formulario de edición
const editButtons = document.querySelectorAll('.editActivityJs');
editButtons.forEach(button => {
    button.addEventListener('click', function() {
        createEditForm(button); 
    });
});

// Funcion para crear el formulario de edición
function createEditForm(element) {
    let closestElement = element;
    while (closestElement && !closestElement.hasAttribute('u-d')) {
        closestElement = closestElement.parentElement;
    }
    idAct = closestElement.getAttribute('a-d');
    
    // Verificar si el formulario ya existe
    if (document.getElementById('editActivityForm')) {
        console.log('El formulario ya existe');
        return;
    }

    const today = new Date().toISOString().split('T')[0];

    // Crear el formulario 
    const formContainer = document.createElement('div');
    formContainer.id = 'editActivityForm';
    formContainer.className = 'editActivity-form';

    formContainer.innerHTML = `
        <div id="editActivity-form" class="addActivity-form">
        <form class="activity-form" id="edit-activity-form" onsubmit="return submitEditActivity()" method="POST">
            <div class="formContainer">
                <div class="title" ><h4>Editar Actividad:</h4></div>
                <input type="text" class="input" name="Fname" id="editFname" placeholder="Nombre de la actividad" oninput="resetField(this)">
                <textarea class="textarea" name="Fdescription" id="editFdescription" placeholder="Descripción de la actividad" oninput="resetField(this)"></textarea>
                
                <div class="fm-content">
                    <div class="section1">
                        <div class="dates">
                            <label for="editFdate">Fecha estimada de finalización:</label>
                            <br>
                            <input type="date" name="Fdate" class="dateCalendar" id="editFdate" value="${today}" oninput="resetField(this)" lang="es">
                        </div>  
                    </div>
                    <div class="selectDiv section2">
                        <label for="editUserRespList">Responsable:</label><br>
                        <select name="userRespList" class='comboBox repSelectCx' id="editUserRespList" oninput="resetField(this)">
                            
                        </select>
                    </div>
                </div>
                <div class="selectDiv">
                    <br><label for="objetivoList" class="lbl">Actividad relacionada al objetivo:</label>
                    <select name='objetivoList' id='editObjetivoList' class='comboBox' onchange='resetField(this);updateObjectiveDescription(this)'>
                    </select>
                    <select class='hide' name='objectiveDescriptionList' id='editObjectiveDescriptionList'>
                    </select>
                    <input type="hidden" name="objetivoEnlazado" id="editObjetivoEnlazado">

                </div>
                <textarea disabled type="text" class="textarea objetivoDisplay" name="ObjectiveDescription" id="editObjectiveDescription"></textarea>
                <div class="form-options">
                    <button class="sumbit-newTask enabled" type="submit">Guardar cambios</button>
                    <button class="close-newTask button" onclick="closeEditForm()">Cancelar</button>
                </div>
            </div>
        </form>
        </div>
    `;

    formContainer.style = "display: none;"
    
    const divH = document.querySelector('.activityManagement ');
    divH.appendChild(formContainer);
    setTimeout(() => {
        setOptionsRep('editUserRespList');
        setOptionsObjectives('editObjetivoList');
        setLimitDates();
        updateFormData(idAct);
    }, 150);
    formContainer.style = "display: flex;"
}

// Destruir el formulario de edición
function closeEditForm() {
    const form = document.getElementById('editActivityForm');
    if (form) {
        form.remove(); 
    }
}

// Crear la lista de usuarios disponibles
function setOptionsRep(idName){
    const select = document.getElementById(idName);
    const url = `../controller/activityManager.php?getMembers=true`;
    makeAjaxRequest(
        url,
        'POST',
        null,
        function (data) { 
            // Vaciar el <select> antes de llenarlo
            select.innerHTML = '';

            // Crear una opción por defecto
            const defaultOption = document.createElement('option');
            defaultOption.value = 'none';
            defaultOption.setAttribute('ob-id', 'none');
            defaultOption.textContent = '- Selecciona un responsable -';
            select.appendChild(defaultOption);

            // Llenar el <select> con los integrantes del proyecto
            data.members.forEach(member => {
                const option = document.createElement('option');
                option.value = member.id_usuario;
                option.textContent = member.nombre;
                select.appendChild(option);
            });
        },
        function (errorMessage) { // errorCallback
            console.error('Error al obtener la información de la actividad:', errorMessage);
        }
    );
}

// Crear la lista de objetivos disponibles
function setOptionsObjectives(idName){
    const select = document.getElementById(idName);
    const desSelect = document.getElementById('editObjectiveDescriptionList');
    const url = `../controller/activityManager.php?getObjectives=true`;
    makeAjaxRequest(
        url,
        'POST',
        null,
        function (data) {
            // Vaciar el <select> antes de llenarlo
            select.innerHTML = '';

            // Crear una opción por defecto
            const defaultOption = document.createElement('option');
            defaultOption.value = 'none';
            defaultOption.setAttribute('ob-id', 'none');
            defaultOption.textContent = '- Selecciona un objetivo -';
            select.appendChild(defaultOption);

            // Llenar el <select> con los objetivos del proyecto
            let piv = 1;
            data.objectives.forEach(objective => {
                const option = document.createElement('option');
                option.value = piv;
                option.textContent = `Objetivo: ${piv}`
                option.setAttribute('ob-id', objective.id_objetivo);
                select.appendChild(option);
                
                const optionDes = document.createElement('option');
                optionDes.value = piv;
                optionDes.textContent = objective.contenido;
                desSelect.appendChild(optionDes);
                piv++;
            });
        },
        function (errorMessage) { // errorCallback
            console.error('Error al obtener la información de la actividad:', errorMessage);
        }
    );

}

// Recupera la información de la actividad para mstrarla en el formulario
function updateFormData(activityId) {
    const url = `../controller/activityManager.php?getActivityInfo=true&activityId=${encodeURIComponent(activityId)}`;
    
    makeAjaxRequest(url, 'POST', null, function(response) {
        // Verificar respuesta
        if (response && response.success) {
            const data = response.data;

            const updateFieldValue = (fieldId, value) => {
                const field = document.getElementById(fieldId);
                if (field) {
                    field.value = value || '';
                }
            };

            // Llenar el formulario con los datos de la actividad
            
            updateFieldValue('editFname', data.nombre_actividad);
            updateFieldValue('editFdescription', data.descripción);
            updateFieldValue('editFdate', data.fecha_estimada);
            updateFieldValue('editUserRespList', data.id_usuario);
            updateFieldValue('editObjectiveDescription', data.objetivo_descripcion);

            document.getElementById('editFname').setAttribute('ac-id', activityId);

            const select = document.getElementById('editObjetivoList');
            if (select) {
                let foundOption = false;
                for (let i = 0; i < select.options.length; i++) {
                    const option = select.options[i];
                    if (option.getAttribute('ob-id') == data.id_objetivo) {
                        select.selectedIndex = i;
                        foundOption = true;
                        break;
                    }
                }
                if (!foundOption) {
                    console.warn('Objetivo no encontrado en la lista de opciones.');
                }
            } else {
                console.warn('Elemento select "editObjetivoList" no encontrado.');
            }
            updateFieldValue('ObjectiveDescription', data.objectiveDescription || 'Descripción no disponible');
        } else {
            console.error('Respuesta inválida o no exitosa:', response);
            createAlertDialog('¡Error!', 'No se pudo cargar la actividad.', null, 'Aceptar');
        }
    },
    function(error) {
        // Manejo de errores
        console.error('Error en la solicitud:', error);
    });
}

// Recupera las fechas de inicio y fin del proyecto
function setLimitDates(){
    const url = `../controller/activityManager.php?getProjectDates=true`;
    
    makeAjaxRequest(url, 'POST', null, function(response) {
        // Verificar respuesta
        if (response && response.success) {
            const data = response.data;

            const dateInput = document.getElementById('editFdate');
            const datesDiv = dateInput.parentElement;
            
            datesDiv.setAttribute('ini-date', data.fecha_inicio);
            datesDiv.setAttribute('end-date', data.fecha_cierre);
        } else {
            console.error('Respuesta inválida o no exitosa:', response);
            createAlertDialog('¡Error!', 'No se pudo cargar la actividad.', null, 'Aceptar');
        }
    },
    function(error) {
        // Manejo de errores
        console.error('Error en la solicitud:', error);
    });
}

function applyBulkAction(){
    const select = document.getElementById('actionSelected');
    const selectedAct = document.querySelectorAll('.activity-checkbox:checked');
    let msj=null;
    if(select.value === 'delete' && selectedAct.length >= 1){ 
        let acti = selectedAct.length === 1 ? 'actividad' : 'actividades';
        msj = `¿Estás seguro de querer eliminar ${selectedAct.length} ${acti}?` }
    if(msj !== null){
        createConfirmationDialog('Confirmar acción', msj, 
            ()=>{
                const activityIds = Array.from(selectedAct).map(actividad => actividad.value).join(',');
                const url = `../controller/activityManager.php?submitBulkActivity=true`;
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        ids: activityIds
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        createAlertDialog('¡Completado!', 'Actividades eliminadas correctamente.', null, 'Aceptar');
                        location.reload();
                    }else{
                        console.error('Error en la solicitud:', data.message);
                    }
                })
                .catch(error => {
                     // Manejo de errores
                     console.error('Error en la solicitud:', error);
                });
            },()=>{}
        );
    }
}

function addApplyEvents(){
    const btn1 = document.getElementById('applyAction');
    const btn2 = document.getElementById('applyAction2');
    btn1.addEventListener('click', ()=>{applyBulkAction()});
    btn2.addEventListener('click', ()=>{applyBulkAction()});
}

document.addEventListener('DOMContentLoaded', function() {
    paginateTable('activity-list-body', 8, 'pagination');
    addApplyEvents();
})