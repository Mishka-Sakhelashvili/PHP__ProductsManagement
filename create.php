<?php 

  require_once "functions.php";

  $pdo = new PDO("mysql:host=localhost;port=3306;dbname=products_managment;", "root", "");
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $errors = [];

  $title = '';
  $description = '';
  $price = '';

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    $image = $_FILES['image'] ?? null;
    $imagePath = '';

    if (!is_dir('images')) {
        mkdir('images');
    }
    if ($image && $image['tmp_name']) {
        $imagePath = 'images/' . randomString(20) . '/' . $image['name'];
        mkdir(dirname($imagePath));
        move_uploaded_file($image['tmp_name'], $imagePath);
    }

    if (!$title) {
      $errors[] = 'Product title is required';
    }
    if (!$price) {
      $errors[] = 'Product price is required';
    }
    if (empty($errors)) {
      $statement = $pdo->prepare("INSERT INTO products (title, image, description, price, create_date)
              VALUES (:title, :image, :description, :price, :date)");
      $statement->bindValue(':title', $title);
      $statement->bindValue(':image', $imagePath);
      $statement->bindValue(':description', $description);
      $statement->bindValue(':price', $price);
      $statement->bindValue(':date', date('Y-m-d H:i:s'));

      $statement->execute();
      header('Location: index.php');
    }
  }
?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link rel="stylesheet" href="./app.css">
    <title>Products Managment!</title>
  </head>
  <body>

    <h1>Create new product!</h1>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
          <?php foreach ($errors as $error): ?>
              <div><?php echo $error ?></div>
          <?php endforeach; ?>
      </div>
    <?php endif; ?>     

    <form method="post" enctype="multipart/form-data">
      <div class="form-group">
        <label>Product Image</label><br>
        <input type="file" name="image">
      </div>
      <div class="form-group">
          <label>Product title</label>
          <input type="text" name="title" class="form-control" value="<?php echo $title ?>">
      </div>
      <div class="form-group">
          <label>Product description</label>
          <textarea class="form-control" name="description"><?php echo $description ?></textarea>
      </div>
      <div class="form-group">
          <label>Product price</label>
          <input type="number" step=".01" name="price" class="form-control" value="<?php echo $price ?>">
      </div>
      <button type="submit" class="btn btn-primary">Submit</button>
    </form>


  </body>
</html>