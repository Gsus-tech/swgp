
//Validar las fechas inicio y cierre
function validateDate(dateIni, dateFin) {
    // console.log(`${dateIni} > ${dateFin}`);
    dateIni.setHours(0, 0, 0, 0);
    dateFin.setHours(0, 0, 0, 0);
    return dateIni > dateFin;
}

function testRegex(elementId){
    //Se valida la introduccion de caracteres especiales innecesarios para ciertos campos.
    var regexEspeciales = /[^a-zA-Z0-9 áéíóúÁÉÍÓÚ.,-]/g;
    const element = document.getElementById(`${elementId}`);

    if(regexEspeciales.test(element.value)){
        element.setCustomValidity('No se permiten caracteres especiales en este campo.');
        element.classList.add('invalidField');
        element.reportValidity();
        return false;
    }
    return true;
}

function testControlledTextInput(elementId){
    //En esta funcion se validan las inyecciones de codigo comunes.
    const injectionPatterns = [
        '<script>','</script>','<img','onerror=',
        'onload=','alert(','document.cookie',
        '--',';--',';','/*','*/','@@',
        'char(','nchar(','varchar(','nvarchar(',
        'sysobjects','syscolumns','information_schema.tables'
    ];
    const element = document.getElementById(`${elementId}`);

    function containsInjectionPattern() {
        return injectionPatterns.find(pattern => element.value.includes(pattern.toLowerCase()));
    }

    const pattern = containsInjectionPattern(element.value);

    if (pattern) {
        element.setCustomValidity(`No se permite la expresión '${pattern}' en este campo.`);
        element.classList.add('invalidField');
        element.reportValidity();
        return false;
    }
    return true;
    
}

function testLenght(type, limit, elementId){
    //Se valida la longitud del input
    const element = document.getElementById(`${elementId}`);
    if(type=='max'){
        if (element.value.length > limit) {
            element.setCustomValidity(`Máximo ${limit} caracteres para este campo.`);
            element.classList.add('invalidField');
            element.reportValidity();
            return false;
        }
    }else{
        if (element.value.length < limit) {
            element.setCustomValidity(`Mínimo ${limit} caracteres para este campo.`);
            element.classList.add('invalidField');
            element.reportValidity();
            return false;
        }
    }
    return true;
}

function testValue(type, elementId, elementTagName){
    const element = document.getElementById(`${elementId}`);
    const onlySpaces = /^\s*$/;
    const doubleSpaces = /\s{2,}/;
    const cadenasSinSentido = [
        'poiuy','lkjhg','mnbv','uhas83e73u','xyz123',
        'random','loremipsum','qwerty','asdf','zxcv',
        'nombre1','ghfjd','iiii','dummytext','blahblah',
        'Usuario123','abcd1234','123','eeee','aaaa', 'uuuu',
        'Proyecto123', '123Usuario', '123Proyecto', 'oooo'
    ];

    if(onlySpaces.test(element.value) || cadenasSinSentido.some(nonsensical => element.value.includes(nonsensical))){
        element.setCustomValidity(`Introduce un nombre de ${elementTagName} válido.\nNo se admiten cadenas sin sentido.`);
        element.classList.add('invalidField');
        element.reportValidity();
        return false;
    }
    if(doubleSpaces.test(element.value) && type == 'strict'){
        element.setCustomValidity('No se admiten espacios dobles en este campo.');
        element.classList.add('invalidField');
        element.reportValidity();
        return false;
    }
    return true;
}

function resetField(element){
    element.setCustomValidity('')
    element.classList.remove('invalidField');
}
