document.addEventListener('DOMContentLoaded', function() {

    if(document.body.classList.contains('bigFont') || document.body.classList.contains('lightMode') || document.body.classList.contains('darkMode')){
        addResetPreferencesBtn();
    }

    document.getElementById('generalTab').addEventListener('click', function() {
        setActiveTab('generalTab');
        localStorage.setItem('generalSettings', 'true');
        localStorage.removeItem('accountSettings');
    });

    document.getElementById('accountTab').addEventListener('click', function() {
        setActiveTab('accountTab');
        localStorage.setItem('accountSettings', 'true');
        localStorage.removeItem('generalSettings');
        initialDiv();
    });

    // document.getElementById('notificationToggle').addEventListener('click', toggleNotification);

    const valor = localStorage.getItem('accountSettings');
    const valor2 = localStorage.getItem('generalSettings');
    if(valor){
        document.getElementById('generalTabContent').classList.remove('activeContent');
        document.getElementById('accountTabContent').classList.add('activeContent');
        document.getElementById('generalTab').classList.remove('active');
        document.getElementById('accountTab').classList.add('active');
        initialDiv();
    }else if(valor2){
        document.getElementById('generalTabContent').classList.add('activeContent');
        document.getElementById('accountTabContent').classList.remove('activeContent');
        document.getElementById('generalTab').classList.add('active');
        document.getElementById('accountTab').classList.remove('active');
    }else{
        localStorage.setItem('generalSettings', 'true');
        this.location.reload();
    }
});

function addResetPreferencesBtn(){
    const btnDiv = document.getElementById('preferenceOptions');
    if(!document.getElementById('resetPreferencesBtn')){
        btnDiv.innerHTML = `
        <button class="generalBtnStyle btn-blue resetBtn" id="resetPreferencesBtn">Reestablecer configuración</button>
        `;
        setTimeout(function() {
            document.getElementById('resetPreferencesBtn').addEventListener('click', resetPreferences);
        }, 350);
    }
}

function resetPreferences(){
    const url = "../controller/accountSettingsManager.php?resetPreferenceValues=true";
    makeAjaxRequest(url, 'POST', null, 
        function(response){
            if(response.success){
                console.log(response.message)
                location.reload();
            }
        },
        ()=>{
            alert('Ocurrió un error al reestablecer las preferencias de usuario. Por favor, intenta de nuevo más tarde.');
        }
    );
}

function toggleNotification() {
    const button = document.getElementById('notificationToggle');
    button.classList.toggle('on');
    button.textContent = button.classList.contains('on') ? 'ON' : 'OFF';
}

function initialDiv(){
    const url = "../controller/accountSettingsManager.php?getUserInfo=true";
    makeAjaxRequest(url, 'POST', null, 
        function(response){
            const dv = document.getElementById('settingsDiv');
            dv.classList.add('section');
            dv.classList.add('sct2');
            dv.innerHTML = `
            <label class="bold" for="name">Nombre:</label><br>
            <input class="input" type="text" name="name" id="name" value="${response.data.nombre}" placeholder="Tu nombre" title="Tu nombre" autocomplete="off" oninput="resetField(this)">
            <input type="hidden" id="nameH" value="${response.data.nombre}">
            
            <label class="bold" for="nickName">Usuario:</label><br>
            <input class="input" type="text" name="nickName" id="nickName" value="${response.data.nickname}" placeholder="Nombre de usuario" title="Nombre que se muestra en tu sesión actual" oninput="resetField(this)">
            <input type="hidden" id="nickNameH" value="${response.data.nickname}">
            
            <label class="bold" for="correo">Correo:</label><br>
            <input disabled class="input" type="text" name="correo" value="${response.data.correo}" placeholder="Correo">

            <label class="bold" for="departamento">Departamento:</label><br>
            <input disabled class="input" type="text" name="departamento" value="${response.data.departamento}" placeholder="Departamento">

            <div class="flexAndSpaceDiv">
                <button class="generalBtnStyle btn-green dataUpdate" id="dataUpdate">Guardar Cambios</button>
                <button class="generalBtnStyle btn-blue passwordUpdate" id="passwordUpdate">Cambiar contraseña</button>
            </div>
            `;
            
            setTimeout(function() {
                document.getElementById('dataUpdate').addEventListener('click', updateData);
                document.getElementById('passwordUpdate').addEventListener('click', updatePassword);
            }, 350);
        }, function(){
            const dv = document.getElementById('settingsDiv');
            dv.classList.add('section');
            dv.innerHTML = `
                <h3>Algo salio mal...</h3>
                <br>
                <p>Sucedió un error al recuperar tus datos.</p>
                <br>
                <p>Por favor, levanta un ticket para reportar este problema.</p>
            `;
        }
    );

}

