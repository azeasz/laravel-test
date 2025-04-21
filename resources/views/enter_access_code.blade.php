<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('storage/logo/icon.png') }}">

    <title>Enter Access Code</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .code-input {
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 24px;
            margin: 0 5px;
        }
        .code-box {
            width: 40px;
            height: 40px;
            border: 1px solid #ccc;
            display: inline-block;
            text-align: center;
            font-size: 24px;
            translate: -400% 0;
            z-index: -1;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Enter Access Code</h1>
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        @if (session('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        @endif
        <form action="{{ route('access.code.verify') }}" method="POST" class="text-center">
            @csrf
            <div class="form-group">
                <label for="access_code_1">Access Code:</label>
                <div class="d-flex justify-content-center">
                    <input type="text" id="access_code" name="access_code" maxlength="6" class="form-control code-input" required style="width: 300px; letter-spacing: 10px; border: none; background: transparent; text-align: center; text-indent: -100000000px; color: transparent; position: relative; left: 120px; bottom: 5px;">
                    <div id="access_code_boxes" style="display: flex; justify-content: space-between;">
                        <div class="code-box"></div>
                        <div class="code-box"></div>
                        <div class="code-box"></div>
                        <div class="code-box"></div>
                        <div class="code-box"></div>
                        <div class="code-box"></div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-dark">Submit</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.getElementById('access_code').addEventListener('input', function() {
            const code = this.value.split('');
            const boxes = document.querySelectorAll('.code-box');
            boxes.forEach((box, index) => {
                box.textContent = code[index] || '';
            });
        });
    </script>
</body>
</html>
