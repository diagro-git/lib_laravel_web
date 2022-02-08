<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kies bedrijf</title>
    <script src="https://kit.fontawesome.com/58a6f9de85.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="shortcut icon" type="image/x-icon" href="/assets/diagro/img/diagro.ico"/>
    <style type="text/css">
        body,html{
            height:100%;
        }
        .overlay {
            background-color: rgba(0, 0, 0, 0.5);
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<body style="background:url('/assets/diagro/img/bg.jpg');background-size:cover;background-repeat:no-repeat;background-position:center;">
<div class="overlay"></div>
<div class="container h-100">
    <div class="row h-100 justify-content-center align-items-center">

        <span class="fa-stack fa-4x" style="position:relative; top:-120px;left:38%;z-index:2;">
            <i class="fas fa-circle fa-stack-2x fa-inverse"></i>
            <i class="fas fa-building fa-stack-1x"></i>
        </span>

        <div class="col-8" style="background:white;padding:60px;border-radius:10px;box-shadow:0 0 14px 0 rgba(0,0,0,0.2);">

            <form class="form" method="post" action="{{ route('company') }}">
                @csrf

                <div class="form-group">
                    <label for="company">Kies bedrijf:</label>
                    <select name="company" id="company" class="form-control">
                        @foreach($companies as $company)
                        <option value="{{ $company['name'] }}">{{ $company['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <button class="btn btn-primary" type="submit">Ga verder</button>

            </form>

        </div>
    </div>
</div>
<!-- jQuery first, then Popper.js, and then Bootstrap's JavaScript -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>
