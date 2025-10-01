<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Bisca-Sueca Playing Cards</title>
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/gh/silvinor/bootlace@5.3.8/dist/css/bootlace.min.css"
      integrity="sha384-9L4CG7bj17EWwf9367x4D4fT62Z55bazBNq+gbrkgoxr0fzXCiKHb1GPxL1KukdQ"
      crossorigin="anonymous">
  </head>
  <style>
    @media (prefers-color-scheme: light) {
      body { background-color: #CCC; }
    }
    @media (prefers-color-scheme: dark) {
      body { background-color: #333; }
    }
  </style>
  <body>
    <div class="container">
      <h1 class="text-center display-1">Bisca-Sueca Playing Cards</h1>
      <div class="text-center">

        <?php for ($i = 1; $i <= 54; $i++) { ?>
          <a href="card.php?card=<?= $i ?>" target="card"><img width="164" height="224" src="card.php?card=<?= $i ?>" alt="<?= $i ?>"></a>
        <?php } ?>

        <?php for ($i = 1; $i <= 13; $i++) { ?>
          <a href="back.php?card=<?= $i ?>" target="card"><img width="164" height="224" src="back.php?card=<?= $i ?>" alt="<?= $i ?>"></a>
        <?php } ?>

      </div>
    </div>
  </body>
</html>
