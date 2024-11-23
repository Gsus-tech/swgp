function imprimirProyecto(){
    createAlertDialog('¡Aviso!', 'Imprimiendo proyecto...', null, 'Aceptar');
}

function exportarProyecto(){
    const existingDiv = document.querySelector('.button-dialog-overlay');
    if(!existingDiv){
        const opcionesDiv = document.createElement('div');
        opcionesDiv.classList.add('mainCreatedDiv');
        const opciones = document.createElement('div');
        opciones.classList.add('opcionesDiv');
        const opcionesContent = document.createElement('div');
        opcionesContent.classList.add('opcionesDiv-content');
        
        const titleC = document.createElement('h3');
        titleC.textContent = "Exportando documento.";
        opcionesContent.appendChild(titleC);
        
        const buttonContainer = document.createElement('div');
        buttonContainer.classList.add('opcionesDiv-buttons');
        
        const projectButton = document.createElement('button');
        projectButton.textContent = 'Datos de proyecto';
        projectButton.classList.add('btn-green');
        buttonContainer.appendChild(projectButton);
        
        const reportButton = document.createElement('button');
        reportButton.textContent = 'Reporte general';
        reportButton.classList.add('btn-blue');
        buttonContainer.appendChild(reportButton);
        
        const cancelButton = document.createElement('button');
        cancelButton.classList.add('btn-red');
        cancelButton.textContent = 'Cancelar';
        buttonContainer.appendChild(cancelButton);
        
        opcionesContent.appendChild(buttonContainer);
        opciones.appendChild(opcionesContent);
        opcionesDiv.appendChild(opciones);
        
        document.body.appendChild(opcionesDiv);
        
        projectButton.addEventListener('click', function() {
            createProjectReport(false);
            opcionesDiv.remove();
        });
        
        reportButton.addEventListener('click', function() {
            createProjectReport(true);
            opcionesDiv.remove();
        });
        
        cancelButton.addEventListener('click', function() {
            opcionesDiv.remove();
        });
    }
}

function createProjectReport(fullReport){
    const projectDetails = document.querySelector('.detailsContainer');

    if(fullReport){
        const url = `../controller/projectManager.php?getFullReportData=true`;
        makeAjaxRequest(url, 'POST', null, function(response) {
            try {
                if (response.success) {
                    const data = response.data;
                    let reporteCompleto = document.createElement('div');
                    let piv = false;

                    // Hay que actualizar como se genera este reporte general. 
                    for (const [activityName, reports] of Object.entries(data)) {
                        if(piv===true){
                            const pageBreak = document.createElement('br');
                            pageBreak.setAttribute("class", "page-break");
                            reporteCompleto.appendChild(pageBreak);
                        }
                        const activityDiv = document.createElement('div'); 
                        activityDiv.classList.add('activity-report');
            
                        let piv2 = false;
                        let fullHtml = '';
                        //Iterar sobre los reportes registrados de cada actividad.
                        reports.forEach(report => {
                            const reportDiv = document.createElement('div'); 
                            reportDiv.classList.add('report-content');
                            reportDiv.innerHTML = '';
                            if(piv2===true){
                                const pageBreak = document.createElement('br');
                                pageBreak.setAttribute("class", "page-break");
                                reportDiv.appendChild(pageBreak);
                            }
                            
                            let htmlContent = '';
                            htmlContent += report.contenido;
                            
                            reportDiv.innerHTML += htmlContent;
                            piv2 = true; //Bandera para poner saltos de pagina
                            activityDiv.appendChild(reportDiv);
                        });
                        
                        reporteCompleto.appendChild(activityDiv);
                        piv = true; //Bandera para poner saltos de pagina
                    }
            
            
                
                    downloadFunction(fullReport, projectDetails, reporteCompleto);
                }else {
                    console.error('Error al obtener el reporte:', data.message);
                }
            } catch (error) {
                console.error('Error al analizar la respuesta JSON:', error);
                console.log('Respuesta recibida:', response); // Ver la respuesta exacta que se recibe
            }
        }, function(error) {
            if(error === 'No se encontraron reportes asociados'){
                createAlertDialog('¡Aviso!', 'Lo sentimos.\nNo se encontraron reportes asociados a este proyecto.', null, 'Aceptar');
            }else{
                console.error('Error en la solicitud AJAX:', error);
            }
        });


    }else{
        downloadFunction(fullReport, projectDetails);
    }
}

