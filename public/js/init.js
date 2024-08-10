//Barra lateral
const menu = document.querySelector(".sidebar-menu")

menu.addEventListener('click', function(){
    expandSideBar();
})

function expandSideBar(){
    document.querySelector('body').classList.toggle('short');
}

function openAccountMenu() {
    const accountMenu = document.getElementById('accountMenu');
    accountMenu.classList.toggle('hide');

    if (!accountMenu.classList.contains('hide')) {
        document.addEventListener('click', closeAccountMenu);
    } else {
        document.removeEventListener('click', closeAccountMenu);
    }
}

function closeAccountMenu(event) {
    const accountMenu = document.getElementById('accountMenu');
    const avatarBtn = document.getElementById('avatarBtn');
    
    if (!accountMenu.contains(event.target) && !avatarBtn.contains(event.target)) {
        accountMenu.classList.add('hide');
        document.removeEventListener('click', closeAccountMenu);
    }
}

function SelectThisRow(element, tbodyName){
    const tbody = document.getElementById(`${tbodyName}`);
    const rows = tbody.getElementsByTagName('tr');
    const state = element.classList.contains('rowSelected');

    for (let i = 0; i < rows.length; i++) {
        if(rows[i].classList.contains('rowSelected')){
            rows[i].classList.remove('rowSelected');
        }
    }
    state===false ? element.classList.add('rowSelected') : element.classList.remove('rowSelected');
}








//Funciones del tablero Kanban
function allowDrop(event) {
    event.preventDefault();
    event.target.classList.add('drag-over');
}

// Manejar el evento dragstart
function drag(event) {
    event.dataTransfer.setData("text", event.target.id);
}

// Manejar el evento drop
function drop(event, newState) {
    event.preventDefault();
    const taskId = event.dataTransfer.getData("text");
    const taskElement = document.getElementById(taskId);
    event.target.appendChild(taskElement);

    // Remover la clase drag-over
    event.target.classList.remove('drag-over');

    // Obtener el ID de la tarea
    const id = taskId.split('-')[1];

    // Enviar solicitud AJAX para actualizar el estado de la tarea en la base de datos
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "updateTaskState.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.send("id=" + id + "&estado=" + newState);
}