function setActiveTab(tabId) {
    const tabs = document.querySelectorAll('.tab');
    tabs.forEach(tab => tab.classList.remove('active'));

    const contents = document.querySelectorAll('.tabContent');
    contents.forEach(content => content.classList.remove('activeContent'));

    document.getElementById(tabId).classList.add('active');
    document.getElementById(`${tabId}Content`).classList.add('activeContent');
}

function validarCampos(){
    const name = document.getElementById('name');
    const nickName = document.getElementById('nickName');
    let nameFlag = testRegex('name');
    if(nameFlag === false){
        return false;
    }
    nameFlag = testLenght('min', 8, 'name');
    if(nameFlag === false){
        return false;
    }
    nameFlag = testLenght('max', 45, 'name');
    if(nameFlag === false){
        return false;
    }
    nameFlag = testValue('strict', 'name');
    if(nameFlag === false){
        return false;
    }
    nameFlag = testControlledTextInput('name');
    if(nameFlag === false){
        return false;
    }

    let nickNameFlag = testControlledTextInput('nickName');
    if(nickNameFlag === false){
        return false;
    }
    nickNameFlag = testRegex('nickName');
    if(nickNameFlag === false){
        return false;
    }
    nickNameFlag = testLenght('min', 3, 'nickName');
    if(nickNameFlag === false){
        return false;
    }
    nickNameFlag = testLenght('max', 45, 'nickName');
    if(nickNameFlag === false){
        return false;
    }

    return true;
}

function updateData(){
    const name = document.getElementById('name').value;
    const nickName = document.getElementById('nickName').value;
    const nameH = document.getElementById('nameH');
    const nickNameH = document.getElementById('nickNameH');
    if (name != nameH.value || nickName != nickNameH.value) {
        if (name.length > 0 && nickName.length > 0) {
            const continuar = validarCampos();
            if(continuar){
                createConfirmationDialog('Mensaje de confirmación', '¿Actualizar datos de la cuenta?', 
                function(){
                    let url = "../controller/accountSettingsManager.php?updateData=true";
                    fetch(url, {
                        method: 'POST',
                        body: new URLSearchParams({
                            name : name,
                            nickName : nickName
                        }),
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success){
                            console.log('Data: ',data.message)
                            location.reload();
                        }
                    });
                    },
                    function(){
                        const divs = document.querySelectorAll('.confirmation-overlay');
                        divs.forEach(div => {
                            div.remove();
                        });
                    }
            );
            }else{
                if(name.value.length == 0){
                    name.classList.add('highlight-error');
                    setTimeout(function() {
                        name.classList.remove('highlight-error');
                    }, 1000);
                }
                if(nickName.value.length == 0){
                    nickName.classList.add('highlight-error');
                    setTimeout(function() {
                        nickName.classList.remove('highlight-error');
                    }, 1000);
                }
            }
        }
    }
}

