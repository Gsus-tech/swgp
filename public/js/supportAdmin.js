document.addEventListener("DOMContentLoaded", () => {
    const tBody = document.getElementById('ticketsTable_tbody');
    const noRowsElement = tBody.querySelector("#noRows");
    // Cambiar la fecha de sql a español
    if (!noRowsElement) {
        const rows = tBody.querySelectorAll('tr');
        rows.forEach(row => {
            const fechaCell = row.querySelector('.dateRow');
            if (fechaCell) {
                const sqlDate = fechaCell.textContent.trim();
                const formattedDate = formatSpanishDate(sqlDate);
                fechaCell.textContent = formattedDate;
            }
        });
    }

    //Agregar eventos de cambio de estado
    const stateTogglesOn = document.querySelectorAll('.fa-toggle-on');
    stateTogglesOn.forEach(btn => {
        const id = btn.closest('tr').getAttribute('tck');
        btn.addEventListener('click', ()=>{switchTicketState(id, 'Pendiente');});
    });
    const stateTogglesOff = document.querySelectorAll('.fa-toggle-off');
    stateTogglesOff.forEach(btn => {
        const id = btn.closest('tr').getAttribute('tck');
        btn.addEventListener('click', ()=>{switchTicketState(id, 'Abierto');});
    });

    //Agregar eventos de resolver ticket
    const btnsResolver = document.querySelectorAll('.fa-reply');
    btnsResolver.forEach(btn => {
        const tr = btn.closest('tr');
        const id = tr.getAttribute('tck');
        const tipo = tr.getAttribute('tcp');
        btn.addEventListener('click', ()=>{resolveTicket(id, tipo);});
    });
});

function switchTicketState(id, toState){
    const url = `../controller/supportManager.php?switchTicketState=true`;
    fetch(url, {
        method: 'POST',
        body: new URLSearchParams({
            ticketId: id,
            toState: toState
        }),
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Respuesta del servidor:', data);
        if(data.success){
            console.log('Estado actualizado:', data.message);
            location.reload();
            // Quiza en lugar de actualizar la pagina quitar boton y crear otro.
        } else {
            console.log('Error al actualizar estado:', data.message);
        }
    })
    .catch(error => {
        console.error('Error en la solicitud AJAX:', error);
    });
}

function fixAndCloseTicket(tck, type, column, fl){
    var regexEspeciales = /[^a-zA-Z0-9 áéíóúÁÉÍÓÚ]/g;
    const field = document.getElementById('newValue');

    if (regexEspeciales.test(field.value)) {
        field.setCustomValidity('No se permiten caracteres especiales.');
        field.classList.add('invalidField');
        field.reportValidity();
        return false;
    }
    if(!testValue('strict', 'newValue', column)){
        return false;
    }
    if(!testLenght('max',45,'newValue') || !testLenght('min',8,'newValue')){
        return false;
    }

    console.log(`tck= ${tck}`);
    const url = `../controller/supportManager.php?${type}=true`;
    let data;
    if(type==='AccountFieldUpdate'){
        const value = document.getElementById('newValue').value; 
        const userId = document.getElementById('ticketCreator').getAttribute('tcid'); 
        data = new URLSearchParams({
            column: column,
            newValue: value,
            ticketRem: userId,
            ticketId: tck
        });
        fl = true;
    }

    if(fl){
        fetch(url, {
            method: 'POST',
            body: data,
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success){
                alert('Datos actualizados correctamente.'); 
                location.reload();
            } else {
                console.log('Error al resolver el ticket: ', data.message);
            }
        })
        .catch(error => {
            console.error('Error en la solicitud AJAX: ', error);
        });
    }
}

function resolveTicket(id, tipo) {
    switch (tipo) {
        case '1':
            console.log(`Ticket ${id} is in state 1.`);
            break;
        case '2':
            console.log(`Ticket ${id} is in state 2.`);
            break;
        case '3':
            const areaDiv = document.querySelector('.solvingArea');
            const url = `../controller/supportManager.php?getTicket=true`;
            fetch(url, {
                method: 'POST',
                body: new URLSearchParams({
                    ticketId: id
                }),
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success){
                    areaDiv.innerHTML = data.html;
                    areaDiv.classList.add('fm-content');
                    setTimeout(
                        ()=>{
                            const solveBtn = document.getElementById('solveAndClose');
                            solveBtn.addEventListener('click', ()=>{
                                const child = areaDiv.children[0];
                                const t = child.getAttribute('t1p0');
                                const col = document.getElementById('newValue').getAttribute('field');
                                fixAndCloseTicket(id, t,col, false);
                            })
                        },
                        350
                    );
                } else {
                    console.log('Error al actualizar estado:', data.message);
                }
            })
            .catch(error => {
                console.error('Error en la solicitud AJAX:', error);
            });
            break;
        default:
            console.log(`No se reconoce el tipo de cambio solicitado.`);
            break;
    }
}
