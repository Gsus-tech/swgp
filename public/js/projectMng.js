//Abrir formulario crear proyecto
const openForm = document.getElementById('showProjectFormBtn');
openForm.addEventListener('click', function(){
    document.getElementById('addProject-form').classList.toggle('hide');
    setDateInFormBtn('inicio');
    setDateInFormBtn('cierre');
});

//EnableButton 'Crear proyecto'
const projectName = document.getElementById("Fname");
const description = document.getElementById("Fdescription");
const metas = document.getElementById("Fmeta");
const addProjectButton = document.getElementById("sumbit-AddProject");
const addProjectButton2 = document.getElementById("sumbit-AddProject-obj");
const diaI = document.getElementById("dia_inicio");
const mesI = document.getElementById("mes_inicio");
const anioI = document.getElementById("anio_inicio");
const diaC = document.getElementById("dia_cierre");
const mesC = document.getElementById("mes_cierre");
const anioC = document.getElementById("anio_cierre");

projectName.addEventListener("keyup", (e)=>{toggleFormBtn();});
description.addEventListener("keyup", (e)=>{toggleFormBtn();});
metas.addEventListener("keyup", (e)=>{toggleFormBtn();});
diaI.addEventListener("change", toggleFormBtn);
diaC.addEventListener("change", toggleFormBtn);
mesI.addEventListener("change", toggleFormBtn);
mesC.addEventListener("change", toggleFormBtn);
anioI.addEventListener("change", toggleFormBtn);
anioC.addEventListener("change", toggleFormBtn);

//Validar las fechas inicio y cierre
function validateDate(dateIni, dateFin){
    return dateIni <= dateFin;
}

function toggleFormBtn(){
    //Obtener el valor actualizado de las fechas
    const diaInicio = parseInt(diaI.value);
    const mesInicio = parseInt(mesI.value);
    const anioInicio = parseInt(anioI.value);
    const diaCierre = parseInt(diaC.value);
    const mesCierre = parseInt(mesC.value);
    const anioCierre = parseInt(anioC.value);

    //Convertir a formato Date
    const date1 = new Date(anioInicio, mesInicio - 1, diaInicio);
    const date2 = new Date(anioCierre, mesCierre - 1, diaCierre);
    const today = new Date();

    if(projectName.value != "" && description.value != "" && metas.value != ""){
        //Validar fechas
        const validateDate1 = validateDate(today, date1);
        const validateDate2 = validateDate(date1, date2);

        if(validateDate1 && validateDate2){
            addProjectButton.disabled = false;
            addProjectButton2.disabled = false;
            if(!addProjectButton.classList.contains('enabled')){
                addProjectButton.classList.add('enabled');
            }
            if(!addProjectButton2.classList.contains('enabled')){
                addProjectButton2.classList.add('enabled');
            }
            newDepto2Validation();
        } else {
            disabledBtns();
        }
    } else {
        disabledBtns();
    }
}


const deptoSelected = document.getElementById('newDepto');
deptoSelected.addEventListener('keyup', function(){
    if(deptoSelected.value === ""){
        disabledBtns();
    }else{
        toggleFormBtn();
    }
});

function newDepto2Validation(){
    const selectedValue = dropDownDepto.value;
    if (selectedValue === 'other') {
        if(deptoSelected.value === ""){
            disabledBtns();
        }else{
            addProjectButton.disabled=false;
            addProjectButton2.disabled=false;
            if(!document.getElementById('sumbit-AddProject').classList.contains('enabled')){
                document.querySelector(".sumbit-AddProject").classList.toggle('enabled');
            }
            if(!document.getElementById('sumbit-AddProject-obj').classList.contains('enabled')){
                document.querySelector(".sumbit-AddProject-obj").classList.toggle('enabled');
            }
        }
    }
}

//Opciones departamento
document.addEventListener('DOMContentLoaded', function () {
    const dropDownDepto = document.getElementById('dropDownDepto');

    dropDownDepto.addEventListener('change', function() {
        const selectedValue = dropDownDepto.value;
        if (selectedValue === 'other') {
            if(document.querySelector('.newDepto').classList.contains('hide')){
                document.querySelector('.newDepto').classList.remove('hide');
                
                toggleFormBtn();
            }
            disabledBtns();
        } else{
            if(!document.querySelector('.newDepto').classList.contains('hide')){
                document.querySelector('.newDepto').classList.add('hide');
                toggleFormBtn();
            }
        }
    });
});

