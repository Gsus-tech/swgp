function closeReportView(){
    const reportView = document.querySelector('.pdf-view-container');
    if(reportView){
        reportView.remove();
    }
}


function downloadReport(element){
    createInputDiv('Descargar reporte', 'Nombre del archivo', downloadFunction);
}

function downloadFunction(reportName) {
    const reportContent = document.querySelector('.pdf-view-container');
    
    // Convertir imágenes a base64 para que word las incluya en la descarga
    const images = reportContent.querySelectorAll('img');
    const promises = [];

    images.forEach(img => {
        promises.push(convertImgToBase64(img));
    });

    Promise.all(promises).then(() => {
        const docContent = `
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <title>Reporte</title>
                <style>
                    img {
                        max-height: 15rem;
                        max-width: 100%;
                        height: auto;
                        width: auto;
                    }
                </style>
            </head>
            <body>
                ${reportContent.innerHTML}
            </body>
            </html>
        `;

        const converted = htmlDocx.asBlob(docContent, { orientation: 'portrait' });

        const link = document.createElement('a');
        link.href = URL.createObjectURL(converted);
        link.download = `${reportName}.docx`;

        link.click();
    });
}

function convertImgToBase64(img) {
    return new Promise((resolve, reject) => {
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        const image = new Image();

        image.crossOrigin = 'Anonymous';
        image.onload = function () {
            const maxHeight = 250; // Las imagenes tendran una altura maxima de 250px
            let { width, height } = image;

            if (height > maxHeight) {
                const aspectRatio = width / height;
                height = maxHeight;
                width = height * aspectRatio;
            }

            canvas.width = width;
            canvas.height = height;
            ctx.drawImage(image, 0, 0, width, height);
            const dataURL = canvas.toDataURL('image/png'); // Conversion de imagen a base64

            img.src = dataURL; 
            img.setAttribute('width', `${width}px`);
            img.setAttribute('height', `${height}px`);
            resolve();
        };
        image.onerror = function (err) {
            reject(err);
        };
        image.src = img.src;
    });
}