document.addEventListener('DOMContentLoaded', function(){
    addMainEvents();
});
    
function addMainEvents(){
    document.getElementById('raiseTicketBtn').addEventListener('click', pageEvents);
    document.getElementById('viewTicketStatusBtn').addEventListener('click', viewTicketStatus);
}

function viewTicketStatus(){
    fetch('../controller/supportManager.php?checkMyticketStatus=true', {
        method: 'POST',
        body: null,
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
    .then(response => response.json()) 
    .then(data => {
        if (data.success) {
            if(data.found){
                document.querySelector('.supportFirstAction').remove();
                typeTicketSelection = 
                `<div id='watchTicketDiv' class='fm-content watchTicketDiv'>
                <button class='closeBtn' id='exWatchTicket'><i class='fa fa-times'></i></button >
                <h3>Ver estado de ticket</h3>
                <i>Selecciona un tipo de ticket: </i>
                <select name='watchTypeSelect' id='watchTypeSelect' class='comboBox'>
                    <option value='none'>Selecciona un tipo de ticket</option>
                    <option value='1'>Reporte de errores del sistema</option>
                    <option value='2'>Actualización de datos de proyecto</option>
                    <option value='3'>Actualización de datos de cuenta</option>
                </select>
                <div>`;
                document.querySelector('.ticketDetails').insertAdjacentHTML('beforeend', typeTicketSelection);
                setTimeout(() => {
                    document.getElementById('exWatchTicket').addEventListener('click', ()=>{
                        cleanAllDivs();
                        createFirstDiv();
                    });
                    document.getElementById('watchTypeSelect').addEventListener('change', ()=>{
                        const selectedValue = document.getElementById('watchTypeSelect').value;
                        const container = document.querySelector('.ticketDetails');
                        const existingDiv = document.getElementById('ticketSelectElement');
                        if(existingDiv){ existingDiv.remove(); }
                        const existingDiv2 = document.getElementById('ticketDetailsDiv');
                        if(existingDiv2){ existingDiv2.remove(); }

                        if(selectedValue !== 'none'){
                            const div = document.createElement('div');
                            div.classList.add('fm-content');
                            div.classList.add('watchTicketDiv');
                            div.id = 'ticketSelectElement';

                            const tag = document.createElement('i');
                            tag.textContent = 'Selecciona un ticket:';
                            let selectHtml;
                            if (selectedValue === '1') {
                                selectHtml = data.selectHtml1;
                            } else if (selectedValue === '2') {
                                selectHtml = data.selectHtml2;
                            } else if (selectedValue === '3') {
                                selectHtml = data.selectHtml3;
                            }
                            div.innerHTML = tag.outerHTML + selectHtml;
                            container.appendChild(div);

                            setTimeout(() => {
                                const sel = document.getElementById('selectTick');
                                if(sel){
                                    if (sel.options.length === 0) {
                                        const noOption = document.createElement('option');
                                        noOption.textContent = 'No se encontraron tickets de este tipo';
                                        noOption.value = 'noTickets';
                                        sel.appendChild(noOption);
                                    }
                                }
                                sel.addEventListener('change', ()=>{showTicketDetails(sel.value);});
                            }, 100);
                        }
                    });
                }, 350);
            }else{
                createAlertDialog('¡Error en la conexión!', 'Error capturado: ' + data.message, null, 'Aceptar');
            }
        } else {
            createAlertDialog('¡Error en la conexión!', 'Error capturado: ' + data.message, null, 'Aceptar');
        }
    })
    .catch(error => {
        console.error('Error en la solicitud AJAX:', error);
    });
}

function showTicketDetails(ticket){
    if(ticket==='noTicketSelected'){
        const existingDiv = document.getElementById('ticketDetailsDiv');
        if(existingDiv){ existingDiv.remove(); }
    }else{
        fetch('../controller/supportManager.php?showTicketDetails=true', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                ticket: ticket
            })
        })
        .then(response => response.json()) 
        .then(data => {
            if (data.success) {
                const existingDiv = document.getElementById('ticketDetailsDiv');
                if(existingDiv){existingDiv.remove();}
                
                const container = document.querySelector('.ticketDetails');
                const div = document.createElement('div');
                div.classList.add('fm-content');
                div.classList.add('ticketDetailsDiv');
                div.id = 'ticketDetailsDiv';

                div.innerHTML = data.data;
                document.querySelector('.main').appendChild(div);


            } else {
                createAlertDialog('¡Error en la conexión!', 'Error capturado: ' + data.message, null, 'Aceptar');
            }
        })
        .catch(error => {
            console.error('Error en la solicitud AJAX:', error);
        });
    }
}

