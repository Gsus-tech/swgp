function confirmCancel(){
    const btn = document.getElementById('sumbit-editProject');
    if(btn.classList.contains('enabled')){
        if(confirm('¡Advertencia!\nNo se guardarán los cambios realizados.\n¿Deseas continuar?')){
            window.location.href = `projectsManagement.php`;
        }
    }else{
        window.location.href = `projectsManagement.php`;
    }
}

function activateBtn(){
    const btn = document.getElementById('sumbit-editProject');
    if(!btn.classList.contains('enabled')){
        btn.disabled = false;
        btn.classList.add('enabled')
    }
}
function deactivateBtn(){
    const btn = document.getElementById('sumbit-editProject');
    if(btn.classList.contains('enabled')){
        btn.disabled = true;
        btn.classList.remove('enabled')
    }
}

function updateBasicInfo(){
    console.log('Enviando el formulario');
    event.preventDefault();
    revertDate('thisDate_inicio', 'displayDate1');
    revertDate('thisDate_cierre', 'displayDate2');
    const form = document.getElementById('editProject-form');
    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('editProject');
    const actionUrl = `../controller/projectManager.php?editProject=true&id=${id}`;
    form.action = actionUrl;
    form.submit();
}

function initialDate(){
    document.getElementById('initDatePicker').classList.toggle('hide');
    document.getElementById('inDt-edit').classList.toggle('hide');
    document.getElementById('inDt-save').classList.toggle('hide');
    document.getElementById('inDt-cancel').classList.toggle('hide');
}

function finalDate(){
    document.getElementById('endDatePicker').classList.toggle('hide');
    document.getElementById('fnDt-edit').classList.toggle('hide');
    document.getElementById('fnDt-save').classList.toggle('hide');
    document.getElementById('fnDt-cancel').classList.toggle('hide');
}

function validateDate(dateIni, dateFin){
    return dateIni <= dateFin;
}

function saveDate1() {
    const today = new Date();
    const diaInicio = parseInt(document.getElementById("dia_inicio").value);
    const mesInicio = parseInt(document.getElementById("mes_inicio").value);
    const anioInicio = parseInt(document.getElementById("anio_inicio").value);
    const date = new Date(anioInicio, mesInicio - 1, diaInicio);

    const valid = validateDate(today, date);

    if (valid) {
        const d1 = document.getElementById('displayDate1');
        document.getElementById('thisDate_inicio').value = date;
        d1.textContent = date;
        convertDate('displayDate1');
        initialDate();
        const date2 = new Date(document.getElementById('thisDate_cierre').value);
        const valid2 = validateDate(date, date2);
        if(valid2){
            const date2 = new Date(anioInicio, mesInicio - 1, diaInicio+1);
            const d2 = document.getElementById('displayDate2');
            d2.textContent = date2;
            convertDate('displayDate2');
            activateBtn();
        }
    } else {
        alert("La fecha de inicio debe ser mayor o igual a la fecha actual.");
    }
}


function saveDate2(){
    const diaC = parseInt(document.getElementById("dia_cierre").value);
    const mesC = parseInt(document.getElementById("mes_cierre").value);
    const anioC = parseInt(document.getElementById("anio_cierre").value);
    const init = new Date(document.getElementById('thisDate_inicio').value);
    const date = new Date(anioC, mesC - 1, diaC);

    const valid = validateDate(init, date);

    if (valid) {
        const d2 = document.getElementById('displayDate2');
        document.getElementById('thisDate_cierre').value = date;
        d2.textContent = date;
        convertDate('displayDate2');
        finalDate();
        activateBtn();
    } else {
        alert("La fecha de cierre debe ser mayor a la fecha de inicio.");
    }
}

function formatSpanishDate(dateString) {
    //Formatear fecha a español
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('es-ES', { day: 'numeric', month: 'long', year: 'numeric' }).format(date);
}

function convertDate(element) {
    const d1 = document.getElementById(element);
    const currentDate = d1.textContent.trim();
    const formattedDate = formatSpanishDate(currentDate);
    d1.textContent = formattedDate;
}

