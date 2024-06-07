<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Downloader</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #div_resultado_sucesso {
            display: none;
            margin-top: 20px;
        }

        #video_metadata {
            margin-top: 20px;
        }

        .video-info {
            margin-top: 10px;
        }
    </style>
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
        <div id="div_resultado_sucesso">
            <center>
                <video id="video_preview" controls style="max-height: 100%; width: 300px;" class="img-fluid mb-2" poster=""></video>
            </center>
            <div class="d-flex justify-content-center">
                <a id="download_sd" class="btn btn-success mx-2" href="#" download="video_sd.mp4">Download SD</a>
                <a id="download_hd" class="btn btn-info mx-2" href="#" download="video_hd.mp4">Download HD</a>
            </div>
            <div id="video_metadata" class="text-center">
                <!-- Placeholder for video metadata -->
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
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
                        if (!data.success) {
                            alert(data.msg || "Ocorreu algum erro.");
                            return;
                        }
                        $('#download_sd').attr('href', data.links.sd);
                        $('#download_hd').attr('href', data.links.hd);
                        //$('#video_thumbnail').attr('src', data.thumbnail).show();

                        $('#video_preview').attr('poster', data.thumbnail); // Set the poster attribute
                        $('#video_preview').attr('src', (data.links.hd || data.links.sd));

                        fetchVideoDuration('sd', data.links.sd);
                        fetchVideoDuration('hd', data.links.hd);
                        $('#div_resultado_sucesso').show();
                    },
                    error: function(error) {
                        console.log('Error:', error);
                    }
                });
            });
        });

        function fetchVideoDuration(quality, videoURL) {
            var video = document.createElement('video');
            video.src = videoURL;
            video.addEventListener('loadedmetadata', function() {
                const videoInfoHtml = `
                    <div class="video-info">
                        <strong>${quality.toUpperCase()} Info:</strong><br>
                        Height: ${video.videoHeight}px<br>
                        Width: ${video.videoWidth}px<br>
                        Duration: ${video.duration.toFixed(2)} seconds
                    </div>`;
                $('#video_metadata').append(videoInfoHtml);
            });
            video.addEventListener('error', function() {
                console.error('Error loading video');
                alert('Failed to load video metadata.');
            });
        }
    </script>
</body>

</html>