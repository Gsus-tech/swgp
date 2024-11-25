document.addEventListener('DOMContentLoaded', function() {
    validateSession();
    setTimeout(() => {
        checkForNotifications();
    }, 250);
});

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
function createInputBox(titleText, descriptionText, attributeArray, btnText1, btnText2) {
    return new Promise((resolve, reject) => {
        // Crear el div principal
        const nombrarReporte = document.createElement('div');
        nombrarReporte.id = 'mainInputDiv';
        nombrarReporte.classList.add('mainCreatedDiv', 'hidden');

        // Crear el contenido
        const inputDiv = document.createElement('div');
        inputDiv.classList.add('createdInputDiv-content');

        const title = document.createElement('h3');
        title.textContent = titleText;
        inputDiv.appendChild(title);

        const label = document.createElement('p');
        label.textContent = descriptionText;
        inputDiv.appendChild(label);

        const inputArea = document.createElement('input');
        inputArea.id = 'textInputContent';
        inputArea.classList.add('cTextarea-input');
        inputArea.maxLength = 80;
        if (attributeArray && Array.isArray(attributeArray)) {
            attributeArray.forEach(attr => {
                inputArea.setAttribute(attr.name, attr.value);
            });
        }
        inputDiv.appendChild(inputArea);

        const buttonContainer = document.createElement('div');
        buttonContainer.classList.add('createdInputDiv-buttons');

        const saveButton = document.createElement('button');
        saveButton.id = 'saveInputBtn';
        saveButton.textContent = btnText1 ? btnText1 : 'Guardar';
        buttonContainer.appendChild(saveButton);

        const cancelButton = document.createElement('button');
        cancelButton.id = 'cancelBtn';
        cancelButton.textContent = btnText2 ? btnText2 : 'Cancelar';
        buttonContainer.appendChild(cancelButton);

        inputDiv.appendChild(buttonContainer);
        nombrarReporte.appendChild(inputDiv);
        document.body.appendChild(nombrarReporte);

        saveButton.addEventListener('click', function() {
            const inputContent = inputArea.value.trim();
            if (inputContent === '') {
                inputArea.classList.add('highlight-error');
                setTimeout(function() {
                    inputArea.classList.remove('highlight-error');
                }, 1000);
            } else {
                resolve(inputContent);
                nombrarReporte.remove();
            }
        });

        cancelButton.addEventListener('click', function() {
            nombrarReporte.remove();
            reject('Input cancelado');
        });

        nombrarReporte.classList.remove('hidden');

        
        
    });
}



function createTextInputBox(titleText, descriptionText, attributeArray) {
    return new Promise((resolve, reject) => {
        // Crear el div principal
        const nombrarReporte = document.createElement('div');
        nombrarReporte.id = 'mainInputDiv';
        nombrarReporte.classList.add('mainCreatedDiv', 'hidden');

        // Crear el contenido
        const inputDiv = document.createElement('div');
        inputDiv.classList.add('createdInputDiv-content');

        const title = document.createElement('h3');
        title.textContent = titleText;
        inputDiv.appendChild(title);

        const label = document.createElement('p');
        label.textContent = descriptionText;
        inputDiv.appendChild(label);

        const textarea = document.createElement('textarea');
        textarea.id = 'textInputContent';
        textarea.classList.add('cTextarea-input');
        textarea.maxLength = 80;
        if (attributeArray && Array.isArray(attributeArray)) {
            attributeArray.forEach(attr => {
                textarea.setAttribute(attr.name, attr.value);
            });
        }
        inputDiv.appendChild(textarea);

        const buttonContainer = document.createElement('div');
        buttonContainer.classList.add('createdInputDiv-buttons');

        const saveButton = document.createElement('button');
        saveButton.id = 'saveInputBtn';
        saveButton.textContent = 'Guardar';
        buttonContainer.appendChild(saveButton);

        const cancelButton = document.createElement('button');
        cancelButton.id = 'cancelBtn';
        cancelButton.textContent = 'Cancelar';
        buttonContainer.appendChild(cancelButton);

        inputDiv.appendChild(buttonContainer);
        nombrarReporte.appendChild(inputDiv);
        document.body.appendChild(nombrarReporte);

        saveButton.addEventListener('click', function() {
            const inputContent = textarea.value.trim();
            if (inputContent === '') {
                textarea.classList.add('highlight-error');
                setTimeout(function() {
                    textarea.classList.remove('highlight-error');
                }, 1000);
            } else {
                resolve(inputContent);
                nombrarReporte.remove();
            }
        });

        cancelButton.addEventListener('click', function() {
            nombrarReporte.remove();
            reject('Input cancelado');
        });

        nombrarReporte.classList.remove('hidden');

        setTimeout(function(){
            document.getElementById('textInputContent').focus();
        }, 350);
    });
}


function checkForLateActivities() {
    const taskList = document.getElementById('task-list-one');
    if (taskList) {
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        const cards = taskList.querySelectorAll('.card');
        cards.forEach(card => {
            const dateElement = card.querySelector('#beforeThisDate');
            if (!dateElement) return;
            const [year, month, day] = dateElement.textContent.trim().split('-').map(Number);
            const dueDate = new Date(year, month - 1, day);
            dueDate.setHours(0, 0, 0, 0);
            // console.log(today, ' : ', dueDate);
            if (dueDate.getTime() === today.getTime()) {
                card.classList.add('almostLate');
            } else if (dueDate.getTime() < today.getTime()) {
                card.classList.add('lateActivity'); 
            }
        });
    }
}

function removeLateClass(el){
    el.classList.remove('almostLate');
    el.classList.remove('lateActivity');
}