function revertDate(element, content){
    const d = document.getElementById(content);
    const d1 = document.getElementById(element);
    const currentDate = d.textContent.trim();
    const formattedDate = parseSpanishDate(currentDate);
    d1.value = formattedDate;
    console.log(d1.value);
}

function parseSpanishDate(dateString) {
    const months = {
        "enero": "01",
        "febrero": "02",
        "marzo": "03",
        "abril": "04",
        "mayo": "05",
        "junio": "06",
        "julio": "07",
        "agosto": "08",
        "septiembre": "09",
        "octubre": "10",
        "noviembre": "11",
        "diciembre": "12"
    };

    // Extraer partes de la fecha
    const [day, month, year] = dateString.toLowerCase().split(' de ');
    const monthNumber = months[month];
    const dayPadded = day.padStart(2, '0');

    // Formatear a YYYY-MM-DD
    return `${year}-${monthNumber}-${dayPadded}`;
}

//Actualizar departamento asignado
function updateDeptoInput(event) {
    const eFdpto = document.getElementById('deptoAssign');
    const eFdptoText = eFdpto.options[eFdpto.selectedIndex].text;
    const eFdptoHidden = document.getElementById('eFdptoText');

    eFdptoHidden.value = eFdptoText;
}

//Filtrar usuarios disponibles
function filtrarUsuariosPorDepartamento() {
    const departamento = document.getElementById('filtroDepartamento').value;
    const usuariosSelect = document.getElementById('listaUsuariosDisponibles');
    let firstVisible = true;

    const opciones = usuariosSelect.getElementsByTagName('option');

    for (let i = 0; i < opciones.length; i++) {
        const opcion = opciones[i];
        const depto = opcion.getAttribute('data-depto');
        
        if (departamento === 'noFilter' || departamento === depto) {
            opcion.style.display = 'block';
            if (firstVisible) {
                opcion.selected = true;
                firstVisible = false;
            }
        } else {
            opcion.style.display = 'none';
        }
    }
}

///Agregar integrante de proyecto
function agregarMiembro(projectId) {
    const usuarioId = document.getElementById('listaUsuariosDisponibles').value;
    const usuarioNombre = document.getElementById('listaUsuariosDisponibles').selectedOptions[0].text;
    const tipoMiembro = document.getElementById('tipoMiembro').value;
    const tipoMiembroTexto = tipoMiembro == '1' ? 'Responsable' : 'Colaborador';
    const tablaBody = document.getElementById('members-list-body');
    const usuariosSelect = document.getElementById('listaUsuariosDisponibles');

    // Verificar y eliminar la fila "No se encontraron integrantes registrados" si existe
    const noIntegrantesRow = document.getElementById('no-integrantes-row');
    if (noIntegrantesRow) {
        noIntegrantesRow.remove();
    }

    // Crear nueva fila y añadirla a la tabla
    const nuevaFila = document.createElement('tr');
    const nombreCelda = document.createElement('td');
    const rolCelda = document.createElement('td');
    const removeBtn = document.createElement('td');

    nombreCelda.textContent = usuarioNombre;
    rolCelda.textContent = tipoMiembroTexto;
    removeBtn.innerHTML = `<a class='fa fa-user-times removeMemberBtn' title='Remover integrante' onclick='ConfirmDeleteMember(${usuarioId}, this)'></a>`;
    nuevaFila.appendChild(nombreCelda);
    nuevaFila.appendChild(rolCelda);
    nuevaFila.appendChild(removeBtn);
    tablaBody.appendChild(nuevaFila);

    // Eliminar la opción del usuario del select de usuarios disponibles
    const opcionAEliminar = usuariosSelect.querySelector(`option[value="${usuarioId}"]`);
    if (opcionAEliminar) {
        opcionAEliminar.remove();
    }
    const addedMembersInput = document.getElementById('addedMembers');
    let addedMembers = JSON.parse(addedMembersInput.value || '[]');
    rol = tipoMiembroTexto == 'Colaborador' ? false : true;
    addedMembers.push({ usuarioId, rol });
    activateBtn();
    membersTableChanged('add');
    actualizarCamposOcultos('addedMembers', addedMembers);
}

