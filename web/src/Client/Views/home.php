<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="GPSUP">

    <title>Painel Administrativo</title>

    <link href="http://print.ati-sm.com.br/public/css/home.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link href="https://superaprendiz.com.br/franquia/css/font-awesome/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>

    <!-- (Optional) Latest compiled and minified JavaScript translation files -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/i18n/defaults-*.min.js"></script>

    <script src="HttpRequest.js"></script>




</head>


<body>

    <header class="d-flex align-items-center">
        <div class="container">
            <span class="letters-upper-spaced">Impressão</span>
            <div id="main-title" class="my-2">
                <h4>Leitura do arquivo &</h4>
                <h4><strong class="text-highlight">impressão</strong> de etiquetas.</h4>
            </div>
            <span>Página destinada ao envio de arquivos de layout de etiquetas, que serão interpretados e convertidos
                para impressão</span>
        </div>
    </header>

    <main class="container">
        <section id="leftside">
            <div id="props" class="dft-card mb-3 main-sh">
                <section class="p-3">
                    <div class="description my-3">
                        <div class="d-flex align-items-center mb-1">
                            <h6>Formato da etiqueta</h6>
                            <i class="fas fa-feather-alt ms-2"></i>
                        </div>
                        <span>Cada etiqueta possui um formato de largura e altura diferente, que precisa ser
                            informado</span>
                    </div>

                    <div>
                        <select class="selectpicker">
                            <option>Mustard</option>
                            <option>Ketchup</option>
                            <option>Relish</option>
                        </select>

                        <!-- <button class="figma-element-2">Salvar modelo</button> -->
                    </div>

                    <div class="d-flex my-3" id="props-size">
                        <div class="d-flex flex-column">
                            <label class="mb-2">Largura</label>
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="" aria-label="" name="largura" aria-describedby="btnGroupAddon" value="4">
                                <div class="input-group-text" id="btnGroupAddon">cm</div>
                            </div>
                        </div>
                        <div class="d-flex flex-column">
                            <label class="mb-2">Altura</label>
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="" aria-label="" name="altura" aria-describedby="btnGroupAddon" value="2">
                                <div class="input-group-text" id="btnGroupAddon">cm</div>
                            </div>
                        </div>
                    </div>

                    <div class="intructions">
                        <i class="far fa-question-circle"></i>
                        <span>Aplique as mudanças para visualizar a nova etiqueta</span>
                    </div>

                </section>

                <footer class="px-3 py-2 d-flex justify-content-end">
                    <button id="apply-edit" class="figma-element-3 d-none">Aplicar alterações</button>
                </footer>
            </div>

            <div id="uploader" class="dft-card p-3 main-sh">
                <div class="dialog px-3 py-2">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle me-2"></i>
                        <h6>Instruções de envio</h6>
                    </div>
                    <div class="pt-1 ms-4">
                        <span>
                            Para que a etiqueta seja gerada para impressão, é necessário enviar o arquivo de texto para
                            ser interpretado pelo sistema
                        </span>
                    </div>
                </div>
                <div id="upload-area" class="dft-card p-3">
                    <content class="d-flex flex-column align-items-center text-center">
                        <h6 class="mb-1">Envie seu arquivo</h6>
                        <span class="mb-3">envie pelo botão abaixo</span>
                        <button id="fileButton" class="figma-element">Procurar arquivo</button>
                        <input type="file" id="fileInput" style="display: none;">
                    </content>
                </div>
                <div id="load-file" class="d-none">
                    <div class="file-loader-content d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-file me-3"></i>
                            <span id="file-name-loaded">arquivos.png</span>
                        </div>
                        <div class="px-3 w-100">
                            <progress class="w-100" id="progress-send" max="100" value="0"></progress>
                        </div>
                        <div class="d-flex align-items-center status">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>


        </section>

        <section id="rightside">
            <div id="tag-preview" class="position-relative main-sh">
                <div id="bkg-tag-preview">
                    <div class="p-3 pt-4">
                        <span>
                            Prévia
                        </span>
                    </div>
                </div>
                <div class="dft-card d-flex flex-column justify-content-between" id="tag-preview-content">
                    <div class="p-3">
                        <div class="dialog px-3 py-2">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-question-circle me-2"></i>
                                <h6>Aguardando envio</h6>
                            </div>
                            <div class="pt-1">
                                <span>Não há arquivo para ser analisado</span>
                            </div>
                        </div>

                        <div id="preview-area" class="mt-3 d-flex flex-column align-items-center justify-content-center">
                            <section class="d-flex justify-content-center">
                                <div class="outer-design-preview dft-card">
                                    <div class="design-preview bg-gradient-blue d-flex align-items-center justify-content-center h-100">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                </div>
                                <div class="arrows-icon d-flex align-items-center">
                                    <i class="fas fa-arrow-left"></i>
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                                <div class="outer-design-preview dft-card">
                                    <div class="design-preview bg-gradient-green d-flex align-items-center justify-content-center h-100">
                                        <i class="fas fa-barcode-read"></i>
                                    </div>
                                </div>
                            </section>

                            <section class="text-center d-flex flex-column description mt-4">
                                <h6>A prévia da etiqueta<br>será exibida nesta área</h6>
                                <span class="mt-2">Faça o envio do arquivo de texto ao<br> lado para visualização</span>
                            </section>
                        </div>
                    </div>

                    <footer class="px-3 py-2">
                        <a id="print-trigger" target="_blank" class="figma-element-3">Imprimir</a>
                    </footer>

                </div>
            </div>
        </section>
    </main>
