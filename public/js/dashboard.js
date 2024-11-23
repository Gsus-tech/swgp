document.addEventListener("DOMContentLoaded", function() {
    // Seleccionar todos los botones con la clase .cardMenu
    const cardMenuButtons = document.querySelectorAll('.cardMenu');
    cardMenuButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.stopPropagation();

            // Crear el nuevo div para las opciones
            const isMenuOn = document.querySelector('.opciones-tarjetas');
            if (!isMenuOn) {
                createDiv()
            }else{
                const oldMenu = document.querySelector('.opciones-tarjetas');
                oldMenu.remove();
                createDiv()
            }

            function createDiv(){
                const closestTaskDiv = button.closest('.tasks');
                const columnId = closestTaskDiv ? closestTaskDiv.id : null;
                const closestActDiv = button.closest('.card');
                const cardId = closestActDiv ? closestActDiv.getAttribute('data-card-id') : null;
                const newMenu = document.createElement('div');
                newMenu.className = 'opciones-tarjetas';
                newMenu.id = cardId;
                
                
                newMenu.innerHTML =  `
                <a href="#" onclick="showMoveOptions(this, '${columnId}', ${cardId})" class="dropdown-item"><i></i>Mover</a>
                <a href="#" onclick="showTags(this, '${columnId}')" class="dropdown-item"><i></i>Etiquetas</a>
                    <a href="#" onclick="confirmDeleteAct(this)" class="dropdown-item"><i></i>Eliminar</a>
                    `;

                // Insertar el nuevo menú en el contenedor del botón

                document.body.appendChild(newMenu);  // Añadirlo al body

                const rect = button.getBoundingClientRect();  // Obtener posición del botón
                newMenu.style.top = `${rect.bottom}px`;  // Posicionarlo justo debajo del botón
                newMenu.style.left = `${rect.left}px`;

                // Evento de clic para cerrar el menú
                document.addEventListener('click', function closeMenu(event) {
                    if (!newMenu.contains(event.target) && event.target !== button) {
                        newMenu.remove();  // Eliminar el menú si se hace clic fuera del Div
                        document.removeEventListener('click', closeMenu);
                    }
                });
            }
        });
    });
});

// Mostrar las opciones para mover las tarjetas
function showMoveOptions(button, columnId, cardId) {
    event.stopPropagation(); // Esto evita que se ejecute el evento de cierre del div
    const menu = button.closest('.opciones-tarjetas');

    // Verificacion del elemento menu
    if (menu) {

        // Dependiendo del id del div 'tasks', mostrar un HTML distinto
        let menuOptionsHTML = '';

        if (columnId === 'pendientes') {
            menuOptionsHTML = `
                <a href="#" onclick="moveCard(2, '${cardId}')" class="dropdown-item"><i></i>Urgentes</a>
                <a href="#" onclick="moveCard(3, '${cardId}')" class="dropdown-item"><i></i>Retrasadas</a>
                <a href="#" onclick="moveCard(4, '${cardId}')" class="dropdown-item"><i></i>Terminadas</a>
            `;
        } else if (columnId === 'proceso') { 
            menuOptionsHTML = `
                <a href="#" onclick="moveCard(1, '${cardId}')" class="dropdown-item"><i></i>Actividades</a>
                <a href="#" onclick="moveCard(3, '${cardId}')" class="dropdown-item"><i></i>Retrasadas</a>
                <a href="#" onclick="moveCard(4, '${cardId}')" class="dropdown-item"><i></i>Terminadas</a>
            `;
        } else if (columnId === 'retrasadas') {
            menuOptionsHTML = `
                <a href="#" onclick="moveCard(1, '${cardId}')" class="dropdown-item"><i></i>Actividades</a>
                <a href="#" onclick="moveCard(2, '${cardId}')" class="dropdown-item"><i></i>Urgentes</a>
                <a href="#" onclick="moveCard(4, '${cardId}')" class="dropdown-item"><i></i>Terminadas</a>
            `;
        } else if (columnId === 'terminadas') {
            menuOptionsHTML = `
                <a href="#" onclick="moveCard(1, '${cardId}')" class="dropdown-item"><i></i>Actividades</a>
                <a href="#" onclick="moveCard(2, '${cardId}')" class="dropdown-item"><i></i>Urgentes</a>
                <a href="#" onclick="moveCard(3, '${cardId}')" class="dropdown-item"><i></i>Retrasada</a>
            `;
        }
        // Reemplazar el contenido del menú actual con las nuevas opciones
        menu.innerHTML = menuOptionsHTML;
    }
}

