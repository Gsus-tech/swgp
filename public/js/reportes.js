function closeReportView(){
    const reportView = document.querySelector('.pdf-view-container');
    if(reportView){
        reportView.remove();
    }
}

function downloadReport(element) {
    const nameAttr = element.closest('[rpname]');
    const savedName = nameAttr ? nameAttr.getAttribute('rpname') : null;
    if(savedName === null){
        createInputBox('Descargar reporte', 'Nombre del archivo:', null, 'Descargar', 'Cancelar')
        .then(reportName => {
            downloadFunction(reportName, false);
        })
        .catch(error => {
            if(error !== 'Input cancelado'){
                console.error(error);
            }
        });
    }else{
        downloadFunction(savedName, false);
    }
}

function downloadFunction(reportName, htmlCleanOut) {
    const reportContent = document.querySelector('.pdf-view-container');

    // Convertir imágenes a base64 para que Word las incluya en la descarga
    const images = reportContent.querySelectorAll('img');
    const promises = Array.from(images).map(img => convertImg(img));

    Promise.all(promises).then(base64Images => {
        base64Images.forEach((base64Img, index) => {
            images[index].src = base64Img;
        });

        let html = reportContent.innerHTML;

        if (htmlCleanOut) {
            const regex = /<div class="nameAndSpace">[\s\S]*?<div class="reportName">[\s\S]*?<\/div>[\s\S]*?<\/div>/g;
            let match;
            let count = 0;
            
            html = html.replace(regex, (match) => {
                count++;
                return count === 1 ? '' : '<div><br class="page-break"></div>';
            });
        }

        const docContent = `
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <title>Reporte</title>
                <style>                 
                    .editor ol, .editor ul {
                        margin-left: 25px;
                    }
                    .report-content ol, .report-content ul {
                        margin-left: 25px;
                    }
                    .page-break{
                        page-break-before: always;
                    }
                </style>
            </head>
            <body>
                ${html}
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


// function downloadFunction(reportName) {
//     const reportContent = document.querySelector('.pdf-view-container');

//     // Convertir imágenes a base64 para que Word las incluya en la descarga
//     const images = reportContent.querySelectorAll('img');
//     const promises = Array.from(images).map(img => convertImg(img));

//     Promise.all(promises).then(base64Images => {
//         base64Images.forEach((base64Img, index) => {
//             images[index].src = base64Img;
//         });

//         const docContent = `
//             <!DOCTYPE html>
//             <html>
//             <head>
//                 <meta charset="utf-8">
//                 <title>Reporte</title>
//                 <style>                 
//                     .editor ol, .editor ul {
//                         margin-left: 25px;
//                     }
//                     .report-content ol, .report-content ul {
//                         margin-left: 25px;
//                     }
//                 </style>
//             </head>
//             <body>
//                 ${reportContent.innerHTML}
//             </body>
//             </html>
//         `;

//         const converted = htmlDocx.asBlob(docContent, { orientation: 'portrait' });

//         const link = document.createElement('a');
//         link.href = URL.createObjectURL(converted);
//         link.download = `${reportName}.docx`;

//         link.click();
//     });
// }

function convertImg(img) {
    return new Promise((resolve) => {
        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');
        canvas.width = img.width;
        canvas.height = img.height;
        context.drawImage(img, 0, 0, canvas.width, canvas.height);
        resolve(canvas.toDataURL('image/png'));
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