function pageEvents(){
    const existingNewTicketDiv = document.getElementById('categorySelectDiv');
    if (existingNewTicketDiv) {
        existingNewTicketDiv.remove();
    }

    const htmlCode = `
        <div class="selectDiv" id="categorySelectDiv">
            <div class="fm-content firstDiv">
                <button class="closeBtn" id="closeBtn">
                    <i class="fa fa-close"></i>
                </button>
                <h3>Levantar un ticket:</h3>
                <span>Categoría del ticket:</span>
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

function createFirstDiv(){
    const htmlCode = `
    <div class="supportFirstAction">
        <div class="fm-content">
            <button class="generalBtnStyle btn-orange" id="raiseTicketBtn">Levantar ticket</button>
            <button class="generalBtnStyle btn-blue" id="viewTicketStatusBtn">Ver estado de ticket</button>
        </div>
    </div>
    `;
            
    document.querySelector('.main').insertAdjacentHTML('beforeend', htmlCode);
    setTimeout(() => {
        addMainEvents();
    }, 250);
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

        const ticketDetailsDiv = document.getElementById('ticketDetailsDiv');
        if(ticketDetailsDiv){ ticketDetailsDiv.remove();}
        const ticketSelectElement = document.getElementById('ticketSelectElement');
        if(ticketSelectElement){ ticketSelectElement.remove();}
        const watchTicketDiv = document.getElementById('watchTicketDiv');
        if(watchTicketDiv){ watchTicketDiv.remove();}

        deleteSubmitBtn();
}
    
function loadTicketTypes() {
    document.getElementById('categorySelect').addEventListener('change', function() {
        const selectedValue = this.value;
        const selectDiv = document.querySelector('.selectDiv');

        cleanAllDivs();
        if(selectedValue != 'none'){
            if (selectedValue === 'cambiosPro') {
                const url = `../controller/supportManager.php?getProjectList=true`;
                
                makeAjaxRequest(url, 'POST', null, function(response) {
                    try {
                        if (response.success) {
                            const projects = response.data;

                            if (Array.isArray(projects) && projects.length > 0) {
                                
                                let projectOptions = projects.map(project => {
                                        return `<option value="${project.id_proyecto}">${project.nombre}</option>`;
                                    }).join('');
                                
                                const htmlCode = `
                                    <div class="fm-content" id="correctionDiv">
                                        <span>Seleccionar proyecto:</span>
                                        <select id="projectSelect" name="projectSelect" class="comboBox">
                                            <option value="none">-- Selecciona un proyecto --</option>
                                            ${projectOptions}
                                        </select>
                                    </div>
                                `;
                                
                                selectDiv.insertAdjacentHTML('beforeend', htmlCode);
                                projectSelectEvent();
                            }else {
                                console.error('Error al obtener el listado de proyectos:', data.message);
                            }
                        }
                    } catch (error) {
                        console.error('Error al analizar la respuesta JSON:', error);
                        console.log('Respuesta recibida:', response); // Ver la respuesta exacta que se recibe
                    }
                }, function(error) {
                    if(error === 'No se encontraron proyectos a cargo del usuario actual'){
                        const htmlCode = `
                            <div class="fm-content" id="correctionDiv">
                                <p>Parece que no hay proyectos a tu cargo.</p><br>
                                <p>Si deseas cambiar datos de algún proyecto, consultalo con el líder del proyecto.</p>
                            </div>
                            `;
                            
                            selectDiv.insertAdjacentHTML('beforeend', htmlCode);
                    }else{
                        console.error('Error en la solicitud AJAX:', error);
                    }
                });
            }
            else if(selectedValue === 'erroresSys'){
                const htmlCode = `
                <div class="ticketExplanationDiv" id="ticketExplanationDiv">
                    <div class="fm-content">
                        <h3>Descripción:</h3>
                        <span for="ticketTitle">Título del ticket:</span>
                        <input type="text" id="ticketTitle" name="ticketTitle" class="input-text ticketInputVl" placeholder="Ingresa el título del ticket" maxlength="100" oninput="resetField(this)">
                        
                        <span for="ticketDescription">Motivo o descripción de la situación:</span>
                        <textarea id="ticketDescription" name="ticketDescription" class="textarea-input ticketInputVl" placeholder="Ingresa la descripción del ticket" maxlength="500" rows="4" oninput="resetField(this)"></textarea>
                        
                        <span for="ticketImage">Adjuntar imagen:</span>
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
                            <option value="mailUpdate">Actualizar mi correo</option>
                        </select>

                        <br>
                        <span for="newValue">Introduce el dato correcto:</span>
                        <input type="text" name="newValue" id="newValue" class="input-text ticketInputVl" oninput="resetField(this)" placeholder="Dato correcto del campo seleccionado." maxlength="90" autocomplete="off">
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
        createFirstDiv();
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
                        <span for="name">Nombre:</span>
                        <input type="text" id="name" name="name" class="input-text ticketInputVl" placeholder="Ingresa el nombre del nuevo integrante" oninput="resetField(this)" autocomplete="off">
                   
                        <span for="email">Correo:</span>
                        <input type="email" id="email" name="email" class="input-text ticketInputVl" placeholder="Ingresa el correo del nuevo integrante" oninput="resetField(this)" autocomplete="off">
                  
                        <span for="department">Departamento:</span>
                        <input type="text" id="department" name="department" class="input-text ticketInputVl" placeholder="Ingresa el departamento del nuevo integrante" oninput="resetField(this)" autocomplete="off">
                    </div>
                </div>
                `;

                document.querySelector('.main').insertAdjacentHTML('beforeend', htmlCode);
                createSubmitBtn('t-2');
            }
            else if(selectedValue === 'removeMember' || selectedValue === 'changePermitions'){
                //Obtener integrantes para ambos selects
                const pid = document.getElementById('projectSelect').value;
                const url = `../controller/supportManager.php?getMemberList=true&pid=${pid}`;
                makeAjaxRequest(url, 'POST', null, function(response) {
                    try {
                        if (response.success) {
                            const users = response.data;
                            if (Array.isArray(users)) {
                                
                                let userOptions = users.length > 0 ? users.map(user => {
                                    return `<option value="${user.id_usuario}" rp="${user.responsable}">${user.nombre}</option>`;
                                }).join('') : '';
                                const active = users.length > 0 ? '' : ' disabled';
                                const defaultOption = users.length > 0 ? `<option value="none">--  Selecciona una opción  --</option>` : `<option value="none">--  Sin integrantes registrados  --</option>`;

                                if(selectedValue === 'removeMember'){
                                    const htmlCode = `
                                    <div class="projectUpdateDiv deleteMemberDiv" id="projectUpdateDiv">
                                        <div class="fm-content" id="divDeleteMember">
                                            <span for="delMemberSelect">Selecciona al miembro que deseas eliminar:</span>
                                            <select id="memberSelect" name="delMemberSelect" class="comboBox" onchange="addCauseBox(this)">
                                                ${defaultOption}
                                                ${userOptions}
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
                                            <span for="permitionMemberSelect">Selecciona un integrante del proyecto:</span>
                                            <select id="memberSelect" name="permitionMemberSelect" class="comboBox" onchange="promoteDemote(this)" ${active}>
                                                ${defaultOption}
                                                ${userOptions}
                                            </select>
                                        </div>
                                    </div>
                                    `;
                                    document.querySelector('.main').insertAdjacentHTML('beforeend', htmlCode);
                                }
                            }
                        }
                    } catch (error) {
                        console.error('Error al analizar la respuesta JSON:', error);
                        console.log('Respuesta recibida:', response); // Ver la respuesta exacta que se recibe
                    }
                }, function(error) {
                    console.error('No se pudo recuperar el listado de integrantes del proyecto.\n', error);
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                });
            }
            else if(selectedValue === 'projectDataCorrection'){
                const htmlCode = `
                <div class="ticketExplanationDiv" id="ticketExplanationDiv">
                    <div class="fm-content">
                        <h3>Descripción:</h3>
                        <span for="ticketTitle">Título del ticket:</span>
                        <input type="text" id="ticketTitle" name="ticketTitle" class="input-text ticketInputVl" placeholder="Ingresa el título del ticket" maxlength="100"
                        value = "Corregir datos de Proyecto." oninput="resetField(this)">
                        
                        <span for="ticketDescription">Describe los cambios que consideras necesarios realizar:</span>
                        <textarea id="ticketDescription" name="ticketDescription" class="textarea-input ticketInputVl" placeholder="Ingresa la descripción del ticket" maxlength="1000" rows="4" oninput="resetField(this)"></textarea>
                        
                    </div>
                </div>
                `;
                    
                document.querySelector('.main').insertAdjacentHTML('beforeend', htmlCode);
                createSubmitBtn('t-2');
            }
        }
    });
}

function projectSelectEvent(){
    document.getElementById('projectSelect').addEventListener('change', function(){
        const targetDiv = document.getElementById('correctionDiv');
        const pid = this.value;
        const psdPid = parseInt(pid, 10);

            if(Number.isInteger(psdPid)){
                const htmlCode = `
                <div id='selectActionPrj'>
                <br>
                <span for="correctionType">Tipo de corrección:</span><br>
                <select id="correctionType" name="correctionType" class="comboBox">
                    <option value="none">--  Selecciona una opción  --</option>
                    <option value="addMember">Agregar integrante al equipo</option>
                    <option value="removeMember">Eliminar integrante del equipo</option>
                    <option value="changePermitions">Cambiar permisos de un integrante</option>
                    <option value="projectDataCorrection">Corrección de datos del proyecto</option>
                </select>
                </div>
            `;
            targetDiv.insertAdjacentHTML('beforeend', htmlCode);

            addProjectSupportEvents();
        }else{
            console.log('is not Integer');
            const removeDiv = document.getElementById('selectActionPrj');
            if(removeDiv){
                removeDiv.remove();
            }
            const removeUpdateDiv = document.getElementById('projectUpdateDiv');
            if(removeUpdateDiv){
                removeUpdateDiv.remove();
            }
            const removeExplanationDiv = document.getElementById('ticketExplanationDiv');
            if(removeExplanationDiv){
                removeExplanationDiv.remove();
            }
            const removeBtn = document.getElementById('submitTicketBtn');
            if(removeBtn){
                removeBtn.remove();
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
        <span for="ticketDescription">Motivo por el que deseas expulsar a este miembro:</span>
        <textarea id="ticketDescription" name="ticketDescription" class="textarea-input ticketInputVl" placeholder="Ingresa el motivo por el que deseas expulsar a este miembro" maxlength="500" rows="4" oninput="resetField(this)"></textarea>            
        `;
        document.getElementById('divDeleteMember').appendChild(dv);
        createSubmitBtn('t-2');
    }
}

