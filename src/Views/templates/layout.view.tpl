<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{SITE_TITLE}}</title>

  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="/{{BASE_DIR}}/public/css/appstyle.css">
  <link rel="stylesheet" href="/{{BASE_DIR}}/public/css/home.css">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

  {{foreach SiteLinks}}
  <link rel="stylesheet" href="/{{~BASE_DIR}}/{{this}}">
  {{endfor SiteLinks}}

  {{foreach BeginScripts}}
  <script src="/{{~BASE_DIR}}/{{this}}"></script>
  {{endfor BeginScripts}}

</head>

<body>

<header>

    <input type="checkbox" class="menu_toggle" id="menu_toggle">

    <label for="menu_toggle" class="menu_toggle_icon">
        <div class="hmb dgn pt-1"></div>
        <div class="hmb hrz"></div>
        <div class="hmb dgn pt-2"></div>
    </label>

    <h1 class="site-logo">
        <a href="index.php?page=Index">
            <img src="/{{BASE_DIR}}/uploads/Inicio/logorepo.png" alt="Repostería Ana">
        </a>
    </h1>

    <nav id="menu">

        <ul>

            <li>
                <a href="index.php?page=Index">
                    <i class="fas fa-home"></i>
                    Inicio
                </a>
            </li>

            <li>
                <a href="index.php?page=Mnt-CatalogoList">
                    <i class="fas fa-store"></i>
                    Catálogo
                </a>
            </li>

            <li>
                <a href="index.php?page=sec_login">
                    <i class="fas fa-sign-in-alt"></i>
                    Iniciar Sesión
                </a>
            </li>

            <li>
                <a href="index.php?page=sec_register">
                    <i class="fas fa-user-plus"></i>
                    Crear Cuenta
                </a>
            </li>

            <li>
                <a href="index.php?page=Mnt-CarritoList">
                    <i class="fas fa-cart-shopping"></i>
                    Carrito
                </a>
            </li>

        </ul>

    </nav>

</header>

<main>
    {{{page_content}}}
</main>

<footer>
    <div>Todos los Derechos Reservados {{CURRENT_YEAR}} &copy;</div>
</footer>

{{foreach EndScripts}}
<script src="/{{~BASE_DIR}}/{{this}}"></script>
{{endfor EndScripts}}

</body>
</html>