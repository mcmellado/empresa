
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Departamentos</title>
</head>
<body>
    <?php
    $desde_codigo = (isset($_GET['desde_codigo'])) ? trim($_GET['desde_codigo']) : null;
    $hasta_codigo = (isset($_GET['hasta_codigo'])) ? trim($_GET['hasta_codigo']) : null;
    $denominacion_empresa = (isset($_GET['denominacion_empresa'])) ? trim($_GET['denominacion_empresa']) : null;
    ?>
    <div>
        <form action="" method="get">
            <fieldset>
                <legend>Criterios de búsqueda</legend>
                <p>
                    <label>
                        Desde código:
                        <input type="text" name="desde_codigo" size="8" value="<?= $desde_codigo ?>">
                    </label>
                </p>
                <p>
                    <label>
                        Hasta código:
                        <input type="text" name="hasta_codigo" size="8" value="<?= $hasta_codigo ?>">
                    </label>

                </p> 
                <p>
                    <label>
                        Denominación:
                        <input type="text" name="denominacion_empresa" size="30" value="<?= $denominacion_empresa ?>">
                    </label>
                </p>
                <button type="submit">Buscar</button>
            </fieldset>
        </form>
    </div>
    <?php
    $pdo = new PDO('pgsql:host=localhost;dbname=empresa', 'empresa', 'empresa');
    $pdo->beginTransaction();
    $sent = $pdo->query('LOCK TABLE departamentos IN SHARE MODE');
    $where = [];
    $execute = [];
    if (isset($desde_codigo) && $desde_codigo != "") {
        $where[] = 'codigo >= :desde_codigo';
        $execute[':$desde_codigo'] = $desde_codigo;

    }

    if (isset($hasta_codigo) && $hasta_codigo != "") {
        $where[] = 'codigo >= :hasta_codigo';
        $execute[':$hasta_codigo'] = $desde_codigo;

    }

    if (isset($denominacion_empresa) && $denominacion_empresa != "") {
        $where[] = 'lower(denominacion) LIKE . lower(:denominacion_empresa)';
        $execute[':denominacion_empresa'] = "%$denominacion_empresa%"; 

    }

    $where = !empty($where) ? 'WHERE' . implode(' AND ', $where) : '';


    $sent = $pdo->prepare("SELECT COUNT(*)
                            FROM departamentos
                            $where");
    $sent->execute($execute);

    $total = $sent->fetchColumn();
    $sent = $pdo->prepare("SELECT *
                            FROM departamentos
                            $where ORDER BY codigo");

    $sent->execute($execute);
    $pdo->commit();
    ?>
    <br>
    <div>
        <table style="margin: auto" border="1">
            <thead>
                <th>Código</th>
                <th>Denominación</th>
            </thead>
            <tbody>
                <?php foreach ($sent as $fila): ?>
                    <tr>
                        <td><?= $fila['codigo'] ?></td>
                        <td><?= $fila['denominacion'] ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
        <p>Número total de filas: <?= $total ?></p>
    </div>
</body>
</html>