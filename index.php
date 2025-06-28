<?php 
session_start();
if(isset($_SESSION['sign_up']) && $_SESSION['sign_up']){
    echo "<style> .start{display:none}  </style>";
}else{
    echo "<a herf='get-started.php' class='start'>Get started</a>";
}

?>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        POS System
    </title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

    <!-- Styles -->
    <style>
        html,
        body {
            background-color: #000;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
            margin-bottom: 80px;

        }

        .title {
            font-size: 64px;
            color: rgb(114, 250, 250);
        }

        .links>a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .m-b-md {
            margin-bottom: 30px;
        }
        @media (max-width:400px) {
            .links>a {
            color: #636b6f;
            padding: 0 10px;
            font-size: 9px;
            font-weight: 90px;
            letter-spacing: 1px;
            text-decoration: none;
            text-transform: uppercase;
        }
        .title {
            font-size: 34px;
        }
        img{
            margin-bottom: 30px;
        }
        }
        .start{
            text-decoration: none;
            color: white;
            border: 1px solid transparent;
            border-radius: 12px;
            background-color: #2ea2a2;
          padding:7px;
        }
    </style>
</head> 

<body>


   

    <div class="flex-center position-ref full-height">
        <div class="content">
        <img src="poslogo.png" alt="">

            <!-- <div  class=" title m-b-md">
                POS SYSTEM
            </div> -->

            <div class="links">
			<!-- For more projects: Visit Final projects  -->
                <a href="pos/admin/">Admin Log In</a>
                <a href="pos/cashier/">Cashier Log In</a>
                <!-- <a href="pos/customer">Customer Log In</a> -->
            </div>
        </div>
    </div>
</body>
<script type="text/javascript">
  (function(d, t) {
      var v = d.createElement(t), s = d.getElementsByTagName(t)[0];
      v.onload = function() {
        window.voiceflow.chat.load({
          verify: { projectID: '67454a4a8d43f36981214fc6' },
          url: 'https://general-runtime.voiceflow.com',
          versionID: 'production',
          voice: {
            url: "https://runtime-api.voiceflow.com"
          }
        });
      }
      v.src = "https://cdn.voiceflow.com/widget-next/bundle.mjs"; v.type = "text/javascript"; s.parentNode.insertBefore(v, s);
  })(document, 'script');
</script>
</html>