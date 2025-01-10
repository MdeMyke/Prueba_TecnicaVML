<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer con Burbujas</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap" rel="stylesheet">
    <style>
        /* Eliminar márgenes y relleno de la página */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            display: grid;
            grid-template-rows: 1fr 10rem auto;
            grid-template-areas: "main" "." "footer";
            overflow-x: hidden;
            background: #F5F7FA;
            min-height: 100vh;
            font-family: 'Open Sans', sans-serif;
        }

        .footer {
            z-index: 1;
            --footer-background: #ED5565;
            display: grid;
            position: relative;
            grid-area: footer;
            min-height: 12rem;
        }

        .bubbles {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1rem;
            background: var(--footer-background);
            filter: url("#blob");
        }

        .bubble {
            position: absolute;
            left: var(--position, 50%);
            background: var(--footer-background);
            border-radius: 100%;
            animation: bubble-size var(--time, 4s) ease-in infinite var(--delay, 0s),
                bubble-move var(--time, 4s) ease-in infinite var(--delay, 0s);
            transform: translate(-50%, 100%);
        }

        .content {
            z-index: 2;
            display: grid;
            grid-template-columns: 1fr auto;
            grid-gap: 4rem;
            padding: 2rem;
            background: var(--footer-background);
        }

        a, p {
            color: #F5F7FA;
            text-decoration: none;
        }

        b {
            color: white;
        }

        p {
            margin: 0;
            font-size: .75rem;
        }

        > div {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        > div > div {
            margin: 0.25rem 0;
        }

        .image {
            align-self: center;
            width: 4rem;
            height: 4rem;
            margin: 0.25rem 0;
            background-size: cover;
            background-position: center;
        }

        /* Animaciones */
        @keyframes bubble-size {
            0%, 75% {
                width: var(--size, 4rem);
                height: var(--size, 4rem);
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
                bottom: var(--distance, 10rem);
            }
        }
    </style>
</head>
<body>
    <main>
        <!-- Aquí va el contenido principal de la página -->
    </main>

    <footer class="footer">
        <div class="bubbles">
            <!-- Generación de burbujas -->
            <script>
                for (var i = 0; i < 128; i++) {
                    var size = 2 + Math.random() * 4;
                    var distance = 6 + Math.random() * 4;
                    var position = -5 + Math.random() * 110;
                    var time = 2 + Math.random() * 2;
                    var delay = -1 * (2 + Math.random() * 2);

                    var bubble = document.createElement("div");
                    bubble.classList.add("bubble");
                    bubble.style.setProperty("--size", `${size}rem`);
                    bubble.style.setProperty("--distance", `${distance}rem`);
                    bubble.style.setProperty("--position", `${position}%`);
                    bubble.style.setProperty("--time", `${time}s`);
                    bubble.style.setProperty("--delay", `${delay}s`);
                    document.querySelector(".bubbles").appendChild(bubble);
                }
            </script>
        </div>

        <div class="content">
            <div>
                <div>
                    <b>Desarrollador</b>
                    <a href="#">Michael Esteban Piña Guerrero</a>
            <div>
                <a class="image" style="background-image: url('https://s3-us-west-2.amazonaws.com/s.cdpn.io/199011/happy.svg')"></a>
                <p>© 2025 Preuba Tecnica</p>
            </div>
        </div>
    </footer>

    <svg style="position: fixed; top: 100vh;">
        <defs>
            <filter id="blob">
                <feGaussianBlur in="SourceGraphic" stdDeviation="10" result="blur" />
                <feColorMatrix in="blur" mode="matrix" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 19 -9" result="blob" />
            </filter>
        </defs>
    </svg>
</body>
</html>
