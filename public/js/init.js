//Barra lateral
const menu = document.querySelector(".sidebar-menu")

menu.addEventListener('click', function(){
    expandSideBar();
})

function expandSideBar(){
    document.querySelector('body').classList.toggle('short');
}

function openAccountMenu() {
    const accountMenu = document.getElementById('accountMenu');
    accountMenu.classList.toggle('hide');

    if (!accountMenu.classList.contains('hide')) {
        document.addEventListener('click', closeAccountMenu);
    } else {
        document.removeEventListener('click', closeAccountMenu);
    }
}

function closeAccountMenu(event) {
    const accountMenu = document.getElementById('accountMenu');
    const avatarBtn = document.getElementById('avatarBtn');
    
    if (!accountMenu.contains(event.target) && !avatarBtn.contains(event.target)) {
        accountMenu.classList.add('hide');
        document.removeEventListener('click', closeAccountMenu);
    }
}

function SelectThisRow(element, tbodyName){
    const tbody = document.getElementById(`${tbodyName}`);
    const rows = tbody.getElementsByTagName('tr');
    const state = element.classList.contains('rowSelected');

    for (let i = 0; i < rows.length; i++) {
        if(rows[i].classList.contains('rowSelected')){
            rows[i].classList.remove('rowSelected');
        }
    }
    state===false ? element.classList.add('rowSelected') : element.classList.remove('rowSelected');
}