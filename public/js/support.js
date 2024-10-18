document.addEventListener('DOMContentLoaded', function(){
    document.getElementById('raiseTicketBtn').addEventListener('click', pageEvents);
});
    
function pageEvents(){
    const existingNewTicketDiv = document.getElementById('categorySelectDiv');
    if (existingNewTicketDiv) {
        existingNewTicketDiv.remove();
    }

    const htmlCode = `
        <div class="selectDiv" id="categorySelectDiv">
            <div class="fm-content">
                <button class="closeBtn" id="closeBtn">
                    <i class="fa fa-close"></i>
                </button>
                <h3>Levantar un ticket:</h3>
                <label for="categorySelect">Categoría del ticket:</label>
                <select id="categorySelect" name="categorySelect" class="comboBox">
                    <option value="none">--  Selecciona una opción  --</option>
                    <option value="erroresSys">Errores del sistema</option>
                    <option value="cambiosPro">Corrección o cambios en proyecto</option>
                    <option value="cuenta">Correción de datos de cuenta</option>
                </select>
            </div>
        </div>
    `;


    document.querySelector('.main').insertAdjacentHTML('beforeend', htmlCode);
    loadTicketTypes();
    document.querySelector('.supportFirstAction').remove();
}

function cleanAllDivs(){
    const existingTypeDiv = document.getElementById('correctionDiv');
        if (existingTypeDiv) {
            existingTypeDiv.remove();
        }
        const existingDescriptionDiv = document.getElementById('ticketExplanationDiv');
        if (existingDescriptionDiv) {
            existingDescriptionDiv.remove();
        }
        const existingUpdateDiv = document.getElementById('projectUpdateDiv');
        if (existingUpdateDiv) {
            existingUpdateDiv.remove();
        }
        deleteSubmitBtn();
}
    
function loadTicketTypes() {
    document.getElementById('categorySelect').addEventListener('change', function() {
        const selectedValue = this.value;
        const selectDiv = document.querySelector('.selectDiv');

        cleanAllDivs();
        if(selectedValue != 'none'){
            if (selectedValue === 'cambiosPro') {
                const htmlCode = `
                <div class="fm-content" id="correctionDiv">
                    <label for="correctionType">Tipo de corrección:</label>
                    <select id="correctionType" name="correctionType" class="comboBox">
                        <option value="none">--  Selecciona una opción  --</option>
                        <option value="addMember">Agregar miembro al equipo</option>
                        <option value="removeMember">Eliminar miembro del equipo</option>
                        <option value="changePermitions">Cambiar permisos de un miembro actual</option>
                        <option value="projectDataCorrection">Corrección de datos del proyecto</option>
                    </select>
                </div>
                `;
                
                selectDiv.insertAdjacentHTML('beforeend', htmlCode);
                addProjectSupportEvents();
            }
            else if(selectedValue === 'erroresSys'){
                const htmlCode = `
                <div class="ticketExplanationDiv" id="ticketExplanationDiv">
                    <div class="fm-content">
                        <h3>Descripción:</h3>
                        <label for="ticketTitle">Título del ticket:</label>
                        <input type="text" id="ticketTitle" name="ticketTitle" class="input-text ticketInputVl" placeholder="Ingresa el título del ticket" maxlength="100" oninput="resetField(this)">
                        
                        <label for="ticketDescription">Motivo o descripción de la situación:</label>
                        <textarea id="ticketDescription" name="ticketDescription" class="textarea-input ticketInputVl" placeholder="Ingresa la descripción del ticket" maxlength="500" rows="4" oninput="resetField(this)"></textarea>
                        
                        <label for="ticketImage">Adjuntar imagen:</label>
                        <div class='selectImageDiv'>
                            <input type="file" id="ticketImage" class="file-input" accept="image/jpeg, image/png" style="display: none;">
                            <button id="selectImageBtn" class="selectImageBtn">Seleccionar una imágen</button>
                            <span id="file-chosen" class="fileChosenLb">Ningún archivo seleccionado</span>
                        </div>
                    </div>
                </div>
                `;

                document.querySelector('.main').insertAdjacentHTML('beforeend', htmlCode);
                createSubmitBtn('t-1');
                setTimeout(function() {
                    updateBtnLanguage();
                }, 200);
            }
            else if(selectedValue === 'cuenta'){
                const htmlCode = `
                <div class="ticketExplanationDiv" id="ticketExplanationDiv" style="max-width: 550px;">
                    <div class="fm-content">
                        <h3>Descripción:</h3>
                        
                        <select id="correctionType" name="correctionType" class="comboBox">
                            <option value="none">--  Selecciona una opción  --</option>
                            <option value="deptoUpdate">Cambio de departamento</option>
                            <option value="deptoUpdate">Actualizar mi correo</option>
                        </select>

                        <br>
                        <label for="newValue">Introduce el dato correcto:</label>
                        <input type="text" name="newValue" id="newValue" class="input-text ticketInputVl" oninput="resetField(this)" placeholder="Tipo de corrección de datos" maxlength="90">
                    </div>
                </div>
                `;
                    
                document.querySelector('.main').insertAdjacentHTML('beforeend', htmlCode);
                createSubmitBtn('t-3');
            }
        }
    });

    document.getElementById('closeBtn').addEventListener('click', function() {
        const ticketDiv = document.getElementById('categorySelectDiv');
        if (ticketDiv) {
            ticketDiv.remove();
        }
        cleanAllDivs();
            const htmlCode = `
            <div class="supportFirstAction">
                <div class="fm-content">
                    <button class="generalBtnStyle btn-orange" id="raiseTicketBtn">Levantar ticket</button>
                    <button class="generalBtnStyle btn-blue" id="viewTicketStatusBtn">Ver estado de ticket</button>
                </div>
            </div>
            `;
                    
            document.querySelector('.main').insertAdjacentHTML('beforeend', htmlCode);
            document.getElementById('raiseTicketBtn').addEventListener('click', pageEvents);
    });
}

