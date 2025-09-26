<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Bisca-Sueca Playing Cards</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
      crossorigin="anonymous"
    />
  </head>
  <style>
    body {
      background-color: #777;
    }
  </style>
  <body>
    <div class="container m-5">
      <h1 class="text-center display-1">Bisca-Sueca Playing Cards</h1>
      <div class="text-center">

        <!--
        <img width="411" height="561" src="./card.php" alt="" class="img-thumbnail shadow" style="background:#7B97B3">

        <br> -->

        <?php for ($i = 1; $i <= 54; $i++) { ?>
        <a href="card.php?card=<?= $i ?>" target="card"><img width="164" height="224" src="card.php?card=<?= $i ?>" alt="<?= $i ?>"></a>
        <?php } ?>

      </div>
    </div>
  </body>
</html>