function downloadFunction(fullReport, projectDetails, reportContent) {  
    const promises = [];
    //Condiciona si se solicito un reporte completo.
    if(fullReport){
        const images = reportContent.querySelectorAll('img');
        images.forEach(img => {
            console.log('Image:');
            console.log(img);
            promises.push(convertImgToBase64(img));
        });
    } else {
        reportContent = null;
    }

    Promise.all(promises).then(() => {
        const docContent = `
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <title>Reporte</title>
                <style>
                    .detailsContainer {
                        position: relative;
                        width: 100%;
                        max-width: 800px;
                        margin: auto;
                        padding: 40px;
                        border: 1px solid #ddd;
                        border-radius: 8px;
                        box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
                        background-color: #fdfdfd;
                        text-align: center;
                    }

                    .detailsContainerTitle {
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        text-align: center;
                        margin-bottom: 20px;
                    }

                    .name h1 {
                        font-size: 3rem;
                        font-weight: bold;
                        margin: 0;
                        padding-top: 20px;
                        text-align: center;
                    }

                    .fechas {
                        position: absolute;
                        right: 20px;
                        top: 20px;
                        text-align: right;
                    }

                    .fechas label {
                        display: block;
                        font-size: 0.9rem;
                        color: #555;
                    }

                    .detailsContainerDiv h3 {
                        font-weight: bold;
                        font-size: 1.25rem;
                        color: #333;
                        margin-bottom: 0.5rem;
                        text-align: left;
                    }

                    .detailsContainerDiv i {
                        display: block;
                        font-size: 1rem;
                        color: #666;
                        margin-bottom: 15px;
                        text-align: left;
                    }

                    a {
                        font-style: normal;
                        color: #555;
                    }

                    #returnToProjects {
                        position: absolute;
                        bottom: 20px;
                        left: 20px;
                        font-size: 1.5rem;
                        color: #e74c3c;
                        text-decoration: none;
                    }

                    .optionsDiv {
                        position: absolute;
                        top: 20px;
                        right: 20px;
                    }

                    .redBtn {
                        color: #fff;
                        background-color: #e74c3c;
                        padding: 10px 15px;
                        border-radius: 50%;
                        text-decoration: none;
                    }
                        
                   .page-break{
                        page-break-before: always;
                    }
                      
                </style>
            </head>
            <body>
                <div>
                    ${projectDetails.innerHTML}
                </div>
                ${fullReport ? `<br class="page-break">
                    <div class="page">
                        <div class="reportTitle">
                            <br><br><br><br><br><br><br><br><br>
                            <h1>Reporte del Proyecto</h1>
                        </div>
                    </div>
                    <br class="page-break">
                    <div>${reportContent.innerHTML}</div>` : ''}
            </body>
            </html>
        `;

        const converted = htmlDocx.asBlob(docContent, { orientation: 'portrait' });

        const link = document.createElement('a');
        link.href = URL.createObjectURL(converted);
        const projectName = document.querySelector('.name h1').textContent;
        const fixedName = projectName.replace(/ /g, '_');
        link.download = `${fixedName}_RG.docx`;

        link.click();
    });
}


function editarProyecto(id){
    window.location.href = `projectsManagement.php?editProject=${id}`;
}

function returnToProjectsList(){
    window.location.href = `projectsManagement.php`;
}

function toggleDocumentOptions(){
    const options = document.getElementById('toggleDocumentOptions');
    // const print = document.getElementById('printDetails');
    const share = document.getElementById('shareProject');
    const edit = document.getElementById('editProject');

    share.classList.toggle('hide');
    options.classList.toggle('hide');
    // print.classList.toggle('hide');
    edit.classList.toggle('hide');
}

document.addEventListener('keydown', function(event) {
    const print = document.getElementById('shareProject');
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
            if(!document.getElementById('shareProject').classList.contains('hide')){
                toggleDocumentOptions();
            }
        }
    });
});