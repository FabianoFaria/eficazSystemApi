<!DOCTYPE html>
<html lang="pt-BR">
    <head>

    	@include('includes.head_email')

    </head>


 	<body bgcolor="#222222" width="100%" style="margin: 0;">

        <center style="width: 100%; background: #222222;">

            @include('includes.header_email')


            @yield('content')  
            
               
            @include('includes.footer_email') 

        </center>

 
    </body>

</html>