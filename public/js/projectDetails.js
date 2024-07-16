function imprimirProyecto(){
    alert('Imprimiendo proyecto');
}

function exportarProyecto(){
    alert('Exportando proyecto');
}

function returnToProjectsList(){
    window.location.href = `projectsManagement.php`;
}

function toggleDocumentOptions(){
    const options = document.getElementById('toggleDocumentOptions');
    const print = document.getElementById('printDetails');
    const edit = document.getElementById('editProject');

    options.classList.toggle('hide');
    print.classList.toggle('hide');
    edit.classList.toggle('hide');
}

document.addEventListener('keydown', function(event) {
    const print = document.getElementById('printDetails');
    if (event.key === 'Escape') {
        if(!print.classList.contains('hide')){
            toggleDocumentOptions();
        }
    }
});

document.addEventListener('DOMContentLoaded', (event) => {
    const options = document.querySelector('.optionsDiv');
    document.addEventListener('click', function(event) {
        const closeDiv = options.contains(event.target);

        if (!closeDiv) {
            if(!document.getElementById('printDetails').classList.contains('hide')){
                toggleDocumentOptions();
            }
        }
    });
});