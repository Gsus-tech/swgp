function generateCalendar(month, year, highlightedDates) {
    const calendar = document.getElementById('calendar');
    calendar.innerHTML = '';

    // Crear encabezado del mes
    const header = document.createElement('div');
    header.classList.add('calendar-header');
    header.textContent = `${month + 1}/${year}`;
    calendar.appendChild(header);

    const table = document.createElement('table');
    table.classList.add('calendar-table');
    calendar.appendChild(table);

    // Días de la semana
    const daysOfWeek = ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'];
    const thead = document.createElement('thead');
    const tr = document.createElement('tr');
    daysOfWeek.forEach(day => {
        const th = document.createElement('th');
        th.textContent = day;
        tr.appendChild(th);
    });
    thead.appendChild(tr);
    table.appendChild(thead);

    // Crear días del mes
    const firstDay = new Date(year, month).getDay(); 
    const daysInMonth = new Date(year, month + 1, 0).getDate(); 

    let date = 1;
    const tbody = document.createElement('tbody');
    
    for (let i = 0; i < 6; i++) {
        const tr = document.createElement('tr');
        for (let j = 0; j < 7; j++) {
            const td = document.createElement('td');
            if (i === 0 && j < firstDay) {
                td.textContent = '';
            } else if (date > daysInMonth) {
                td.textContent = '';
            } else {
                td.textContent = date;
                
                const currentDate = new Date(year, month, date);
                const formattedDate = currentDate.toISOString().split('T')[0];
                if (highlightedDates.includes(formattedDate)) {
                    td.classList.add('highlight-day');
                }
                date++;
            }
            tr.appendChild(td);
        }
        tbody.appendChild(tr);
    }
    table.appendChild(tbody);
}

const highlightedDates = ["2024-05-28", "2024-06-15", "2024-09-01"];
generateCalendar(4, 2024, highlightedDates); // Mes de mayo (0 = enero, 1 = febrero, etc.)
