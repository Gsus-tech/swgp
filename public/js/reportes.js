function closeReportView(){
    const reportView = document.querySelector('.pdf-view-container');
    if(reportView){
        reportView.remove();
    }
}


function downloadReport(element){
    createInputDiv('Descargar reporte', 'Nombre del archivo', downloadFunction);
}

function downloadFunction(reportName){
    const reportContent = document.querySelector('.pdf-view-container').innerHTML;
    const docContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Reporte</title>
        </head>
        <body>
            ${reportContent}
        </body>
        </html>
    `;

    const converted = htmlDocx.asBlob(docContent, {orientation: 'portrait'});

    const link = document.createElement('a');
    link.href = URL.createObjectURL(converted);
    link.download = `${reportName}.docx`;

    link.click();
}