function promoteDemote(element){
    const existingPermitionsDiv = document.getElementById('permitionDv');
    const usersDiv = document.getElementById('memberSelect');
    const crRp = usersDiv.options[usersDiv.selectedIndex].getAttribute('rp');

    let msj = "No se pudo recuperar la información del usuario.";
    let btnTxt = "- - - - -";
    let btnClass = "";
    let atributo = "";
    let dis = 'disabled';

    if (existingPermitionsDiv){
        existingPermitionsDiv.remove();
    }
    if(element.value != 'none' && crRp){
        if(crRp === '0'){
            msj = 'Este usuario cuenta con permisos limitados.';
            btnTxt = 'Ascender usuario';
            btnClass = 'btn-green';
            atributo ='0';
            dis = '';
        }else if(crRp === '1'){
            msj = 'Este usuario cuenta con privilegios de líder de proyecto.';
            btnTxt = 'Quitar privilegios';
            btnClass = 'btn-red';
            atributo = '1';
            dis = '';
        }
 
        const dv = document.createElement('div');
        dv.id = 'permitionDv';
        dv.innerHTML = `
        <br>
        <p>${msj}</p>
        <br>
        <button id='submitTicketBtn' submitType='t-2' class='generalBtnStyle ${btnClass}' onclick="sendTicket()" cr-rp="${atributo}" ${dis}>${btnTxt}</button>
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
                createAlertDialog('¡Aviso!', 'Por favor, selecciona un archivo de imagen válido (jpeg, jpg o png).', 
                ()=>{fileInput.value = '';
                fileChosen.textContent = "Ningún archivo seleccionado";},
                'Aceptar');
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
                    createAlertDialog('¡Aviso!', 'La imagen seleccionada excede los 10Mb.\nPor favor, selecciona otra imagen para continuar.', 
                        ()=>{ return reject('Image size too large'); }, 'Aceptar');   
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
        }  else if (submitType === "t-2") {
            const projectCorrectionType = document.getElementById('correctionType').value;
            const sbtBtn = document.getElementById('submitTicketBtn');
            if(projectCorrectionType === 'changePermitions'){
                const crRp = sbtBtn.getAttribute('cr-rp');
                
                if (crRp !== null) {
                    formData.append('crRp', crRp);
                    console.log(formData);
                } else {
                    return reject('Error: No se pudo recuperar el atributo cr-rp.');
                }
            }
            resolve(formData);
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
            if(input.id === 'ticketDescription'){
                const validation3 = testLenght('min', 10, 'ticketDescription');
                if(!validation3){submitFlag = false;}
            }
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
    }  else if (submitType === "t-2") {
        if (submitFlag === true) {
            createConfirmationDialog(
                "Levantando ticket de corrección de datos de proyecto",
                `¿Confirmas que deseas que se realicen los cambios a los datos del proyecto que describes en el ticket?`,
                function() {
                    sendTicket();
                },
                function() {
                    submitFlag = false;
                }
            );
        }
    }else if (submitType === "t-3") {
        if (submitFlag === true) {
            const newValue = document.querySelector('.ticketInputVl').value;
            const selectValue = document.getElementById('correctionType').value;
            const field2Change = selectValue === 'deptoUpdate' ? 'Departamento' : 'Correo';

            createConfirmationDialog(
                "Confirmar actualización de información",
                `¿Confirmas que deseas cambiar tu ${field2Change} por ${newValue}?`,
                function() {
                    sendTicket();
                },
                function() {
                    submitFlag = false;
                }
            );
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
                            createAlertDialog('¡Aviso!', data.message, ()=>{location.reload();}, 'Aceptar');  
                        } else {
                            createAlertDialog('¡Error!', data.message, ()=>{location.reload();}, 'Aceptar');  
                        }
                    })
                    .catch(error => {
                        console.error('Error en la solicitud AJAX:', error);
                        createAlertDialog('¡Error!', 'Ocurrió un error al establecer la conexión.\nPor favor, toma captura de pantalla y levanta un ticket de soporte.', null, 'Aceptar');
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
