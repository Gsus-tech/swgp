function imprimirProyecto(){
    alert('Imprimiendo proyecto');
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

                    // Iterar sobre las actividades existentes.
                    for (const [activityName, reports] of Object.entries(data)) {
                        if(piv===true){
                            const pageBreak = document.createElement('br');
                            pageBreak.setAttribute("class", "page-break");
                            reporteCompleto.appendChild(pageBreak);
                        }
                        const activityDiv = document.createElement('div'); 
                        activityDiv.classList.add('activity-report');
            
                        const activityTitle = document.createElement('h1');
                        activityTitle.textContent = activityName;
                        activityDiv.appendChild(activityTitle);
                        let piv2 = false;
                        //Iterar sobre los reportes registrados de cada actividad.
                        reports.forEach(report => {
                            const reportDiv = document.createElement('div'); 
                            reportDiv.classList.add('report-content');
                            
                            if(piv2===true){
                                const pageBreak = document.createElement('br');
                                pageBreak.setAttribute("class", "page-break");
                                reportDiv.appendChild(pageBreak);
                            }
                            const reportContentArray = JSON.parse(report.contenido);
                            
                            //Iterar sobre el contenido de cada reporte para dar formato.
                            reportContentArray.forEach(item => {
                                let element;
                                if (item.type === 'h2' || item.type === 'h3' || item.type === 'p') {
                                    element = document.createElement(item.type);
                                    element.innerHTML = item.value.replace(/\n/g, '<br>');
                                } else if (item.type === 'img') {
                                    element = document.createElement('img');
                                    element.setAttribute('src', item.value);
                                    element.alt = 'Report image';
                                }else {
                                    element.textContent = item.value; 
                                }
            
                                if (element) {
                                    reportDiv.appendChild(element);
                                }
                            });
                            
                            piv2 = true; //Bandera para poner saltos de pagina
                            activityDiv.appendChild(reportDiv);
                        });
                        
                        const separador = document.createElement('hr');
                        activityDiv.appendChild(separador);
                        
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
                alert('Lo sentimos.\nNo se encontraron reportes asociados a este proyecto.');
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
                    .pdf-view-container {
                        position: fixed;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        background: rgba(0, 0, 0, 0.6);
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        z-index: 1000;
                    }
                    .report-content {
                        position: relative;
                        width: 80%;
                        max-width: 800px;
                        background-color: white;
                        padding: 2rem;
                        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                        border-radius: 8px;
                        z-index: 1000;
                        text-align: left;
                        overflow: auto;
                        max-height: 80vh;
                    }
                    .report-content h2 {
                        text-align: center;
                        margin: 2rem auto;
                    }
                    .report-content h3, .report-content p {
                        text-align: justify;
                        margin-bottom: 1rem;
                    }
                    .report-content img {
                        max-width: 80%;
                        display: block;
                        margin: 1rem auto;
                        text-align: center;
                        max-height: 15rem;
                    }
                    .file-options {
                        position: fixed;
                        padding: 9px 15px;
                        justify-content: space-between;
                        background-color: var(--navBtnBg);
                        width: 5.5rem;
                        display: flex;
                        gap: 1rem;
                        border-radius: 10px;
                    }
                    .spacer {
                        display: flex;
                        align-items: center;
                        text-align: center;
                        margin: 20px 0 40px 0;
                    }
                    .spacer-line {
                        flex-grow: 1;
                        border-bottom: 1px solid #ccc;
                        margin: 0 10px;
                    }
                    .f-mg {
                        margin: 60px 0 40px 0;
                    }

                    .name h1, .reportTitle br{
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-style: normal;
                        font-size:2rem;
                        font-weight: bold;
                    }

                    .page-break{
                        page-break-before: always;
                    }
                        
                    .page {
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        height: 100vh; /* Full height of the page */
                    }

                    .reportTitle {
                        text-align: center;
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