function addProjectSupportEvents(){
    document.getElementById('correctionType').addEventListener('change', function() {
        const selectedValue = this.value;

        const existingDescriptionDiv = document.getElementById('ticketExplanationDiv');
        if (existingDescriptionDiv) {
            existingDescriptionDiv.remove();
        }
        const existingUpdateDiv = document.getElementById('projectUpdateDiv');
        if (existingUpdateDiv) {
            existingUpdateDiv.remove();
        }
        deleteSubmitBtn();

        if(selectedValue != 'none'){
            if(selectedValue === 'addMember'){
                const htmlCode = `
                <div class="projectUpdateDiv" id="projectUpdateDiv">
                    <div class="fm-content">
                        <label for="name">Nombre:</label>
                        <input type="text" id="name" name="name" class="input-text ticketInputVl" placeholder="Ingresa el nombre del nuevo integrante" oninput="resetField(this)" autocomplete="off">
                   
                        <label for="email">Correo:</label>
                        <input type="email" id="email" name="email" class="input-text ticketInputVl" placeholder="Ingresa el correo del nuevo integrante" oninput="resetField(this)" autocomplete="off">
                  
                        <label for="department">Departamento:</label>
                        <input type="text" id="department" name="department" class="input-text ticketInputVl" placeholder="Ingresa el departamento del nuevo integrante" oninput="resetField(this)" autocomplete="off">
                    </div>
                </div>
                `;

                document.querySelector('.main').insertAdjacentHTML('beforeend', htmlCode);
                createSubmitBtn('t-2');
            }
            else if(selectedValue === 'removeMember' || selectedValue === 'changePermitions'){
                //Obtener integrantes para ambos selects
                if(selectedValue === 'removeMember'){
                    const htmlCode = `
                    <div class="projectUpdateDiv deleteMemberDiv" id="projectUpdateDiv">
                        <div class="fm-content" id="divDeleteMember">
                            <label for="delMemberSelect">Selecciona al miembro que deseas eliminar:</label>
                            <select id="memberSelect" name="delMemberSelect" class="comboBox" onchange="addCauseBox(this)">
                                <option value="none">--  Selecciona una opción  --</option>
                                <option value="1">Oscar Ramirez</option>
                                <option value="2">Jorge Campos</option>
                            </select>
                        </div>
                    </div>
                    `;
                    document.querySelector('.main').insertAdjacentHTML('beforeend', htmlCode);
                }
                if(selectedValue === 'changePermitions'){
                    const htmlCode = `
                    <div class="projectUpdateDiv permitionMemberDiv" id="projectUpdateDiv">
                        <div class="fm-content" id="setPermitionDiv">
                            <label for="permitionMemberSelect">Selecciona un integrante del proyecto:</label>
                            <select id="memberSelect" name="permitionMemberSelect" class="comboBox" onchange="promoteDemote(this)">
                                <option value="none">--  Selecciona una opción  --</option>
                                <option value="1">Oscar Ramirez</option>
                                <option value="2">Jorge Campos</option>
                            </select>
                        </div>
                    </div>
                    `;
                    document.querySelector('.main').insertAdjacentHTML('beforeend', htmlCode);
                }
            }
            else if(selectedValue === 'projectDataCorrection'){
                const htmlCode = `
                <div class="ticketExplanationDiv" id="ticketExplanationDiv">
                    <div class="fm-content">
                        <h3>Descripción:</h3>
                        <label for="ticketTitle">Título del ticket:</label>
                        <input type="text" id="ticketTitle" class="input-text ticketInputVl" placeholder="Ingresa el título del ticket" maxlength="100"
                        value = "Corregir datos de Proyecto." oninput="resetField(this)">
                        
                        <label for="ticketDescription">Describe los cambios que consideras necesarios realizar:</label>
                        <textarea id="ticketDescription" class="textarea-input ticketInputVl" placeholder="Ingresa la descripción del ticket" maxlength="1000" rows="4" oninput="resetField(this)"></textarea>
                        
                    </div>
                </div>
                `;
                    
                document.querySelector('.main').insertAdjacentHTML('beforeend', htmlCode);
                createSubmitBtn('t-2');
            }
        }
    });
}

