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
        if(password.value == password2.value){
            addUserButton.disabled=false;
            if(!document.getElementById('sumbit-AddUser').classList.contains('enabled')){
                document.querySelector(".sumbit-AddUser").classList.toggle('enabled');
            }
        }
    }else{
        addUserButton.disabled=true;
        if(document.getElementById('sumbit-AddUser').classList.contains('enabled')){
            document.querySelector(".sumbit-AddUser").classList.toggle('enabled');
        }
    }

    if(password.value != password2.value){
        if(!document.getElementById('FpasswordCon').classList.contains('wrongCP')){
            document.getElementById('FpasswordCon').classList.toggle('wrongCP');
        }
        addUserButton.disabled=true;
        if(document.getElementById('sumbit-AddUser').classList.contains('enabled')){
            document.querySelector(".sumbit-AddUser").classList.toggle('enabled');
        }
    }
    if(password.value == password2.value){
        if(document.getElementById('FpasswordCon').classList.contains('wrongCP')){
            document.getElementById('FpasswordCon').classList.toggle('wrongCP');
        }
    }
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
    const searchDiv = document.querySelector('.nav-buttons');

    document.addEventListener('click', function(event) {
        const closeSearchBar = searchDiv.contains(event.target);

        if (!closeSearchBar) {
            if(!document.querySelector('.search-bar').classList.contains('hide')){
                toggleSearchItems();
            }
        }
    });
});

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
});

//NEW VERSION
document.addEventListener('DOMContentLoaded', function() {
    updateCancelLink();

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

function updateCancelLink(){
    var btn = document.getElementById('cancel-Adduser');
    var currentUrl = new URL(window.location.href);
    btn.href = currentUrl;
}

//OLD VERSION
// document.addEventListener('DOMContentLoaded', function() {
//     const rolFilter = document.getElementById('filtersForRol');

//     rolFilter.addEventListener('change', function() {
//         const selectedValue = rolFilter.value;
//         if (selectedValue === 'noFilter') {
//             window.location.href = `userManagement.php`;
//         } else{
//             window.location.href = `userManagement.php?filterRol=${selectedValue}`;
//         }
//     });
// });

// document.addEventListener('DOMContentLoaded', function() {
//     const dtoFilter = document.getElementById('filtersForDto');

    // dtoFilter.addEventListener('change', function() {
    //     const selectedOption = dtoFilter.options[dtoFilter.selectedIndex];
    //     const selectedValue = selectedOption.value;
    //     const selectedText = selectedOption.textContent || selectedOption.innerText;

    //     if (selectedValue === 'noFilter') {
    //         window.location.href = `userManagement.php`;
    //     } else {
    //         const encodedText = encodeURIComponent(selectedText);
    //         window.location.href = `userManagement.php?filterDto=${encodedText}`;
    //     }
    // });
// });

function toggleFilterItems(){
    document.querySelector('.dropDownFilter1').classList.toggle('hide');
    document.querySelector('.dropDownFilter2').classList.toggle('hide');
    document.querySelector('.filterDiv').classList.toggle('openFilterDiv');
    document.querySelector('.filterDiv').classList.toggle('closedFilterDiv');
    document.querySelector('.nav-buttons').classList.toggle('hide')
}

//Cerrar filtros al hacer clic fuera del div
document.addEventListener('DOMContentLoaded', (event) => {
    const filterDiv = document.querySelector('.filterDiv');

    document.addEventListener('click', function(event) {
        const closeFilterBar = filterDiv.contains(event.target);

        if (!closeFilterBar) {
            if(document.querySelector('.filterDiv').classList.contains('openFilterDiv')){
                toggleFilterItems();
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
        }
        if(document.querySelector('.filterDiv').classList.contains('openFilterDiv')){
            toggleFilterItems();
        }
    }
});

function editUserAccount(idToEdit){
    window.location.href = `userMng/manageUserAccount.php?editId=${idToEdit}`;   
}

function seeUserAccount(idToEdit){
    window.location.href = `userMng/manageUserAccount.php?detailsId=${idToEdit}`;   
}

//Confirmar eliminar usuario
function confirmDelete(idToEdit) {
    if(confirm("¿Estás seguro de que deseas eliminar esta cuenta?")){
        window.location.href = `../controller/userManager.php?delete=true&deleteUser=${idToEdit}`;   
    }else{
        return false;
    }
}
