function closeReportView(){
    const reportView = document.querySelector('.pdf-view-container');
    if(reportView){
        reportView.remove();
    }
}


function printReport(element){
    alert('Imprimiendo reporte.');
}