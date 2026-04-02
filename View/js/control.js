let formSelectempleados
async function consulta_empleado_1(event) {
    try {

        const idForm = event.target.getAttribute('vinculado')//trae entre los parametros del input el id del form
        const codigo = event.target.value// trae el contenido del input

        if (!codigo) {
            document.querySelector(idForm).reset()
            return
        }

        alertCargando()
        const respuesta = await fetch(`Controller/consultarEmpleado.php?codigo_interno=${codigo}`)
        cerrarAlerta()

        if (!respuesta.ok) {
            alertBoton('Hubo un error inesperado', '', 'error')
            document.querySelector(idForm).reset()
            return
        }

        const json = await respuesta.json()

        if (!json.status) {
            alertBoton(json.error, json.mensaje, 'error')
            document.querySelector(idForm).reset()
            return
        }

        if (!json.data.length) {
            alertBoton('Usuario no encontrado o inactivo', '', 'warning')
            document.querySelector(idForm).reset()
            return
        }

        llenarInputs(json.data, idForm)

    } catch (error) {
        console.error("Error en la búsqueda automática:", error)
    }
}

async function consulta_empleado(event) {
    try {
        event.preventDefault()
        const form = event.target
        const data = new FormData(form)
        const validacion = Array.from(data.values()).some(valor => valor.trim() !== "")
        const dataParams = new URLSearchParams(data)
        const tablaBody = document.getElementById('datos-consulta')

        if (!validacion) {
            tablaBody.innerHTML = ''
            return
        }

        alertCargando()
        const respuesta = await fetch(`Controller/consultarEmpleados.php?${dataParams}`)
        cerrarAlerta()

        if (!respuesta.ok) {
            alertBoton('Hubo un error inesperado', '', 'error')
            tablaBody.innerHTML = ''
            return
        }

        const json = await respuesta.json()

        if (!json.status) {
            alertBoton(json.error, json.mensaje, 'error')
            tablaBody.innerHTML = ''
            return
        }

        if (!json.data.length) {
            alertBoton('Sin datos', 'No se encontraron datos con la información ingresada', 'info')
            tablaBody.innerHTML = ''
            return
        }

        const contenido = json.data.map(fila => `
            <tr>
              <td>${fila.codigo_interno}</td>
              <td>${fila.nombres}</td>
              <td>${fila.apellidos}</td>
              <td>${fila.apellidos}</td>
            </tr>
        `).join(' ')//siempre se utiliza con el map si se devuelve un texto OJITOOO

        tablaBody.innerHTML = contenido

        tablaBody.addEventListener('click', traerDatos);

    } catch (error) {
        console.error("Error en la búsqueda automática:", error)
    }
}

async function crear_usuario(event) {
    try {
        const form = document.getElementById('registrar-usuario')
        event.preventDefault()
        const data = new FormData(form)
        data.append('creador', codigo_interno)
        let usuario = String(document.querySelector('[name="nombres"]').value.slice(0, 1) + document.querySelector('[name="Apellidos"]').value.slice(0, 3)).toLocaleLowerCase()
        if (usuario.length < 4) {
            usuario += "0"
        }
        data.append('usuario', usuario)
        alertCargando()
        const respuesta = await fetch(`Controller/registrarUsuario.php`, {
            method: "POST",
            body: data,
        })
        cerrarAlerta()

        const json = await respuesta.json()

        if (!json.status || !respuesta.ok) {
            alertBoton('Usuario ' + usuario + ' ya existe', '', 'warning')
            form.reset()
            return
        }

        console.log(json)
        if (json.status == true) {
            alertBoton('El usuario ' + usuario + ' se ha creado con exito', '', 'success')
            form.reset()
        }

    } catch (error) {
        console.error("Error en la búsqueda automática:", error)
    }
}

async function llenarInputs(json, idForm) {
    try {
        document.querySelector(`${idForm} [name="codigo_interno"]`).value = json[0].codigo_interno
        document.querySelector(`${idForm} [name="nombres"]`).value = json[0].nombres
        document.querySelector(`${idForm} [name="Apellidos"]`).value = json[0].apellidos
        document.querySelector(`${idForm} [name="nombre_cargo"]`).value = json[0].nombre_cargo
    } catch (error) {
        console.error("Error en la búsqueda automática:", error)
    }
}

const traerDatos = async (event) => {
    const celda = event.target.closest('td');
    if (!celda) return;

    const fila = celda.parentElement;
    const datosFila = [{
        codigo_interno: fila.cells[0].textContent,
        nombres: fila.cells[1].textContent,
        apellidos: fila.cells[2].textContent,
        nombre_cargo: fila.cells[3].textContent
    }];

    llenarInputs(datosFila, formSelectempleados)
    document.getElementById('modal-consulta').style.display = 'none'
    document.getElementById('datos-consulta').innerHTML = ''
    document.getElementById('consultar-empleado').reset()
}

document.getElementById('registrar-usuario').addEventListener('change', consulta_empleado_1)
document.getElementById('consultar-empleado').addEventListener('submit', consulta_empleado)
document.getElementById('enviar-formulario').addEventListener('click', crear_usuario)
document.querySelectorAll('.lupa').forEach(e => {
    e.addEventListener('click', (event) => {
        formSelectempleados = event.target.getAttribute('referente')
    })
})