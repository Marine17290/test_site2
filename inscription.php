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
		
		if(isset($_POST['inscription'])){
			$pseudo = (String) trim($pseudo);
			$mail = (String) strtolower(trim($mail));
			$password = (String) trim($password);
			$jour = (int) $jour;
			$mois = (int) $mois;
			$annee = (int) $annee;
			$departement = (String) trim($departement);
			$date_naissance = (String) null;
			
			if(empty($pseudo)){
				$valid = false;
				$err_pseudo = "Choisissez un pseudo !";
			}else{
				$req = $BDD->prepare("SELECT id
					FROM utilisateur 
					WHERE pseudo = ?");
					
				$req->execute(array($pseudo));
				$utilisateur = $req->fetch();
				
				if(isset($utilisateur['id'])){
					$valid = false;
					$err_pseudo = "Ce pseudo existe déjà";
				}
			}
			
			if(empty($mail)){
				$valid = false;
				$err_mail = "Veuillez saisir votre E-mail !";
			}else{
				$req = $BDD->prepare("SELECT id
					FROM utilisateur 
					WHERE mail = ?");
					
				$req->execute(array($mail));
				$utilisateur = $req->fetch();
				
				if(isset($utilisateur['id'])){
					$valid = false;
					$err_mail = "Ce mail existe déjà";
				}
			}
			
			if(empty($password)){
				$valid = false;
				$err_password = "Veuillez saisir un mot de passe !";
			}
						
			if($jour <= 0 || $jour > 31){
				$valid = false;
				$err_jour = "Choisissez votre jour de naissance !";
			}
			
			$verif_mois = array(1, 2, 3);
						
			if(!in_array($mois, $verif_mois)){
				$valid = false;
				$err_mois = "Choisissez votre mois de naissance !";
			}
			
			$verif_annee = array(1990, 2000, 3);
						
			if(!in_array($annee, $verif_annee)){
				$valid = false;
				$err_annee = "Choisissez votre année de naissance !";
			}
			
			if(!checkdate($mois, $jour, $annee)){
				$valid = false;
				$err_date = "Date fausse";
			}else{
				$date_naissance = $annee . '-' . $mois . '-' . $jour;
			}
			
			
			$req = $BDD->prepare("SELECT departement
				FROM departement
				WHERE departement = ?");
			$req->execute(array($departement));
			
			$verif_departement = $req->fetch();
						
			if(!isset($verif_departement['departement'])){
				$valid = false;
				$err_departement = "Choisissez votre département !";
			}
			
			if($valid){
				$date_inscription = date("Y-m-d h:m:s");
				
				$password = crypt($password, '$6$rounds=5000$H4eoaj87enek72Ondehb923Ybelman82jn83nN31O$');
				
				$req = $BDD->prepare("INSERT INTO utilisateur (pseudo, mail, password, date_naissance, departement, date_inscription, date_connexion) 
					VALUES (?, ?, ?, ?, ?, ?, ?)");
					
				$req->execute(array($pseudo, $mail, $password, $date_naissance, $departement, $date_inscription, $date_inscription));
				
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
		
		<h1>Inscription</h1>
		
		<form method="post">
			<section>
				<div>
					<?php
						if(isset($err_pseudo)){
							echo $err_pseudo;
						}	
					?>
					<input type="text" name="pseudo" placeholder="Pseudo" value="<?php if(isset($pseudo)){ echo $pseudo;} ?>">
				</div>
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
				<div>
					<?php
						if(isset($err_jour)){
							echo $err_jour;
						}	
						if(isset($err_mois)){
							echo $err_mois;
						}	
						if(isset($err_annee)){
							echo $err_annee;
						}	
						if(isset($err_date)){
							echo $err_date;
						}	
					?>
					<select name="jour">
						<?php
							for($i = 1; $i <= 31; $i++){
						?>
						<option value="<?= $i ?>"><?= $i ?></option>
						<?php
							}
						?>
					</select>
					<select name="mois">
            <option value="1">Janvier</option>
            <option value="2">Février</option>
            <option value="3">Mars</option>
            <option value="4">Avril</option>
            <option value="5">Mai</option>
            <option value="6">Juin</option>
            <option value="7">Juillet</option>
            <option value="8">Août</option>
            <option value="9">Septembre</option>
            <option value="10">Octobre</option>
            <option value="11">Novembre</option>
            <option value="12">Décembre</option>
					</select>

					<select name="annee">
            <?php
				for ($i = 1945; $i <2010; $i ++)
				{
					echo '<option value=\'' . $i . '\'>' . $i . '</option>';
				}
				?>
          </select>
					
				</div>
				<div>
					<?php
						if(isset($err_departement)){
							echo $err_departement;
						}	
					?>
					<select name="departement">
          <option value="0">--</option>
          <option value="1">Ain</option>
          <option value="2">Aisne</option>
          <option value="3">Allier</option>
          <option value="4">Alpes de Haute Provence</option>
          <option value="5">Hautes Alpes</option>
          <option value="6">Alpes Maritimes</option>
          <option value="7">Ardèche</option>
          <option value="8">Ardennes</option>
          <option value="9">Ariège</option>
          <option value="10">Aube</option>
          <option value="11">Aude</option>
          <option value="12">Aveyron</option>
          <option value="13">Bouches du Rhône</option>
          <option value="14">Calvados</option>
          <option value="15">Cantal</option>
          <option value="16">Charente</option>
          <option value="17">Charente Maritime</option>
          <option value="18">Cher</option>
          <option value="19">Corrèze</option>
          <option value="2A">Corse du Sud</option>
          <option value="2B">Haute-Corse</option>
          <option value="21">Côte d'Or</option>
          <option value="22">Côtes d'Armor</option>
          <option value="23">Creuse</option>
          <option value="24">Dordogne</option>
          <option value="25">Doubs</option>
          <option value="26">Drôme</option>
          <option value="27">Eure</option>
          <option value="28">Eure et Loir</option>
          <option value="29">Finistère</option>
          <option value="30">Gard</option>
          <option value="31">Haute Garonne</option>
          <option value="32">Gers</option>
          <option value="33">Gironde</option>
          <option value="34">Hérault</option>
          <option value="35">Ille et Vilaine</option>
          <option value="36">Indre</option>
          <option value="37">Indre et Loire</option>
          <option value="38">Isère</option>
          <option value="39">Jura</option>
          <option value="40">Landes</option>
          <option value="41">Loir et Cher</option>
          <option value="42">Loire</option>
          <option value="43">Haute Loire</option>
          <option value="44">Loire Atlantique</option>
          <option value="45">Loiret</option>
          <option value="46">Lot</option>
          <option value="47">Lot et Garonne</option>
          <option value="48">Lozère</option>
          <option value="49">Maine et Loire</option>
          <option value="50">Manche</option>
          <option value="51">Marne</option>
          <option value="52">Haute Marne</option>
          <option value="53">Mayenne</option>
          <option value="54">Meurthe et Moselle</option>
          <option value="55">Meuse</option>
          <option value="56">Morbihan</option>
          <option value="57">Moselle</option>
          <option value="58">Nièvre</option>
          <option value="59">Nord</option>
          <option value="60">Oise</option>
          <option value="61">Orne</option>
          <option value="62">Pas de Calais</option>
          <option value="63">Puy de Dôme</option>
          <option value="64">Pyrénées Atlantiques</option>
          <option value="65">Hautes Pyrénées</option>
          <option value="66">Pyrénées Orientales</option>
          <option value="67">Bas Rhin</option>
          <option value="68">Haut Rhin</option>
          <option value="69">Rhône</option>
          <option value="70">Haute Saône</option>
          <option value="71">Saône et Loire</option>
          <option value="72">Sarthe</option>
          <option value="73">Savoie</option>
          <option value="74">Haute Savoie</option>
          <option value="75">Paris</option>
          <option value="76">Seine Maritime</option>
          <option value="77">Seine et Marne</option>
          <option value="78">Yvelines</option>
          <option value="79">Deux Sèvres</option>
          <option value="80">Somme</option>
          <option value="81">Tarn</option>
          <option value="82">Tarn et Garonne</option>
          <option value="83">Var</option>
          <option value="84">Vaucluse</option>
          <option value="85">Vendée</option>
          <option value="86">Vienne</option>
          <option value="87">Haute Vienne</option>
          <option value="88">Vosges</option>
          <option value="89">Yonne</option>
          <option value="90">Territoire de Belfort</option>
          <option value="91">Essonne</option>
          <option value="92">Hauts de Seine</option>
          <option value="93">Seine Saint Denis</option>
          <option value="94">Val de Marne</option>
          <option value="95">Val d'Oise</option>

						<?php
							if(isset($departement)){
								$req = $BDD->prepare("SELECT departement, departement_nom
									FROM departement
									WHERE departement = ?");
								$req->execute(array($departement));
								$voir_departement = $req->fetch();
						?>
						<option value="<?= $voir_departement['departement_id'] ?>"><?= $voir_departement['departement_nom'] ?></option>
						<?php
							}	

							$req = $BDD->prepare("SELECT departement_id, departement_nom
								FROM departement");
							$req->execute();
							$voir_departement = $req->fetchAll();	
							
							foreach($voir_departement as $vd){
						?>
						<option value="<?= $vd['departement_id'] ?>"><?= $vd['departement_nom'] ?></option>
						<?php	
							}
						?>
					</select>
				</div>
			
			
			<input type="submit" name="inscription" value="S'inscrire">
		</form>
		</section>
		<!-- Optional JavaScript -->
		<!-- jQuery first, then Popper.js, then Bootstrap JS -->
		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
	</body>
</html>