<!DOCTYPE HTML>
<html>

<head>
    <title>Sistema Shine Blue</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
</head>

<body>
    <div class="clearfix mb-5" style="background-color: #254f7a">
        <div class="clearfix"><span class="float-left">&nbsp;</span></div>
        <img class="rounded mx-auto d-block" src="{{ asset('img/logo_branco.png') }}" width="300px" height="auto" alt="Shine Blue Cosméticos">
    </div>

    <div class="container" id="login" data-role="page">

        <h4 style="text-align: center;">Sistema Shine Blue</h4>

        @if ($message = Session::get('error'))
        <div class="alert alert-danger alert-block">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>{{ $message }}</strong>
        </div>
        @endif

        <form action="{{ route('telaLogin') }}" method="post">
            @csrf
            <div class="form-group">
                <label for="usuario">Usuário:</label>
                <input name="usuario" type="text" required="" id="usuario" class="form-control" placeholder="Digite o usuário (CPF/CNPJ)" required />
                <div class="invalid-feedback">Por favor, preencha este campo</div>
            </div>
            <div class="form-group">
                <label for="senha">Senha:</label>
                <input name="senha" type="password" required="" id="senha" class="form-control" placeholder="Digite a senha" required />
                <div class="invalid-feedback">Por favor, preencha este campo</div>
            </div>
            <button type="submit" class="btn btn-primary">Enter</button>
        </form>

    </div>

    <script src="js/jquery-1.9.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $("#usuario").focus();
        });
    </script>
</body>

</html>