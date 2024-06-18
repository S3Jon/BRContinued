<?php include_once 'partials/header.php'; ?>

<body class="bg-background">
    <div class="max-w-screen-xl mx-auto px-5">
        <div class="flex justify-between items-center mt-[80px]">
            <div>
                <h2 class="font-extrabold font-serif4 text-3xl">Contacto</h2>
            </div>
        </div>
        <div>
            <div class="mx-auto mb-10 mt-6">
                <p class="text-base">
                En BookReaders estamos encantados de que hayas decidido ponerte en contacto con nosotros. Tus sugerencias y preguntas son importantes para nosotros. Por favor, completa el siguiente formulario y nos pondremos en contacto contigo lo antes posible. Gracias por elegirnos para ayudarte. ¡Estamos aquí para ti!
                <br><br>
                Si deseas información sobre la sección "El Libro de la Semana" por favor especifícalo en el asunto. ¡Te daremos toda la información encantados!
                <br><br>
                </p>
            </div>
            <div class="mb-[80px]">
                <form action="mailto:bookreadersweb@gmail.com" method="post" enctype="text/plain"> <!-- Cambiado el email por el de la empresa -->
            
					<input type="text" id="nombre" name="nombre" placeholder="Tu nombre" class="p-3 rounded-md" required><br><br>

					<textarea id="mensaje" name="mensaje" rows="4" cols="50" placeholder="¿Qué nos quieres contar?" class="p-3 rounded-md" required></textarea><br><br>

					<input class="w-auto px-6 py-1 mt-2 text-right bg-accent font-semibold rounded-3xl" type="submit" value="Enviar">
                
				</form>
			</div>
        </div>
    </div>
</body>

<?php include_once 'partials/footer.php'; ?>