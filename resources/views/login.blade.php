<!DOCTYPE html>
<html dir="ltr">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	<link rel="icon" type="image/png" sizes="16x16" href="{{asset('matrix/assets/images/favicon.png')}}">
	<title>Iniciar Sesión</title>
	<link href="{{asset('matrix/dist/css/style.min.css')}}" rel="stylesheet">
</head>

<body>
	<div class="main-wrapper">
		<div class="preloader">
			<div class="lds-ripple">
				<div class="lds-pos"></div>
				<div class="lds-pos"></div>
			</div>
		</div>
		<div class="auth-wrapper d-flex no-block justify-content-center align-items-center bg-dark">
			<div class="auth-box bg-dark border-top border-secondary">
				<div id="loginform">
					<div class="text-center p-t-20 p-b-20">
						<span class="db">
							<img src="{{asset('matrix/assets/images/logo-icon.png')}}" alt="logo"/>
						</span>
						<!--<span class="db"><img src="/assets/assets/images/favicon.png" alt="logo" size="120%" /></span>-->
						<h2 class="text-white">Sistema</h2>
					</div>
					<!-- Form -->
					<form class="form-horizontal m-t-20" id="loginform" method="POST" action="{{ route('validar') }}">
						@csrf
						<div class="row p-b-30">
							<div class="col-12">
								<div class="input-group mb-3">
									<div class="input-group-prepend">
										<span class="input-group-text bg-success text-white" id="basic-addon1"><i class="mdi mdi-account-key"></i></span>
									</div>
									<input type="text" name="usuario" value="" class="form-control form-control-lg" placeholder="Usuario" aria-label="Usuario" aria-describedby="basic-addon1" required="">
								</div>
								<div class="input-group mb-3">
									<div class="input-group-prepend">
										<span class="input-group-text bg-warning text-white" id="basic-addon2"><i class="mdi mdi-key-variant"></i></span>
									</div>
									<input type="password" name="contrasena" value="" class="form-control form-control-lg" placeholder="Contraseña" aria-label="Contraseña" aria-describedby="basic-addon1" required="">
								</div>
								@if(Session::has('mensaje'))
								<div class="text-center text-white">
									<h3>{{Session::get('mensaje')}}</h3>
								</div>
								@endif
							</div>
						</div>
						<div class="row border-top border-secondary">
							<div class="col-12">
								<div class="form-group">
									<div class="p-t-20">
										<!--<button class="btn btn-info" id="to-recover" type="button"><i class="fa fa-lock m-r-5"></i> Lost password?</button>-->
										<button class="btn btn-success float-right" type="submit">Iniciar Sesión</button>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
				{{--
					<div id="recoverform">
						<div class="text-center">
							<span class="text-white">Enter your e-mail address below and we will send you instructions how to recover a password.</span>
						</div>
						<div class="row m-t-20">
							<form class="col-12" action="index.html">
								<div class="input-group mb-3">
									<div class="input-group-prepend">
										<span class="input-group-text bg-danger text-white" id="basic-addon1"><i class="ti-email"></i></span>
									</div>
									<input type="text" class="form-control form-control-lg" placeholder="Email Address" aria-label="Username" aria-describedby="basic-addon1">
								</div>
								<div class="row m-t-20 p-t-20 border-top border-secondary">
									<div class="col-12">
										<a class="btn btn-success" href="#" id="to-login" name="action">Back To Login</a>
										<button class="btn btn-info float-right" type="button" name="action">Recover</button>
									</div>
								</div>
							</form>
						</div>
					</div>
					--}}
				</div>
			</div>
		</div>
		<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
		<script src="{{asset('matrix/assets/libs/bootstrap/dist/js/bootstrap.min.js')}}"></script>
		<script>
		$('[data-toggle="tooltip"]').tooltip();
		$(".preloader").fadeOut();
		$('#to-recover').on("click", function() {
			$("#loginform").slideUp();
			$("#recoverform").fadeIn();
		});
		$('#to-login').click(function(){
			$("#recoverform").hide();
			$("#loginform").fadeIn();
		});
		</script>
	</body>
	</html>
