<!DOCTYPE HTML>
<html>

<head>
    <title>Sistema Shine Blue</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
</head>

<body>

    <div class="clearfix" style="background-color: #254f7a">
        <div class="clearfix">
            <form action="{{ route('logoff') }}" method="post">
                @csrf
                <button type="submit" class="btn btn-dark float-left" id="btnSair">Sair</button>
            </form>
        </div>
        <img class="rounded mx-auto d-block" src="{{ asset('img/logo_branco.png') }}" width="300px" height="auto" alt="Shine Blue Cosméticos">
    </div>

    <nav class="navbar navbar-expand-sm bg-light mb-1">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('notificacao') }}">Enviar Notificação</a>
            </li>
        </ul>
    </nav>

    <div class="container" id="login">

        <div class="mx-auto">
            <h4 style="text-align:center">Sistema Shine Blue</h4>
        </div>

        <table class="table table-hover">
            <tbody>
                <tr>
                    <td>Usuario logado=></td>
                    <td class="font-weight-bolder">{{ Session::get('usuario')['nome'] }}</td>
                </tr>
                <tr>
                    <td>Nivel=></td>
                    <td class="font-weight-bolder">{{ Session::get('nivel') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <script src="{{ asset('js/jquery-1.9.1.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script type="text/javascript">
    </script>

</body>

</html>