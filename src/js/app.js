let paso = 1;
const pasoInicial = 1;
const pasoFinal = 3;

const cita = {
    id: '',
    nombre: '',
    fecha: '',
    hora: '',
    servicios: []
};

document.addEventListener('DOMContentLoaded', function(){
    iniciarApp();
})

function iniciarApp(){
    mostrarSeccion(); //Muestra y oculta las secciones
    tabs(); // Cambia la sección cuando se presionen los tabs
    botonesPaginador(); // Agrega o quita los botones del paginador
    paginaSiguiente();
    paginaAnterior();

    consultarAPI(); // Consulta la api en el bakcedn de PHP

    idCliente();
    nombreCliente(); //añade el nombre del cliente al objeto de cita
    seleccionarfecha(); //añade la fecha de la cita al objeto
    seleccionarHora(); // añade la hora de la cita en el objeto

    mostrarResumen(); // mostrar el resumen de la cita
}

function mostrarSeccion(){

    // Ocultar la sección que tenga la clase de mostrar
    const seccionAnterior = document.querySelector('.mostrar');
    if(seccionAnterior){
        seccionAnterior.classList.remove('mostrar');
    }

    // Seleccionar la seccion con el paso
    const seccion = document.querySelector(`#paso-${paso}`);
    seccion.classList.add('mostrar');

    // Quita la clase de actual al tab anterior
    const tabAnterior = document.querySelector('.actual');
    if(tabAnterior){
        tabAnterior.classList.remove('actual');
    }

    // Resalta el tab actual
    const tab = document.querySelector(`[data-paso="${paso}"]`);
    tab.classList.add('actual');
}

function tabs(){
    const botones = document.querySelectorAll('.tabs button');

    botones.forEach( boton =>{
        boton.addEventListener('click', function(e){
            paso = parseInt(e.target.dataset.paso);

            mostrarSeccion();
            botonesPaginador();
        })
    })
}

function botonesPaginador(){
    const paginaAnterior = document.querySelector('#anterior');
    const paginaSiguiente = document.querySelector('#siguiente');

    if(paso===1){
        paginaAnterior.classList.add('ocultar');
        paginaSiguiente.classList.remove('ocultar');
    } else if(paso===3){
        paginaAnterior.classList.remove('ocultar');
        paginaSiguiente.classList.add('ocultar');

        mostrarResumen();
    } else{
        paginaAnterior.classList.remove('ocultar');
        paginaSiguiente.classList.remove('ocultar');
    }
    mostrarSeccion();
}

function paginaAnterior(){
    const paginaAnterior = document.querySelector('#anterior');
    paginaAnterior.addEventListener('click', function(){
        if(paso <= pasoInicial) return;
        paso--;
        botonesPaginador();
    })
}

function paginaSiguiente(){
    const paginaSiguiente = document.querySelector('#siguiente');
    paginaSiguiente.addEventListener('click', function(){
        if(paso >= pasoFinal) return;
        paso++;
        botonesPaginador();
    })
}

async function consultarAPI(){
    try {
        const url = 'http://127.0.0.1:3000/api/servicios';
        const resultado = await fetch(url);
        const servicios = await resultado.json();
        mostrarServicios(servicios);

    } catch (error) {
        console.log(error);
    }
}

function mostrarServicios(servicios){
    servicios.forEach(servicio => {
        const { id, nombre, precio } = servicio;

        const nombreServicio = document.createElement('P');
        nombreServicio.classList.add('nombre-servicio');
        nombreServicio.textContent = nombre;

        const precioServicio = document.createElement('P');
        precioServicio.classList.add('precio-servicio');
        precioServicio.textContent = `$${precio}`;

        const servicioDiv = document.createElement('DIV');
        servicioDiv.classList.add('servicio');
        servicioDiv.dataset.idServicio = id;
        servicioDiv.onclick = function(){
            seleccionarServicio(servicio);
        };


        servicioDiv.appendChild(nombreServicio);
        servicioDiv.appendChild(precioServicio);

        document.querySelector('#servicios').appendChild(servicioDiv);
    })
}

function seleccionarServicio(servicio){
    const { id } = servicio;
    const { servicios } = cita;

    // Identificar al elemento al que se le da click
    const divServicio = document.querySelector(`[data-id-servicio="${id}"]`);

    // Comprobar si servicio ya fue agregado o quitarlo
    if(servicios.some(agregado => agregado.id === id)){
        //eliminarlo
        cita.servicios = servicios.filter(agregado => agregado.id !== id);
        divServicio.classList.remove('seleccionado');
    } else{
        //agregarlo
        cita.servicios = [...servicios, servicio];
        divServicio.classList.add('seleccionado');
    }

    console.log(cita);
}

function idCliente(){
    cita.id = document.querySelector('#id').value
}

function nombreCliente(){
    const nombre = document.querySelector('#nombre').value;
    cita.nombre = nombre;
    
}