function addColorActTags(){
    const kanbanBody = document.querySelector('.kanban-board');
    if(kanbanBody){
        const tagsDiv = document.getElementById('bg-color-descriptions');
        if(tagsDiv && tagsDiv.innerHTML === ''){
            const lateAct = kanbanBody.querySelectorAll('.lateActivity');
            const almostLateAct = kanbanBody.querySelectorAll('.almostLate');
            if (lateAct.length > 0) {
                console.log('la: ',lateAct);
                const frDiv = document.createElement('div');
                frDiv.classList.add('flexAndSpaceDiv');
                frDiv.innerHTML = `<div class="lateActSq">
                <i class='fa fa-square'></i></div><p>Actividades retrasadas</p>`;
                tagsDiv.appendChild(frDiv);
            }
            
            if (almostLateAct.length > 0) {
                console.log('al: ',almostLateAct);
                const frDiv = document.createElement('div');
                frDiv.classList.add('flexAndSpaceDiv');
                frDiv.innerHTML =`<div class="almostLateSq">
                <i class='fa fa-square'></i></div><p>Último día para finalizar</p>`
                tagsDiv.appendChild(frDiv);
            }
        }
    }
}

function createConfirmationDialog(title, message, onConfirm, onCancel, gText, rText) {
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
    confirmButton.textContent = gText ? gText : 'Confirmar';
    confirmButton.id = 'confirmarAction';
    buttonContainer.appendChild(confirmButton);

    // Botón de cancelar
    const cancelButton = document.createElement('button');
    cancelButton.textContent = rText ? rText : 'Cancelar';
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
        if(onCancel){onCancel();}
        confirmationDiv.remove();
        return false;
    });

    setTimeout(function(){
        document.getElementById('confirmarAction').focus();
    }, 350);

}

function createAlertDialog(title, message, onClose, buttonText) {
    // Crear el overlay y el contenedor del diálogo
    const alertDiv = document.createElement('div');
    alertDiv.classList.add('confirmation-overlay');
    const alertDialog = document.createElement('div');
    alertDialog.classList.add('confirmation-dialog');
    const alertContent = document.createElement('div');
    alertContent.classList.add('confirmation-content');
    
    // Título
    const titleElement = document.createElement('h3');
    titleElement.textContent = title;
    alertContent.appendChild(titleElement);

    // Mensaje del alert
    const messageParagraph = document.createElement('pre');
    messageParagraph.textContent = message;
    alertContent.appendChild(messageParagraph);

    // Botón para cerrar el diálogo
    const buttonContainer = document.createElement('div');
    buttonContainer.classList.add('alert-button');
    const closeButton = document.createElement('button');
    closeButton.textContent = buttonText ? buttonText : 'Cerrar';
    closeButton.classList.add('generalBtnStyle');
    closeButton.classList.add('btn-green');
    buttonContainer.appendChild(closeButton);

    alertContent.appendChild(buttonContainer);
    alertDialog.appendChild(alertContent);
    alertDiv.appendChild(alertDialog);

    document.body.appendChild(alertDiv);

    // Evento para cerrar el diálogo
    closeButton.addEventListener('click', function() {
        if (onClose) {
            onClose();
        }
        alertDiv.remove();
    });

    // Foco automático en el botón
    setTimeout(function(){
        closeButton.focus();
    }, 350);
}


function addButtonEvents(){
    const buttons = document.querySelectorAll('.button');
    buttons.forEach(button => {
        button.setAttribute('tabindex', '0');
        button.addEventListener('keydown', function(event) {
            if (event.key === "Enter" || event.key === " ") {
                event.preventDefault();
                event.target.click();
            }
        });
    });
}

function formatSpanishDate(dateString) {
    //Convertir la fecha a formato en español
    const date = new Date(dateString);

    const adjustedDate = new Date(date.getUTCFullYear(), date.getUTCMonth(), date.getUTCDate());
    return new Intl.DateTimeFormat('es-ES', { day: 'numeric', month: 'long', year: 'numeric' }).format(adjustedDate);
}

document.addEventListener('DOMContentLoaded', function() {
    addButtonEvents();
    document.getElementById('verCuenta').addEventListener('click', function(){ 
        localStorage.removeItem('accountSettings');
        localStorage.setItem('accountSettings', 'true');
        location.href = "accountSettings.php";
    })
    document.getElementById('ajustes').addEventListener('click', function(){ 
        localStorage.removeItem('accountSettings');
        localStorage.setItem('generalSettings', 'true');
        location.href = "accountSettings.php";
    })
});

function validateSession(){
    const url = '../controller/PHP-Request.php?validateSession=true'
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body:null})
    .then(response => response.json())
    .then(data => {
        if (data.success && data.value) {
            console.log('Session: '+data.value);
            return true;
        } else if (!data.success) {
            console.error('Error:', data.message);
            return false;
        } else {
            console.warn('Sesión no válida, redirigiendo al login...',data.message);
            createAlertDialog('Sesión inválida.', 'Por favor, inicia sesión nuevamente.', ()=>{window.location.href = '../index.php';}, 'Aceptar');
        }
    })
    .catch(error => console.error('Error:', error));
}

function checkForNotifications(){
    const url = '../controller/PHP-Request.php?checkForNotifications=true'
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body:null})
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log(data.message);
            console.log(data.notificaciones.length+' notificaciones encontradas');
            const existingBellIcon = document.getElementById('notificationsBell');
            if (existingBellIcon) {
                existingBellIcon.classList.add('foundNotifications')
            }
        } else if (!data.success) {
            if(data.none){
                console.log(data.message);
            }else{
                console.error('Error:', data.message);
            }
        }
    })
    .catch(error => console.error('Error:', error));
}

