<?php

$base = new Database();
$con = $base->connect();

$stock_id = StockData::getPrincipal()->id;

// Asegurar que exista al menos 1 registro para este stock
$con->query("INSERT INTO preference (stock_id, descripcion) 
             SELECT $stock_id, '' FROM DUAL 
             WHERE NOT EXISTS (
                 SELECT 1 FROM preference WHERE stock_id = $stock_id
             )");

// Actualizar si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['code'])) {
    $stmt = $con->prepare("UPDATE preference SET descripcion = ? WHERE stock_id = ?");
    $stmt->bind_param("si", $_POST['code'], $stock_id);
    $stmt->execute();
}

// Obtener contenido actual
$result = $con->query("SELECT descripcion FROM preference WHERE stock_id = $stock_id");
$data = $result->fetch_assoc();
$contenido = $data ? $data['descripcion'] : '';
?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editor plantilla</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.15/codemirror.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.15/theme/dracula.min.css">
  <style>
  

    .header {
      background: #222;
      padding: 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: 0 2px 4px rgba(0,0,0,0.5);
    }

    .header h2 {
      margin: 0;
      color: #fff;
      font-weight: bold;
    }

    .btn-save {
      background-color: orange;
      color: white;
      border: none;
      padding: 12px 20px;
      font-size: 16px;
      border-radius: 6px;
      cursor: pointer;
      transition: 0.3s;
    }

   

    .editor-container {
        background: #333;
      padding: 20px;
    }

    .CodeMirror {
  height: 500px;
  border-radius: 6px;
  font-size: 15px;
  background: #222 !important;
  color: #f8f8f2; /* texto claro */
}

  </style>
</head>
<body>

  <div class="header">
    <h2>📝 Editor plantilla → Terminos del contrato</h2>
    <form method="POST" style="margin: 0;">
      <button type="submit" class="btn-save">💾 Guardar</button>
  </div>

   <div class="editor-container">
    <textarea id="code" name="code"><?= htmlspecialchars($contenido) ?></textarea>
  </div>
  </form>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.15/codemirror.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.15/mode/htmlmixed/htmlmixed.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.15/mode/xml/xml.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.15/mode/javascript/javascript.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.15/mode/css/css.min.js"></script>
  <script>
    CodeMirror.fromTextArea(document.getElementById("code"), {
      mode: "htmlmixed",
       theme: "material-darker",
      lineNumbers: true,
      tabSize: 2,
      lineWrapping: true,
      autoCloseBrackets: true
    });
  </script>

</body>
</html>