// Mostrar las etiquetas
function showTags(button){
    event.stopPropagation(); // Esto evita que se ejecute el evento de cierre del div
    const menu = button.closest('.opciones-tarjetas');

    // Verificacion del elemento menu
    if (menu) {
        // Dependiendo del id del div 'tasks', mostrar un HTML distinto
        let menuOptionsHTML = '';

        menuOptionsHTML = `
        <a href="#" onclick="cardFlags(this, 1)" class="dropdown-item"><i class="fa fa-circle rdFlag"></i>  Importante / Urgente</a>
        <a href="#" onclick="cardFlags(this, 2)" class="dropdown-item"><i class="fa fa-circle grFlag"></i>  Completado o bajo control</a>
        <a href="#" onclick="cardFlags(this, 3)" class="dropdown-item"><i class="fa fa-circle ylFlag"></i>  Información adicional requerida</a>
        <a href="#" onclick="cardFlags(this, 4)" class="dropdown-item"><i class="fa fa-close"></i>  Quitar etiqueta</a>
        `;

        // Reemplazar el contenido del menú actual con las nuevas opciones
        menu.innerHTML = menuOptionsHTML;
    }
}

function cardFlags(element, action){
    const floatMenuDiv = document.querySelector('.opciones-tarjetas');
    const id = floatMenuDiv.id;

    if (action === 4) {
        async function deleteNote(){
            const url = '../controller/activityManager.php?deleteNote=true';
            const data = new URLSearchParams({
                id_actividad: id,
            });

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: data
                });

                const result = await response.json();

                if (result.success) {
                    createAlertDialog('Aviso', '¡Etiqueta eliminada!', ()=>{location.reload();}, 'Aceptar');
                } else {
                    if(result.message === 'No hay notas registradas.'){
                        floatMenuDiv.remove();
                    }else{
                        console.error("Error al eliminar la etiqueta:", result.message);
                        return null;
                    }
                }
            } catch (error) {
                console.error('Error en la solicitud AJAX:', error);
                return null;
            }
        }

        deleteNote();
    }else if(action >= 1 && action <= 3){
        const id = document.querySelector('.opciones-tarjetas').id;
        async function sendAjaxRequest(data) {
            // console.log('Entering sendAjaxRequest.');
            const url = '../controller/activityManager.php?setNote=true';
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: data
                });
        
                const result = await response.json();
                if (result.success) {
                    createAlertDialog('¡Aviso!', '¡Etiqueta guardada exitosamente!', ()=>{location.reload();}, 'Aceptar');
                } else {
                    console.error("Error al asignar la etiqueta:", result.message);
                }
            } catch (error) {
                console.error('Error en la solicitud AJAX:', error);
            }
        }
        

        async function addTag(nota) {
            // console.log('Entering addtag.');
            let data;
            if (nota) {
                try {
                    const contenido = await createTextInputBox('Creación de nota', 'Contenido:');
                    data = new URLSearchParams({
                        id_actividad: id,
                        tipo: action,
                        contenido: contenido
                    });
                    console.log(`Contenido: ${contenido}`);
                } catch (error) {
                    if (error !== 'Input cancelado') {
                        console.error(error);
                    }
                    return;
                }
            } else {
                data = new URLSearchParams({
                    id_actividad: id,
                    tipo: action
                });
            }
            sendAjaxRequest(data);
        }
        
        
        createConfirmationDialog('Guardando etiqueta...', '¿Deseas agregar una nota?', () => addTag(true), () => addTag(false), 'Si', 'No');

    }
    floatMenuDiv.remove();
}

// Eliminar actividad
function confirmDeleteAct(element) {
    createConfirmationDialog(
        "Confirmar eliminación",
        "¿Estás seguro de querer eliminar esta actividad?",
        async function() {
            const id = element.parentElement.getAttribute('id');
            const rep = await getRepId(id);
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
                
                const dsh = document.createElement('input');
                dsh.type = 'hidden';
                dsh.name = 'dsh';
                dsh.value = 'true';
                form.appendChild(dsh);

                // Agregar el formulario al documento y enviarlo
                document.body.appendChild(form);
                form.submit();
            });
        },
        function() {
            console.log("Eliminación cancelada");
        }
    );
}

async function getRepId(id) {
    const data = new URLSearchParams({
        id_actividad: id,
    });

    try {
        const response = await fetch('../controller/activityManager.php?getRepId=true', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: data
        });
        const result = await response.json();
        if (result.success) {
            return result.id_usuario;
        } else {
            console.error("Error al obtener el id_usuario:", result.message);
            return null;
        }
    } catch (error) {
        console.error('Error en la solicitud AJAX:', error);
        return null;
    }
}