function seleccionarfecha(){
    const inputFecha = document.querySelector('#fecha');
    inputFecha.addEventListener('input', function(e){
        const dia = new Date(e.target.value).getUTCDay();
        
        if([6,0].includes(dia)){
            e.target.value = ''
            mostrarAlerta('Fines de Semana No Abrimos', 'error', '.formulario');
        } else{
            cita.fecha = e.target.value;
        }
    })
}

function seleccionarHora(){
    const inputHora = document.querySelector('#hora');
    inputHora.addEventListener('input', function(e){
        const horaCita = e.target.value;
        const hora = horaCita.split(":")[0];
        if(hora < 10 || hora > 18){
            e.target.value = '';
            mostrarAlerta('Hora No Válida', 'error', '.formulario');
        }else{
            cita.hora = e.target.value;
        }

    })
}

function mostrarAlerta(mensaje, tipo, elemento, desaparece = true){

    // previene que se generen mas de una alerta
    const alertaPrevia = document.querySelector('.alerta');
    if(alertaPrevia) {
        alertaPrevia.remove();
    };

    // scripting para crear la alerta
    const alerta = document.createElement('DIV');
    alerta.textContent = mensaje;
    alerta.classList.add('alerta');
    alerta.classList.add(tipo);

    const referencia = document.querySelector(elemento);
    referencia.appendChild(alerta);

    if(desaparece){
        // eliminar la alerta
        setTimeout(() =>{
            alerta.remove();
        }, 3000)
    }
    
}

function mostrarResumen(){
    const resumen = document.querySelector('.contenido-resumen');

    // limpiar contenido resumen
    while(resumen.firstChild){
        resumen.removeChild(resumen.firstChild);
    }

    if(Object.values(cita).includes('') || cita.servicios.length === 0){
        mostrarAlerta('Falta seleccionar un Horario o algún Servicio', 'error', '.contenido-resumen', false);
        return;
    }

    //formatear el div de resumen
    const { nombre, fecha, hora, servicios } = cita;
    
    //header
    const headingServicios = document.createElement('h3');
    headingServicios.textContent = 'Resumen de Servicios';
    resumen.appendChild(headingServicios);

    // Iterando y mostrando los servicios
    let total = 0;
    servicios.forEach( servicio =>{
        const { id, nombre, precio } = servicio;
        total += parseFloat(precio);
        const contenedorServicio = document.createElement('div');
        contenedorServicio.classList.add('contenedor-servicio');

        const textoServicio = document.createElement('p');
        textoServicio.textContent = nombre;

        const precioServicio = document.createElement('p');
        precioServicio.innerHTML = `<span>Precio:</span> $${precio}`;

        contenedorServicio.appendChild(textoServicio);
        contenedorServicio.appendChild(precioServicio);

        resumen.appendChild(contenedorServicio);
    });

    const headingCita = document.createElement('h3');
    headingCita.textContent = 'Resumen de Cita';
    resumen.appendChild(headingCita);

    const nombreCliente = document.createElement('P');
    nombreCliente.innerHTML = `<span>Nombre:</span> ${nombre}`;

    //Formatear la fecha en español
    const fechaObj = new Date(fecha);
    const mes = fechaObj.getMonth();
    const dia = fechaObj.getDate() + 2;
    const year = fechaObj.getFullYear();

    const fechaUTC = new Date(Date.UTC(year, mes, dia));
    
    const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'}
    const fechaFormateada = fechaUTC.toLocaleDateString('es-MX', opciones);

    const fechaCita = document.createElement('P');
    fechaCita.innerHTML = `<span>Fecha:</span> ${fechaFormateada}`;

    const horaCita = document.createElement('P');
    horaCita.innerHTML = `<span>Hora:</span> ${hora} Horas`;

    const totalCita = document.createElement('P');
    totalCita.innerHTML = `<span>Total:</span> $${total} MXN`;

    //Boton para crear una cita
    const botonReservar = document.createElement('BUTTON');
    botonReservar.classList.add('boton-blanco');
    botonReservar.textContent = 'Reservar Cita';
    botonReservar.onclick = reservarCita;

    resumen.appendChild(nombreCliente);
    resumen.appendChild(fechaCita);
    resumen.appendChild(horaCita);
    resumen.appendChild(totalCita);
    resumen.appendChild(botonReservar);
}

async function reservarCita(){

    const { id, fecha, hora, servicios, nombre } = cita;

    const idServicios = servicios.map( servicio => servicio.id);

    const datos = new FormData();
    datos.append('fecha', fecha);
    datos.append('hora', hora);
    datos.append('usuarioId', id);
    datos.append('servicios', idServicios);

    try {
        //Peticion hacia la API
        const url = 'http://127.0.0.1:3000/api/citas';
        const respuesta = await fetch(url, {
            method: 'POST',
            body: datos
        });

        const resultado = await respuesta.json();
        console.log(resultado.resultado);

        if(resultado.resultado){
            Swal.fire({
                icon: 'success',
                title: 'Cita Creada',
                text: '¡Tu cita fue creada correctamente!'
            }).then(() =>{
                window.location.reload();
            })
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Hubo un error al guardar la cita'
        })
    }

    

    //console.log([...datos]);
}