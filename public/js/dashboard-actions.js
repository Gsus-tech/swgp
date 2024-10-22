document.addEventListener("DOMContentLoaded", function() {
    // Seleccionar todos los botones con la clase .cardMenu
    const cardMenuButtons = document.querySelectorAll('.cardMenu');
    cardMenuButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.stopPropagation();

            // Crear el nuevo div para las opciones
            const isMenuOn = document.querySelector('.opciones-tarjetas');
            if (!isMenuOn) {
                createDiv()
            }else{
                const oldMenu = document.querySelector('.opciones-tarjetas');
                oldMenu.remove();
                createDiv()
            }

            function createDiv(){
                const actId = button.closest('.card-body').id;
                const newMenu = document.createElement('div');
                newMenu.className = 'opciones-tarjetas';
                
                const closestTaskDiv = button.closest('.tasks');
                const columnId = closestTaskDiv ? closestTaskDiv.id : null;
                const closestActDiv = button.closest('.card');
                const cardId = closestActDiv ? closestActDiv.getAttribute('data-card-id') : null;
                newMenu.setAttribute("act-id",cardId);
                
                newMenu.innerHTML =  `
                    <a href="#" onclick="makeReport(this)" class="dropdown-item"><i>Crear reporte</i></a>`;

                document.body.appendChild(newMenu);

                const rect = button.getBoundingClientRect();  // Obtener posición del botón
                newMenu.style.top = `${rect.bottom}px`;  // Posicionarlo justo debajo del botón
                newMenu.style.left = `${rect.left}px`;

                // Evento de clic para cerrar el menú
                document.addEventListener('click', function closeMenu(event) {
                    if (!newMenu.contains(event.target) && event.target !== button) {
                        newMenu.remove();  // Eliminar el menú si se hace clic fuera del Div
                        document.removeEventListener('click', closeMenu);
                    }
                });
            }
        });
    });
});

function makeReport(element){
    const cardId = element.closest('.opciones-tarjetas').getAttribute('act-id');
    localStorage.setItem('openReportEditor', 'true');
    localStorage.setItem('actId', cardId);
    window.location.href = "../php/actionsManagement.php";
}


document.addEventListener("DOMContentLoaded", function() {

    const progressIcon = document.getElementById('progressIcon');
    const progressDiv = document.querySelector('.progressIcon-div');;

    progressIcon.addEventListener('click', function(){
        progressDiv.style = "width:90%;";
        progressIcon.classList.add('hide');
        document.querySelector('.progress-bar').classList.remove('hide');
    })

    document.addEventListener('click', function(event) {
        if(!progressDiv.classList.contains('hide')){
            const closeBar = progressDiv.contains(event.target);     
            if (!closeBar) {
                if(!document.querySelector('.progress-bar').classList.contains('hide')){
                    progressDiv.style = "width:fit-content;";
                    document.querySelector('.progress-bar').classList.add('hide');
                    progressIcon.classList.remove('hide');
                }
            }
        }
    });
});