function moveCard(targetColumnId, cardId) {    
    const nm = {
        1: "one",
        2: "two",
        3: "three",
        4: "four"
    };

    const idString = `task-list-${nm[targetColumnId]}`;
    const cardIdString = `[data-card-id="${cardId}"]`;
    const card = document.querySelector(cardIdString);
    const targetColumn = document.getElementById(idString);
    if (card && targetColumn) {
        const sourceColumn = card.closest('.task-list-items');
        const sourceColumnId = sourceColumn.id;

        targetColumn.appendChild(card);
        actualizarContador(idString);
        
        //Actualizar datos columna origen
        actualizarContador(sourceColumnId);

        //Actualizar el porcentaje del proyecto
        updateProjectPercentage();

        //Cerrar menu
        const menu = document.querySelector('.opciones-tarjetas');
        if (menu) menu.remove();

        // Codigo para actualizar en la base de datos
        updateCardColumn(cardId, targetColumnId);

        removeLateClass(card);
        setTimeout(350,checkForLateActivities());
    }
}

function actualizarContador(columnId) {
    const column = document.getElementById(`${columnId}`);
    const noTasksMessage = column.querySelector('.emptyTaskDivMsj');
    const hasTasks = column.querySelector('.card');
    
    //Remover o agregar el mensaje de Sin tareas registradas...
    if (hasTasks) {
        if (noTasksMessage) {
            noTasksMessage.remove();
            const taskContainer = column.closest('.task-list-items');
                taskContainer.style.border = 'none';
        }
    } else {
        if (!noTasksMessage) {
            const placeholder = document.createElement('div');
                placeholder.className = 'emptyTaskDivMsj';
                placeholder.textContent = 'Sin tareas...';
                column.appendChild(placeholder);
                const taskContainer = column.closest('.task-list-items');
                taskContainer.style.border = '1px dashed #ccc';
        }
    }

    //Contador de actividades en la columna
    const titleElement = column.closest('.tasks').querySelector('.task-header');
    const cardCount = column.querySelectorAll('.card').length;
    const [columnName] = titleElement.textContent.split(' (');
    titleElement.textContent = `${columnName} (${cardCount})`;
}

function getColumnaId(name){
    const getIds = {
        1: 'task-list-one',
        2: 'task-list-two',
        3: 'task-list-three',
        4: 'task-list-four'
    };
    const IdsInverted = Object.fromEntries(Object.entries(getIds).map(([key, value]) => [value, key]));
    return IdsInverted[name];
}


function updateCardColumn(cardId, targetColumnId) {
    fetch('../controller/activityManager.php?actId=' + encodeURIComponent(cardId) + '&moveToColumn=' + encodeURIComponent(targetColumnId), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        }
    })
    .then(response => response.text())
    .then(data => {
        console.log('PHP response:', data);
    })
    .catch(error => {
        console.error('Error en la solicitud AJAX:', error);
    });
}

function updateProjectPercentage(){
    const containers = document.querySelector('.kanban');
    const totalCardCount = containers.querySelectorAll('.card').length;
    const column = document.getElementById('task-list-four');
    const cardCount = column.querySelectorAll('.card').length;
    
    var percent = totalCardCount > 0 ? (cardCount / totalCardCount)*100 : 0;
    percent = percent.toFixed(2);

    const  progressBar = document.getElementById('progress-bar-div');

    progressBar.style = `width: ${percent}%;`;
    progressBar.innerText = `${percent}%`;
}

document.addEventListener("DOMContentLoaded", function() {

    const progressIcon = document.getElementById('progressIcon');
    const progressDiv = document.querySelector('.progressIcon-div');;

    progressIcon.addEventListener('click', function(){
        progressDiv.style = "width:90%;";
        progressIcon.classList.add('hide');
        document.querySelector('.progress-bar').classList.remove('hide');
    })

    document.addEventListener('click', function(event) {
        if(!progressDiv.classList.contains('hide')){
            const closeBar = progressDiv.contains(event.target);     
            if (!closeBar) {
                if(!document.querySelector('.progress-bar').classList.contains('hide')){
                    progressDiv.style = "width:fit-content;";
                    document.querySelector('.progress-bar').classList.add('hide');
                    progressIcon.classList.remove('hide');
                }
            }
        }
    });

    checkForLateActivities();
    addColorActTags();
});

