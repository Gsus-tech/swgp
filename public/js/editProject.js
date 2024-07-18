function updateBasicInfo(){
    console.log('Enviando el formulario');
    event.preventDefault();
    const form = document.getElementById('basicInfo-form');
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
        if(!valid2){
            const date2 = new Date(anioInicio, mesInicio - 1, diaInicio+1);
            const d2 = document.getElementById('displayDate2');
            d2.textContent = date2;
            convertDate('displayDate2');
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
        initialDate();
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
    removeBtn.innerHTML = `<a class='fa fa-minus removeMemberBtn' title='Remover integrante' onclick='ConfirmDeleteMember(${usuarioId}, ${projectId})'></a>`;
    nuevaFila.appendChild(nombreCelda);
    nuevaFila.appendChild(rolCelda);
    nuevaFila.appendChild(removeBtn);
    tablaBody.appendChild(nuevaFila);

    // Eliminar la opción del usuario del select de usuarios disponibles
    const opcionAEliminar = usuariosSelect.querySelector(`option[value="${usuarioId}"]`);
    if (opcionAEliminar) {
        opcionAEliminar.remove();
    }
}

document.addEventListener("DOMContentLoaded", function() {
    function init() {
        console.log("La página ha cargado completamente");
        convertDate('displayDate1');
        convertDate('displayDate2');
    }
    
    init();
    
    // const usuariosSelect = document.getElementById('listaUsuariosDisponibles');
    // const opcionesOriginales = Array.from(usuariosSelect.options);

    // window.ConfirmDeleteMember = function(buttonElement) {
    //     if (confirm('¿Estás seguro de que deseas eliminar este miembro?')) {
    //         const fila = buttonElement.closest('tr');
    //         const usuarioId = fila.dataset.usuarioId;
    //         // Recuperar la opción del usuario y volver a agregarla al select
    //         const usuarioNombre = fila.querySelector('td:first-child').textContent;
    //         const usuarioOpcion = opcionesOriginales.find(opcion => opcion.value == usuarioId);
    //         if (usuarioOpcion) {
    //             const usuarioDepto = usuarioOpcion.getAttribute('data-depto');
    //             const nuevaOpcion = document.createElement('option');
    //             nuevaOpcion.value = usuarioId;
    //             nuevaOpcion.setAttribute('data-depto', usuarioDepto);
    //             nuevaOpcion.textContent = usuarioNombre;
    //             usuariosSelect.appendChild(nuevaOpcion);
    //         }
            
    //         fila.remove();

    //     }
    // }
});