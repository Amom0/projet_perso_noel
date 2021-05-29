<?php
session_start();
include("header.php"); 
if(isset($_SESSION['pseudo'])){
    $cases = $bdd->prepare("SELECT * FROM cases WHERE devoile=1");
    $cases->execute();
?>
    <body>
        <h1 id='titre_newpost'>Ecrire un nouveau post :</h1>
        <div id='box_newpost'>
            <form id="form_poster" method='POST' enctype='multipart/form-data'>
                <input id="box_titre" type="text" name="titre" placeholder="*Titre" maxlength=100 required>
                <span class='clear'></span>
		<div class="parent-div">
                    <button class="btn-upload">Choisir une image</button>
                    <input type="hidden" name="MAX_FILE_SIZE" value="5000000">
		    <input id='btn_up' type="file" name="picture" onchange="choix_image();">
                </div>
                <p id='fichier'></p>
		<span class='clear'></span>
		<label for="defi-select">JOUR numéro :</label>
                <select name="defi" id="defi-select" required>
                    <option value="">--Choisir--</option>
                    <?php foreach($cases as $case){?>
                        <option value="<?= $case['id_case']?>"><?= $case['intitule']?></option>
                    <?php }?>
                </select>
                <textarea class="box_text" name="contenu" placeholder="Contenu" form="form_poster" maxlength=30000 ></textarea>
                <button class="bouton_submit" type="submit" name="action" value="Publier">Publier !</button>
            </form>
            

            <?php
                $upload_folder = "./img/";
                
                if(isset($_POST['action']) && $_POST['action'] == "Publier"){
                    $_randomID = '';
                    if (isset($_FILES['picture']) && UPLOAD_ERR_NO_FILE != $_FILES['picture']['error']){
                        $_randomID = uniqid();
                        move_uploaded_file($_FILES['picture']['tmp_name'],$upload_folder.$_randomID);
                    }
		    $newpost = $bdd->prepare('INSERT INTO posts (id_user, titre, post_image, contenu, id_case) VALUES ('.$_SESSION["id"].',"'.htmlspecialchars($_POST["titre"], ENT_QUOTES).'","'.$_randomID.'","'.htmlspecialchars($_POST["contenu"],ENT_QUOTES).'",'.$_POST['defi'].')');
                    $newpost->execute();
		    echo "<script type='text/javascript'>document.location.replace('menu.php');</script>";exit();
                }
            
            ?>

            

        </div>
    </body>
<?php
}
else{
    ?>
    <h1>Connecte toi !</h1>
    <?php
}
?>    


<?php
    include("footer.php"); 
?>  
