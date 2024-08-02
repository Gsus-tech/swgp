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
const diaI = document.getElementById("dia_inicio");
const mesI = document.getElementById("mes_inicio");
const anioI = document.getElementById("anio_inicio");
const diaC = document.getElementById("dia_cierre");
const mesC = document.getElementById("mes_cierre");
const anioC = document.getElementById("anio_cierre");
const newDepto = document.getElementById('newDepto');


function submitNewProject(){

    let nameFlag = testRegex('Fname');
    if(nameFlag === false){
        return false;
    }
    nameFlag = testLenght('min', 8, 'Fname');
    if(nameFlag === false){
        return false;
    }
    nameFlag = testLenght('max', 45, 'Fname');
    if(nameFlag === false){
        return false;
    }
    nameFlag = testValue('strict', 'Fname');
    if(nameFlag === false){
        return false;
    }

    if(validateDate(new Date(), new Date(anioI.value, mesI.value -1, diaI.value))){
        mesI.setCustomValidity('Fecha de inicio inválida.\nEl proyecto no puede iniciar antes de la fecha actual.');
        mesI.reportValidity();        
        return false;
    }
    if(validateDate(new Date(anioI.value, mesI.value -1, diaI.value), new Date(anioC.value, mesC.value -1, diaC.value))){
        mesC.setCustomValidity('Fecha de cierre inválida.\nLa fecha de cierre debe ser posterior a la fecha de inicio.');
        mesC.reportValidity(); 
        return false;
    }


    if (document.getElementById('dropDownDepto').value === 'other') {
        let deptoFlag = testRegex('newDeptoInput');
        if (deptoFlag === false) {
            return false;
        }
        deptoFlag = testLenght('min', 8, 'newDeptoInput');
        if (deptoFlag === false) {
            return false;
        }
        deptoFlag = testLenght('max', 45, 'newDeptoInput');
        if (deptoFlag === false) {
            return false;
        }
        deptoFlag = testValue('strict', 'newDeptoInput');
        if (deptoFlag === false) {
            return false;
        }
    }

    
    let descriptionFlag = testControlledTextInput('Fdescription');
    if(descriptionFlag === false){
        return false;
    }
    descriptionFlag = testLenght('min', 20, 'Fdescription');
    if(descriptionFlag === false){
        return false;
    }
    descriptionFlag = testLenght('max', 1000, 'Fdescription');
    if(descriptionFlag === false){
        return false;
    }
    descriptionFlag = testValue('light', 'Fdescription');
    if(descriptionFlag === false){
        return false;
    }

    let metaFlag = testControlledTextInput('Fmeta');
    if(metaFlag === false){
        return false;
    }
    metaFlag = testLenght('min', 20, 'Fmeta');
    if(metaFlag === false){
        return false;
    }
    metaFlag = testLenght('max', 1000, 'Fmeta');
    if(metaFlag === false){
        return false;
    }
    metaFlag = testValue('light', 'Fmeta');
    if(metaFlag === false){
        return false;
    }
     
    var form = document.getElementById('addProject-form');
    form.action = "../controller/projectManager.php?addProject=true";
}

//Opciones departamento
function changeDepto() {
    const dropDownDepto = document.getElementById('dropDownDepto');

    const selectedValue = dropDownDepto.value;
    if (selectedValue === 'other') {
        if(document.querySelector('.newDepto').classList.contains('hide')){
            document.querySelector('.newDepto').classList.remove('hide');
        }
    } else{
        if(!document.querySelector('.newDepto').classList.contains('hide')){
            document.querySelector('.newDepto').classList.add('hide');
        }
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
    const date = today;
    if (uniqueKey === 'cierre') {
        date.setDate(date.getDate() + 1);
    }
    var month = date.getMonth();
    var day = date.getDate();
    var year = date.getFullYear();

    const d = document.getElementById("dia_"+uniqueKey);
    const m = document.getElementById("mes_"+uniqueKey);
    const y = document.getElementById("anio_"+uniqueKey);
    
    d.selectedIndex = day-1;
    m.selectedIndex = month;
    y.selectedIndex = year == today.getFullYear() ? 0 : 1;
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
    // document.querySelector('.filtroFecha').classList.toggle('hide');
    document.querySelector('.nav-buttons').classList.toggle('hide');
    document.querySelector('.dropDownFilter1').classList.toggle('hide');
    document.querySelector('.fa-history').classList.toggle('hide');    
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

//Cerrar formulario de alta -- no se recarga la pagina para esta accion.
function cerrarFormulario() {
    if (confirmCancel() == true) {
        document.getElementById("addProject-form").reset();
        document.getElementById('addProject-form').classList.toggle('hide');
    }
}

function confirmCancel() {
    const projectName = document.getElementById("Fname").value;
    const description = document.getElementById("Fdescription").value;
    const metas = document.getElementById("Fmeta").value;

    if (projectName !== '' || description !== '' || metas !== '') {
        return confirm("¿Estás seguro de que deseas cancelar? Se perderá la información ingresada.");
    }
    return true;
}

//Ver detalles de proyecto
function seeProjectAccount(idProyecto){
    window.location.href = `projectsManagement.php?projectDetails=${idProyecto}`;
}

//Habilitar acciones para proyectos seleccionados
document.addEventListener('DOMContentLoaded', (event) => {
    const checkboxes = document.querySelectorAll('.project-checkbox');
    const projectSelectedDiv = document.getElementById('projectSelected');
    const selectAllBox = document.getElementById('selectAllBoxes');
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateprojectSelectedDiv);
    });

    selectAllBox.addEventListener('change', () => {
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAllBox.checked;
        });
        updateprojectSelectedDiv();
    });

    function updateprojectSelectedDiv() {
        const checkedCheckboxes = Array.from(checkboxes).filter(chk => chk.checked);
        if (checkedCheckboxes.length > 0) {
            projectSelectedDiv.classList.remove('hide');
        } else {
            projectSelectedDiv.classList.add('hide');
        }
    }

    const applyAction1 = document.getElementById('applyAction');
    const applyAction2 = document.getElementById('applyAction2');
    applyAction1.addEventListener('click', applyAction);
    applyAction2.addEventListener('click', applyAction);
});