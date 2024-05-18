<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Image Extension Converter</title>
    
    <!--Bootstrap-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

    <div class="container">
        <div class="card mt-5">
            <div class="card-header">Upload Images</div>
            <div class="card-body">
                <form action="{{route('image-converter')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Images</label>
                        <input type="file" name="files[]" multiple  class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Format (Convert to)</label>
                        <select name="convert_to" class="form-select">
                            <option value="png">PNG</option>
                            <option value="jpg">JPG</option>
                            <option value="jpeg">JPEG</option>
                            <option value="gif">GIF</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <button class="btn btn-primary mt-2">Convert</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>