//Eliminar miembro
function ConfirmDeleteMember(idUsuario, buttonElement) {
    if (confirm('¿Estás seguro de que deseas eliminar este miembro?')) {
        const fila = buttonElement.closest('tr');
        fila.remove();
        const removedMembersInput = document.getElementById('removedMembers');
        let removedMembers = JSON.parse(removedMembersInput.value || '[]');
        removedMembers.push({ idUsuario });
        console.log(JSON.stringify(removedMembers));
        membersTableChanged('del');
        actualizarCamposOcultos('removedMembers', removedMembers);
    }
}

function membersTableChanged(action){
    const tableChangedInput = document.getElementById(action=='add'?'membersTableFlagAdd':'membersTableFlagDel');
    tableChangedInput.value = "true";
}


function actualizarCamposOcultos(name, element) {
    const addedMembersInput = document.getElementById(name);
    addedMembersInput.value = JSON.stringify(element);
}

//Agregar Objetivos
function agregarObjetivo(projectId, tipo){
    const tablaBody = document.getElementById(tipo=='general'?'objectiveG-list-body':'objectiveE-list-body');
    const contenido = document.getElementById(tipo=='general'?'objetivoG':'objetivoE');

    if(contenido.value.length < 10){
        alert('Longitud mínima de 10 caracteres para la descripción del objetivo.');
    }else{
        // Verificar y eliminar la fila "No se encontraron objetivos registrados" si existe
        const noObjGRow = document.getElementById(tipo=='general'?'no-objectiveG-row':'no-objectiveE-row');
        if (noObjGRow) {
            noObjGRow.remove();
        }

        //Calcular el nuevo ID del nuevo objetivo
        const rows = tablaBody.getElementsByTagName('tr');
        let maxId = 0;

        for (let i = 0; i < rows.length; i++) {
            const idObjetivo = parseInt(rows[i].getAttribute('value'));
            if (idObjetivo > maxId) {
                maxId = idObjetivo;
            }
        }

        // Crear un nuevo ID
        const newId = maxId + 1;


        // Crear nueva fila y añadirla a la tabla
        const nuevaFila = document.createElement('tr');
        nuevaFila.setAttribute('value', newId);
        const descriptionCelda = document.createElement('td');
        descriptionCelda.setAttribute('class','descripcion');
        const removeBtn = document.createElement('td');


        descriptionCelda.textContent = contenido.value;
        removeBtn.innerHTML = 
        `<a class='fa fa-trash removeMemberBtn' title='Eliminar objetivo' onclick=\"DeleteObjective(this,'${tipo}',${projectId}, ${newId})\"></a>
        <a class='fa fa-edit tableIconBtn mt1r' title='Editar objetivo' onclick=\"EditObjective(this)\"></a>
        <a id='saveChangesObj' class='fa fa-save tableIconBtn mt1r hide' title='Guardar cambios' onclick=\"SaveObjectiveChanges(this,'${tipo}',${projectId},${newId})\"></a>`;
        nuevaFila.appendChild(descriptionCelda);
        nuevaFila.appendChild(removeBtn);
        tablaBody.appendChild(nuevaFila);

        //Guardar los datos en input hidden para actualizar la BD al confirmar los cambios
        const addedObjectivesInput = document.getElementById(tipo=='general'?'addedObjG':'addedObjE');
        let addedObjectives = JSON.parse(addedObjectivesInput.value || '[]');
        const content = contenido.value;
        addedObjectives.push({ newId,  content});
        actualizarCamposOcultos(tipo=='general'?'addedObjG':'addedObjE', addedObjectives);
        objectivesTableChanged(tipo, 'add');
        activateBtn();

        contenido.value = "";
    }
}

//Eliminar objetivos
function DeleteObjective(element, type, projectId, objId){
    console.log('tipo: ' + type + "\nProyecto: "+projectId);
    if(confirm(`¿Estás seguro de que deseas eliminar este objetivo '${type}'?`)){
        const fila = element.closest('tr');
        fila.remove();
        const removedObjInput = document.getElementById(type=='general'?'removedObjG':'removedObjE');
        let removedObj = JSON.parse(removedObjInput.value || '[]');
        removedObj.push({ objId });
        objectivesTableChanged(type, 'del');
        actualizarCamposOcultos(type=='general'?'removedObjG':'removedObjE', removedObj);
        console.log(JSON.stringify(removedObj));

    }
}

