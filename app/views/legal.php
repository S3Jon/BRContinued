<?php include_once 'partials/header.php'; ?>

<body class="bg-background">
    <div class="max-w-screen-xl mx-auto px-5">
        <div class="flex flex-col md:flex-row justify-between items-center mt-[80px]">
            <div>
                <h2 id="titulo-seccion" class="font-extrabold font-serif4 text-3xl">Política de cookies</h2>
            </div>
            <div class="flex flex-wrap">
                <button id="aviso-legal-btn" class="w-auto px-6 ml-7 py-1 mt-2 bg-background border-solid border-2 border-primary font-semibold rounded-3xl">Aviso legal</button>
                <button id="politica-privacidad-btn" class="w-auto px-6 ml-7 py-1 mt-2 bg-background border-solid border-2 border-primary font-semibold rounded-3xl">Política de privacidad</button>
                <button id="politica-cookies-btn" class="w-auto px-6 ml-7 py-1 mt-2 bg-background border-solid border-2 border-primary font-semibold rounded-3xl">Política de cookies</button>
            </div>
        </div>
        <div class="bg-primary w-auto h-[0.5px] mt-8 mb-8"></div>
        <div>
            <div id="aviso-legal-section" class="avisolegal hidden">
                <p class="text-base">
                    Bienvenido a BookReaders. Esta web es propiedad de BookReaders S.L., con CIF 1234567W y domicilio social en: <br>
                    <br>
                    C. de Pelai, 7 <br>
                    08001 Barcelona <br>
                    <br>
                    Al acceder y utilizar este sitio web, usted acepta los siguientes términos y condiciones: <br><br>
                    1. <b>Propiedad Intelectual:</b> Todos los contenidos de este sitio web, incluyendo textos, imágenes, logotipos, diseños y software, están protegidos por las leyes de propiedad intelectual e industrial. Estos contenidos son propiedad de BookReaders o de terceros autorizados. Queda prohibida su reproducción, distribución o modificación sin autorización previa por escrito.
                    <br><br>
                    2. <b>Responsabilidad:</b> BookReaders no se hace responsable por el uso que los usuarios hagan del contenido de este sitio web. Tampoco garantiza la disponibilidad, exactitud, integridad o actualización de la información proporcionada. 
                    <br><br>
                    3. <b>Registro de Usuarios:</b> Al registrarse en BookReaders, los usuarios aceptan proporcionar información veraz y actualizada. La empresa se compromete a proteger la privacidad de los datos personales de los usuarios de acuerdo con nuestra Política de Privacidad. 
                    <br><br>
                    4. <b>Ley Aplicable y Jurisdicción:</b> Estos términos y condiciones se rigen por la legislación española. Cualquier controversia que surja en relación con este sitio web se someterá a la jurisdicción de los tribunales de Barcelona.
                    <br><br>
                </p>
            </div>
            <div id="politica-privacidad-section" class="privacidad hidden">
                <p class="text-base">
                    En BookReaders, la privacidad y la seguridad de los datos de nuestros usuarios son una prioridad. A continuación, explicamos cómo recopilamos, utilizamos y protegemos la información personal de nuestros usuarios: 
                    <br><br>
                    1. <b>Recopilación de Información:</b> Recopilamos información personal de nuestros usuarios cuando se registran en nuestro sitio web, participan en encuestas o interactúan con nuestro contenido. 
                    <br><br>
                    2. <b>Uso de la Información:</b> Utilizamos la información personal de los usuarios para gestionar sus cuentas, proporcionar los servicios solicitados, enviar comunicaciones comerciales y mejorar la experiencia del usuario en nuestro sitio web. 
                    <br><br>
                    3. <b>Protección de Datos:</b> Implementamos medidas de seguridad técnicas y organizativas para proteger la información personal de nuestros usuarios contra el acceso no autorizado, la pérdida o la alteración. 
                    <br><br>
                    4. <b>Compartir Información:</b> No compartimos información personal de nuestros usuarios con terceros sin su consentimiento, excepto cuando sea necesario para cumplir con obligaciones legales o para proteger nuestros intereses legítimos. 
                    <br><br>
                    5. <b>Derechos de los Usuarios:</b> Los usuarios tienen derecho a acceder, rectificar, cancelar u oponerse al tratamiento de sus datos personales. Para ejercer estos derechos, pueden ponerse en contacto con nosotros a través de los medios proporcionados en nuestro sitio web. 
					<br><br>
				</p>
            </div>
            <div id="politica-cookies-section" class="cookies hidden">
                <p class="text-base">
                    BookReaders utiliza cookies y tecnologías similares para mejorar la experiencia de navegación de nuestros usuarios y proporcionar servicios personalizados. A continuación, explicamos cómo utilizamos las cookies: 
                    <br><br>
                    1. <b>¿Qué son las Cookies?:</b> Las cookies son pequeños archivos de texto que se almacenan en el dispositivo del usuario cuando visita nuestro sitio web. Estas cookies nos permiten reconocer al usuario, recordar sus preferencias y ofrecerle contenido relevante. 
                    <br><br>
                    2. <b>Tipos de Cookies:</b> Utilizamos cookies de sesión, que se eliminan cuando el usuario cierra el navegador, y cookies persistentes, que se almacenan en el dispositivo del usuario durante un período de tiempo determinado. También utilizamos cookies de terceros para analizar el uso del sitio web y mostrar publicidad personalizada. 
                    <br><br>
                    3. <b>Control de Cookies:</b> Los usuarios pueden aceptar o rechazar el uso de cookies a través de la configuración de su navegador. Sin embargo, la desactivación de ciertas cookies puede afectar la funcionalidad del sitio web. 
                    <br><br>
                    Al utilizar nuestro sitio web, los usuarios aceptan el uso de cookies de acuerdo con esta Política de Cookies. 
                    <br><br>
                    Recuerda que estos textos son solo ejemplos y es importante adaptarlos a las necesidades específicas de tu sitio web y a la normativa vigente. Te recomendaría consultar con un profesional del derecho para asegurarte de cumplir con todas las obligaciones legales aplicables. 
                    <br><br>
                </p>
            </div>
			<br><br><br>
        </div>
    </div>