</body>

<script>
    file = 0;

    document.getElementById('fileButton').addEventListener('click', function() {
        document.getElementById('fileInput').click();
    });

    document.getElementById('apply-edit').addEventListener('click', async function() {
        await getZpl();
    });

    document.getElementById('fileInput').addEventListener('change', function() {
        // Opcional: fazer algo quando o arquivo for selecionado
        console.log(this.files[0].name);
    });

    document.getElementById('fileInput').addEventListener('change', async function() {
        if (this.files && this.files[0]) {
            file = this.files[0];
            document.querySelector('#file-name-loaded').innerText = file.name;
            $("#load-file i").removeClass('fa-circle');
            $("#load-file .status i").addClass('fa-check-circle');
            $('#load-file').removeClass('d-none');

            $('#uploader').addClass('opened');
            $('#progress-send').attr('value', 0);
            await getZpl('#progress-send');
            $('#progress-send').attr('value', 100);
            $("#apply-edit").removeClass('d-none');



        }
    });

    async function getZpl() {
        const formData = new FormData();
        formData.append('layout', file);
        formData.append('height', document.querySelector('[name=altura]').value);
        formData.append('width', document.querySelector('[name=largura]').value);

        //        await PostHTTPRequest(formData, 'url');

        let response = await $.ajax({
            type: 'POST',
            url: '/api/render', // Substitua pela URL correta
            data: formData,
            processData: false, // Impedir que jQuery processe os dados
            contentType: false, // Impedir que jQuery defina o cabeçalho Content-Type
            dataType: "json",
            xhr: function() {
                var xhr = new window.XMLHttpRequest();

                const progressBar = document.querySelector('progress');
                progressBar.value = 0;
                console.log(progressBar);

                let width = 0;
                const interval = setInterval(() => {
                    if (progressBar.value >= 90) {
                        clearInterval(interval);
                    } else {
                        let random_progress = Math.floor(Math.random() * (10 - 1 + 1)) + 1;
                        width += random_progress;
                        progressBar.value = width;
                    }
                }, Math.floor(Math.random() * (4000 - 400 + 1)) + 400); // Ajuste este valor para alterar a velocidade da animação

                return xhr;
            }
        }).done(() => {
            const progressBar = document.querySelector('progress');
            progressBar.value = 100;
        });



        console.log(response);
        document.querySelector('#preview-area').innerHTML = `<img src="${response.images[0]}">`
        document.querySelector('#preview-area').classList.add('img-previewer')
        document.querySelector('#preview-area').classList.remove('justify-content-center')
        document.querySelector('#print-trigger').href = response.url;

    }
</script>