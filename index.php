<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload and Duplication Check</title>
    <style>
    body {
        font-family: Arial, sans-serif;
    }

    .container {
        max-width: 600px;
        margin: 50px auto;
    }
    </style>
</head>

<body>
    <div class="container">
        <h1>Upload File</h1>
        <form action="rename.php" method="post" enctype="multipart/form-data">
            <label for="file">Choose file to upload:</label>
            <input type="file" id="file" name="file" required>
            <button type="submit">Upload</button>
        </form>
    </div>
</body>

</html>