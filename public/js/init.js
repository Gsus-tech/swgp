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


//Funcion para hacer solicitudes AJAX reutilizables.
function makeAjaxRequest(url, method, data = null, successCallback, errorCallback) {
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
        },
        body: data ? new URLSearchParams(data) : null,
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Error en la solicitud: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            successCallback(data);
        } else {
            errorCallback(data.message || 'Ocurrió un error desconocido');
        }
    })
    .catch(error => {
        errorCallback(error.message);
    });
}

function showLoadingCursor() {
    document.body.classList.add('loading');
}

function hideLoadingCursor() {
    document.body.classList.remove('loading');
}

function createConfirmationDialog(title, message, onConfirm) {
    //Div del confirm
    const confirmationDiv = document.createElement('div');
    confirmationDiv.classList.add('confirmation-overlay');
    const confirmationDialog = document.createElement('div');
    confirmationDialog.classList.add('confirmation-dialog');
    const confirmationContent = document.createElement('div');
    confirmationContent.classList.add('confirmation-content');
    //Titulo
    const titleC = document.createElement('h3');
    titleC.textContent = title;
    confirmationContent.appendChild(titleC);

    // Mensaje del confirm
    const messageParagraph = document.createElement('pre');
    messageParagraph.textContent = message;
    confirmationContent.appendChild(messageParagraph);

    const buttonContainer = document.createElement('div');
    buttonContainer.classList.add('confirmation-buttons');

    // Botón de confirmar
    const confirmButton = document.createElement('button');
    confirmButton.textContent = 'Confirmar';
    buttonContainer.appendChild(confirmButton);

    // Botón de cancelar
    const cancelButton = document.createElement('button');
    cancelButton.textContent = 'Cancelar';
    buttonContainer.appendChild(cancelButton);

    confirmationContent.appendChild(buttonContainer);
    confirmationDialog.appendChild(confirmationContent);
    confirmationDiv.appendChild(confirmationDialog);

    document.body.appendChild(confirmationDiv);

    // Evento confirmar
    confirmButton.addEventListener('click', function() {
        onConfirm();
        confirmationDiv.remove();
    });
    // Evento cancelar
    cancelButton.addEventListener('click', function() {
        confirmationDiv.remove();
    });
}

function addButtonEvents(){
    const buttons = document.querySelectorAll('.button');
    buttons.forEach(button => {
        console.log('adding events.');
        button.setAttribute('tabindex', '0');
        button.addEventListener('keydown', function(event) {
            if (event.key === "Enter" || event.key === " ") {
                event.preventDefault();
                event.target.click();
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    addButtonEvents();
});