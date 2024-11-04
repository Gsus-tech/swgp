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

function fixAndCloseTicket(tck, type, vle, fl){
    const userId = document.getElementById('ticketCreator').getAttribute('tcid'); 
    const url = `../controller/supportManager.php?${type}=true`;
    let data;
    if(type==='AccountFieldUpdate'){
        var regexEspeciales = /[^a-zA-Z0-9 áéíóúÁÉÍÓÚ]/g;
        const field = document.getElementById('newValue');
        if (regexEspeciales.test(field.value)) {
            field.setCustomValidity('No se permiten caracteres especiales.');
            field.classList.add('invalidField');
            field.reportValidity();
            return false;
        }
        if(!testValue('strict', 'newValue', vle)){
            return false;
        }
        if(!testLenght('max',45,'newValue') || !testLenght('min',8,'newValue')){
            return false;
        }
        const value = document.getElementById('newValue').value; 
        data = new URLSearchParams({
            column: vle,
            newValue: value,
            ticketRem: userId,
            ticketId: tck
        });
        fl = true;
    }

    if(type==='systemErrorReport'){
        data = new URLSearchParams({
            response: vle,
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
                        areaDiv.scrollIntoView({
                            behavior: 'smooth'
                        });
                        const solveBtn = document.getElementById('solveAndClose');
                        solveBtn.addEventListener('click', ()=>{
                            const child = areaDiv.children[0];
                            const t = child.getAttribute('t1p0');
                            if(t==='AccountFieldUpdate'){
                                const col = document.getElementById('newValue').getAttribute('field');
                                fixAndCloseTicket(id, t, col, false);
                            }
                            else if(t==='systemErrorReport'){
                                createConfirmationDialog('Respuesta', '¿Deseas agregar un mensaje de respuesta para el usuario?',
                                    async ()=>{
                                        try{
                                            const mensaje = await createTextInputBox( 'Respondiendo...',
                                                'Ingresa el mensaje de respuesta para el usuario:',
                                                [{name:'maxlength', value:'250'}]
                                            );
                                            fixAndCloseTicket(id, t, mensaje, false);
                                        } catch (error) {
                                            if (error !== 'Input cancelado') {
                                                console.error(error);
                                            }else{
                                                console.log('Acción cancelada.')
                                            }
                                            return;
                                        }
                                    }, 
                                    ()=>{
                                        fixAndCloseTicket(id, t, null, false);
                                    }, 'Agregar mensaje', 'Enviar sin mensaje'
                                );
                            }
                        })
                        const cancelBtn = document.getElementById('cancelAndClose');
                        cancelBtn.addEventListener('click', ()=>{
                            areaDiv.innerHTML = '';
                            areaDiv.classList.remove('fm-content');
                        });
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

    // switch (tipo) {
    //     case '1':
    //         console.log(`Ticket ${id} is in state 1.`);
    //         break;
    //     case '2':
    //         console.log(`Ticket ${id} is in state 2.`);
    //         break;
    //     case '3':
    //         // Esto deberia ser lo mismo para los 3 escenarios. 
    //         // El case solo define el nombre de la clase conjunto al div solvingArea
        
    //         break;
    //     default:
    //         console.log(`No se reconoce el tipo de cambio solicitado.`);
    //         break;
    // }
}
