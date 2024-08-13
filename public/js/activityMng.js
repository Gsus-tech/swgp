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
    const filterValue = selectElement.value;
    const tbody = document.getElementById('activity-list-body');
    const rows = tbody.getElementsByTagName('tr');

    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        const estado = row.cells[2].textContent;
        
        if (filterValue === 'noFilter' || estado === filterValue) {
            row.classList.remove('hide');
        } else {
            row.classList.add('hide');
        }
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
    const select = document.getElementById('objectiveDescriptionList');
    select.value=element.value;
    if(element.selectedIndex == 0){
        var description = "";
        document.getElementById('ObjectiveDescription').value = description;
        document.getElementById('objetivoEnlazado').value = "";
    }else{
        var selectedOption = select.options[select.selectedIndex];
        var description = selectedOption.text;
        document.getElementById('ObjectiveDescription').value = description;
        document.getElementById('objetivoEnlazado').value = element.value;
    }
}

function getDateSelected(){
    const d = parseInt(document.getElementById('dia_meta').value);
    const m = parseInt(document.getElementById('mes_meta').value);
    const y = parseInt(document.getElementById('anio_meta').value);

    return new Date(y, m - 1, d);
}

//Agregar actividad - verificacion de los campos:
function submitNewActivity(){    
    //Validar objetivo seleccionado
    const selectObj = document.getElementById('objetivoList');
    const selectPer = document.getElementById('userRespList').disabled == false ? document.getElementById('userRespList') : false;
       
    if(selectObj.value == 'noObjectivesRegister'){
        selectObj.setCustomValidity('No hay objetivos registrados.\nSolicita al administrador agregar un objetivo del proyecto e intenta de nuevo.');
        selectObj.reportValidity();
        return false;
    }
    
    //Validar nombre
    let nameFlag = testRegex('Fname');
    if(nameFlag === false){
        return false;
    }
    nameFlag = testLenght('min', 8, 'Fname');
    if(nameFlag === false){
        return false;
    }
    nameFlag = testLenght('max', 45, 'Fname');
    if(nameFlag === false){
        return false;
    }
    nameFlag = testValue('strict', 'Fname', 'actividad');
    if(nameFlag === false){
        return false;
    }

    //Validar descripcion
    let descriptionFlag = testControlledTextInput('Fdescription');
    if(descriptionFlag === false){
        return false;
    }
    descriptionFlag = testLenght('min', 20, 'Fdescription');
    if(descriptionFlag === false){
        return false;
    }
    descriptionFlag = testLenght('max', 1000, 'Fdescription');
    if(descriptionFlag === false){
        return false;
    }
    descriptionFlag = testValue('light', 'Fdescription');
    if(descriptionFlag === false){
        return false;
    }

    //Validar fecha
    if(document.getElementById('noDateSelected').checked == false){
        let [year, month, day] = document.getElementById('projectInitDate').value.split('-');
        const d1 = new Date(year, month -1, day);
        [year, month, day] = document.getElementById('projectFinDate').value.split('-');
        const d2 = new Date(year, month -1, day);
        const date = getDateSelected();
        const rp = document.getElementById('mes_meta');
        if(!validateDate(date, d1)){
            rp.setCustomValidity('La fecha estimada debe ser posterior a la fecha de inicio del proyecto.');
            rp.reportValidity();
            return false;
        }
        if(!validateDate(d2, date)){
            rp.setCustomValidity('La fecha estimada debe ser previa a la fecha de cierre del proyecto.');
            rp.reportValidity();
            return false;
        }   
    }

    if(selectPer == false && selectPer.value == 'none'){
        selectPer.setCustomValidity('Selecciona un responsable para continuar.');
        selectPer.reportValidity();
        return false;
    }

    if(selectObj.value == 'none'){
        selectObj.setCustomValidity('Selecciona un objetivo para continuar.');
        selectObj.reportValidity();
        return false;
    }

    const form = document.getElementById('activity-form');
    const actionUrl = `../controller/activityManager.php?addNew=true`;
    form.action = actionUrl;
    form.submit();
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

document.addEventListener('DOMContentLoaded', function() {
    paginateTable('activity-list-body', 8, 'pagination');
    
})