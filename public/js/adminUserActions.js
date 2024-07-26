//EnableButton 'Agregar usuario'
const userName = document.getElementById("Fname");
const depto = document.getElementById("Fdepto");
const mail = document.getElementById("Fmail");
const password = document.getElementById("Fpassword");
const password2 = document.getElementById("FpasswordCon");
const addUserButton = document.getElementById("sumbit-AddUser");

userName.addEventListener("keyup", (e)=>{toggleFormBtn();});
depto.addEventListener("keyup", (e)=>{toggleFormBtn();});
mail.addEventListener("keyup", (e)=>{toggleFormBtn();});
password.addEventListener("keyup", (e)=>{toggleFormBtn();});
password2.addEventListener("keyup", (e)=>{toggleFormBtn();});
userName.addEventListener("change", (e)=>{toggleFormBtn();});
depto.addEventListener("change", (e)=>{toggleFormBtn();});
mail.addEventListener("change", (e)=>{toggleFormBtn();});
password.addEventListener("change", (e)=>{toggleFormBtn();});
password2.addEventListener("change", (e)=>{toggleFormBtn();});


function toggleFormBtn(){
    if(userName.value != "" && depto.value != "" && mail.value != "" && password.value != ""  && password2.value != ""){
        // if(password.value == password2.value){
            addUserButton.disabled=false;
            if(!document.getElementById('sumbit-AddUser').classList.contains('enabled')){
                document.querySelector(".sumbit-AddUser").classList.toggle('enabled');
            }
        // }
    }else{
        addUserButton.disabled=true;
        if(document.getElementById('sumbit-AddUser').classList.contains('enabled')){
            document.querySelector(".sumbit-AddUser").classList.toggle('enabled');
        }
    }

    // if(password.value != password2.value){
    //     if(!document.getElementById('FpasswordCon').classList.contains('wrongCP')){
    //         document.getElementById('FpasswordCon').classList.toggle('wrongCP');
    //     }
    //     addUserButton.disabled=true;
    //     if(document.getElementById('sumbit-AddUser').classList.contains('enabled')){
    //         document.querySelector(".sumbit-AddUser").classList.toggle('enabled');
    //     }
    // }
    // if(password.value == password2.value){
    //     if(document.getElementById('FpasswordCon').classList.contains('wrongCP')){
    //         document.getElementById('FpasswordCon').classList.toggle('wrongCP');
    //     }
    // }
}

//Departamento select e introducir nuevo
const dropBox = document.getElementById('dropDownDepto');
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('Fdepto').value = dropBox.options[dropBox.selectedIndex].text;
});
const dptoInputDiv = document.getElementById('Fdpto');
dropBox.addEventListener('change', function(){
    const selectedValue = dropBox.value;
    if (selectedValue === 'other') {
        document.getElementById('Fdepto').value = "";    
        if(dptoInputDiv.classList.contains('hide')){
            dptoInputDiv.classList.remove('hide');
            toggleFormBtn();
        }
    } else{
        document.getElementById('Fdepto').value = dropBox.options[dropBox.selectedIndex].text;
        if(!dptoInputDiv.classList.contains('hide')){
            dptoInputDiv.classList.add('hide');
            toggleFormBtn();
        }
    }
});

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('Fdepto').value = dropBox.options[dropBox.selectedIndex].text;
});

//Visibilidad de contraseña
const passVisibilityToggle = document.getElementById('passwordVisibility');
passVisibilityToggle.addEventListener('click', function(){
    document.getElementById('passwordVisibility').classList.toggle('fa-lock');
    document.getElementById('passwordVisibility').classList.toggle('fa-unlock');
    var x = document.getElementById('Fpassword');
    if(x.type === 'password'){x.type = 'text';}
    else{ x.type = 'password';}
})

//Abrir formulario crear usuario
const openForm = document.getElementById('showUserFormBtn');
openForm.addEventListener('click', function(){
    document.getElementById('addUser-form').classList.toggle('hide');
});

//Cerrar formulario crear usuario
function cerrarFormulario(){
    if(confirmCancel()==true){
        document.getElementById("addUser-form").reset();
        document.getElementById('addUser-form').classList.toggle('hide');
    }
}

