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
    .clickable {
      cursor: pointer;
    }
    @media (prefers-color-scheme: light) {
      .modal-body,
      .popover-img,
      body { background-color: #CCC; }
    }
    @media (prefers-color-scheme: dark) {
      .modal-body,
      .popover-img,
      body { background-color: #333; }
    }
    #preview {
      transition: transform 0.5s ease;
      cursor: pointer;
    }
    .modal-body {
      overflow: auto;
      scrollbar-width: none;
      -ms-overflow-style: none;
    }
    .modal-body::-webkit-scrollbar {
      display: none;
    }
    .popover {
      max-width: inherit;
    }
    .popover-body {
      padding: 8px !important;
    }
  </style>
  <body>
    <div class="container">
      <h1 class="text-center display-1">Bisca-Sueca Playing Cards</h1>
      <div class="text-center">

        <?php for ($i = 1; $i <= 54; $i++) { ?>
          <span class="popable d-inline-block" tabindex="0" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content="card.php?card=<?= $i ?>">
          <img width="164" height="224" src="card.php?card=<?= $i ?>" alt="<?= $i ?>"
            data-bs-toggle="modal" data-bs-target="#exampleModal"
            class="clickable"
          />
          </span>
        <?php } ?>

        <?php for ($i = 1; $i <= 16; $i++) { ?>
          <span class="popable d-inline-block" tabindex="0" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-content="back.php?card=<?= $i ?>">
          <img width="164" height="224" src="back.php?card=<?= $i ?>" alt="<?= $i ?>"
             data-bs-toggle="modal" data-bs-target="#exampleModal"
             class="clickable" />
          </span>
        <?php } ?>

      </div>
    </div>
    <!-- Native JS here -->
    <script
      src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
      integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
      crossorigin="anonymous"></script>
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"
      integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y"
      crossorigin="anonymous"></script>
    <script>
      document.addEventListener("DOMContentLoaded", function() {

        const popoverTriggerList = document.querySelectorAll('.popable');
        const popoverList = [...popoverTriggerList].map(el => new bootstrap.Popover(el,
          {
            html: true,
            sanitize: true, // keep sanitiser
            customClass: 'my-popover',
            content: function() {
              const s = el.getAttribute('data-bs-content') || '';
              // 300x430 is 2.5"x3.5" (+ bleed incl.) on an iMac 27" .. so should be "real size" (at least for me)
              return `<img width="300" height="430" class="popover-img" src="${s}" alt="" /> `;
            }
          }
        ));

        document.querySelectorAll('.clickable').forEach(img => {
          img.addEventListener('click', function() {
            const card = document.getElementById('card');

            // Insert the new image dynamically
            card.innerHTML = `<img id="preview" src="${this.src}" width="822" height="1122" class="img-fluid">`;

            // Attach rotation logic to the new #preview
            const preview = document.getElementById('preview');
            let rotation = 0;

            preview.addEventListener('click', () => {
              rotation = (rotation + 90) % 360;
              preview.style.transform = `rotate(${rotation}deg)`;
            });
          });
        });

      });
    </script>
    <!-- Modal pop-up -->
    <div class="modal fade" tabindex="-1" id="exampleModal">
      <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
          <div class="modal-header">
            <small>Press <code>[Esc]</code> to close.</small>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p id="card" class="text-center"></p>
          </div>
          <div class="modal-footer justify-content-center">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
