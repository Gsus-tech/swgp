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

    if(type==='projectInfoUpdate'){

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
                createAlertDialog('¡Completado!', 'Datos actualizados correctamente.', ()=>{location.reload();}, 'Aceptar');
            } else {
                console.log('Error al resolver el ticket: ', data.message);
                createAlertDialog('Acción no realizada', 'Error al resolver el ticket: ', data.message, null, 'Aceptar');
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
                        else if(t==='projectInfoUpdate'){
                            t2 = child.getAttribute('tcp2');
                            pid = child.getAttribute('pid');
                            if(t2 === 'addMember') {
                                addUserToProject(pid, id);
                            }
                            else if(t2 === 'removeMember') {
                                createConfirmationDialog('Eliminando integrante',
                                    'Estas a punto de eliminar un integrante de un proyecto.\n'+
                                    'Está acción es irreversible.\nTodas las responsabilidades de este integrante pasarán al representante del proyecto.\n\n'+
                                    'Esto no eliminará la cuenta de usuario, simplemente removerá el acceso del usuario al proyecto seleccionado.\n\n'+
                                    '¿Deseas continuar?',
                                    ()=>{
                                        const user = document.getElementById('alterThisUser').getAttribute('uid');
                                        deleteMemberFromProject(pid, user, id);
                                    }, 
                                    ()=>{
                                        console.log('Acción cancelada. No se eliminó al integrante del proyecto.')
                                    }, 'Continuar'
                                );
                            }
                            else if(t2 === 'changePermitions') {
                                const currentRol = document.getElementById('currentRol').getAttribute('currol');
                                let msg = ''
                                if(currentRol === 'rep'){
                                    msg = 'Esta acción quitará los privilegios de representante de proyecto.\n\n'+
                                    'Esto significa que el usuario no podrá administrar actividades del proyecto.\n\n';
                                }else{
                                    msg = 'Esta acción brindará privilegios de representante de proyecto al usuario.\n\n'+
                                    'Esto significa que el usuario podrá crear y administrar actividades del proyecto.\n\n';
                                }
                                createConfirmationDialog(`Cambio de permisos de usuario`,
                                    `${msg}¿Deseas continuar?`, 
                                    ()=>{
                                        changeUserRolInProject(currentRol, pid, id);
                                    }, 
                                    ()=>{ console.log('Acción cancelada.'); }
                                );
                            }
                            else if(t2 === 'projectDataCorrection') {
                                createConfirmationDialog('Redirigiendo...',
                                    '¿Ir a la edición del proyecto?',
                                    ()=>{
                                        location.href = `../php/projectsManagement.php?editProject=${pid}`;
                                    },
                                    ()=>{
                                        console.log('Acción Cancelada');
                                    },'Redirígeme', 'Cancelar'
                                );
                            }
                            
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
}

function addUserToProject(project, tik){
    const userName = document.getElementById('newUserName').value;
    const userDepto = document.getElementById('newUserDepto').value;
    const userMail = document.getElementById('newUserMail').value;
    const url = `../controller/supportManager.php?findExistingUser=true`;
    fetch(url, {
        method: 'POST',
        body: new URLSearchParams({
            name: userName,
            mail: userMail
        }),
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success){
            console.log(data.message);
            if(data.exists){
                if(data.message === 'Correo de usuario existente.'){
                    $hdr = "Correo registrado";
                    $texto = "Se encontró una cuenta asociada al correo";
                }else{
                    $hdr = "Nombre registrado";
                    $texto = "Se encontraron coincidencias del nombre de usuario por agregar";
                }
                foundId = data.userInfo.id_usuario;
                createConfirmationDialog($hdr, 
                    `${$texto}.\n\nInformación de cuenta:\n`+
                    `Nombre: ${data.userInfo.nombre}\nCorreo: ${data.userInfo.correo}\nDepartamento: ${data.userInfo.departamento}`,
                    ()=>{
                        fetch('../controller/supportManager.php?addTeamMember=true', {
                            method: 'POST',
                            body: new URLSearchParams({
                                userId: foundId,
                                proyecto: project
                            })
                        })
                        .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                createAlertDialog('¡Completado!', result.message, ()=>{closeTicketConfirmation(tik);}, 'Aceptar');
                            } else {
                                createAlertDialog('Acción no realizada', 'Error al añadir al usuario al equipo del proyecto.\n\n'+result.message , null, 'Aceptar');
                            }
                        })
                        .catch(error => {
                            console.error('Error en la solicitud:', error);
                            createAlertDialog('¡Error!', 'Error al añadir al usuario al equipo del proyecto: '+error, null, 'Aceptar');
                        });
                        
                    },
                    ()=>{
                        console.log('Acción cancelada.');                            
                    }, 'Agregar esta cuenta', 'Cancelar'
                );
            }else{
                createConfirmationDialog('Crear nuevo usuario', 
                    `No se encontró una cuenta asociada al correo '${userMail}'.\n¿Crear una cuenta para el usuario ${userName}?`,
                    ()=>{
                        fetch('../controller/supportManager.php?createAndAddTeamMember=true', {
                            method: 'POST',
                            body: new URLSearchParams({
                                proyecto: project,
                                name: userName,
                                depto: userDepto,
                                mail: userMail
                            })
                        })
                        .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                createAlertDialog('¡Completado!', result.message, ()=>{closeTicketConfirmation(tik);}, 'Aceptar');
                            } else {
                                createAlertDialog('Acción no realizada', 'Error al añadir al usuario al equipo del proyecto.\n\n'+result.message , null, 'Aceptar');
                            }
                        })
                        .catch(error => {
                            console.error('Error en la solicitud:', error);
                            createAlertDialog('¡Error!', 'Error al añadir al usuario al equipo del proyecto: '+error, null, 'Aceptar');
                        });
                    },
                    ()=>{
                        createAlertDialog('¡Acción cancelada!', 'No se realizaron cambios.', null, 'Aceptar');
                    }, 'Crear cuenta', 'Cancelar'
                );
            }
        } else {
            console.log('Error al validar existencia de usuario:', data.message);
        }
    })
    .catch(error => {
        console.error('Error en la solicitud AJAX:', error);
    });
}

