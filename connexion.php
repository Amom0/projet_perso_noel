<?php 
    session_start();
    include("header.php"); 
    $users = $bdd->prepare("SELECT * FROM user");
    $users->execute(); 

?>

<body>

    <div class="box_connec" id="Login">
        <h2 class='titre_connec'>Login</h2>
        <form method='POST'>
            <input class="box_grande" type="text" name="login" placeholder="Identifiant" required>
            <input class="box_grande" type="password" name="passwrd" placeholder="Mot de passe" required>
            <button class="bouton_submit" type="submit" name="action" value="connexion">Se connecter</button>
        </form>

        <?php
            if(isset($_POST['action']) && $_POST['action'] == "connexion" && isset($_POST['login']) && isset($_POST['passwrd'])){
                foreach($users as $user){
                    //echo $_POST['login'].' '.$user['pseudo'].' '.$_POST['passwrd'].' '.$user['passwd'];
                    if($_POST['login'] == $user['pseudo'] && md5($_POST['passwrd']) == $user['passwd']){
                        $_SESSION['id'] = $user['id_user'];
                        $_SESSION['pseudo'] = $user['pseudo'];
                        $_SESSION['mdp'] = $user['passwd'];
                        $_SESSION['grade'] = $user['grade'];
			echo "<script type='text/javascript'>document.location.replace('menu.php');</script>";
                        exit();	
                    } 
                    // Si le pseudo ou le mot de passe n'est pas bon on ne connecte pas l'utilisateur
                    else{
                        $wrong = 1;
                    }
                }
		if($wrong==1){
                    echo "<script>wrong();</script>";
                }
            }
        ?>

    </div>
    <div class="box_connec" id="Register">
        <h2 class='titre_connec'>Register</h2>
        <form method='POST'>
            <input class="box_grande" type="text" name="pseudo" placeholder="Votre Pseudo / Identifiant" required>
            <p id='message' class='verif_mdp'></p>
            <input class="box_grande" type="password" name="password" id="password1" placeholder="Mot de passe" required>
            <input class="box_grande" onmouseout="check_pass('password1','password2','inscription');" type="password" name="password2" id="password2" placeholder="Vérification du mot de passe" required> 
            <button class="bouton_submit" id="inscription" type="submit" name="action" value="Inscription">S'inscrire</button>
        </form>

        <?php
            if(isset($_POST['action']) && $_POST['action'] == "Inscription"){
                $inscription = $bdd->prepare("INSERT INTO user (pseudo, passwd) VALUES ('".$_POST['pseudo']."','".md5($_POST['password'])."')");
                
                
                foreach($users as $user){
                    if($_POST['pseudo']==$user['pseudo']){
                        $exist = 1;
                    }else{
                        $exist = 0;
                    }
                }
                if($exist == 0){
                    $inscription->execute();
                    $id = $bdd->lastInsertId(); 
                    $_SESSION['id'] = $id;
                    $_SESSION['pseudo'] = $_POST['pseudo'];
                    $_SESSION['mdp'] = md5($_POST['password']);
                    $_SESSION['grade'] = 0;
                    echo "<script type='text/javascript'>document.location.replace('menu.php');</script>";
		    exit;
                }
                else{ ?>
                    <script>exist();</script>
                    <?php
                }
            }
        ?>
        
        

    
       
    </div>