function updatePassword(){
    const attributes = [
        { name: 'title', value: 'Ingresa tu contraseña actual' },
        { name: 'type', value: 'password' }
    ];
    createInputBox('Actualizar contraseña', 'Ingresa tu contraseña actual', attributes, 'Confirmar', 'Cancelar').then(curPass => {
        fetch("../controller/accountSettingsManager.php?verify=true", {
            method: 'POST',
            body: new URLSearchParams({
                password: curPass
            }),
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).then(response => response.json())
          .then(data => {
                if(data.success){
                document.getElementById('passwordUpdate').remove();
                const dv = document.getElementById('settingsDiv');
                dv.innerHTML = `
                <label class="bold" for="newPass">Ingresa tu nueva contraseña:</label><br>
                <div class="password1Div">
                <input class="input" type="password" name="newPass" id="newPass" placeholder="Nueva contraseña" title="Ingresa tu nueva contraseña" oninput="resetField(this)">    
                <i id="swapVis" class="fa fa-lock swapVis button"></i>
                <i id="swapVis2" class="fa fa-unlock swapVis button hide"></i>
                </div>
                <label class="bold" for="newPassConfirm">Confirmación de nueva contraseña:</label><br>
                <input class="input password2" type="password" name="newPassConfirm" id="newPassConfirm" placeholder="Confirmación de contraseña" title="Confirmación de contraseña" oninput="resetField(this)">
                <div class="flexAndSpaceDiv">
                    <button class="generalBtnStyle btn-blue" id="saveNewPassword">Guardar contraseña</button>
                    <button class="generalBtnStyle btn-red" id="cancelPassUpd">Cancelar</button>
                </div>
                `;
                setTimeout(function() {
                    document.getElementById('newPass').addEventListener('input', () => { removeSpaces(document.getElementById('newPass')) });
                    document.getElementById('newPassConfirm').addEventListener('input', () => { removeSpaces(document.getElementById('newPassConfirm')) });
                    document.getElementById('saveNewPassword').addEventListener('click', sendPassword);
                    document.getElementById('cancelPassUpd').addEventListener('click', cancelPassUpdate);
                    document.getElementById('swapVis').addEventListener('click', swapPasswordVis);
                    document.getElementById('swapVis2').addEventListener('click', swapPasswordVis);
                }, 350);
            }else{
                alert('Contraseña incorrecta.');
            }
          })
          .catch(error => {
            if (error !== 'Input cancelado') {
                console.error(error);
            }
        });
    })
    .catch(error => {
        if (error !== 'Input cancelado') {
            console.error('Error inesperado:', error);
        }
    });
}

function swapPasswordVis(){ 
    const eyeBtn = document.getElementById('swapVis');
    eyeBtn.classList.toggle('hide');
    const eyeBtn2 = document.getElementById('swapVis2');
    eyeBtn2.classList.toggle('hide');
    const input = document.getElementById('newPass');
    if(input.type === 'password'){
        input.type = 'text';
    }else if(input.type === 'text'){
        input.type = 'password';
    }
}

function sendPassword(){
    const password = document.getElementById('newPass');
    const password2 = document.getElementById('newPassConfirm');

    if(politicaContrasena()){
        if(password.value === password2.value){
            createConfirmationDialog('Actualizar contraseña', '¿Seguro que deseas actualizar tu contraseña?',
                function(){
                    let url = "../controller/accountSettingsManager.php?updatePassword=true";
                    fetch(url, {
                        method: 'POST',
                        body: new URLSearchParams({
                            password : password.value
                        }),
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success){
                            alert('Contraseña actualizada');
                            initialDiv();
                        }else{
                            alert('No se pudieron guardar los cambios.');
                        }
                    })
                    .catch(error => {
                        if (error !== 'Input cancelado') {
                            console.error(error);
                        }
                    });
                },
                function(){

                }, 'Confirmar'
            )
        }else{
            password2.setCustomValidity('Las contraseñas no coinciden.');
            password2.reportValidity();
        }
    }
}

function cancelPassUpdate(){
    initialDiv();
}

