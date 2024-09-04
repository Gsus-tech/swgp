
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
    const injectionPatterns = [
       /<script\b[^>]*>(.*?)<\/script>/gi,
       /<img\b[^>]*onerror=/gi,
       /<img\b[^>]*onload=/gi,
       /alert\(/gi,
       /document\.cookie/gi,
       /--/g,
       /;--/g,
       /;/g,
       /\/\*/g,
       /\*\//g,
       /@@/g,
       /or\s*['"]?\s*1\s*=\s*1/gi,
       /or\s*['"]?\s*\w\s*=\s*\w/gi,
       /['"]?\s*or\s*['"]?1['"]?\s*=\s*['"]?1['"]?/gi,
       /['"]?\s*or\s*['"]?\w['"]?\s*=\s*['"]?\w['"]?/gi,
       /char\(/gi,
       /nchar\(/gi,
       /varchar\(/gi,
       /nvarchar\(/gi,
       /sysobjects/gi,
       /syscolumns/gi,
       /information_schema\.tables/gi
   ];
   
   const searchBar = document.getElementById('search-bar');
   const searchButton = document.getElementById('searchAccount');
   
   searchButton.addEventListener('click', function() {
       const query = searchBar.value.trim().toLowerCase(); 
       if (query) {
           const foundInjectionPattern = injectionPatterns.some(pattern => pattern.test(query));
           if (!foundInjectionPattern) {
               window.location.href = `userManagement.php?search=${encodeURIComponent(query)}`;
           } else {
               alert('Se detectaron patrones maliciosos en tu entrada de datos. \nIntenta de nuevo con diferentes parámetros de búsqueda.');
               toggleSearchItems();
           }
       } else {
           toggleSearchItems();
       }
   });
   
   // Manejar el evento Enter en la barra de búsqueda
   searchBar.addEventListener('keypress', function(event) {
       if (event.key === 'Enter') {
           event.preventDefault();
           searchButton.click();
       }
   });
});

//Cerrar barra al hacer clic fuera del div
document.addEventListener('DOMContentLoaded', (event) => {
    
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


//Filtrar busqueda
{
const filterBtn = document.getElementById('filterUserList');
filterBtn.addEventListener('click', function(){
    toggleFilterItems();
    moveSelectDiv(true, false);
});

function toggleFilterItems(){
    document.querySelector('.dropDownFilter1').classList.toggle('hide');
    document.querySelector('.dropDownFilter2').classList.toggle('hide');
    document.querySelector('.filterDiv').classList.toggle('openFilterDiv');
    document.querySelector('.filterDiv').classList.toggle('closedFilterDiv');
    document.querySelector('.nav-buttons').classList.toggle('hide')
}


function FilterResults(selectElement, name, cell, cell2) {
    const otherSelect = document.getElementById(`${name}`);
    const value = selectElement.value;
    const options = selectElement.options;
    let filterValue = '';
    let value2 = '';
    if(name == 'filtersForRol'){
        for (let i = 0; i < options.length; i++) {
            if (options[i].value == value) {
                filterValue = i === 0 ? 'noFilter' : options[i].textContent.trim();
                break;
            }
        }
        value2 = otherSelect.value;
        console.log(`Other filter: ${value2}`);
    }else{
        filterValue = value;
        value2 = otherSelect.selectedIndex === 0 ? 'noFilter' : otherSelect.options[otherSelect.selectedIndex].textContent.trim();
        console.log(`Other filter: ${value2}`);
    }

    //limpiar tabla del 0rows encontrados
    const noResultsRow = document.getElementById('no-results-row');
    if (noResultsRow) {
        noResultsRow.remove();
    }

    const TwoFilters = otherSelect.value === 'noFilter' ? false : true;
    console.log(`Two filters: ${TwoFilters}`);
    const tbody = document.getElementById('users-list-body');
    const rows = tbody.getElementsByTagName('tr');
    let visibleRows = 0;
    //Buscar los rows compatibles con el filtrado
    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        const filter = row.cells[cell].textContent;
        const filter2 = row.cells[cell2].textContent;
        // console.log(`i es igual a: ${i}`);

        // console.log(`Filters selected: 1: ${filterValue}      2: ${value2}\n`);
        // console.log(`Row: 1: ${filter}     2: ${filter2}\n\n`);
        if (TwoFilters == true && (filter === filterValue && filter2 === value2)) {
            row.classList.remove('hide');
            visibleRows++;
            console.log('visible: true')
        }
        else if(TwoFilters == false && (filterValue === 'noFilter' || filter === filterValue)){
            row.classList.remove('hide');
            visibleRows++;
        }
        else {
            row.classList.add('hide');
        }
    }

    //Agregar el mensaje de 0 rows encontrados
    if (visibleRows === 0) {
        const newRow = tbody.insertRow();
        newRow.id = 'no-results-row';
        const newCell = newRow.insertCell(0);
        newCell.colSpan = 6;
        newCell.textContent = "No se encontraron resultados.";
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

document.addEventListener("DOMContentLoaded", function(){
    const urlParams = new URLSearchParams(window.location.search);

    if (urlParams.has('editId')) {
        updateValue();
        const btnSaveChanges = document.getElementById('sumbit-editUser');
        const eUserName = document.getElementById("Ename");
        const eDepto = document.getElementById("Edepto");
        const eDropBox = document.getElementById('edropDownDepto');
        const userDropBox = document.getElementById('FcmbBox2');
        const eFdepto = document.getElementById("eFdepto");
        const eMail = document.getElementById("Email");
        eDropBox.addEventListener('change', (e)=>{toggleBtn();});
        if(userDropBox){
            userDropBox.addEventListener('change', (e)=>{toggleBtn();});
        }
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

function invalidEmail(element){
    element.setCustomValidity('Formato de correo inválido.');
    element.classList.add('invalidField');
}

function submitFormEditUser(){
    var regexEspeciales = /[^a-zA-Z0-9 áéíóúÁÉÍÓÚ]/g;
    const onlySpaces = /^\s*$/;
    const doubleSpaces = /\s{2,}/;
    const cadenasSinSentido = [
        'poiuy','lkjhg','mnbv','uhas83e73u','xyz123',
        'random','loremipsum','qwerty','asdf','zxcv',
        'nombre1','ghfjd','iiii','dummytext','blahblah',
        'Usuario123','abcd1234','123','eeeee','aaaa'
    ];
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
    }else if(onlySpaces.test(eUserName.value) || doubleSpaces.test(eUserName.value) || cadenasSinSentido.some(nonsensical => eUserName.value.includes(nonsensical))){
        eUserName.setCustomValidity('Introduce un nombre de usuario válido.\nNo se admiten cadenas sin sentido o espacios dobles.');
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
    }else if(onlySpaces.test(eFdepto.value) || doubleSpaces.test(eFdepto.value) || cadenasSinSentido.some(nonsensical => eFdepto.value.includes(nonsensical))){
        eFdepto.setCustomValidity('Introduce un nombre de departamento válido.\nNo se admiten cadenas sin sentido o espacios dobles.');
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

document.addEventListener('DOMContentLoaded', function() {
    paginateTable('users-list-body', 7, 'pagination');
    
})