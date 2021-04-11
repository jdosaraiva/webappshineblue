<!DOCTYPE html>
<html lang="en">

<head>
    <title>Sistema Shine Blue</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
</head>

<body>
    <div class="clearfix mb-5" style="background-color: #254f7a">
        <div class="clearfix">
            <form action="{{ route('logoff') }}" method="post">
                @csrf
                <button type="submit" class="btn btn-dark float-left mr-1" id="btnSair">Sair</button>

            </form>
            <a href="{{ route('menu') }}" title="Voltar">
                <button type="button" class="btn btn-dark float-left" id="btnBack">Voltar</button>
            </a>
        </div>
        <img class="rounded mx-auto d-block" src="{{ asset('img/logo_branco.png') }}" width="300px" height="auto" alt="ShineBlue Cosméticos">
    </div>

    <div class="container">
        <h4>Envio de Notificação</h4>

        <div class="form-group">
            <label for="texto_notificacao">Texto da notificação:</label>
            <textarea class="form-control" rows="5" id="texto_notificacao"></textarea>

            <label for="retorno">Resposta:</label>
            <textarea class="form-control" rows="2" id="retorno"></textarea>

        </div>

        <div class="clearfix">
            <button id="btnEnviar" type="button" class="btn btn-primary active" onclick="enviaParaWebService();">Enviar</button>
            <div id="spinnerId" class="spinner-border text-primary float-right" role="status" style="display: none;">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>

    <script>
        function enviaParaWebService() {

            var spinner = document.getElementById("spinnerId");
            spinner.style.display = "block";

            var botao = document.getElementById("btnEnviar");
            botao.classList.remove("active");
            botao.classList.add("disabled");
            var url = "https://onesignal.com/api/v1/notifications";

            var textoNotificacao = document.getElementById("texto_notificacao").value;

            var strJsonBody = "{" +
                "\"app_id\": \"e946e207-41d1-48c1-8a11-dff04be1d265\"," +
                "\"included_segments\": [\"All\"]," +
                "\"data\": {\"foo\": \"bar\"}," +
                "\"template_id\": \"9cf3173b-4a6e-4527-8f17-d21da3ae2cd2\"," +
                "\"contents\": {\"en\": \"" + textoNotificacao + "\"}" +
                "}";

            var jsonInput = strJsonBody;
            var retorno = document.getElementById("retorno");
            retorno.value = "";

            var xhr = new XMLHttpRequest();

            xhr.onreadystatechange = () => {
                if (xhr.readyState === 4) {
                    console.log("\n" + xhr.responseText + "\n");
                    retorno.value = xhr.responseText;
                    if (!(xhr.status >= 200 || xhr.status < 300)) {
                        console.error("erro");
                        console.error("status=" + xhr.status);
                        console.error("mensagem=" + xhr.responseText);
                    }
                    console.log("---------------------------- headers ---------------------------------");
                    console.log(xhr.getAllResponseHeaders());
                    console.log("----------------------------------------------------------------------");
                    botao.classList.remove("disabled");
                    botao.classList.add("active");
                    spinner.style.display = "none";
                }
            }

            var json = jsonInput
            xhr.open("POST", url, true);
            xhr.setRequestHeader("Content-Type", "application/json; charset=UTF-8");
            xhr.setRequestHeader("Authorization", "Basic OGY5MGUwMzctMmQxMC00YWM1LTk5NTUtNTE2ZmMzZmI1ZTQx");
            xhr.send(json);

        }
    </script>
    <script src="{{ asset('js/jquery-1.9.1.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
</body>

</html>