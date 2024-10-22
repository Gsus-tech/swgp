document.addEventListener('DOMContentLoaded', function() {
    
    document.getElementById('generalTab').addEventListener('click', function() {
        setActiveTab('generalTab');
        localStorage.setItem('generalSettings', 'true');
        localStorage.removeItem('accountSettings');
    });

    document.getElementById('accountTab').addEventListener('click', function() {
        setActiveTab('accountTab');
        localStorage.setItem('accountSettings', 'true');
        localStorage.removeItem('generalSettings');
    });

    const valor = localStorage.getItem('accountSettings');
    const valor2 = localStorage.getItem('generalSettings');
    if(valor){
        document.getElementById('generalTabContent').classList.remove('activeContent');
        document.getElementById('accountTabContent').classList.add('activeContent');
        document.getElementById('generalTab').classList.remove('active');
        document.getElementById('accountTab').classList.add('active');
    }else if(valor2){
        document.getElementById('generalTabContent').classList.add('activeContent');
        document.getElementById('accountTabContent').classList.remove('activeContent');
        document.getElementById('generalTab').classList.add('active');
        document.getElementById('accountTab').classList.remove('active');
    }else{
        localStorage.setItem('generalSettings', 'true');
        this.location.reload();
    }
});

function setActiveTab(tabId) {
    const tabs = document.querySelectorAll('.tab');
    tabs.forEach(tab => tab.classList.remove('active'));

    const contents = document.querySelectorAll('.tabContent');
    contents.forEach(content => content.classList.remove('activeContent'));

    document.getElementById(tabId).classList.add('active');
    document.getElementById(`${tabId}Content`).classList.add('activeContent');
}

function updatePassword(){
    const curPass = createTextInputBox('Actualizar contraseña', 'Ingresa tu contraseña actual');
    data = new URLSearchParams({
        password : curPass
    });
    let url = "accountSettingsManager.php?verify=true";
    makeAjaxRequest(url, 'POST', data, 
        function(){
            document.getElementById('passwordUpdate').remove();
            const html = `
                <label class="bold" for="newPass">Ingresa tu nueva contraseña:</label><br>
                <input class="input" type="password" name="newPass" id="newPass" placeholder="Nueva contraseña" title="Ingresa tu nueva contraseña">    
                <label class="bold" for="newPassConfirm">Confirmación de nueva contraseña:</label><br>
                <input class="input" type="password" name="newPassConfirm" id="newPassConfirm" placeholder="Confirmación de contraseña" title="Confirmación de contraseña">
                `;
        },
        function(){
            alert('Contraseña incorrecta.');
        }
    );
}