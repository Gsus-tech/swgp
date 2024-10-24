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