function objectivesTableChanged(type, action){
    if(type=='general'){
        const tableChangedInput = document.getElementById(action=='add'?'objGTableFlagAdd':'objGTableFlagDel');
        tableChangedInput.value = "true";
    }
    if(type=='especifico'){
        const tableChangedInput = document.getElementById(action=='add'?'objETableFlagAdd':'objETableFlagDel');
        tableChangedInput.value = "true";
    }
}

function objectivesTableUpdated(type){
    if(type=='general'){
        const tableChangedInput = document.getElementById('objGTableFlagUpd');
        tableChangedInput.value = "true";
    }
    if(type=='especifico'){
        const tableChangedInput = document.getElementById('objETableFlagUpd');
        tableChangedInput.value = "true";
    }
}

//editar objetivo
function EditObjective(button) {
    const fila = button.closest('tr');
    const descripcionCelda = fila.querySelector('.descripcion');

    // crear un textarea para editar la descripción
    const textarea = document.createElement('textarea');
    textarea.classList.add('editable');
    textarea.value = descripcionCelda.textContent;
    
    // Encimar el textarea para editar el objetivo
    descripcionCelda.textContent = '';
    descripcionCelda.appendChild(textarea);

    button.classList.add('hide');
    fila.querySelector('.fa-save').classList.remove('hide');
}
//Guardar cambios
function SaveObjectiveChanges(button, tipo, idProyecto, idObjetivo) {
    const fila = button.closest('tr');
    const descripcionCelda = fila.querySelector('.descripcion');
    const textarea = descripcionCelda.querySelector('textarea');

    // Obtener el nuevo valor de la descripción
    const nuevaDescripcion = textarea.value;
    
    // Ocultar el textarea y sobrescribir los datos
    descripcionCelda.textContent = nuevaDescripcion;
    button.classList.add('hide');
    fila.querySelector('.fa-edit').classList.remove('hide');

    const updatedObjInput = document.getElementById(tipo=='general'?'updatedObjG':'updatedObjE');
    let updatedObj = JSON.parse(updatedObjInput.value || '[]');
    updatedObj.push({ idObjetivo, tipo, nuevaDescripcion});
    objectivesTableUpdated(tipo);
    actualizarCamposOcultos(tipo=='general'?'updatedObjG':'updatedObjE', updatedObj);
}


document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById('editProject-form');
    const inputs = form.getElementsByTagName('input');
    const textareas = form.getElementsByTagName('textarea');
    const selectDto = document.getElementById('deptoAssign');
    const date1 = document.getElementById('displayDate1');
    const date2 = document.getElementById('displayDate2');
    const initialValues = new Map();

    function init() {
        console.log("La página ha cargado completamente");
        convertDate('displayDate1');
        convertDate('displayDate2');
    }
    function addEvents(){
        for (let input of inputs) {
            initialValues.set(input, input.value);
        }    
        for (let textarea of textareas) {
            if (textarea.id != 'objetivoG' && textarea.id != 'objetivoE') {
                initialValues.set(textarea, textarea.value);
            }
        }
        initialValues.set(selectDto, selectDto.value)
        initialValues.set(date1, date1.value)
        initialValues.set(date2, date2.value)
        for (let input of inputs) {
            input.addEventListener('input', procesarEvento);
        }
        for (let textarea of textareas) {
            if (textarea.id != 'objetivoG' && textarea.id != 'objetivoE') {
                textarea.addEventListener('input', procesarEvento);
            }
        }
        selectDto.addEventListener('input', procesarEvento);
        date1.addEventListener('input', procesarEvento);
        date2.addEventListener('input', procesarEvento);
    }

    function procesarEvento(event) {
        const element = event.target;
        if (initialValues.get(element) !== element.value) {
            activateBtn();
        }else{
            deactivateBtn();
        }
    }
    
    init();    
    addEvents();
});
