<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer con Burbujas</title>
    <style>
        

        /* Estilos específicos para el contenedor principal */
        .footer-container {
            display: grid;
            grid-template-rows: 1fr 10rem auto;
            grid-template-areas: "main" "." "footer";
            overflow-x: hidden;
            background: rgb(255, 255, 255);
            font-family: 'Open Sans', sans-serif;
        }

        /* Estilos para el footer */
        .footer-container .footer {
            z-index: 1;
            --footer-background-color: #ED5565; /* Color de fondo del footer */
            display: grid;
            position: relative;
            grid-area: footer;
            min-height: 12rem;
        }

        /* Estilos para las burbujas */
        .footer-container .footer .footer-bubbles {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1rem;
            background: var(--footer-background-color);
            filter: url("#blob-filter");
        }

        /* Estilo de cada burbuja */
        .footer-container .footer .footer-bubbles .footer-bubble {
            position: absolute;
            left: var(--bubble-position, 50%);
            background: var(--footer-background-color);
            border-radius: 100%;
            animation: bubble-size var(--bubble-time, 4s) ease-in infinite var(--bubble-delay, 0s),
                      bubble-move var(--bubble-time, 4s) ease-in infinite var(--bubble-delay, 0s);
            transform: translate(-50%, 100%);
        }

        /* Contenido dentro del footer */
        .footer-container .footer .footer-content {
            z-index: 2;
            display: grid;
            grid-template-columns: 1fr auto;
            grid-gap: 4rem;
            padding: 2rem;
            background: var(--footer-background-color);
        }

        .footer-container .footer .footer-content a,
        .footer-container .footer .footer-content p {
            color: #F5F7FA;
            text-decoration: none;
        }

        .footer-container .footer .footer-content b {
            color: white;
        }

        .footer-container .footer .footer-content p {
            margin: 0;
            font-size: 0.75rem;
        }

        .footer-container .footer .footer-content > div {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .footer-container .footer .footer-content > div > div {
            margin: 0.25rem 0;
        }

        /* Imagen dentro del footer */
        .footer-container .footer .footer-content .footer-image {
            align-self: center;
            width: 4rem;
            height: 4rem;
            margin: 0.25rem 0;
            background-size: cover;
            background-position: center;
        }

        /* Animaciones de burbujas */
        @keyframes bubble-size {
            0%, 75% {
                width: var(--bubble-size, 4rem);
                height: var(--bubble-size, 4rem);
            }
            100% {
                width: 0rem;
                height: 0rem;
            }
        }

        @keyframes bubble-move {
            0% {
                bottom: -4rem;
            }
            100% {
                bottom: var(--bubble-distance, 10rem);
            }
        }
    </style>
</head>
<body1 class="footer-container">

    <footer class="footer">
        <div class="footer-bubbles">
            <!-- Generación de burbujas -->
            <script>
                for (var i = 0; i < 128; i++) {
                    var size = 2 + Math.random() * 4;
                    var distance = 6 + Math.random() * 4;
                    var position = -5 + Math.random() * 110;
                    var time = 2 + Math.random() * 2;
                    var delay = -1 * (2 + Math.random() * 2);

                    var bubble = document.createElement("div");
                    bubble.classList.add("footer-bubble");
                    bubble.style.setProperty("--bubble-size", `${size}rem`);
                    bubble.style.setProperty("--bubble-distance", `${distance}rem`);
                    bubble.style.setProperty("--bubble-position", `${position}%`);
                    bubble.style.setProperty("--bubble-time", `${time}s`);
                    bubble.style.setProperty("--bubble-delay", `${delay}s`);
                    document.querySelector(".footer-bubbles").appendChild(bubble);
                }
            </script>
        </div>

        <div class="footer-content">
            <div>
                <div>
                    <b>Desarrollador</b>
                    <a href="#">Michael Esteban Piña Guerrero</a>
                </div>
                <div>
                    <p>© 2025 Prueba Técnica</p>
                </div>
            </div>
        </div>
    </footer>

    <svg style="position: fixed; top: 100vh;">
        <defs>
            <filter id="blob-filter">
                <feGaussianBlur in="SourceGraphic" stdDeviation="10" result="blur" />
                <feColorMatrix in="blur" mode="matrix" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 19 -9" result="blob" />
            </filter>
        </defs>
    </svg>

</body1>
</html>
