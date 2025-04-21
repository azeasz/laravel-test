<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unpkg.com/98.css">
    <title>Overseer Management</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            display: flex;
        }
        .content {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .title-bar {
            background: linear-gradient(90deg, #347928, #679995);
            color: white;
        }
    </style>
</head>
<body>
    @include('admin.sidebar')
    <div class="content">
        <div class="window" style="width: 600px;">
            <div class="title-bar">
                <div class="title-bar-text">Overseer Management</div>
            </div>
            <div class="window-body">
                <p>Manage Overseer Users Here</p>
                <!-- Tambahkan konten manajemen overseer di sini -->
            </div>
        </div>
    </div>
</body>
</html>