function disabledBtns(){
    addProjectButton.disabled=true;
    addProjectButton2.disabled=true;
    if(document.getElementById('sumbit-AddProject').classList.contains('enabled')){
        document.querySelector(".sumbit-AddProject").classList.toggle('enabled');
    }
    if(document.getElementById('sumbit-AddProject-obj').classList.contains('enabled')){
        document.querySelector(".sumbit-AddProject-obj").classList.toggle('enabled');
    }
}

//Confirmar eliminar proyecto
function confirmDelete() {
    return confirm("¿Estás seguro de que deseas eliminar esta cuenta?");
}

document.getElementById("Fdescription").addEventListener("input", function(){
    this.style.height = "auto";
    this.style.height = (this.scrollHeight) + "px";
});
document.getElementById("Fmeta").addEventListener("input", function(){
    this.style.height = "auto";
    this.style.height = (this.scrollHeight) + "px";
});

function setDateInFormBtn(uniqueKey){
    const today = new Date();
    var month = today.getMonth();
    var day = today.getDate();

    var d = document.getElementById("dia_"+uniqueKey);
    var m = document.getElementById("mes_"+uniqueKey);
    var y = document.getElementById("anio_"+uniqueKey);
    
    if(uniqueKey == 'cierre'){
        day++;
        d.selectedIndex = day;
    }else{
        d.selectedIndex = day;
    }
    m.selectedIndex = month;
    y.selectedIndex = 0;
}


const closeProjectBtns = document.querySelectorAll('.closeProject');
const closeProjectBgs = document.querySelectorAll('.closeProject-bg');

// Iterar sobre todos los botones closeProject
closeProjectBtns.forEach(function(btn, index) {
    // Agregar un event listener a cada botón closeProject
    btn.addEventListener('click', function() {
        // Quitar la clase 'hidden' del div closeProject-bg correspondiente
        closeProjectBgs[index].classList.remove('hidden');
    });
});

//Buscar proyecto
{
const searchBtn = document.getElementById('searchProject');
searchBtn.addEventListener('click', function(){
    toggleSearchItems();
});

document.addEventListener('DOMContentLoaded', function() {
    const searchBar = document.getElementById('search-bar');
    const searchButton = document.getElementById('searchProyecto');

    searchButton.addEventListener('click', function() {
        const query = searchBar.value;
        if (query) {
            window.location.href = `projectsManagement.php?search=${encodeURIComponent(query)}`;
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
            if(document.querySelector('.nav-buttons').classList.contains('searchingMode')){
                toggleSearchItems();
            }
        }
    });
});

function toggleSearchItems(){
    document.querySelector('.search-bar').classList.toggle('hide');
    document.querySelector('.nav-buttons').classList.toggle('searchingMode');
    document.getElementById('searchProyecto').classList.toggle('hide');
    document.getElementById('searchProject').classList.toggle('hide');
}
}

//Filtrar lista de resultados
const filtros = document.getElementById('filterProjectsList');
filtros.addEventListener('click', function(){
    toggleFilterItems()
});

document.addEventListener('DOMContentLoaded', function() {
    const dtoFilter = document.getElementById('dropDownDeptoFilter');

    dtoFilter.addEventListener('change', function() {
        const selectedOption = dtoFilter.options[dtoFilter.selectedIndex];
        const selectedValue = selectedOption.value;
        const selectedText = selectedOption.textContent || selectedOption.innerText;

        if (selectedValue === 'noFilter') {
            window.location.href = `projectsManagement.php`;
        } else {
            const encodedText = encodeURIComponent(selectedText);
            window.location.href = `projectsManagement.php?filterDto=${encodedText}`;
        }
    });
});

function toggleFilterItems(){
    filtros.classList.toggle('hide');
    document.querySelector('.filterDiv').classList.toggle('openFilterDiv');
    document.querySelector('.filterDiv').classList.toggle('closedFilterDiv');
    document.querySelector('.filtroFecha').classList.toggle('hide');
    document.querySelector('.dropDownFilter1').classList.toggle('hide');    
}

//Cerrar barra al hacer clic fuera del div
document.addEventListener('DOMContentLoaded', (event) => {
    const divFiltros = document.querySelector('.filterDiv');
    document.addEventListener('click', function(event) {
        const closeFilterBar = divFiltros.contains(event.target);

        if (!closeFilterBar) {
            if(document.querySelector('.filterDiv').classList.contains('openFilterDiv')){
                toggleFilterItems();
            }
        }
    });
});

//esconder elementos de busqueda
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        if(document.querySelector('.nav-buttons').classList.contains('searchingMode')){
            toggleSearchItems();
        }
        if(document.querySelector('.filterDiv').classList.contains('openFilterDiv')){
            toggleFilterItems();
        }
    }

});