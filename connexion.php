<?php
	session_start();
	include_once('db/connexiondb.php');	
	
	if(isset($_SESSION['id'])){
		header('Location: /');
		exit;
	}

	
	if(!empty($_POST)){
		extract($_POST);
		$valid = (boolean) true;
		
		if(isset($_POST['connexion'])){
			$mail = (String) strtolower(trim($mail));
			$password = (String) trim($password);
			
			if(empty($mail)){
				$valid = false;
				$err_mail = "Veuillez renseigner ce champs !";
			}else{
				$req = $BDD->prepare("SELECT id
					FROM utilisateur 
					WHERE mail = ?");
					
				$req->execute(array($mail));
				$utilisateur = $req->fetch();
				
				if(!isset($utilisateur['id'])){
					$valid = false;
					$err_mail = "Veuillez renseigner ce champs !";
				}
			}
			
			if(empty($password)){
				$valid = false;
				$err_password = "Veuillez renseigner ce champs !";
			}
			
			$req = $BDD->prepare("SELECT id
					FROM utilisateur 
					WHERE mail = ? AND password = ?");
					
			$req->execute(array($mail, crypt($password, '$6$rounds=5000$H4eoaj87enek72Ondehb923Ybelman82jn83nN31O$')));
			$verif_utilisateur = $req->fetch();
				
			if(!isset($verif_utilisateur['id'])){
				$valid = false;
				$err_mail = "Veuillez renseigner ce champs !";
			}
			
			if($valid){
				
				$req = $BDD->prepare("INSERT INTO utilisateur (date_connexion) VALUES (?)");
				$req->execute(array(date("Y-m-d h:m:s")));
				
				
				$req = $BDD->prepare("SELECT *
					FROM utilisateur 
					WHERE id = ?");
					
				$req->execute(array($verif_utilisateur['id']));
				$verif_utilisateur = $req->fetch();
				
				$_SESSION['id'] = $verif_utilisateur['id'];
				$_SESSION['pseudo'] = $verif_utilisateur['pseudo'];
				$_SESSION['mail'] = $verif_utilisateur['mail'];
				
				header('Location: /');
				exit;
			}
		}	
	}	
?>
<!doctype html>
<html lang="fr">
	<head>
		<!-- Required meta tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		
		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

		<link rel="stylesheet" href="style.css">
				
		<title>Connexion</title>
	</head>
	<body>
		<?php
			require_once('menu.php');	
		?>
		
		<h1>Se connecter</h1>
		
		<form method="post">
			<section>
				<div>
					<?php
						if(isset($err_mail)){
							echo $err_mail;
						}	
					?>
					<input type="text" name="mail" placeholder="Mail" value="<?php if(isset($mail)){ echo $mail;} ?>">
				</div>
				<div>
					<?php
						if(isset($err_password)){
							echo $err_password;
						}	
					?>
					<input type="password" name="password" placeholder="Mot de passe" value="<?php if(isset($password)){ echo $password;} ?>">
				</div>
				
				<input type="submit" name="connexion" value="Se connecter">
			</section>
		</form>
		
		<!-- Optional JavaScript -->
		<!-- jQuery first, then Popper.js, then Bootstrap JS -->
		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
	</body>
</html>