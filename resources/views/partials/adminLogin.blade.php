<!DOCTYPE html>
<html lang="en">
<head>
    <title>E-Moto Login</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        *{
    margin: 0;padding: 0;
}
body{
    display: block;
}
img{
    width: 100%;
}
.profile{
    position: relative;
    width: 100%;
    height: 100%;
}
.profile img{
    width: 200px;
    height: 200px;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%,-50%);
    border-radius: 100%;
    box-shadow: 0px 0px 20px #434343;
}

.head h2{
    font-size: 30px;
    font-weight: 500;
    margin-bottom: 45px;
}

.inputData input{
    width: 100%;
    margin-bottom: 10px;
    border: none;
    outline: none;
    position: relative;
    height: 50px;
    padding: 20px;
    border-radius: 25px;
    background: #eee;
}
.submit button{
    width: 100%;
    padding: 10px;
    text-align: center;
    color: #fff;
    background: green;
    margin-top: 30px;
    margin-bottom: 10px;
    outline: none;
    border: none;
    cursor: pointer;
    border-radius: 25px;
}

.forget a{
    color: darkgreen;
    text-align: center;
    display: block;
    font-weight: 600;
    margin-top: 10px;
}

a:hover{
    text-decoration: none;
}

.center-wrap{
    background: #fff;
}
@media (min-width:768px){
    body{
        height: 100vh;
        position: relative;
        background-image: linear-gradient(to right, #520680, #7A216C);
    }
    .center-wrap{
        border-radius: 10px;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%,-50%);
        overflow: hidden;
        padding: 50px 30px;
    }
}
 
    </style>
</head>
<body>
   <div class="container center-wrap">
      <div class="center">
       <div class="row">
           <div class="col-md-6 d-none d-md-block">
               <div class="profile">
                   <img src="{{asset('/logo.png')}}" alt="">
               </div>
           </div>
           <div class="col-md-6">
                @include('admin.partials.errorMassage')
                @include('admin.partials.massege')
               <form action="{{route('admin.onLogin')}}" method="POST">
                @csrf
               <div class="head pt-md-0 pt-4">
                   <h2 class="text-center text-capitalize">Admin login</h2>
               </div>
               <div class="inputData">
                <div class="inputmail">
                    <!--icon email-->
                  <input type="email" class="email" placeholder="Email Id" name="email"> 
                </div>
                  <div class="inputpass">
                      <input type="password" class="password" name="password" placeholder="Password">
                <!--icon lock-->
                  </div>
                  
               </div>
               <div class="submit">
                   <button type="submit" class="text-capitalize">login</button>
               </div>
            </form>
               <div class="forget pb-md-4">
                   <a href="{{route('admin.forgot')}}" class="text-capitalize">forget username / password</a>
               </div>
           </div>
       </div>
       </div>
   </div>
</body>
</html>