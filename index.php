<?php

// Connexion à la BDD mdb
require_once __DIR__ . '/vendor/autoload.php';

$client = new MongoDB\Client("mongodb://localhost:27017");
$db = $client->TestDB;
$collection = $db->Wouf;

// Créer un document
if (isset($_POST['action']) && $_POST['action'] === 'Créer' && !empty($_POST['nom'])) {
    $collection->insertOne(['name' => $_POST['nom']]);
}

// Modification
if (isset($_POST['action']) && $_POST['action'] === 'Modifier' && !empty($_POST['id']) && !empty($_POST['nom'])) {
    $collection->updateOne(
        ['_id' => new \MongoDB\BSON\ObjectID($_POST['id'])],
        ['$set' => ['name' => $_POST['nom']]]
    );
    header('Location: index.php');
    exit();
}

// Supprimer un document
if (isset($_GET['supprimer'])) {
    $collection->deleteOne(['_id' => new \MongoDB\BSON\ObjectId($_GET['supprimer'])]);
    header('Location: index.php'); // Redirige vers la page principale pour éviter le rechargement du script de suppression
    exit;
}

$documents = $collection->find();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Test / Wouf</title>
</head>
<body>
    <h1>MongoDB</h1>
    <form action="index.php" method="post">
        Nom : <input type="text" name="nom" required>
        <input type="submit" name="action" value="Créer">
    </form>

    <!-- Formulaire pour entrer un nom -->
    <?php foreach ($documents as $document): ?>
        <div>
            <?php echo htmlspecialchars($document['name']); ?> - 
            <a href="?modifier=<?php echo htmlspecialchars($document['_id']); ?>">Modifier</a> | 
            <a href="?supprimer=<?php echo htmlspecialchars($document['_id']); ?>">Supprimer</a>
        </div>
    <?php endforeach; ?>

    <!-- Formulaire qui apparaît quand on appuie sur modifier -->
    <?php
        if (isset($_GET['modifier'])) {
            $document = $collection->findOne(['_id' => new \MongoDB\BSON\ObjectID($_GET['modifier'])]);
            ?>
            <form action="index.php" method="post">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($_GET['modifier']); ?>">
                Nom : <input type="text" name="nom" value="<?php echo htmlspecialchars($document['name']); ?>" required>
                <input type="submit" name="action" value="Modifier">
            </form>
            <?php
        }
?>
</body>
</html>