function addCauseBox(element){
    const existingExpulsionDiv = document.getElementById('expulsionDiv');
    if (existingExpulsionDiv) {
        existingExpulsionDiv.remove();
    }
    deleteSubmitBtn();
    if(element.value != 'none'){
        const dv = document.createElement('div');
        dv.id = 'expulsionDiv';
        dv.innerHTML = `
        <br>
        <label for="ticketDescription">Motivo por el que deseas expulsar a este miembro:</label>
        <textarea id="ticketDescription" class="textarea-input ticketInputVl" placeholder="Ingresa el motivo por el que deseas expulsar a este miembro" maxlength="500" rows="4" oninput="resetField(this)"></textarea>            
        `;
        document.getElementById('divDeleteMember').appendChild(dv);
        createSubmitBtn('t-2');
    }
}

function promoteDemote(element){
    const existingPermitionsDiv = document.getElementById('permitionDv');
    if (existingPermitionsDiv) {
        existingPermitionsDiv.remove();
    }
    if(element.value != 'none'){
        const dv = document.createElement('div');
        dv.id = 'permitionDv';
        dv.innerHTML = `
        <br>
        <p>Este usuario cuenta con permisos de usuario limitados.</p>
        <br>
        <button class='generalBtnStyle' onclick="submitTicket()">Otorgar permisos</button>
        `;
        document.getElementById('setPermitionDiv').appendChild(dv);
    }
}

function createSubmitBtn(submitType){
    deleteSubmitBtn();

    const ticketButton = document.createElement('button');
    ticketButton.textContent = 'Levantar ticket';
    ticketButton.classList.add('generalBtnStyle');
    ticketButton.classList.add('submitTicketBtn');
    ticketButton.classList.add('btn-green');
    ticketButton.id = 'submitTicketBtn';
    if(submitType == 't-1'){
        ticketButton.classList.add('type1Btn');
    }else if(submitType == 't-2'){
        ticketButton.classList.add('type2Btn');
    }else{
        ticketButton.classList.add('type3Btn');
    }
    ticketButton.setAttribute('submitType', submitType);
    ticketButton.addEventListener('click', submitTicket);
    
    document.body.appendChild(ticketButton);
}

function deleteSubmitBtn(){
    const existingBtn = document.getElementById('submitTicketBtn');
    if (existingBtn) {
        existingBtn.remove();
    }
}

function updateBtnLanguage() {
    const fileInput = document.getElementById('ticketImage');
    const fileChosen = document.getElementById('file-chosen');
    const customButton = document.getElementById('selectImageBtn');

    customButton.addEventListener('click', function() {
        fileInput.click();
    });

    fileInput.addEventListener('change', function() {
        if (fileInput.files.length > 0) {
            const file = fileInput.files[0];
            const validImageTypes = ['image/jpeg', 'image/png'];
            if (!validImageTypes.includes(file.type)) {
                alert('Por favor, selecciona un archivo de imagen válido (jpeg, jpg o png).');
                fileInput.value = '';
                fileChosen.textContent = "Ningún archivo seleccionado";
            } else {
                fileChosen.textContent = file.name;
            }
        } else {
            fileChosen.textContent = "Ningún archivo seleccionado";
        }
    });
}