//Cerrar barra al hacer clic fuera del div
document.addEventListener('DOMContentLoaded', (event) => {
    //Validacion de entrada de datos
    const forbiddenChars = /[<>`#*]/g;

    depto.addEventListener('input', function(){
        this.value = this.value.replace(forbiddenChars, '');
    })
    userName.addEventListener('input', function(){
        this.value = this.value.replace(forbiddenChars, '');
    })
});

const userRolDrop = document.getElementById('FcmbBox');
function submitFormUser(){
    var regexEspeciales = /[^a-zA-Z0-9 áéíóúÁÉÍÓÚ]/g;
    const onlySpaces = /^\s*$/;
    const doubleSpaces = /\s{2,}/;
    const cadenasSinSentido = [
        'poiuy','lkjhg','mnbv','uhas83e73u','xyz123',
        'random','loremipsum','qwerty','asdf','zxcv',
        'nombre1','ghfjd','iiii','dummytext','blahblah',
        'Usuario123','abcd1234','123','eeeee','aaaa'
    ];
    if (regexEspeciales.test(userName.value)) {
        userName.setCustomValidity('No se permiten caracteres especiales en el nombre.');
        userName.classList.add('invalidField');
        userName.reportValidity();
        return false;
    }
    else if (userName.value.length < 8) {
        userName.setCustomValidity('El nombre debe tener al menos 8 caracteres.');
        userName.classList.add('invalidField');
        userName.reportValidity();
        return false;
    }
    else if (userName.value.length > 45) {
        userName.setCustomValidity('Máximo 45 caracteres para el campo nombre.');
        userName.classList.add('invalidField');
        userName.reportValidity();
        return false;
    }else if(onlySpaces.test(userName.value) || doubleSpaces.test(userName.value) || cadenasSinSentido.some(nonsensical => userName.value.includes(nonsensical) || cadenasSinSentido.includes(userName.value))){
        userName.setCustomValidity('Introduce un nombre de usuario válido.\nNo se admiten cadenas sin sentido o espacios dobles.');
        userName.reportValidity();
        return false;
    }

    if(dropBox.value === 'noSelected'){
        dropBox.setCustomValidity('Seleccione un departamento');
        userName.reportValidity();
        return false;
    }

    if (regexEspeciales.test(depto.value)) {
        depto.setCustomValidity('No se permiten caracteres especiales en el nombre del departamento.');
        depto.classList.add('invalidField');
        depto.reportValidity();
        return false;
    }
    else if (depto.value.length < 8) {
        depto.setCustomValidity('El nombre del departamento debe tener al menos 8 caracteres.');
        depto.classList.add('invalidField');
        depto.reportValidity();
        return false;
    }
    else if (depto.value.length > 45) {
        depto.setCustomValidity('Máximo 45 caracteres para el campo departamento.');
        depto.classList.add('invalidField');
        depto.reportValidity();
        return false;
    }else if(onlySpaces.test(depto.value) || doubleSpaces.test(depto.value) || cadenasSinSentido.some(nonsensical => depto.value.includes(nonsensical)) || cadenasSinSentido.includes(userName.value)){
        depto.setCustomValidity('Introduce un nombre de departamento válido.\nNo se admiten cadenas sin sentido o espacios dobles.');
        depto.reportValidity();
        return false;
    }

    const pC = politicaContrasena();
    if(pC ==false){return false;}
    
    if(userRolDrop.value === 'noSelected'){
        userRolDrop.setCustomValidity('Seleccione un tipo de usuario');
        userRolDrop.reportValidity();
        return false;
    }

    if(password.value != password2.value){
        password2.setCustomValidity('Las contraseñas no coinciden.');
        password2.classList.add('invalidField');   
        password2.reportValidity();   
        return false;
    }

    var form = document.getElementById('addUser-form');
    form.action = "../controller/userManager.php?addUser=true";

}

function politicaContrasena(){
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
            // password.reportValidity();
            r = false;
        }
        if (!uppercasePattern.test(passwordValue)) {
            mensaje += '\n- una letra mayúscula';
            // password.reportValidity();
            r = false;
        }
        if (!lowercasePattern.test(passwordValue)) {
            mensaje += '\n- una letra minúscula';
            // password.reportValidity();
            r = false;
        }
        if (!numberPattern.test(passwordValue)) {
            mensaje += '\n- un número';
            // password.reportValidity();
            r = false;
        }
        if (!specialCharPattern.test(passwordValue)) {
            mensaje += '\n- un carácter especial';
            // password.reportValidity();
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

//Confirmar eliminar usuario
function confirmDelete(idToEdit) {
    if(confirm("¿Estás seguro de que deseas eliminar esta cuenta?")){
        var form = document.createElement("form");
        form.method = "POST";
        form.action = `../controller/userManager.php?delete=true&deleteUser=${idToEdit}`;
        document.body.appendChild(form);
        form.submit();  
    }else{
        return false;
    }
}

// Habilitar boton de Eliminar multiples cuentas
document.addEventListener('DOMContentLoaded', (event) => {
    
    const checkboxes = document.querySelectorAll('.account-checkbox');
    const accountsSelectedDiv = document.getElementById('accountsSelected');
    const selectAllBox = document.getElementById('selectAllBoxes');
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateAccountsSelectedDiv);
    });

    selectAllBox.addEventListener('change', () => {
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAllBox.checked;
        });
        updateAccountsSelectedDiv();
    });

    function updateAccountsSelectedDiv() {
        const checkedCheckboxes = Array.from(checkboxes).filter(chk => chk.checked);
        if (checkedCheckboxes.length > 0) {
            accountsSelectedDiv.classList.remove('hide');
        } else {
            accountsSelectedDiv.classList.add('hide');
        }
    }

    const applyAction1 = document.getElementById('applyAction');
    const applyAction2 = document.getElementById('applyAction2');
    applyAction1.addEventListener('click', applyAction);
    applyAction2.addEventListener('click', applyAction);
});

function applyAction() {
    var actionSelected = document.getElementById('actionSelected').value;
    if (actionSelected === 'delete') {
        deleteSelectedAccounts();
    } else {}
}

function deleteSelectedAccounts(){
    const checkboxes = document.querySelectorAll('.account-checkbox');
    const checkedCheckboxes = Array.from(checkboxes).filter(chk => chk.checked);
    
    if(checkedCheckboxes.length>0){
        const confirmationMessage = `¿Estás seguro de querer eliminar ${checkedCheckboxes.length} cuentas?\nEsta acción es irreversible.`;
        const userConfirmed = confirm(confirmationMessage);

        if (userConfirmed) {
            var parametros = "?deleteAccounts="+checkedCheckboxes[0].value;
            for(i=1; i<checkedCheckboxes.length; i++){
                parametros += ","+checkedCheckboxes[i].value;
            }
            window.location.href = `../controller/userManager.php${parametros}`;
        }   
    }
}

function validarCuenta(search){
    x = Count(search) != 0;
    x == true ? true : window.location.href = 'userManagement.php';
}