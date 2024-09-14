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

function deleteProject(element){
    
}