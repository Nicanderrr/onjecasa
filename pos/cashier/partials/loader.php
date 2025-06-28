<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <script src="jquery-2.1.4.js"></script>
    <style>
*{box-sizing:border-box}
body {
 
}
@keyframes loader {
  0% {transform:rotate(-45deg)}
  50%{transform:rotate(-135deg)}
  100%{transform:rotate(-225deg)}}
@keyframes span-1 {
  0% {transform:translate(0);}
  50%{transform:translate(-50px, 0);border-color:#EE4D68}
  100%{transform:translate(0);}}
@keyframes span-2 {
  0%{transform:translate(0);}
  50%{transform:translate(50px, 0);border-color:#875678}
  100%{transform:translate(0);}}
@keyframes span-3 {
  0%{transform:translate(0);}
  50%{transform:translate(0, -50px);border-color:#FF9900}
  100%{transform:translate(0);}}
@keyframes span-4 {
  0%{transform:translate(0);}
  50%{transform:translate(0, 50px);border-color:#00E4F6}
  100%{transform:translate(0);}}
.loader {
  width: 50px;
  height: 50px;
  position: relative;
  animation: loader 2s infinite ease-in-out;

}
.loader span {
  width: 50px;
  height: 50px;
  position: absolute;
  left: 0;
  top: 0;
  border: 4px solid #0B1B48;
}
.loader span:nth-child(1) {
  animation: span-1 2s ease-in-out infinite;
}
.loader span:nth-child(2) {
  animation: span-2 2s ease-in-out infinite;
}
.loader span:nth-child(3) {
  animation: span-3 2s ease-in-out infinite;
}
.loader span:nth-child(4) {
  animation: span-4 2s ease-in-out infinite;
}
#loader-wrapper{
  background-color: rgba(22, 22, 22, 1%);
    width: 100%;
    height:100vh;
    display: flex;
    align-items: center;
justify-content: center;    
}

    </style>
</head>
<body>
    <div id="loader-wrapper">
<div class="loader">
  <span></span>
  <span></span>
  <span></span>
  <span></span>
</div>
</div>
</body>
<script>
window.addEventListener("load",function(){
document.getElementById("loader-wrapper").style.display="none"; 
})

</script>
</html>