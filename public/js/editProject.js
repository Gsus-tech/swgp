function confirmCancel(){
    const btn = document.getElementById('sumbit-editProject');
    if(btn.classList.contains('enabled')){
        createConfirmationDialog(
            "Advertencia",
            "¡Advertencia!\nNo se guardarán los cambios realizados.\n¿Deseas continuar?",
            function() { 
                window.location.href = `projectsManagement.php`;
            },
            function() { 
                console.log("Acción cancelada, cambios no descartados.");
            }
        );
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

    event.preventDefault();
    revertDate('thisDate_inicio', 'displayDate1');
    revertDate('thisDate_cierre', 'displayDate2');
    const date1 = document.getElementById('displayDate1');
    const date2 = document.getElementById('displayDate2');

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
    nameFlag = testValue('strict', 'Fname', 'proyecto');
    if(nameFlag === false){
        return false;
    }

    if(date1.classList.contains('invalidField')){
        document.getElementById('date1Label').scrollIntoView({ behavior: 'smooth' });
        return false;
    }

    if(date2.classList.contains('invalidField')){
        document.getElementById('date2Label').scrollIntoView({ behavior: 'smooth' });
        return false;
    }


    if (document.getElementById('deptoAssign').value === 'other') {
        let deptoFlag = testRegex('newDeptoInput');
        if (deptoFlag === false) {
            return false;
        }
        deptoFlag = testLenght('min', 8, 'newDeptoInput');
        if (deptoFlag === false) {
            return false;
        }
        deptoFlag = testLenght('max', 45, 'newDeptoInput');
        if (deptoFlag === false) {
            return false;
        }
        deptoFlag = testValue('strict', 'newDeptoInput', 'proyecto');
        if (deptoFlag === false) {
            return false;
        }
    }

    
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
    descriptionFlag = testValue('light', 'Fdescription', 'proyecto');
    if(descriptionFlag === false){
        return false;
    }

    let metaFlag = testControlledTextInput('Fmeta');
    if(metaFlag === false){
        return false;
    }
    metaFlag = testLenght('min', 20, 'Fmeta');
    if(metaFlag === false){
        return false;
    }
    metaFlag = testLenght('max', 1000, 'Fmeta');
    if(metaFlag === false){
        return false;
    }
    metaFlag = testValue('light', 'Fmeta', 'proyecto');
    if(metaFlag === false){
        return false;
    }
     

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

function saveDate1() {
    const today = new Date();
    const diaInicio = parseInt(document.getElementById("dia_inicio").value);
    const mesInicio = parseInt(document.getElementById("mes_inicio").value);
    const anioInicio = parseInt(document.getElementById("anio_inicio").value);
    const date = new Date(anioInicio, mesInicio - 1, diaInicio);
    const d1 = document.getElementById('displayDate1');
    const dFs = document.getElementById('date1Fs');


    const valid = validateDate(today, date);
    document.getElementById('thisDate_inicio').value = date;
    d1.textContent = date;
    convertDate('displayDate1');
    initialDate();
    const date2 = new Date(document.getElementById('thisDate_cierre').value);

    // if (!valid) {
        const valid2 = validateDate(date, date2);
        if(valid2){
            const date2 = new Date(anioInicio, mesInicio - 1, diaInicio+1);
            const d2 = document.getElementById('displayDate2');
            d2.textContent = date2;
            convertDate('displayDate2');
            // activateBtn();
        }
        dFs.classList.remove('invalidField');
        activateBtn();

    // } else {
    //     dFs.classList.add('invalidField');
    // }
}


function saveDate2(){
    const diaC = parseInt(document.getElementById("dia_cierre").value);
    const mesC = parseInt(document.getElementById("mes_cierre").value);
    const anioC = parseInt(document.getElementById("anio_cierre").value);
    const init = new Date(document.getElementById('thisDate_inicio').value);
    const date = new Date(anioC, mesC - 1, diaC);
    const d2 = document.getElementById('displayDate2');
    const dFs = document.getElementById('date2Fs');
    const errorMsg = document.getElementById('errorMessageDate2');

    const valid = validateDate(init, date);
    document.getElementById('thisDate_cierre').value = date;
    d2.textContent = date;
    convertDate('displayDate2');
    finalDate();

    if (!valid) {
        dFs.classList.remove('invalidField');
        errorMsg.classList.add('hide');
        activateBtn();
    } else {
        dFs.classList.add('invalidField');
        errorMsg.classList.remove('hide');
    }
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

    for (let i = 1; i < opciones.length; i++) {
        const opcion = opciones[i];
        const depto = opcion.getAttribute('data-depto');
        
        if (departamento === 'noFilter' || departamento === depto) {
            opcion.style.display = 'block';
            if (firstVisible) {
                // opcion.selected = true;
                firstVisible = false;
            }
        } else {
            opcion.style.display = 'none';
        }
    }
}

///Agregar integrante de proyecto
function agregarMiembro(projectId) {
    const usersSelect = document.getElementById('listaUsuariosDisponibles');
    const usuarioId = usersSelect.value;
    const usuarioNombre = document.getElementById('listaUsuariosDisponibles').selectedOptions[0].text;
    const rolSelect = document.getElementById('tipoMiembro');
    const tipoMiembro = rolSelect.value;
    const tipoMiembroTexto = tipoMiembro == '1' ? 'Responsable de proyecto' : 'Colaborador';
    const tablaBody = document.getElementById('members-list-body');
    const usuariosSelect = document.getElementById('listaUsuariosDisponibles');

    if (usuarioId === 'non') {
        usersSelect.setCustomValidity('No has seleccionado un usuario.');
        usersSelect.reportValidity();
        return;
    }
    if (tipoMiembro === 'non') {
        rolSelect.setCustomValidity('Define el rol del usuario dentro del proyecto.');
        rolSelect.reportValidity();
        return;
    }

    createConfirmationDialog(
        "Confirmar agregar miembro",
        `¿Estás seguro de que deseas agregar a ${usuarioNombre} como ${tipoMiembroTexto}?`,
        function() {
            // Verificar si existe y eliminar la fila "No se encontraron integrantes registrados"
            const noIntegrantesRow = document.getElementById('no-integrantes-row');
            if (noIntegrantesRow) {
                noIntegrantesRow.remove();
            }

            const data = new URLSearchParams({
                usuarioId: usuarioId,
                projectId: projectId,
                tipoMiembro: tipoMiembro
            });

            // Enviar la solicitud AJAX para agregar el miembro a la base de datos
            fetch('../controller/projectManager.php?addMember=true', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: data
            })
            .then(response => response.json())
            .then(response => {
                if (response.success) {
                    // Crear la nueva fila que se añadira a la tabla
                    const nuevaFila = document.createElement('tr');
                    const nombreCelda = document.createElement('td');
                    const rolCelda = document.createElement('td');
                    const removeBtn = document.createElement('td');

                    nuevaFila.setAttribute('onclick', 'SelectThisRow(this, "members-list-body")');
                    nombreCelda.textContent = usuarioNombre;
                    rolCelda.textContent = tipoMiembroTexto;
                    removeBtn.innerHTML = `<a class='fa fa-user-times removeMemberBtn' title='Remover integrante' onclick='ConfirmDeleteMember(${usuarioId}, this)'></a>`;
                    nuevaFila.appendChild(nombreCelda);
                    nuevaFila.appendChild(rolCelda);
                    nuevaFila.appendChild(removeBtn);
                    tablaBody.appendChild(nuevaFila);

                    const opcionAEliminar = usuariosSelect.querySelector(`option[value="${usuarioId}"]`);
                    if (opcionAEliminar) {
                        opcionAEliminar.remove();
                    }

                    console.log(response.message);
                } else {
                    alert('Error al agregar el miembro: ' + response.message);
                }
            })
            .catch(error => {
                console.error('Error en la solicitud AJAX:', error);
                alert('Error al agregar el miembro: ' + error.message);
            });
        },
        function() {
            console.log("Agregación de miembro cancelada");
        }
    );
}


//Eliminar miembro
function ConfirmDeleteMember(idUsuario, buttonElement) {
    const projectId = document.querySelector('.form-options').getAttribute('pg-d');
    createConfirmationDialog(
        "Confirmar eliminación",
        "¿Estás seguro de que deseas eliminar este miembro?",
        function() {
            let rowNumber = buttonElement.getAttribute('row');
            const resultado = checkDependencies(idUsuario, rowNumber);
            
            if (resultado === true) {
                createConfirmationDialog(
                    "Cambio de responsable",
                    "Todas las actividades de este usuario quedarán a cargo del Responsable del proyecto.\n\nPuedes cambiar esto más adelante en Gestión de actividades.\n\n¿Deseas continuar?",
                    function() {
                        // Enviar solicitud AJAX para eliminar al miembro en la base de datos
                        const data = new URLSearchParams({
                            idUsuario: idUsuario,
                            projectId: projectId
                        });

                        fetch('../controller/projectManager.php?deleteMember=true', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: data
                        })
                        .then(response => response.json())
                        .then(response => {
                            if (response.success) {
                                // Eliminar la fila visualmente
                                const fila = buttonElement.closest('tr');
                                fila.remove();
                                console.log('Miembro eliminado correctamente');
                            } else {
                                alert('Error al eliminar el miembro: ' + response.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error en la solicitud AJAX:', error);
                            alert('Error al eliminar el miembro: ' + error.message);
                        });
                    },
                    function() {
                        console.log("Cambio de responsable cancelado");
                    }
                );
            } else {
                alert('Error al eliminar al usuario.\nAgrega un Responsable de proyecto antes de eliminar al Responsable actual.');
            }
        },
        function() {
            console.log("Eliminación de miembro cancelada");
        }
    );
}



function checkDependencies(id, rowNumber) {
    const tbody = document.getElementById('members-list-body');
    const rows = tbody.getElementsByTagName('tr');
    let flag =1;
    let isRep=false;
    let allReps=0;
    for (let row of rows) {
        const tds = row.getElementsByTagName('td');
        if (flag === parseInt(rowNumber)) {
            isRep = tds[1].textContent === 'Responsable de proyecto' ? true : false;
            console.log(`isRep: ${isRep}`);
        }
        if(tds[1].textContent === 'Responsable de proyecto'){
            allReps++;
        }
        flag++;
    }

    if(isRep === false || isRep === true && allReps >=2){
        return true;
    }else{
        return false;
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
function agregarObjetivo(projectId, tipo) {
    const tablaBody = document.getElementById(tipo === 'general' ? 'objectiveG-list-body' : 'objectiveE-list-body');
    const contenido = document.getElementById(tipo === 'general' ? 'objetivoG' : 'objetivoE');

    if (contenido.value.length < 10) {
        alert('Longitud mínima de 10 caracteres para la descripción del objetivo.');
    } else {
        const cadenasSinSentido = [
            'poiuy', 'lkjhg', 'mnbv', 'uhas83e73u', 'xyz123',
            'random', 'loremipsum', 'qwerty', 'asdf', 'zxcv',
            'nombre1', 'ghfjd', 'iiii', 'dummytext', 'blahblah',
            'Usuario123', 'abcd1234', '123', 'eeee', 'aaaa', 'uuuu',
            'Proyecto123', '123Usuario', '123Proyecto', 'oooo'
        ];

        if (cadenasSinSentido.some(nonsensical => contenido.value.includes(nonsensical))) {
            createConfirmationDialog(
                "Confirmar descripción",
                "Se detectó cadenas sin sentido dentro de la descripción del objetivo. ¿Deseas continuar?",
                function() { agregarObjetivoReal(projectId, tipo, contenido, tablaBody); },
                function() { return false; }
            );
        } else {
            agregarObjetivoReal(projectId, tipo, contenido, tablaBody);
        }
    }
}

function agregarObjetivoReal(projectId, tipo, contenido, tablaBody) {
    const onlySpaces = /^\s*$/;
    if (onlySpaces.test(contenido.value)) {
        createConfirmationDialog(
            "Confirmar descripción",
            "El texto introducido solo contiene espacios. ¿Deseas continuar?",
            function() { agregarObjetivoAjax(projectId, tipo, contenido, tablaBody); },
            function() { return false; }
        );
    } else {
        agregarObjetivoAjax(projectId, tipo, contenido, tablaBody);
    }
}

function agregarObjetivoAjax(projectId, tipo, contenido, tablaBody) {
    const noObjGRow = document.getElementById(tipo === 'general' ? 'no-objectiveG-row' : 'no-objectiveE-row');
    const url = tipo === 'general' ? '../controller/projectManager.php?addObjectiveGeneral=true' : '../controller/projectManager.php?addObjectiveEspecifico=true';
    console.log(`url = ${url}`);
    if (noObjGRow) {
        noObjGRow.remove();
    }

    // Preparar los datos para la solicitud AJAX
    const data = new URLSearchParams({
        idProyecto: projectId,
        tipo: tipo,
        contenido: contenido.value
    });

    // Realizar la solicitud AJAX para guardar el nuevo objetivo
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: data
    })
    .then(response => response.json())
    .then(response => {
        if (response.success) {
            const newId = response.data.newId;  // Obtén el ID del nuevo objetivo de la respuesta AJAX
            const content = response.data.descripcion;
            // Crear nueva fila y añadirla a la tabla
            const nuevaFila = document.createElement('tr');
            nuevaFila.setAttribute('value', newId);
            nuevaFila.setAttribute('onclick', `SelectThisRow(this, '${tablaBody.id}')`);
            const descriptionCelda = document.createElement('td');
            descriptionCelda.setAttribute('class', 'descripcion');
            const removeBtn = document.createElement('td');

            descriptionCelda.textContent = content;
            removeBtn.innerHTML = 
            `<a class='fa fa-trash removeMemberBtn' title='Eliminar objetivo' onclick=\"DeleteObjective(this,'${tipo}',${projectId}, ${newId})\"></a>
            <a class='fa fa-edit tableIconBtn mt1r' title='Editar objetivo' onclick=\"EditObjective(this)\"></a>
            <a id='saveChangesObj' class='fa fa-save tableIconBtn mt1r hide' title='Guardar cambios' onclick=\"SaveObjectiveChanges(this,'${tipo}',${projectId},${newId})\"></a>`;

            nuevaFila.appendChild(descriptionCelda);
            nuevaFila.appendChild(removeBtn);   
            tablaBody.appendChild(nuevaFila);

            // Limpiar el campo de entrada
            contenido.value = "";

        } else {
            alert('Error al agregar el objetivo: ' + response.message);
        }
    })
    .catch(error => {
        console.error('Error en la solicitud AJAX:', error);
        alert('Error al agregar el objetivo: ' + error.message);
    });
}


//Eliminar objetivos
function DeleteObjective(element, type, projectId, objId) {
    createConfirmationDialog(
        "Confirmar eliminación",
        `¿Estás seguro de que deseas eliminar este objetivo '${type}'?`,
        function() {
            const warningMessage = type === 'general' 
                ? "Este objetivo será eliminado. ¿Deseas continuar?" 
                : "¡Advertencia!\nSe eliminarán las actividades vinculadas al cumplimiento de este objetivo.\n\n¿Deseas continuar?";
            createConfirmationDialog(
                "Advertencia",
                warningMessage,
                function() {
                    // Realizar la solicitud AJAX para eliminar el objetivo de la base de datos
                    const data = new URLSearchParams({
                        projectId: projectId,
                        objId: objId,
                        type: type
                    });

                    fetch('../controller/projectManager.php?deleteObjective=true', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: data
                    })
                    .then(response => response.json())
                    .then(response => {
                        if (response.success) {
                             // Quitar la fila de la tabla visualmente
                                const fila = element.closest('tr');
                                fila.remove()
                            console.log(`Objetivo eliminado correctamente: ${JSON.stringify(response.data)}`);
                        } else {
                            alert('Error al eliminar el objetivo: ' + response.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error en la solicitud AJAX:', error);
                        alert('Error al eliminar el objetivo: ' + error.message);
                    });
                },
                function() {
                    console.log("Eliminación cancelada");
                }
            );
        },
        function() {
            console.log("Eliminación de objetivo cancelada");
        }
    );
}

//editar objetivo
function EditObjective(button) {
    const fila = button.closest('tr');
    const descripcionCelda = fila.querySelector('.descripcion');
    const textarea = document.createElement('textarea');
    textarea.classList.add('editable');
    textarea.value = br2nl(descripcionCelda.innerHTML); // Uso de innerHTML
    console.log("Texto convertido para edición: ", textarea.value); // Debugging
    descripcionCelda.innerHTML = ''; // Limpia la celda
    descripcionCelda.appendChild(textarea);
    button.classList.add('hide');
    fila.querySelector('.fa-save').classList.remove('hide');
}

//Validar los cambios realizados
function SaveObjectiveChanges(button, tipo, idProyecto, idObjetivo) {
    const fila = button.closest('tr');
    const descripcionCelda = fila.querySelector('.descripcion');
    const textarea = descripcionCelda.querySelector('textarea');

    // Obtener el nuevo valor de la descripción
    const nuevaDescripcion = textarea.value;
    
    const cadenasSinSentido = [
        'poiuy', 'lkjhg', 'mnbv', 'uhas83e73u', 'xyz123',
        'random', 'loremipsum', 'qwerty', 'asdf', 'zxcv',
        'nombre1', 'ghfjd', 'iiii', 'dummytext', 'blahblah',
        'Usuario123', 'abcd1234', '123', 'eeee', 'aaaa', 'uuuu',
        'Proyecto123', '123Usuario', '123Proyecto', 'oooo'
    ];

    if (cadenasSinSentido.some(nonsensical => nuevaDescripcion.includes(nonsensical))) {
        createConfirmationDialog(
            "Advertencia",
            "Se detectó cadenas sin sentido dentro de la descripción del objetivo.\n¿Deseas continuar?",
            function() {
                proceedWithSave(button, fila, tipo, idProyecto, idObjetivo, nuevaDescripcion, descripcionCelda);
            },
            function() {
                console.log("Confirmación rechazada por cadenas sin sentido.");
            }
        );
        return false;
    }

    const onlySpaces = /^\s*$/;
    if (onlySpaces.test(nuevaDescripcion)) {
        createConfirmationDialog(
            "Advertencia",
            "El texto introducido solo contiene espacios.\n¿Deseas continuar?",
            function() {
                proceedWithSave(button, fila, tipo, idProyecto, idObjetivo, nuevaDescripcion, descripcionCelda);
            },
            function() {
                console.log("Confirmación rechazada por solo espacios.");
            }
        );
        return false;
    }

    if (nuevaDescripcion.length < 10) {
        alert("Mínimo 10 caracteres para este campo");
        return false;
    }

    if (nuevaDescripcion.length > 1000) {
        alert("Máximo 1000 caracteres para este campo");
        return false;
    }

    // Si pasa todas las validaciones, continuar con el guardado
    proceedWithSave(button, fila, tipo, idProyecto, idObjetivo, nuevaDescripcion, descripcionCelda);
}

// Guardar los cambios con AJAX
function proceedWithSave(button, fila, tipo, idProyecto, idObjetivo, nuevaDescripcion, descripcionCelda) {
    const data = new URLSearchParams({
        idProyecto: idProyecto,
        idObjetivo: idObjetivo,
        tipo: tipo,
        nuevaDescripcion: nuevaDescripcion
    });

    // Realizar la solicitud AJAX para guardar los cambios
    fetch('../controller/projectManager.php?editObjetctive=true', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: data
    })
    .then(response => response.json())
    .then(response => {
        if (response.success) {
            // Actualizar la descripción visualmente solo si se guarda correctamente
            descripcionCelda.innerHTML = nl2br(nuevaDescripcion);
            button.classList.add('hide');
            fila.querySelector('.fa-edit').classList.remove('hide');
            console.log('Objetivo actualizado correctamente:', response.message);
        } else {
            alert('Error al actualizar el objetivo: ' + response.message);
        }
    })
    .catch(error => {
        console.error('Error en la solicitud AJAX:', error);
        alert('Error al actualizar el objetivo: ' + error.message);
    });
}


function nl2br(str) {
    // if (typeof str !== 'string') return str;
    return str.replace(/(\r\n|\n\r|\r|\n)/g, '<br>');
}

function br2nl(str) {
    return str.replace(/<br\s*\/?>/gi, '\n');
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
        addPadding();
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

    function addPadding(){
        const mn = document.querySelector('.main');
        mn.classList.add('pB-70');
    }
    
    init();    
    addEvents();
});
