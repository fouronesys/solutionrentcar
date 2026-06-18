<?php
$base = new Database();
$con = $base->connect();

$opt = isset($_GET["opt"]) ? $_GET["opt"] : "";

$stock_id = isset($_SESSION["stock_id"]) ? intval($_SESSION["stock_id"]) : 1;
$user_id  = isset($_SESSION["user_id"]) ? intval($_SESSION["user_id"]) : 0;

function colExists($con, $table, $column){
    $table = $con->real_escape_string($table);
    $column = $con->real_escape_string($column);

    $q = $con->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
    return ($q && $q->num_rows > 0);
}

function safe($con, $value){
    return $con->real_escape_string(trim($value));
}

/* =====================================================
   AGREGAR CAMPAÑA
===================================================== */
if($opt == "add"){

    $title             = safe($con, $_POST["title"] ?? "");
    $start_date        = safe($con, $_POST["start_date"] ?? "");
    $end_date          = safe($con, $_POST["end_date"] ?? "");
    $min_rental_days   = intval($_POST["min_rental_days"] ?? 1);
    $winners_limit     = intval($_POST["winners_limit"] ?? 1);
    $prize_description = safe($con, $_POST["prize_description"] ?? "");
    $rule_description  = safe($con, $_POST["rule_description"] ?? "");
    $description       = safe($con, $_POST["description"] ?? "");

    if($title == "" || $start_date == "" || $end_date == "" || $prize_description == ""){
        Core::redir("./?view=raffle&opt=new");
        exit;
    }

    $sql = "
    INSERT INTO raffles
    (
        stock_id,
        title,
        campaign_type,
        description,
        rule_description,
        start_date,
        end_date,
        min_rental_days,
        winners_limit,
        participation_type,
        ticket_price,
        total_tickets,
        sold_tickets,
        status,
        created_at
    )
    VALUES
    (
        '$stock_id',
        '$title',
        'rental',
        '$description',
        '$rule_description',
        '$start_date',
        '$end_date',
        '$min_rental_days',
        '$winners_limit',
        'automatic',
        0,
        0,
        0,
        'active',
        NOW()
    )
    ";

    $con->query($sql);

    $raffle_id = $con->insert_id;

    if($raffle_id > 0){

        $sql_prize = "
        INSERT INTO raffle_prizes
        (
            stock_id,
            raffle_id,
            prize_type,
            prize_description,
            prize_value,
            prize_order
        )
        VALUES
        (
            '$stock_id',
            '$raffle_id',
            'other',
            '$prize_description',
            0,
            1
        )
        ";

        $con->query($sql_prize);
    }

    Core::redir("./?view=raffle&opt=all");
    exit;
}


/* =====================================================
   ACTUALIZAR CAMPAÑA
===================================================== */
if($opt == "update"){

    $id                = intval($_POST["id"] ?? 0);
    $title             = safe($con, $_POST["title"] ?? "");
    $start_date        = safe($con, $_POST["start_date"] ?? "");
    $end_date          = safe($con, $_POST["end_date"] ?? "");
    $min_rental_days   = intval($_POST["min_rental_days"] ?? 1);
    $winners_limit     = intval($_POST["winners_limit"] ?? 1);
    $rule_description  = safe($con, $_POST["rule_description"] ?? "");
    $description       = safe($con, $_POST["description"] ?? "");
    $prize_description = safe($con, $_POST["prize_description"] ?? "");

    if($id <= 0){
        Core::redir("./?view=raffle&opt=all");
        exit;
    }

    $sql = "
    UPDATE raffles SET
        title = '$title',
        description = '$description',
        rule_description = '$rule_description',
        start_date = '$start_date',
        end_date = '$end_date',
        min_rental_days = '$min_rental_days',
        winners_limit = '$winners_limit'
    WHERE id = '$id'
    AND stock_id = '$stock_id'
    LIMIT 1
    ";

    $con->query($sql);

    if($prize_description != ""){

        $check = $con->query("
            SELECT id 
            FROM raffle_prizes 
            WHERE raffle_id = '$id' 
            LIMIT 1
        ");

        if($check && $check->num_rows > 0){

            $p = $check->fetch_assoc();

            $con->query("
                UPDATE raffle_prizes SET
                    prize_description = '$prize_description'
                WHERE id = '".$p["id"]."'
                LIMIT 1
            ");

        }else{

            $con->query("
                INSERT INTO raffle_prizes
                (
                    stock_id,
                    raffle_id,
                    prize_type,
                    prize_description,
                    prize_value,
                    prize_order
                )
                VALUES
                (
                    '$stock_id',
                    '$id',
                    'other',
                    '$prize_description',
                    0,
                    1
                )
            ");
        }
    }

    Core::redir("./?view=raffle&opt=all");
    exit;
}


/* =====================================================
   SI NO EXISTE OPCIÓN
===================================================== */
Core::redir("./?view=raffle&opt=all");
exit;
?>