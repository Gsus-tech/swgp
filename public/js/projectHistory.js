const filtros = document.getElementById('filterProjectsList');
filtros.addEventListener('click', function(){
    toggleFilterItems()
});

function toggleFilterItems(){
    filtros.classList.toggle('hide');
    document.querySelector('.filterDiv').classList.toggle('openFilterDiv');
    document.querySelector('.filterDiv').classList.toggle('closedFilterDiv');
    document.querySelector('.dropDownFilters').classList.toggle('hide');
}

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

function reactivateProject(element){
    if(confirm('¿Reactivar este proyecto?')){
        let closestElement = element;
        while (closestElement && !closestElement.hasAttribute('p-i')) {
            closestElement = closestElement.parentElement;
        }
            const projectId = closestElement.getAttribute('p-i');
        fetch(`../controller/projectManager.php?reactivate=${encodeURIComponent(projectId)}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            }
        })
        .then(response => response.text())
        .then(data => {
            window.location.href = `projectsManagement.php?project-history`;
        })
        .catch(error => {
            console.error('Error en la solicitud AJAX:', error);
        });
    }
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

function applyAction() {
    showLoadingCursor();
    var actionSelected = document.getElementById('actionSelected').value;
    if (actionSelected === 'delete') {
        const checkboxes = document.querySelectorAll('.project-checkbox');
        const checkedCheckboxes = Array.from(checkboxes).filter(chk => chk.checked);
        
        if(checkedCheckboxes.length>0){
            const confirmationMessage = 
            `¿Estás seguro de querer eliminar ${checkedCheckboxes.length} proyecto(s)?\n`+
            `\nEsta acción es irreversible y no podrás acceder a la información de este proyecto en el futuro.\n`+
            `Esto incluye objetivos, actividades y eventos del proyecto.\n\n¿Continuar?`;
            const userConfirmed = confirm(confirmationMessage);

            if (userConfirmed) {
                if(confirm('Confirmar la acción')){
                    let promises = [];
                    checkedCheckboxes.forEach(box => {
                        promises.push(deleteProject(box.value));
                    });
                    Promise.all(promises).then(() => {
                        setTimeout(() => {
                            hideLoadingCursor();
                            localStorage.setItem('showProjectsPermanentlyDeleted', 'true');
                            window.location.reload();
                        }, 1000);
                    });
                }else{
                    hideLoadingCursor();
                }
            }else{
                hideLoadingCursor();
            }
        }
    }
}

function deleteProject(projectId){
    let urlString = `../controller/projectManager.php?deleteProjectPermanently=${encodeURIComponent(projectId)}`;
    fetch(urlString, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        }
    })
    .then(response => response.text())
    .then(data => {
        console.log(`Proyecto eliminado correctamente de la base de datos.`);
    })
    .catch(error => {
        console.error('Error en la solicitud AJAX:', error);
    });
}

function returnToProjectsList(){
    window.location.href = `projectsManagement.php`;
}

function FilterHistoryResults() {
    const deptoFilterValue = document.getElementById('dropDownDeptoFilter').value;
    const stateFilterValue = document.getElementById('dropDownStateFilter').value;
    const allRows = document.querySelectorAll('#projects-list-body tr');
    let visibleRowCount = 0;

    // Eliminar el mensaje de "No se encontraron resultados" si existe
    const noResultsRow = document.getElementById('no-results-row');
    if (noResultsRow) {
        noResultsRow.remove();
    }

    // Oculta o muestra filas basadas en los filtros seleccionados
    allRows.forEach(row => {
        // Verificar si la fila tiene al menos 4 celdas (para evitar errores con filas de mensajes)
        if (row.cells.length >= 4) {
            const deptoCellValue = row.cells[2].textContent.trim(); // Valor de la celda de departamento
            const stateCellValue = row.cells[3].textContent.trim(); // Valor de la celda de estado

            const deptoMatch = (deptoFilterValue === 'noFilter' || deptoCellValue === deptoFilterValue);
            const stateMatch = (stateFilterValue === 'noFilter' || 
                                (stateFilterValue === 'concluded' && stateCellValue === 'Concluido') ||
                                (stateFilterValue === 'canceled' && stateCellValue === 'Cancelado'));

            if (deptoMatch && stateMatch) {
                row.style.display = ''; // Muestra la fila si coincide con ambos filtros
                visibleRowCount++;
            } else {
                row.style.display = 'none'; // Oculta la fila si no coincide con los filtros
            }
        } else {
            // Oculta filas que no cumplen con el formato esperado
            row.style.display = 'none';
        }
    });

    // Agregar un mensaje de "No se encontraron resultados" si no hay filas visibles
    if (visibleRowCount === 0) {
        const tbody = document.getElementById('projects-list-body');
        const newRow = document.createElement('tr');
        newRow.id = 'no-results-row';
        newRow.innerHTML = "<td colspan='6'>No se encontraron resultados.</td>";
        tbody.appendChild(newRow);
    }

    // Actualiza el texto del filtro aplicado
    const currentFilterDisplay = document.getElementById('currentFilter');
    if (currentFilterDisplay) {
        let filterText = 'No hay filtro aplicado';
        if (deptoFilterValue !== 'noFilter' || stateFilterValue !== 'noFilter') {
            filterText = `Filtros aplicados: `;
            if (deptoFilterValue !== 'noFilter') {
                const deptoOptionText = document.querySelector(`#dropDownDeptoFilter option[value="${deptoFilterValue}"]`).textContent;
                filterText += `Departamento: ${deptoOptionText} `;
            }
            if (stateFilterValue !== 'noFilter') {
                const stateOptionText = document.querySelector(`#dropDownStateFilter option[value="${stateFilterValue}"]`).textContent;
                filterText += `Estado: ${stateOptionText} `;
            }
        }
        currentFilterDisplay.textContent = filterText;
    }

    // Mostrar u ocultar paginación
    const pagination = document.getElementById('pagination');
    if (deptoFilterValue === 'noFilter' && stateFilterValue === 'noFilter') {
        pagination.style.display = '';
        const activeButton = document.querySelector('#pagination .active');
        if (activeButton) {
            activeButton.click();
        }
    } else {
        pagination.style.display = 'none';
    }
}