function dataForTicket() {
    return new Promise((resolve, reject) => {
        const submitType = document.getElementById('submitTicketBtn').getAttribute('submitType');
        console.log('getting data.');
        const formData = new URLSearchParams();
        formData.append('ticketType', submitType);

        const inputs = document.querySelectorAll('.ticketInputVl');
        inputs.forEach(input => {
            formData.append(input.name, input.value);
        });

        const selects = document.querySelectorAll('.comboBox');
        selects.forEach(select => {
            if(select.id !== 'categorySelect'){
                formData.append(select.name, select.value);
            }
        });

        if (submitType === "t-1") {
            const fileInput = document.getElementById('ticketImage');
            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];
                const maxSize = 10 * 1024 * 1024;
                if (file.size > maxSize) {
                    alert('La imagen seleccionada excede los 10Mb.\nPor favor, selecciona otra imagen para continuar.');
                    return reject('Image size too large'); 
                }

                const reader = new FileReader();
                reader.onloadend = function() {
                    formData.append('ticketImageBase64', reader.result); // <- esta es otra forma de convertir la imagen 
                    resolve(formData); 
                };
                reader.onerror = function() {
                    reject('Error al leer la imagen');
                };
                reader.readAsDataURL(file);
            } else {
                resolve(formData);
            }
        } else {
            resolve(formData);
        }
    });
}


function submitTicket(){
    const submitType = document.getElementById('submitTicketBtn').getAttribute('submitType');
    let submitFlag = true;
    const inputs = document.querySelectorAll('.ticketInputVl');
    
    inputs.forEach(input => {
        if (input.value === "") {
            submitFlag = false;
            input.classList.add('highlight-error');
            setTimeout(function() {
                input.classList.remove('highlight-error');
            }, 1000);
        } else if (input.type === "email") {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(input.value)) {
                submitFlag = false;
                input.classList.add('highlight-error');
                console.log('Email inválido:', input.value);
                setTimeout(function() {
                    input.classList.remove('highlight-error');
                }, 1000);
            }
        }else{
            const validation1 = testValue('light', input.id, 'ticket');
            if(!validation1){submitFlag = false;}
            const validation2 = testControlledTextInput(input.id);
            if(!validation2){submitFlag = false;}
        }
    });

    const selects = document.querySelectorAll('.comboBox');
    selects.forEach(select => {
        if (select.value === "none"){
            submitFlag = false;
            select.classList.add('highlight-error');
            setTimeout(function() {
                select.classList.remove('highlight-error');
            }, 1000);
        }
    });

    if (submitType === "t-1") {
        const fileInput = document.getElementById('ticketImage');
        if(submitFlag === true){
            if (fileInput.files.length === 0) {
                createConfirmationDialog(
                    "Mensaje de confimarción",
                    "Nuestro equipo de sistemas podrá identificar y resolver este problema mucho más rápido si proporcionas una captura de pantalla del error.\n\n¿Enviar ticket sin imagenes?",
                    function() {
                        sendTicket();
                    },
                    function() {
                        submitFlag = false;
                    },
                    'Continuar', 'Cancelar'
                );
            } else {
                sendTicket();
            } 
        }
    } else {
        if (submitFlag === true) {
            sendTicket();
        }
    }
}

function sendTicket(){
    createConfirmationDialog(
        "Abriendo Ticket de Soporte",
        "¿Estás seguro que deseas abrir este ticket?",
        function() {
            dataForTicket().then(data => {
                if (data !== null) {
                    fetch('../controller/supportManager.php?newTicket=true', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: data
                    })
                    .then(response => response.json()) 
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            location.reload();
                        } else {
                            alert(`Error: ${data.message}`);
                        }
                    })
                    .catch(error => {
                        console.error('Error en la solicitud AJAX:', error);
                    });
                }
            }).catch(error => {
                console.error('Error en el procesamiento del ticket:', error);
            });
        },
        function() {
            console.log("Confirmación de nuevo ticket cancelada.");
        }
    );
}
