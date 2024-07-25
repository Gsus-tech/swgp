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

function confirmCancel() {
    if(userName.value != "" || mail.value != "" || password.value != ""  || password2.value != ""){
        return confirm("¿Estás seguro de que deseas cancelar? Se perderá la información ingresada.");
    }
    return true;
}

//Buscar usuario
{
const searchBtn = document.getElementById('searchUser');
searchBtn.addEventListener('click', function(){
    toggleSearchItems();
    moveSelectDiv(false, true);
});

document.addEventListener('DOMContentLoaded', function() {
    const searchBar = document.getElementById('search-bar');
    const searchButton = document.getElementById('searchAccount');
    document.getElementById('Fdepto').value = dropBox.options[dropBox.selectedIndex].text;
    searchButton.addEventListener('click', function() {
        const query = searchBar.value;
        if (query) {
            window.location.href = `userManagement.php?search=${encodeURIComponent(query)}`;
        } else {
            toggleSearchItems();
            moveSelectDiv(false, false);
        }
    });

    // Opción adicional: Permitir la búsqueda al presionar Enter
    searchBar.addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            searchButton.click();
        }
    });
});

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
    //Boton de buscar
    const searchDiv = document.querySelector('.nav-buttons');
    document.addEventListener('click', function(event) {
        const closeSearchBar = searchDiv.contains(event.target);

        if (!closeSearchBar) {
            if(!document.querySelector('.search-bar').classList.contains('hide')){
                toggleSearchItems();
                moveSelectDiv(false, false);
            }
        }
    });
});
const userRolDrop = document.getElementById('FcmbBox');
function submitFormUser(){
    var regexEspeciales = /[^a-zA-Z0-9 áéíóúÁÉÍÓÚ]/g;

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
    }

    if(dropBox.value === 'noSelected'){
        dropBox.setCustomValidity('Seleccione un departamento');
        userName.reportValidity();
        return false;
    }

    if (regexEspeciales.test(depto.value)) {
        depto.setCustomValidity('No se permiten caracteres especiales en el nombre.');
        depto.classList.add('invalidField');
        depto.reportValidity();
        return false;
    }
    else if (depto.value.length < 8) {
        depto.setCustomValidity('El nombre debe tener al menos 8 caracteres.');
        depto.classList.add('invalidField');
        depto.reportValidity();
        return false;
    }
    else if (depto.value.length > 45) {
        depto.setCustomValidity('Máximo 45 caracteres para el campo nombre.');
        depto.classList.add('invalidField');
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

function resetField(element){
    element.setCustomValidity('')
    element.classList.remove('invalidField');
}

function toggleSearchItems(){
    document.querySelector('.search-bar').classList.toggle('hide');
    document.querySelector('.nav-buttons').classList.toggle('searchingMode');
    document.getElementById('searchAccount').classList.toggle('hide');
    document.getElementById('searchUser').classList.toggle('hide');
}
}


//Filtrar busqueda
{
const filterBtn = document.getElementById('filterUserList');
filterBtn.addEventListener('click', function(){
    toggleFilterItems();
    moveSelectDiv(true, false);
});

//NEW VERSION
document.addEventListener('DOMContentLoaded', function() {

    var currentUrl = new URL(window.location.href);
    var paramUrl = window.location.search;
    var clsParam = paramUrl.substring(1);
    var parametro = new URLSearchParams(clsParam);

    const rolFilter = document.getElementById('filtersForRol');
    rolFilter.addEventListener('change', function() {
        const selectedValue = rolFilter.value;

        if (parametro.has('search')) {
            parametro.delete('search');
        }

        if (selectedValue === 'noFilter') {
            parametro.delete('filterRol');
            if (parametro.has('filterDto')) {
                window.location.href = `${currentUrl.pathname}?${parametro.toString()}`;
            } else {
                window.location.href = `userManagement.php`;
            }
        } else {
            parametro.set('filterRol', selectedValue);
            var newUrl = `${currentUrl.origin}${currentUrl.pathname}?${parametro.toString()}`;
            window.location.href = newUrl;
        }
    });

    const dtoFilter = document.getElementById('filtersForDto');
    dtoFilter.addEventListener('change', function() {
        const selectedOption = dtoFilter.options[dtoFilter.selectedIndex];
        const selectedValue = selectedOption.value;
        const selectedText = selectedOption.textContent || selectedOption.innerText;

        if (parametro.has('search')) {
            parametro.delete('search');
        }

        if (selectedValue === 'noFilter') {
            parametro.delete('filterDto');
            if (parametro.has('filterRol')) {
                window.location.href = `${currentUrl.pathname}?${parametro.toString()}`;
            } else {
                window.location.href = `userManagement.php`;
            }
        } else {
            // const encodedText = encodeURIComponent(selectedText);
            parametro.set('filterDto', selectedText);
            var newUrl = `${currentUrl.origin}${currentUrl.pathname}?${parametro.toString()}`;
            window.location.href = newUrl;
        }
    });
});

function toggleFilterItems(){
    document.querySelector('.dropDownFilter1').classList.toggle('hide');
    document.querySelector('.dropDownFilter2').classList.toggle('hide');
    document.querySelector('.filterDiv').classList.toggle('openFilterDiv');
    document.querySelector('.filterDiv').classList.toggle('closedFilterDiv');
    document.querySelector('.nav-buttons').classList.toggle('hide')
}

function moveSelectDiv(f1, f2){
    const selectionDiv = document.getElementById('accountsSelected');
    if(!selectionDiv.classList.contains('top-10') && f1==true){
        selectionDiv.classList.add('top-10');
    }
    else if(!selectionDiv.classList.contains('top-7') && f2==true){
        selectionDiv.classList.add('top-7');
    }

    if(selectionDiv.classList.contains('top-7') && f1==false && f2==false){
        selectionDiv.classList.remove('top-7');
    }
    if(selectionDiv.classList.contains('top-10') && f1==false && f2==false){
        selectionDiv.classList.remove('top-10');
    }
}

//Cerrar filtros al hacer clic fuera del div
document.addEventListener('DOMContentLoaded', (event) => {
    const filterDiv = document.querySelector('.filterDiv');

    document.addEventListener('click', function(event) {
        const closeFilterBar = filterDiv.contains(event.target);

        if (!closeFilterBar) {
            if(document.querySelector('.filterDiv').classList.contains('openFilterDiv')){
                toggleFilterItems();
                moveSelectDiv(false, false);
            }
        }
    });
});

}