function politicaContrasena(){
    const password = document.getElementById('newPass');
    let r = true;

    var lengthPattern = /.{8,45}/;
    var uppercasePattern = /[A-Z]/;
    var lowercasePattern = /[a-z]/;
    var numberPattern = /[0-9]/;
    var specialCharPattern = /[!@#$%^&*(),.?":{}|<>]/;
    var noSpacesPattern = /^\S*$/;
    var noCommonSequencesPattern = /^(?!.*(123456|abcdef)).*$/;
    var passwordValue = password.value;
    let mensaje = "La contraseña debe incluir:"

        // Validaciones
        if (!lengthPattern.test(passwordValue)) {
            mensaje += '\n- entre 8 y 45 caracteres';
            r = false;
        }
        if (!uppercasePattern.test(passwordValue)) {
            mensaje += '\n- una letra mayúscula';
            r = false;
        }
        if (!lowercasePattern.test(passwordValue)) {
            mensaje += '\n- una letra minúscula';
            r = false;
        }
        if (!numberPattern.test(passwordValue)) {
            mensaje += '\n- un número';
            r = false;
        }
        if (!specialCharPattern.test(passwordValue)) {
            mensaje += '\n- un carácter especial';
            r = false;
        }
        if(r==false){
            password.setCustomValidity(mensaje);
            password.reportValidity();
            return false;   
        }
        if (!noSpacesPattern.test(passwordValue)) {
            password.setCustomValidity('La contraseña no debe contener espacios en blanco.');
            password.reportValidity();
            return false;
        }
        if (!noCommonSequencesPattern.test(passwordValue)) {
            password.setCustomValidity('La contraseña no debe contener secuencias comunes como "123456" o "abcdef".');
            password.reportValidity();
            return false;
        }
    
    return r;
}

function updateNotifications(){
    let url = "../controller/accountSettingsManager.php?notificationToggle=true";
    fetch(url, {
        method: 'POST',
        body: null,
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success){
            console.log('Data: ',data.message)
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('notificationToggle').addEventListener('change', function() {
        if (this.checked) {
            createConfirmationDialog('Activar notificaciones', '¿Seguro que deseas activar las notificaciones?', 
                updateNotifications,
                ()=>{
                    this.checked = false;
                }, 'Activar'
            );
        } else {
            createConfirmationDialog('Desactivar notificaciones', '¿Seguro que deseas desactivar las notificaciones?', 
                updateNotifications,
                ()=>{
                    this.checked = true;
                }, 'Desactivar'
            );
        }
    });

    function changeTheme(theme){
        const url = `../controller/accountSettingsManager.php?setTheme=true`;
        fetch(url, {
            method: 'POST',
            body: new URLSearchParams({
                theme : theme
            }),
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success){
                document.body.style.cursor = 'progress';
                setTimeout(() => {
                    if(theme === 'darkMode'){
                        document.body.classList.add(`darkMode`);
                        document.body.classList.remove('lightMode');
                        addResetPreferencesBtn();
                    }
                    else if(theme === 'lightMode'){
                        document.body.classList.add(`lightMode`);
                        document.body.classList.remove('darkMode'); 
                        addResetPreferencesBtn();
                    }
                    else if(theme === 'systemMode'){
                        document.body.classList.remove(`lightMode`);
                        document.body.classList.remove('darkMode'); 
                        if(!document.body.classList.contains('bigFont')) {
                            if(document.getElementById('resetPreferencesBtn')){
                                document.getElementById('resetPreferencesBtn').remove();
                            }
                        }
                    }
                    setTimeout(() => {
                        document.body.style.cursor = 'default';
                    }, 500);
                }, 500);
            }
        });
    }

    document.querySelectorAll('input[name="themeToggle"]').forEach((theme) => {
        theme.addEventListener('change', function() {
            if (document.getElementById('dk-tg').checked) {
                changeTheme('darkMode');
            } else if (document.getElementById('sy-tg').checked) {
                changeTheme('systemMode');
            } else if (document.getElementById('lg-tg').checked) {
                changeTheme('lightMode');
            }
        });
    });

    function changeLetterSize(fontSize){
        const url = `../controller/accountSettingsManager.php?setFontSize=true`;
        fetch(url, {
            method: 'POST',
            body: new URLSearchParams({
                fontSize : fontSize
            }),
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success){
                document.body.style.cursor = 'progress';
                setTimeout(() => {
                    if(fontSize === 'largeSize'){
                        document.body.classList.add(`bigFont`);
                        addResetPreferencesBtn();
                        console.log('Fuente grande');
                    }
                    else if(fontSize === 'normalSize'){
                        document.body.classList.remove('bigFont');
                        if(!document.body.classList.contains('lighMode') && !document.body.classList.contains('darkMode')) {
                            if(document.getElementById('resetPreferencesBtn')){
                                document.getElementById('resetPreferencesBtn').remove();
                            }
                        }
                        console.log('Fuente Normal');
                    }
                    setTimeout(() => {
                        document.body.style.cursor = 'default';
                    }, 500);
                }, 500);
            }else{
                console.log(data.message);
            }
        });
    }

    document.getElementById('letterToggle').addEventListener('change', function() {
        if (this.checked) {
            changeLetterSize('largeSize')
        } else {
            changeLetterSize('normalSize')
        }
    });
});