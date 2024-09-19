function FiltersToggle(){
    const filterDiv = document.getElementById('filterDiv');
    const filtros = document.querySelectorAll('.dropDownFilter');

    filterDiv.classList.toggle('closedFilterDiv');
    filterDiv.classList.toggle('openFilterDiv');

    filtros.forEach(filtro => {
        filtro.classList.toggle('hide');
    });
}

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


function openAddForm(){
    const filtros = document.getElementById('filterDiv');
    filtros.scrollIntoView({
        behavior: 'smooth'
    });
    const formDiv = document.getElementById('addActivity-form');
    formDiv.classList.remove('hide');
}

function confirmCancelEdit(){
    if(confirm('Cerrar formulario sin guardar los cambios?')){
        const form = document.getElementById('activity-form');
        const formDiv = document.getElementById('addActivity-form');
        form.reset();
        formDiv.classList.add('hide');
    }
}

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

function SelectThisRowAndDetails(element, tbodyName){
    const tbody = document.getElementById(`${tbodyName}`);
    const rows = tbody.getElementsByTagName('tr');
    const state = element.classList.contains('rowSelected');
    const textArea = document.getElementById('descriptionDetails');

    for (let i = 0; i < rows.length; i++) {
        if(rows[i].classList.contains('rowSelected')){
            rows[i].classList.remove('rowSelected');
        }
    }
    if(state===false){ 
        const descripcionCelda = element.querySelector('.thisDescription');
        const descripcionTexto = descripcionCelda ? descripcionCelda.textContent : 'Sin descripción';
        textArea.value = descripcionTexto;
        textArea.classList.remove('italic');
        element.classList.add('rowSelected');
    }else{
        element.classList.remove('rowSelected');
        textArea.value = '-- Selecciona una actividad --';
        textArea.classList.add('italic');
    }
}

function doubleClickRow(element){
    SelectThisRowAndDetails(element, "activity-list-body");
    if(element.classList.contains('rowSelected')){
        const textArea = document.getElementById('descriptionDetails');
        textArea.scrollIntoView({
            behavior: 'smooth'
        });
    }
}

function switchDatesState(){
    const dia = document.getElementById("dia_meta");
    const mes = document.getElementById("mes_meta");
    const anio = document.getElementById("anio_meta");
    const state = dia.disabled===true ? true : false;
    dia.disabled = state==true ? false : true;
    mes.disabled = state==true ? false : true;
    anio.disabled = state==true ? false : true;
}

function switchRepState(element){
    const select = document.getElementById('userRespList');
    if(select.classList.contains('noRepsEncountered')){
        element.setCustomValidity('Debido a la falta de miembros deberás ser el responsable de la actividad.');
        element.reportValidity();
        element.checked = true;

        setTimeout(() => {
            element.setCustomValidity('');
        }, 4000);
    }
    else if(element.checked == true){
        select.disabled = true;
        select.value = 'I';
        updateRep(document.getElementById('userRespList'));
    }
    else if(element.checked == false){
        select.disabled = false;
        select.selectedIndex = 0;
    }
}

function updateRep(element) {
    const input = document.getElementById('responsableActividad'); 
    if (element.disabled === true) {
        input.value = document.getElementById('myId').value;
    } else {
        input.value = element.value;
    }
}

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

function getDateSelected(){
    const d = parseInt(document.getElementById('dia_meta').value);
    const m = parseInt(document.getElementById('mes_meta').value);
    const y = parseInt(document.getElementById('anio_meta').value);

    return new Date(y, m - 1, d);
}



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


function submitEditActivity() {
    console.log('Validando datos de actividad');
    const isValid = validateActivityForm('editFname', 'editFdescription', 'editFdate', 'editUserRespList', 'editObjetivoList');
    if (!isValid) {
        return false;
    }
    
    // Código adicional para el envío del formulario nuevo
    const n = document.getElementById('editFname');
    let id = n.getAttribute('ac-id');
    id = parseInt(id);
    console.log(`id actividad: ${id}`);
    if(Number.isInteger(id)){
        const form = document.getElementById('edit-activity-form');
        console.log(`../controller/activityManager.php?editActivity=true&editId=${id}`);
        const actionUrl = `../controller/activityManager.php?editActivity=true&editId=${id}`;
        form.action = actionUrl;
        form.submit();
        return true;
    }else{
        alert('El id de la actividad ha sido modificado y es inválido.\nIntenta de nuevo.');
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




function DeleteActivity(id, rep) {
    if (confirm("¿Estás seguro de que deseas eliminar esta actividad?")) {
        // Crear un formulario
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
    }
}

const editButtons = document.querySelectorAll('.editActivityJs');
editButtons.forEach(button => {
    button.addEventListener('click', function() {
        createEditForm(button); 
    });
});

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
                        <select name="userRespList" class='comboBox' id="editUserRespList" oninput="resetField(this)">
                            
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
    setOptionsRep('editUserRespList');
    setOptionsObjectives('editObjetivoList');
    setLimitDates();
    updateFormData(idAct);
    formContainer.style = "display: flex;"
}

// Destruir el formulario de edición
function closeEditForm() {
    const form = document.getElementById('editActivityForm');
    if (form) {
        form.remove(); 
    }
}

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
            alert('No se pudo cargar la actividad.');
        }
    },
    function(error) {
        // Manejo de errores
        console.error('Error en la solicitud:', error);
    });
}

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
            alert('No se pudo cargar la actividad.');
        }
    },
    function(error) {
        // Manejo de errores
        console.error('Error en la solicitud:', error);
    });
}


document.addEventListener('DOMContentLoaded', function() {
    paginateTable('activity-list-body', 8, 'pagination');
    
})