<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Downloader</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <form id="form_download">
            <div class="mb-3 row">
                <label for="url_download" class="col-md-8 col-form-label">URL *</label>
                <div class="col-md-10">
                    <input type="text" class="form-control" id="url_download" placeholder="Digite o URL">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">Enviar</button>
                </div>
            </div>
        </form>

        <hr>

        <div id="div_resultado_sucesso" class="d-flex justify-content-center">
            <img id="video_thumbnail" src="" alt="Video Thumbnail" class="img-fluid mb-2" class="img-responsive" style="max-height: 50px;">
            <div>
                <a id="download_sd" class="btn btn-primary mx-2" href="#" download="video_sd.mp4">SD</a>
                <a id="download_hd" class="btn btn-primary mx-2" href="#" download="video_hd.mp4">HD</a>
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {

            $("#div_resultado_sucesso").hide();

            $("#form_download").submit(function(e) {
                e.preventDefault();
                const url_download = $("#url_download").val();

                if (!url_download) {
                    $("#url_download").focus();
                    return;
                }

                $.ajax({
                    url: "src/video_download.php",
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        videoURL: url_download
                    },
                    success: function(data) {
                        console.log('Success:', data);
                        if (!data.success) {
                            alert(data.msg || "Ocorreu algum erro.");
                            return;
                        }

                        // Update download links and thumbnail
                        $('#download_sd').attr('href', data.links.sd);
                        $('#download_hd').attr('href', data.links.hd);
                        $('#video_thumbnail').attr('src', data.thumbnail).show();

                        // Display the success div if hidden
                        $('#div_resultado_sucesso').show();
                    },
                    error: function(error) {
                        console.log('Error:', error);
                    }
                });
            });
        });
    </script>
</body>

</html>