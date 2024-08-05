<!-- <label for="dia">día:</label> -->
<select name="dia_<?php echo $idUnico?>" class="dia comboBox" id="dia_<?php echo $idUnico?>" title='dia' 
onchange="this.setCustomValidity('')"></select>


<!-- <label for="mes">mes:</label> -->
<select name="mes_<?php echo $idUnico?>" class="mes comboBox" id='mes_<?php echo $idUnico?>' title='mes'
onchange="this.setCustomValidity('')">
    <option value="1">Enero</option>
    <option value="2">Febrero</option>
    <option value="3">Marzo</option>
    <option value="4">Abril</option>
    <option value="5">Mayo</option>
    <option value="6">Junio</option>
    <option value="7">Julio</option>
    <option value="8">Agosto</option>
    <option value="9">Septiembre</option>
    <option value="10">Octubre</option>
    <option value="11">Noviembre</option>
    <option value="12">Diciembre</option>
</select>

<!-- <label for="anio">año:</label> -->
<select name="anio_<?php echo $idUnico?>" class="anio comboBox" id='anio_<?php echo $idUnico?>' title='año'
onchange="this.setCustomValidity('')">
    <?php
    $year = date("Y"); 
    for($i=0;$i<25;$i++){
        echo "<option value='$year'>$year</option>";
        $year++;
    }
    ?>
</select>

<script>
    // Función para obtener el número de días en un mes y año específicos
    function obtenerDiasEnMes(mes, anio) {
        return new Date(anio, mes, 0).getDate();
    }

    // Función para actualizar las opciones del select de días
    function actualizarDias() {
        var dia = document.getElementById("dia_<?php echo $idUnico?>");
        var cDia = dia.value;
        var mesSeleccionado = document.getElementById("mes_<?php echo $idUnico?>").value;
        var anioSeleccionado = document.getElementById("anio_<?php echo $idUnico?>").value;
        var numDias = obtenerDiasEnMes(mesSeleccionado, anioSeleccionado);

        var selectDias = document.getElementById("dia_<?php echo $idUnico?>");
        selectDias.innerHTML = ''; // Limpiar opciones actuales

        for (var i = 1; i <= numDias; i++) {
            var option = document.createElement("option");
            option.text = i;
            option.value = i;
            if (i == 1) {
                option.selected = true;
            }
            selectDias.appendChild(option);
        }
        if(cDia!=0 && cDia <= dia.options.length){dia.value = cDia;}
        else{dia.selected = '1';}
    }

    function cleanSelects(keyName) {
        document.getElementById(`dia_${keyName}`).setCustomValidity('');
        document.getElementById(`mes_${keyName}`).setCustomValidity('');
        document.getElementById(`anio_${keyName}`).setCustomValidity('');
    }
    // Agregar eventos de cambio a los select de mes y año
    document.getElementById("mes_<?php echo $idUnico?>").addEventListener("change", actualizarDias);
    document.getElementById("anio_<?php echo $idUnico?>").addEventListener("change", actualizarDias);

    actualizarDias();
</script>