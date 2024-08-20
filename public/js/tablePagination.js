function paginateTable(tbodyId, rowsPerPage, paginationId) {
    const tableBody = document.getElementById(tbodyId);
    const rows = tableBody.getElementsByTagName('tr');
    const pagination = document.getElementById(paginationId);
    let currentPage = 1;

    function displayTable(rows, rowsPerPage, page) {
        const start = (page - 1) * rowsPerPage;
        const end = start + rowsPerPage;

        for (let i = 0; i < rows.length; i++) {
            rows[i].style.display = i >= start && i < end ? '' : 'none';
        }
    }

    function setupPagination(rows, rowsPerPage, paginationId) {
        const pageCount = Math.ceil(rows.length / rowsPerPage);
        pagination.innerHTML = "";

        if (pageCount <= 1) {
            pagination.style.display = 'none';
            return;
        }
        for (let i = 1; i <= pageCount; i++) {
            const btn = paginationButton(i);
            pagination.appendChild(btn);
        }
        // displayTable(rows, rowsPerPage, 1); // Siempre reinicia en la primera página

    }

    function paginationButton(page) {
        const button = document.createElement('button');
        button.innerText = page;

        if (currentPage === page) button.classList.add('active');

        button.addEventListener('click', function () {
            currentPage = page;
            displayTable(rows, rowsPerPage, currentPage);

            const currentBtn = document.querySelector(`#${paginationId} button.active`);
            if (currentBtn) currentBtn.classList.remove('active');

            button.classList.add('active');
        });

        return button;
    }

    displayTable(rows, rowsPerPage, currentPage);
    setupPagination(rows, rowsPerPage, paginationId);
}