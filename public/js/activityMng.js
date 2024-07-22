function FiltersToggle(){
    const filterDiv = document.getElementById('filterDiv');
    const filtros = document.querySelectorAll('.dropDownFilter');

    filterDiv.classList.toggle('closedFilterDiv');
    filterDiv.classList.toggle('openFilterDiv');

    filtros.forEach(filtro => {
        filtro.classList.toggle('hide');
    });
}



document.addEventListener("DOMContentLoaded", function() {
    const checkboxes = document.querySelectorAll('.activity-checkbox');
    const allBoxs = document.getElementById('selectAllActivities');
    const actSelectedDiv = document.getElementById('selectedRowsOptions');
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', ActivitiesSelectedOptions);
    });

    allBoxs.addEventListener('change', () => {
        checkboxes.forEach(checkbox => {
            checkbox.checked = allBoxs.checked;
        });
        ActivitiesSelectedOptions();
    });

    function ActivitiesSelectedOptions() {
        const checkedCheckboxes = Array.from(checkboxes).filter(chk => chk.checked);
        if (checkedCheckboxes.length > 0) {
            actSelectedDiv.classList.remove('hide');
        } else {
            actSelectedDiv.classList.add('hide');
        }
    }


    //Cerrar barra al hacer clic fuera del div
    const filterDiv = document.getElementById('filterDiv');
    document.addEventListener('click', function(event) {
        const closeFilterBar = filterDiv.contains(event.target);

        if (!closeFilterBar) {
            if(filterDiv.classList.contains('openFilterDiv')){
                FiltersToggle();
            }
        }
    });

});