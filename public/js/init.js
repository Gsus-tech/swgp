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