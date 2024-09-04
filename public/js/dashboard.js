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
                const actId = button.closest('.card-body').id;
                const newMenu = document.createElement('div');
                newMenu.className = 'opciones-tarjetas';
                newMenu.id = actId;

                const closestTaskDiv = button.closest('.tasks');
                const columnId = closestTaskDiv ? closestTaskDiv.id : null;
                const closestActDiv = button.closest('.card');
                const cardId = closestActDiv ? closestActDiv.getAttribute('data-card-id') : null;

                newMenu.innerHTML =  `
                <a href="#" onclick="showTags(this, '${columnId}')" class="dropdown-item"><i class="mdi mdi-pencil me-1"></i>Etiquetas</a>
                    <a href="#" onclick="showMoveOptions(this, '${columnId}', ${cardId})" class="dropdown-item"><i class="mdi mdi-pencil me-1"></i>Mover</a>
                    <a href="#" onclick="confirmDeleteAct(this)" class="dropdown-item"><i class="mdi mdi-delete me-1"></i>Eliminar</a>
                    `;

                // Insertar el nuevo menú en el contenedor del botón
                // const cardBody = button.closest('.card-body');
                // cardBody.appendChild(newMenu);

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

function showMoveOptions(button, columnId, cardId) {
    event.stopPropagation(); // Esto evita que se ejecute el evento de cierre del div
    const menu = button.closest('.opciones-tarjetas');

    // Verificacion del elemento menu
    if (menu) {

        // Dependiendo del id del div 'tasks', mostrar un HTML distinto
        let menuOptionsHTML = '';

        if (columnId === 'pendientes') {
            menuOptionsHTML = `
                <a href="#" onclick="moveCard(2, '${cardId}')" class="dropdown-item"><i class="mdi mdi-pencil me-1"></i>En proceso</a>
                <a href="#" onclick="moveCard(3, '${cardId}')" class="dropdown-item"><i class="mdi mdi-pencil me-1"></i>Retrasadas</a>
                <a href="#" onclick="moveCard(4, '${cardId}')" class="dropdown-item"><i class="mdi mdi-delete me-1"></i>Terminadas</a>
            `;
        } else if (columnId === 'proceso') {
            menuOptionsHTML = `
                <a href="#" onclick="moveCard(1, '${cardId}')" class="dropdown-item"><i class="mdi mdi-pencil me-1"></i>Pendiente</a>
                <a href="#" onclick="moveCard(3, '${cardId}')" class="dropdown-item"><i class="mdi mdi-pencil me-1"></i>Retrasadas</a>
                <a href="#" onclick="moveCard(4, '${cardId}')" class="dropdown-item"><i class="mdi mdi-delete me-1"></i>Terminadas</a>
            `;
        } else if (columnId === 'retrasadas') {
            menuOptionsHTML = `
                <a href="#" onclick="moveCard(1, '${cardId}')" class="dropdown-item"><i class="mdi mdi-pencil me-1"></i>Pendiente</a>
                <a href="#" onclick="moveCard(2, '${cardId}')" class="dropdown-item"><i class="mdi mdi-pencil me-1"></i>En proceso</a>
                <a href="#" onclick="moveCard(4, '${cardId}')" class="dropdown-item"><i class="mdi mdi-delete me-1"></i>Terminadas</a>
            `;
        } else if (columnId === 'terminadas') {
            menuOptionsHTML = `
                <a href="#" onclick="moveCard(1, '${cardId}')" class="dropdown-item"><i class="mdi mdi-pencil me-1"></i>Pendiente</a>
                <a href="#" onclick="moveCard(2, '${cardId}')" class="dropdown-item"><i class="mdi mdi-pencil me-1"></i>En proceso</a>
                <a href="#" onclick="moveCard(3, '${cardId}')" class="dropdown-item"><i class="mdi mdi-delete me-1"></i>Retrasada</a>
            `;
        }
        // Reemplazar el contenido del menú actual con las nuevas opciones
        menu.innerHTML = menuOptionsHTML;
    }
}


function showTags(button){
    event.stopPropagation(); // Esto evita que se ejecute el evento de cierre del div
    const menu = button.closest('.opciones-tarjetas');

    // Verificacion del elemento menu
    if (menu) {
        // Dependiendo del id del div 'tasks', mostrar un HTML distinto
        let menuOptionsHTML = '';

        menuOptionsHTML = `
        <a href="#" onclick="cardFlags(this, 2)" class="dropdown-item"><i class="mdi mdi-pencil me-1 fa fa-circle rdFlag"></i>  Importante / Urgente</a>
        <a href="#" onclick="cardFlags(this, 3)" class="dropdown-item"><i class="mdi mdi-pencil me-1 fa fa-circle grFlag"></i>  Completado o bajo control</a>
        <a href="#" onclick="cardFlags(this, 4)" class="dropdown-item"><i class="mdi mdi-delete me-1 fa fa-circle ylFlag"></i>  Información adicional requerida</a>
        `;

        // Reemplazar el contenido del menú actual con las nuevas opciones
        menu.innerHTML = menuOptionsHTML;
    }
}


function confirmDeleteAct(element){
    if(confirm('¿Estas seguro de querer eliminar esta actividad?')){
            //Codigo para eliminar una tarea.
    }
}

0
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
        //Info de columna origen
        const sourceColumn = card.closest('.task-list-items');
        const sourceColumnId = sourceColumn.id;

        targetColumn.appendChild(card);
        
        //Actualizar datos columna destino
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
    console.log('URL: activityManager.php?actId=' + encodeURIComponent(cardId) + '&moveToColumn=' + encodeURIComponent(targetColumnId));
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
});

