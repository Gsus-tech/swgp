
document.addEventListener("DOMContentLoaded", function() {
    const containers = [
        document.getElementById('task-list-one'),
        document.getElementById('task-list-two'),
        document.getElementById('task-list-three'),
        document.getElementById('task-list-four')
    ];

    const drake = dragula(containers, {
        accepts: function(el, target, source, sibling) {
            // Permitir soltar en contenedores vacíos
            return true;
        },
        moves: function (el, source, handle, sibling) {
            return true; // Permitir que todos los elementos se puedan arrastrar
        }
    });
    
    // Revisar y agregar placeholders a contenedores vacíos
    drake.on('drop', function(el, target, source, sibling) {
        checkForEmptyContainers(containers);

        //Codigo para actualizar la posicion de la actividad en la BD
        actualizarContador(target.id);
        actualizarContador(source.id);
        updateProjectPercentage();
        const col = getColumnaId(target.id);
        updateCardColumn(el.getAttribute('data-card-id'), col);
    });

    function checkForEmptyContainers(containers) {
        containers.forEach(container => {
            const hasPlaceholder = container.querySelector('.emptyTaskDivMsj');
            const childCount = Array.from(container.children).filter(child => !child.classList.contains('emptyTaskDivMsj')).length;
    
            if (childCount === 0 && !hasPlaceholder) {
                // Add placeholder if the container is empty
                const placeholder = document.createElement('div');
                placeholder.className = 'emptyTaskDivMsj';
                placeholder.textContent = 'Sin tareas...';
                container.appendChild(placeholder);
    
                // Optionally, add a border
                const taskContainer = container.closest('.task-list-items');
                if (taskContainer) {
                    taskContainer.style.border = '1px dashed #ccc';
                }
            } else if (childCount > 0 && hasPlaceholder) {
                // Remove placeholder if the container is not empty
                hasPlaceholder.remove();
                const taskContainer = container.closest('.task-list-items');
                if (taskContainer) {
                    taskContainer.style.border = '';  // Remove border
                }
            }
        });
    }    

    // Verificar los contenedores al cargar la página
    checkForEmptyContainers(containers);

});



//Auto scroll - todavia no esta terminada.

let autoScrollInterval;

function startAutoScroll(e) {
    const scrollMargin = 200; 
    const scrollSpeed = 20;  

    
    if (e.clientY < scrollMargin) {
        autoScrollInterval = setInterval(() => {
            window.scrollBy(0, -scrollSpeed); 
        }, 50);
    } else if (e.clientY > window.innerHeight - scrollMargin) {
        autoScrollInterval = setInterval(() => {
            window.scrollBy(0, scrollSpeed); 
        }, 50);
    } else {
        clearInterval(autoScrollInterval);
    }
}

// Escuchar el evento de drag
document.addEventListener('drag', startAutoScroll);

// Detener el scroll automático cuando se suelta el elemento arrastrado
document.addEventListener('dragend', () => {
    clearInterval(autoScrollInterval);
});

