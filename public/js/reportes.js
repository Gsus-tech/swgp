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

                    .file-options:hover {
                        height: 40px;
                        background-color: var(--navBtnHover);
                    }

                    .file-options .fa-times-rectangle{
                        color: #aa0808;
                        transition: color .25s ease-in-out;
                    }

                    .file-options .fa:hover {
                        cursor:pointer;
                        font-size: 130%;
                        text-shadow: 0 0 10px rgba(255, 255, 255, 0.8);
                    }

                    .file-options .fa-times-rectangle:hover {
                        color: #ff0a0a;
                    }

                    .file-options .fa-print:hover {
                        color: #037e72;
                    }

                    .reportName{
                        color:#82919e;
                        font-style:italic;
                        text-decoration:underline;
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

                    .f-mg{
                        margin: 60px 0 40px 0;
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