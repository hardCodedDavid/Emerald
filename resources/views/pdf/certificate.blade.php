<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Sacramento&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300&display=swap" rel="stylesheet">
    <title>Sponsor Certificate</title>
    <style type="text/css" media="all">
        @page {
            margin: 0;
            padding: 0;
        }
        html{
            width: 100%;
            max-height: 100%;
            padding: 0 !important;
            margin: 0 !important;
        }
        body{
            padding: 0 !important;
            width: 100%;
            margin: 0 !important;
            height: 100%;
        }
        .certificate{
            width: 100%;
            max-height: 100%;
            font-size: 50px;
            font-family: 'Poppins', sans-serif;
            position: relative !important;
        }

        .certificate #bg img{
            position: absolute;
            max-width: 100%;
        }

        .certificate .name{
            position: absolute !important;
            text-transform: capitalize;
            top: 480px;
            left: 50%;
            font-size: 38px;
            transform: translateX(-50%);
            font-family: 'Sacramento', cursive;
            color: #03c003;
            width: 100% !important;
            text-align: center;
            letter-spacing: 5px;
            word-spacing: 10px;
            line-height: 23px;
        }

        .certificate .details{
            position: absolute;
            top: 590px;
            left: 50%;
            font-size: 18px;
            text-align: center;
            width: 100% !important;
            transform: translateX(-50%);
        }

        .certificate .date{
            position: absolute;
            bottom: 250px;
            left: 50%;
            font-size: 18px;
            text-align: center;
            transform: translateX(-50%);
            font-weight: bold;
        }

        @media (max-width: 700px) {
            body{
                width: 100%;
            }
        }
    </style>
</head>
<body>
<div class="certificate" style="position: relative">
    @if($sponsor == 'silver')
        <div style="position: absolute" id="bg"><img src="https://test.emeraldfarms.ng/assets/img/certificates/silver.png" alt="bg"></div>
    @elseif($sponsor == 'gold')
        <div style="position: absolute" id="bg"><img src="https://test.emeraldfarms.ng/assets/img/certificates/gold.png" alt="bg"></div>
    @elseif($sponsor == 'platinum')
        <div style="position: absolute" id="bg"><img src="https://test.emeraldfarms.ng/assets/img/certificates/platinum.png" alt="bg"></div>
    @endif
    <div style="position: absolute" class="name">
        {{ ucwords(strtolower($name)) }}
    </div>
    <div style="position: absolute" class="details">
        Has successfully invested in {{ $units }} unit of Emerald Farms ({{ $farm }}) and approved by the Board of
        Emerald Farms and Consultants Limited and is therefore awarded this.
    </div>
    <div style="position: absolute" class="date">
        {{ $date }}
    </div>
    <div style="position: absolute" class="date"></div>
</div>
</body>
</html>