function changeUserRolInProject(rol, project, tik){
    const user = document.getElementById('alterThisUser').getAttribute('uid');
    const url = `../controller/supportManager.php?changeUserRolInProject=true`;
    fetch(url, {
        method: 'POST',
        body: new URLSearchParams({
            user: user,
            project: project,
            rol: rol
        }),
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success){
            console.log(data.message);
            if(data.chg){
                createAlertDialog('¡Completado!', 'Permisos de usuario actualizados.', null, 'Aceptar');
            }else{
                createAlertDialog('¡Completado!', `${data.message}\nYa puedes cerrar este ticket.`, null, 'Aceptar');
            }
            closeTicketConfirmation(tik);
        } else {
            console.log('Error al cambiar los permisos del usuario:', data.message);
            createAlertDialog('Acción no realizada', 'Error al cambiar los permisos del usuario: '+data.message , null, 'Aceptar');
        }
    })
    .catch(error => {
        console.error('Error en la solicitud AJAX:', error);
    });
}

function deleteMemberFromProject(project, user, tik){
    const url = `../controller/projectManager.php?deleteMember=true`;
    fetch(url, {
        method: 'POST',
        body: new URLSearchParams({
            idUsuario: user,
            projectId: project
        }),
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success){
            console.log(data.message);
            createAlertDialog('¡Completado!', 'Integrante eliminado del Proyecto.', ()=>{closeTicketConfirmation(tik);}, 'Aceptar');
        } else {
            console.log('Error al eliminar al integrante del Proyecto:', data.message);
        }
    })
    .catch(error => {
        console.error('Error en la solicitud AJAX:', error);
    });
}

function closeTicketConfirmation(tik){
    createConfirmationDialog('Mensaje de confirmación',
        '¿Deseas cerrar este ticket?',
        ()=>{
            switchTicketState(tik, 'Cerrado');
            console.log('Ticket cerrado.')
        },
        ()=>{
            console.log('El ticket no se cerró.'); 
            const areaDiv = document.querySelector('.solvingArea');
            areaDiv.innerHTML = '';
            areaDiv.classList.remove('fm-content');
        }, 
        'Cerrar ticket', 'Dejar como está'
    );
}