//esconder elementos de busqueda y filtracion
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        if(!document.querySelector('.search-bar').classList.contains('hide')){
            toggleSearchItems();
            moveSelectDiv(false, false);
        }
        if(document.querySelector('.filterDiv').classList.contains('openFilterDiv')){
            toggleFilterItems();
            moveSelectDiv(false, false);
        }
    }
});

function editUserAccount(idToEdit){
    var currentUrl = new URL(window.location.href);
    window.location.href = `${currentUrl.origin}${currentUrl.pathname}?editId=${idToEdit}`;   
}

function seeUserAccount(idToSee){
    var currentUrl = new URL(window.location.href);
    window.location.href = `${currentUrl.origin}${currentUrl.pathname}?detailsId=${idToSee}`;   
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

document.addEventListener("DOMContentLoaded", function(){
    const urlParams = new URLSearchParams(window.location.search);

    if (urlParams.has('editId')) {
        updateValue();
        const btnSaveChanges = document.getElementById('sumbit-editUser');
        const eUserName = document.getElementById("Ename");
        const eDepto = document.getElementById("Edepto");
        const eDropBox = document.getElementById('edropDownDepto');
        const eFdepto = document.getElementById("eFdepto");
        const eMail = document.getElementById("Email");
        eDropBox.addEventListener('change', (e)=>{toggleBtn();});
        eUserName.addEventListener('change', (e)=>{toggleBtn();});
        eDepto.addEventListener('change', (e)=>{toggleBtn();});
        eMail.addEventListener('change', (e)=>{toggleBtn();});
        eUserName.addEventListener('keyup', (e)=>{toggleBtn();});
        eDepto.addEventListener('keyup', (e)=>{toggleBtn();});
        eMail.addEventListener('keyup', (e)=>{toggleBtn();});
        function toggleBtn(){
            if((eUserName.value != "" && eMail.value != "")&&(eFdepto.value != "" ||eDropBox.value != "other")){
                btnSaveChanges.disabled=false;
                if(!document.getElementById('sumbit-editUser').classList.contains('enabled')){
                    document.getElementById("sumbit-editUser").classList.toggle('enabled');
                }
            }else{
                btnSaveChanges.disabled=true;
                if(document.getElementById('sumbit-editUser').classList.contains('enabled')){
                    document.getElementById("sumbit-editUser").classList.toggle('enabled');
                }
            }
        }

        //Departamento select e introducir nuevo
        eDropBox.addEventListener('change', function(){
            updateValue();
        });
    }
});

function submitFormEditUser(){
    var regexEspeciales = /[^a-zA-Z0-9 áéíóúÁÉÍÓÚ]/g;
    const eUserName = document.getElementById("Ename");
    const eFdepto = document.getElementById("eFdepto");

    if (regexEspeciales.test(eUserName.value)) {
        eUserName.setCustomValidity('No se permiten caracteres especiales en el nombre.');
        eUserName.classList.add('invalidField');
        eUserName.reportValidity();
        return false;
    }
    else if (eUserName.value.length < 8) {
        eUserName.setCustomValidity('El nombre debe tener al menos 8 caracteres.');
        eUserName.classList.add('invalidField');
        eUserName.reportValidity();
        return false;
    }
    else if (eUserName.value.length > 45) {
        eUserName.setCustomValidity('Máximo 45 caracteres para el campo nombre.');
        eUserName.classList.add('invalidField');
        eUserName.reportValidity();
        return false;
    }

    if (regexEspeciales.test(eFdepto.value)) {
        eFdepto.setCustomValidity('No se permiten caracteres especiales.');
        eFdepto.classList.add('invalidField');
        eFdepto.reportValidity();
        return false;
    }
    else if (eFdepto.value.length < 8) {
        eFdepto.setCustomValidity('El nombre del departamento debe tener al menos 8 caracteres.');
        eFdepto.classList.add('invalidField');
        eFdepto.reportValidity();
        return false;
    }
    else if (eFdepto.value.length > 45) {
        eFdepto.setCustomValidity('Máximo 45 caracteres para el nombre del departamento.');
        eFdepto.classList.add('invalidField');
        eFdepto.reportValidity();
        return false;
    }

    var form = document.getElementById('editUser-form');
    form.action = "../controller/userManager.php?updateUser=true";
}

function toggleInput(){
    const eDepto = document.getElementById("Edepto");
    if(eDepto.classList.contains('hide')){
        eDepto.classList.remove('hide');
    }
}

function updateValue(){
    const eDepto = document.getElementById("Edepto");
    const eDropBox = document.getElementById('edropDownDepto');
    const eFdepto = document.getElementById("eFdepto");
    const selectedValue = eDropBox.value;
    if (selectedValue === 'other') {
        eFdepto.value = "";
        eFdepto.type === 'hidden' ? eFdepto.type = 'text': eFdepto.type = 'text';
    } else{
        eFdepto.value = eDropBox.options[eDropBox.selectedIndex].text;
        eFdepto.type === 'text' ? eFdepto.type = 'hidden': eFdepto.type = 'hidden';
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