<?php

require_once "auth-check.php";

header("Content-Type: application/json");

try {

    /*
    |--------------------------------------------------------------------------
    | GET ACTION
    |--------------------------------------------------------------------------
    */

    $action = $_POST["action"] ?? $_GET["action"] ?? "";


    /*
    |--------------------------------------------------------------------------
    | LIST MODEL PAPERS
    |--------------------------------------------------------------------------
    */

    if ($action === "list") {

        $search  = trim($_GET["search"] ?? "");
        $subject = trim($_GET["subject"] ?? "");
        $grade   = trim($_GET["grade"] ?? "");

        $sql = "
            SELECT
                id,
                title,
                description,
                subject,
                grade,
                paper_type,
                paper_year,
                file_name,
                file_path,
                uploaded_by,
                created_at
            FROM model_papers
            WHERE 1 = 1
        ";

        $params = [];
        $types = "";


        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if ($search !== "") {

            $sql .= "
                AND (
                    title LIKE ?
                    OR description LIKE ?
                    OR subject LIKE ?
                    OR paper_type LIKE ?
                )
            ";

            $searchValue = "%" . $search . "%";

            $params[] = $searchValue;
            $params[] = $searchValue;
            $params[] = $searchValue;
            $params[] = $searchValue;

            $types .= "ssss";
        }


        /*
        |--------------------------------------------------------------------------
        | SUBJECT FILTER
        |--------------------------------------------------------------------------
        */

        if ($subject !== "") {

            $sql .= " AND subject = ?";

            $params[] = $subject;

            $types .= "s";
        }


        /*
        |--------------------------------------------------------------------------
        | GRADE FILTER
        |--------------------------------------------------------------------------
        */

        if ($grade !== "") {

            $sql .= " AND grade = ?";

            $params[] = $grade;

            $types .= "s";
        }


        /*
        |--------------------------------------------------------------------------
        | ORDER
        |--------------------------------------------------------------------------
        */

        $sql .= "
            ORDER BY paper_year DESC, created_at DESC
        ";


        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            throw new Exception(
                "Database prepare error: " . $conn->error
            );
        }


        /*
        |--------------------------------------------------------------------------
        | BIND FILTER PARAMETERS
        |--------------------------------------------------------------------------
        */

        if (count($params) > 0) {

            $stmt->bind_param(
                $types,
                ...$params
            );

        }


        $stmt->execute();

        $result = $stmt->get_result();

        $papers = [];


        while ($row = $result->fetch_assoc()) {

            $papers[] = $row;

        }


        $stmt->close();


        echo json_encode([
            "success" => true,
            "papers" => $papers,
            "count" => count($papers)
        ]);

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | ADD MODEL PAPER
    |--------------------------------------------------------------------------
    */

    if ($action === "add") {

        $title = trim(
            $_POST["title"] ?? ""
        );

        $description = trim(
            $_POST["description"] ?? ""
        );

        $subject = trim(
            $_POST["subject"] ?? ""
        );

        $grade = trim(
            $_POST["grade"] ?? ""
        );

        $paperType = trim(
            $_POST["paper_type"] ?? "Model Paper"
        );

        $paperYear = (int) (
            $_POST["paper_year"] ?? 0
        );


        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        if (
            $title === "" ||
            $subject === "" ||
            $grade === "" ||
            $paperYear <= 0
        ) {

            echo json_encode([
                "success" => false,
                "message" => "Please fill all required fields."
            ]);

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | FILE CHECK
        |--------------------------------------------------------------------------
        */

        if (
            !isset($_FILES["paper"]) ||
            $_FILES["paper"]["error"] !== UPLOAD_ERR_OK
        ) {

            echo json_encode([
                "success" => false,
                "message" => "Please select a PDF file."
            ]);

            exit;
        }


        $file = $_FILES["paper"];


        /*
        |--------------------------------------------------------------------------
        | FILE SIZE
        |--------------------------------------------------------------------------
        */

        if ($file["size"] > 10 * 1024 * 1024) {

            echo json_encode([
                "success" => false,
                "message" => "Maximum file size is 10 MB."
            ]);

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | FILE EXTENSION
        |--------------------------------------------------------------------------
        */

        $extension = strtolower(
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


        /*
        |--------------------------------------------------------------------------
        | MIME TYPE
        |--------------------------------------------------------------------------
        */

        $mimeType = "";

        if (
            function_exists("finfo_open")
        ) {

            $finfo = finfo_open(
                FILEINFO_MIME_TYPE
            );

            $mimeType = finfo_file(
                $finfo,
                $file["tmp_name"]
            );

            finfo_close($finfo);

        } else {

            $mimeType = $file["type"] ?? "";

        }


        if (
            $mimeType !== "application/pdf"
        ) {

            echo json_encode([
                "success" => false,
                "message" => "Invalid PDF file."
            ]);

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | UPLOAD DIRECTORY
        |--------------------------------------------------------------------------
        */

        $uploadDir = "../uploads/model-papers/";


        if (!is_dir($uploadDir)) {

            if (
                !mkdir(
                    $uploadDir,
                    0777,
                    true
                )
            ) {

                throw new Exception(
                    "Unable to create upload folder."
                );

            }

        }


        /*
        |--------------------------------------------------------------------------
        | UNIQUE FILE NAME
        |--------------------------------------------------------------------------
        */

        $newFileName =
            "model_" .
            date("Ymd_His") .
            "_" .
            bin2hex(random_bytes(5)) .
            ".pdf";


        $filePath =
            "uploads/model-papers/" .
            $newFileName;


        $fullFilePath =
            $uploadDir .
            $newFileName;


        /*
        |--------------------------------------------------------------------------
        | MOVE FILE
        |--------------------------------------------------------------------------
        */

        if (
            !move_uploaded_file(
                $file["tmp_name"],
                $fullFilePath
            )
        ) {

            throw new Exception(
                "Failed to upload PDF file."
            );

        }


        /*
        |--------------------------------------------------------------------------
        | ADMIN USER ID
        |--------------------------------------------------------------------------
        */

        $uploadedBy =
            isset($_SESSION["user_id"])
                ? (int) $_SESSION["user_id"]
                : null;


        /*
        |--------------------------------------------------------------------------
        | INSERT DATABASE
        |--------------------------------------------------------------------------
        */

        $stmt = $conn->prepare("
            INSERT INTO model_papers
            (
                title,
                description,
                subject,
                grade,
                paper_type,
                paper_year,
                file_name,
                file_path,
                uploaded_by
            )
            VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");


        if (!$stmt) {

            if (file_exists($fullFilePath)) {
                unlink($fullFilePath);
            }

            throw new Exception(
                "Database prepare error: " .
                $conn->error
            );

        }


        /*
        |--------------------------------------------------------------------------
        | IMPORTANT BIND PARAM
        |--------------------------------------------------------------------------
        |
        | 9 variables:
        |
        | title       = s
        | description = s
        | subject     = s
        | grade       = s
        | paper_type  = s
        | paper_year  = i
        | file_name   = s
        | file_path   = s
        | uploaded_by = i
        |
        |--------------------------------------------------------------------------
        */

        $stmt->bind_param(
            "ssssisssi",
            $title,
            $description,
            $subject,
            $grade,
            $paperType,
            $paperYear,
            $file["name"],
            $filePath,
            $uploadedBy
        );


        if (!$stmt->execute()) {

            if (file_exists($fullFilePath)) {
                unlink($fullFilePath);
            }

            throw new Exception(
                "Database insert error: " .
                $stmt->error
            );

        }


        $newId = $stmt->insert_id;

        $stmt->close();


        echo json_encode([
            "success" => true,
            "message" => "Model paper uploaded successfully.",
            "id" => $newId
        ]);

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE MODEL PAPER
    |--------------------------------------------------------------------------
    */

    if ($action === "delete") {

        $id = (int) (
            $_POST["id"] ?? 0
        );


        if ($id <= 0) {

            echo json_encode([
                "success" => false,
                "message" => "Invalid paper ID."
            ]);

            exit;
        }


        /*
        |--------------------------------------------------------------------------
        | GET FILE PATH
        |--------------------------------------------------------------------------
        */

        $stmt = $conn->prepare("
            SELECT file_path
            FROM model_papers
            WHERE id = ?
            LIMIT 1
        ");


        if (!$stmt) {

            throw new Exception(
                "Database prepare error: " .
                $conn->error
            );

        }


        $stmt->bind_param(
            "i",
            $id
        );

        $stmt->execute();

        $result = $stmt->get_result();


        if ($result->num_rows !== 1) {

            $stmt->close();

            echo json_encode([
                "success" => false,
                "message" => "Model paper not found."
            ]);

            exit;
        }


        $paper = $result->fetch_assoc();

        $stmt->close();


        /*
        |--------------------------------------------------------------------------
        | DELETE DATABASE RECORD
        |--------------------------------------------------------------------------
        */

        $stmt = $conn->prepare("
            DELETE FROM model_papers
            WHERE id = ?
        ");


        if (!$stmt) {

            throw new Exception(
                "Database prepare error: " .
                $conn->error
            );

        }


        $stmt->bind_param(
            "i",
            $id
        );


        if (!$stmt->execute()) {

            throw new Exception(
                "Database delete error: " .
                $stmt->error
            );

        }


        $stmt->close();


        /*
        |--------------------------------------------------------------------------
        | DELETE PDF
        |--------------------------------------------------------------------------
        */

        $physicalPath =
            "../" .
            $paper["file_path"];


        if (
            file_exists($physicalPath)
        ) {

            unlink($physicalPath);

        }


        echo json_encode([
            "success" => true,
            "message" => "Model paper deleted successfully."
        ]);

        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | INVALID ACTION
    |--------------------------------------------------------------------------
    */

    echo json_encode([
        "success" => false,
        "message" => "Invalid action."
    ]);

} catch (Throwable $e) {

    /*
    |--------------------------------------------------------------------------
    | DEBUG ERROR
    |--------------------------------------------------------------------------
    */

    http_response_code(200);

    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);

}

?>