<?php

session_start();

require_once "../db.php";

header("Content-Type: application/json");


// =====================================================
// CHECK ADMIN
// =====================================================

if (
    !isset($_SESSION["user_id"]) ||
    !isset($_SESSION["role"]) ||
    $_SESSION["role"] !== "admin"
) {

    echo json_encode([
        "success" => false,
        "message" => "Unauthorized access."
    ]);

    exit;
}


$action = $_POST["action"] ?? $_GET["action"] ?? "";


// =====================================================
// UPLOAD MATERIAL
// =====================================================

if ($action === "add") {

    $title =
        trim($_POST["title"] ?? "");

    $description =
        trim($_POST["description"] ?? "");

    $subject =
        trim($_POST["subject"] ?? "");

    $grade =
        trim($_POST["grade"] ?? "");

    $material_type =
        trim($_POST["material_type"] ?? "Notes");

    $material_year =
        $_POST["material_year"] ?? null;

    $uploaded_by =
        (int) $_SESSION["user_id"];


    // ================================================
    // VALIDATION
    // ================================================

    if (
        $title === "" ||
        $subject === "" ||
        $grade === ""
    ) {

        echo json_encode([
            "success" => false,
            "message" => "Please fill in all required fields."
        ]);

        exit;
    }


    // ================================================
    // FILE CHECK
    // ================================================

    if (
        !isset($_FILES["material_file"]) ||
        $_FILES["material_file"]["error"] !== UPLOAD_ERR_OK
    ) {

        echo json_encode([
            "success" => false,
            "message" => "Please select a PDF file."
        ]);

        exit;
    }


    $file =
        $_FILES["material_file"];


    // ================================================
    // FILE SIZE
    // ================================================

    if ($file["size"] > 10 * 1024 * 1024) {

        echo json_encode([
            "success" => false,
            "message" => "File size must be less than 10MB."
        ]);

        exit;
    }


    // ================================================
    // FILE EXTENSION
    // ================================================

    $extension =
        strtolower(
            pathinfo(
                $file["name"],
                PATHINFO_EXTENSION
            )
        );


    if ($extension !== "pdf") {

        echo json_encode([
            "success" => false,
            "message" => "Only PDF files are allowed."
        ]);

        exit;
    }


    // ================================================
    // MIME TYPE
    // ================================================

    $finfo =
        finfo_open(FILEINFO_MIME_TYPE);

    $mime =
        finfo_file(
            $finfo,
            $file["tmp_name"]
        );

    finfo_close($finfo);


    if ($mime !== "application/pdf") {

        echo json_encode([
            "success" => false,
            "message" => "Invalid PDF file."
        ]);

        exit;
    }


    // ================================================
    // UPLOAD DIRECTORY
    // ================================================

    $uploadDir =
        "../uploads/materials/";


    if (!is_dir($uploadDir)) {

        mkdir(
            $uploadDir,
            0777,
            true
        );
    }


    // ================================================
    // UNIQUE FILE NAME
    // ================================================

    $newFileName =
        time() .
        "_" .
        bin2hex(random_bytes(5)) .
        ".pdf";


    $destination =
        $uploadDir .
        $newFileName;


    // ================================================
    // MOVE FILE
    // ================================================

    if (
        !move_uploaded_file(
            $file["tmp_name"],
            $destination
        )
    ) {

        echo json_encode([
            "success" => false,
            "message" => "Failed to upload file."
        ]);

        exit;
    }


    $filePath =
        "uploads/materials/" .
        $newFileName;


    // ================================================
    // DATABASE INSERT
    // ================================================

    $stmt = $conn->prepare("
        INSERT INTO materials
        (
            title,
            description,
            subject,
            grade,
            material_type,
            material_year,
            file_name,
            file_path,
            uploaded_by
        )
        VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");


    $stmt->bind_param(
        "ssssssssi",
        $title,
        $description,
        $subject,
        $grade,
        $material_type,
        $material_year,
        $file["name"],
        $filePath,
        $uploaded_by
    );


    if ($stmt->execute()) {

        echo json_encode([
            "success" => true,
            "message" => "Material uploaded successfully."
        ]);

    } else {

        if (file_exists($destination)) {
            unlink($destination);
        }

        echo json_encode([
            "success" => false,
            "message" => "Database error: " . $stmt->error
        ]);
    }


    $stmt->close();

    exit;
}


// =====================================================
// GET MATERIALS
// =====================================================

if ($action === "list") {

    $search =
        trim($_GET["search"] ?? "");

    $subject =
        trim($_GET["subject"] ?? "");

    $grade =
        trim($_GET["grade"] ?? "");


    $sql = "
        SELECT
            id,
            title,
            description,
            subject,
            grade,
            material_type,
            material_year,
            file_name,
            file_path,
            created_at
        FROM materials
        WHERE 1=1
    ";


    $params = [];
    $types = "";


    if ($search !== "") {

        $sql .= "
            AND (
                title LIKE ?
                OR description LIKE ?
                OR subject LIKE ?
            )
        ";

        $searchValue =
            "%" . $search . "%";

        $params[] = $searchValue;
        $params[] = $searchValue;
        $params[] = $searchValue;

        $types .= "sss";
    }


    if ($subject !== "") {

        $sql .= " AND subject = ?";

        $params[] = $subject;

        $types .= "s";
    }


    if ($grade !== "") {

        $sql .= " AND grade = ?";

        $params[] = $grade;

        $types .= "s";
    }


    $sql .= "
        ORDER BY created_at DESC
    ";


    $stmt =
        $conn->prepare($sql);


    if (!empty($params)) {

        $stmt->bind_param(
            $types,
            ...$params
        );
    }


    $stmt->execute();

    $result =
        $stmt->get_result();


    $materials = [];


    while (
        $row =
        $result->fetch_assoc()
    ) {

        $materials[] = $row;
    }


    echo json_encode([
        "success" => true,
        "materials" => $materials,
        "count" => count($materials)
    ]);


    $stmt->close();

    exit;
}


// =====================================================
// DELETE MATERIAL
// =====================================================

if ($action === "delete") {

    $id =
        (int) ($_POST["id"] ?? 0);


    if ($id <= 0) {

        echo json_encode([
            "success" => false,
            "message" => "Invalid material ID."
        ]);

        exit;
    }


    $stmt =
        $conn->prepare("
            SELECT file_path
            FROM materials
            WHERE id = ?
            LIMIT 1
        ");


    $stmt->bind_param(
        "i",
        $id
    );

    $stmt->execute();

    $result =
        $stmt->get_result();

    $material =
        $result->fetch_assoc();

    $stmt->close();


    if (!$material) {

        echo json_encode([
            "success" => false,
            "message" => "Material not found."
        ]);

        exit;
    }


    // ================================================
    // DELETE DATABASE RECORD
    // ================================================

    $stmt =
        $conn->prepare("
            DELETE FROM materials
            WHERE id = ?
        ");


    $stmt->bind_param(
        "i",
        $id
    );


    if ($stmt->execute()) {

        $file =
            "../" .
            $material["file_path"];


        if (file_exists($file)) {
            unlink($file);
        }


        echo json_encode([
            "success" => true,
            "message" => "Material deleted successfully."
        ]);

    } else {

        echo json_encode([
            "success" => false,
            "message" => "Unable to delete material."
        ]);
    }


    $stmt->close();

    exit;
}


// =====================================================
// INVALID ACTION
// =====================================================

echo json_encode([
    "success" => false,
    "message" => "Invalid action."
]);

exit;

?>