</body>

<!-- Script para cambiar de sección y actualizar la URL -->
<!-- Bug menor: al recargar la página, la sección activa no se mantiene y va a la default (cookies) -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const buttons = {
            'aviso-legal-btn': 'aviso-legal-section',
            'politica-privacidad-btn': 'politica-privacidad-section',
            'politica-cookies-btn': 'politica-cookies-section'
        };

        const sections = Object.values(buttons);
        const buttonElements = Object.keys(buttons).map(id => document.getElementById(id));
        const tituloSeccion = document.getElementById('titulo-seccion');

        function showSection(sectionId) {
            sections.forEach(sec => document.getElementById(sec).classList.add('hidden'));
            document.getElementById(sectionId).classList.remove('hidden');

            switch (sectionId) {
                case 'aviso-legal-section':
                    tituloSeccion.textContent = 'Aviso legal';
                    break;
                case 'politica-privacidad-section':
                    tituloSeccion.textContent = 'Política de privacidad';
                    break;
                case 'politica-cookies-section':
                    tituloSeccion.textContent = 'Política de cookies';
                    break;
                default:
                    tituloSeccion.textContent = 'Política de cookies';
                    break;
            }
        }

        function activateButton(buttonId) {
            buttonElements.forEach(btn => {
                if (btn.id === buttonId) {
                    btn.classList.remove('bg-background', 'border-primary');
                    btn.classList.add('bg-accent');
                } else {
                    btn.classList.remove('bg-accent');
                    btn.classList.add('bg-background', 'border-primary');
                }
            });
        }

        const urlParams = new URLSearchParams(window.location.search);
        const section = urlParams.get('section');

        if (section) {
            const sectionId = {
                'legal': 'aviso-legal-section',
                'privacy': 'politica-privacidad-section',
                'cookies': 'politica-cookies-section'
            }[section];

            if (sectionId) {
                showSection(sectionId);
                activateButton(Object.keys(buttons).find(key => buttons[key] === sectionId));
            } else {
                showSection('politica-cookies-section');
                activateButton('politica-cookies-btn');
            }
        } else {
            showSection('politica-cookies-section');
            activateButton('politica-cookies-btn');
        }

        buttonElements.forEach(button => {
            button.addEventListener('click', () => {
                const sectionId = buttons[button.id];
                showSection(sectionId);
                activateButton(button.id);
                const newUrl = `${window.location.origin}${window.location.pathname}?section=${button.id.replace('-btn', '')}`;
                window.history.pushState({ path: newUrl }, '', newUrl);
            });
        });
    });
</script>


<?php include_once 'partials/footer.php'; ?>
