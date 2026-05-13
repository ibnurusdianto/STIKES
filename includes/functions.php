<?php

function getAll($conn, $table, $order = "id DESC", $limit = null) {
    $query = "SELECT * FROM $table ORDER BY $order";
    if ($limit) {
        $query .= " LIMIT " . intval($limit);
    }
    $result = $conn->query($query);
    if ($result) {
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    return [];
}

function getBeritaTerbaru($conn, $limit = 3) {
    $query = "SELECT * FROM berita WHERE status = 'Publish' ORDER BY created_at DESC LIMIT ?";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $data;
    }
    return [];
}
?>
